#!/usr/bin/env bash
set -euo pipefail

ENDPOINT_ARGS=()
if [ -n "${S3_ENDPOINT:-}" ]; then
    ENDPOINT_ARGS=(--endpoint-url "${S3_ENDPOINT}")
fi

backup_database() {
    local db="$1"
    local timestamp filename tmpfile
    timestamp="$(date +%Y-%m-%dT%H-%M-%S)"
    filename="${timestamp}_${db}.sql.gz"
    tmpfile="/tmp/${filename}"

    echo "[$(date)] Iniciando backup de ${db}..."
    PGPASSWORD="${POSTGRES_PASSWORD}" pg_dump \
        -h "${POSTGRES_HOST}" \
        -U "${POSTGRES_USER}" \
        -d "${db}" \
        | gzip > "${tmpfile}"

    echo "[$(date)] Enviando para s3://${S3_BUCKET}/${S3_PREFIX}/${filename}..."
    aws s3 cp "${tmpfile}" "s3://${S3_BUCKET}/${S3_PREFIX}/${filename}" "${ENDPOINT_ARGS[@]}"
    rm -f "${tmpfile}"
    echo "[$(date)] Backup de ${db} concluído."
}

cleanup_old_backups() {
    if [ -z "${BACKUP_KEEP_DAYS:-}" ]; then
        return
    fi

    local cutoff
    cutoff="$(date -d "-${BACKUP_KEEP_DAYS} days" +%Y-%m-%d)"
    aws s3api list-objects-v2 \
        --bucket "${S3_BUCKET}" \
        --prefix "${S3_PREFIX}/" \
        --query 'Contents[].Key' --output text \
        "${ENDPOINT_ARGS[@]}" \
    | tr '\t' '\n' \
    | while read -r key; do
        [ -z "$key" ] && continue
        local base file_date
        base="$(basename "$key")"
        file_date="${base:0:10}"
        if [[ "$file_date" < "$cutoff" ]]; then
            echo "[$(date)] Removendo backup antigo: ${key}"
            aws s3 rm "s3://${S3_BUCKET}/${key}" "${ENDPOINT_ARGS[@]}"
        fi
    done
}

run_backup() {
    IFS=',' read -ra DBS <<< "${POSTGRES_DATABASES}"
    for db in "${DBS[@]}"; do
        backup_database "$(echo "$db" | xargs)"
    done
    cleanup_old_backups
    echo "[$(date)] Todos os backups concluídos com sucesso."
}

if [ "${1:-}" = "once" ]; then
    run_backup
    exit 0
fi

# O daemon cron executa os jobs agendados com um ambiente mínimo — não herda
# as variáveis definidas no container via docker-compose (POSTGRES_*, S3_*,
# AWS_*). Sem isso, o backup automático (via cron) falha na hora com
# "unbound variable", enquanto rodar manualmente (docker exec, que herda o
# ambiente do container) funciona normalmente. Por isso persistimos as
# variáveis num arquivo agora, enquanto o processo ainda as tem, para o job
# do cron carregá-las explicitamente antes de rodar o backup.
{
    for var in POSTGRES_HOST POSTGRES_USER POSTGRES_PASSWORD POSTGRES_DATABASES \
               S3_BUCKET S3_PREFIX S3_ENDPOINT \
               AWS_ACCESS_KEY_ID AWS_SECRET_ACCESS_KEY AWS_DEFAULT_REGION; do
        printf 'export %s=%q\n' "$var" "${!var:-}"
    done
} > /etc/backup-env
chmod 600 /etc/backup-env

CRON_SCHEDULE="${SCHEDULE:-0 3 * * *}"
echo "Serviço de backup iniciado. Agendamento (cron): ${CRON_SCHEDULE}"
{
    echo "${CRON_SCHEDULE} /bin/bash -c '. /etc/backup-env; /usr/local/bin/backup.sh once' >> /proc/1/fd/1 2>>/proc/1/fd/2"
    echo ""
} > /etc/cron.d/backup-cron
chmod 0644 /etc/cron.d/backup-cron
crontab /etc/cron.d/backup-cron
cron -f
