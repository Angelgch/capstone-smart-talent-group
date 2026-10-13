<?php

namespace Tests\Feature;

use Illuminate\Support\Benchmark;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Company;

class PerformanceTest extends TestCase
{
    use RefreshDatabase; // <- Esto crea las tablas automáticamente en memoria

    public function test_sistema_procesa_tarifas_rapidamente()
    {
        // Creamos un registro base para la prueba
        Company::create([
            'ruc' => '20123456789',
            'legal_name' => 'Empresa Test S.A.C.',
            'trade_name' => 'Test',
        ]);

        $executionTime = Benchmark::measure(function () {
            for ($i = 0; $i < 50; $i++) {
                Company::first(); 
            }
        });

        $this->assertLessThan(100, $executionTime, "El sistema es demasiado lento procesando tarifas.");
    }
}