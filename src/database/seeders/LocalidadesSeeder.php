<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LocalidadesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. REGIÕES
        $regioes = Http::get('https://servicodados.ibge.gov.br/api/v1/localidades/regioes')->json();

        foreach ($regioes as $reg) {
            DB::table('regioes')->updateOrInsert(
                ['codigo_ibge' => $reg['id']],
                [
                    'nome' => $reg['nome'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 2. ESTADOS
        $estados = Http::get('https://servicodados.ibge.gov.br/api/v1/localidades/estados')->json();

        foreach ($estados as $est) {
            $regiaoId = DB::table('regioes')
                ->where('codigo_ibge', $est['regiao']['id'])
                ->value('id');

            DB::table('estados')->updateOrInsert(
                ['codigo_ibge' => $est['id']],
                [
                    'regiao_id' => $regiaoId,
                    'nome' => $est['nome'],
                    'sigla' => $est['sigla'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 3. MESORREGIÕES
        foreach ($estados as $est) {
            $estadoId = DB::table('estados')
                ->where('codigo_ibge', $est['id'])
                ->value('id');

            $mesos = Http::retry(5, 1000)
                ->get("https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$est['id']}/mesorregioes")
                ->json();

            foreach ($mesos as $meso) {
                DB::table('mesorregioes')->updateOrInsert(
                    ['codigo_ibge' => $meso['id']],
                    [
                        'estado_id' => $estadoId,
                        'nome' => $meso['nome'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            usleep(150000);
        }

        // 4. MICRORREGIÕES
        $mesorregioes = DB::table('mesorregioes')->get();

        foreach ($mesorregioes as $meso) {
            try {
                $micros = Http::retry(5, 2000)
                    ->timeout(120)
                    ->get("https://servicodados.ibge.gov.br/api/v1/localidades/mesorregioes/{$meso->codigo_ibge}/microrregioes")
                    ->json();

                foreach ($micros as $micro) {
                    DB::table('microrregioes')->updateOrInsert(
                        ['codigo_ibge' => $micro['id']],
                        [
                            'mesorregiao_id' => $meso->id,
                            'nome' => $micro['nome'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            } catch (\Throwable $e) {
                continue;
            }

            usleep(150000);
        }

        // 5. MUNICÍPIOS
        $microrregioes = DB::table('microrregioes')->get();

        foreach ($microrregioes as $micro) {
            try {
                $municipios = Http::retry(5, 2000)
                    ->timeout(120)
                    ->get("https://servicodados.ibge.gov.br/api/v1/localidades/microrregioes/{$micro->codigo_ibge}/municipios")
                    ->json();

                foreach ($municipios as $mun) {
                    $estadoId = DB::table('estados')
                        ->where('codigo_ibge', $mun['microrregiao']['mesorregiao']['UF']['id'])
                        ->value('id');

                    DB::table('cidades')->updateOrInsert(
                        ['codigo_ibge' => $mun['id']],
                        [
                            'microrregiao_id' => $micro->id,
                            'estado_id' => $estadoId,
                            'nome' => $mun['nome'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            } catch (\Throwable $e) {
                continue;
            }

            usleep(150000);
        }
    }
}
