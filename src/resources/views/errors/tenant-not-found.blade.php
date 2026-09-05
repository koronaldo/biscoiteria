<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endereço não encontrado</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #FBF1E4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        .icon {
            width: 72px;
            height: 72px;
            background: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .icon svg {
            width: 36px;
            height: 36px;
            color: #ef4444;
        }

        h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }

        p {
            font-size: 15px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .domain {
            display: inline-block;
            background: #f1f5f9;
            color: #334155;
            font-family: monospace;
            font-size: 14px;
            padding: 4px 12px;
            border-radius: 6px;
            margin: 8px 0 20px;
            word-break: break-all;
        }

        .contact {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
            font-size: 13px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </div>

        <h1>Endereço não encontrado</h1>

        <p>O endereço que você tentou acessar não existe:</p>

        <span class="domain">{{ $host }}</span>

        <p>Verifique se o endereço está correto ou entre em contato com o suporte para obter o endereço da sua conta.</p>

        <div class="contact">
            Se você acredita que isso é um erro, entre em contato com o suporte.
        </div>
    </div>
</body>
</html>
