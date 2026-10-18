<?php

namespace Tests\Unit;

use App\Support\ServiceCatalog;
use PHPUnit\Framework\TestCase;

class ServiceCatalogTest extends TestCase
{
    /**
     * Valida que el catálogo devuelva la lista completa de servicios de verificación disponibles.
     */
    public function test_catalog_returns_all_expected_services(): void
    {
        $services = ServiceCatalog::all();

        $this->assertIsArray($services);
        $this->assertCount(8, $services);

        $expectedKeys = [
            'antecedentes_policiales',
            'antecedentes_judiciales',
            'antecedentes_penales',
            'crediticias',
            'laborales',
            'record',
            'academicas',
            'domiciliarias',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $services, "El servicio '{$key}' debe estar definido en el catálogo.");
        }
    }

    /**
     * Valida que cada servicio cuente con sus metadatos obligatorios ('full' y 'short').
     */
    public function test_each_service_definition_has_required_attributes(): void
    {
        $services = ServiceCatalog::all();

        foreach ($services as $key => $data) {
            $this->assertIsArray($data, "La definición de '{$key}' debe ser un arreglo.");
            $this->assertArrayHasKey('full', $data, "El servicio '{$key}' debe tener el atributo 'full'.");
            $this->assertArrayHasKey('short', $data, "El servicio '{$key}' debe tener el atributo 'short'.");
            $this->assertNotEmpty($data['full'], "El nombre completo de '{$key}' no debe estar vacío.");
            $this->assertNotEmpty($data['short'], "El nombre corto de '{$key}' no debe estar vacío.");
        }

        // Verifica la agrupación específica de Antecedentes Nacionales
        $nationalBackgrounds = ['antecedentes_policiales', 'antecedentes_judiciales', 'antecedentes_penales'];
        foreach ($nationalBackgrounds as $key) {
            $this->assertSame(
                'Antecedentes Nacionales',
                $services[$key]['group'] ?? null,
                "El servicio '{$key}' debe pertenecer al grupo 'Antecedentes Nacionales'."
            );
        }
    }

    /**
     * Valida que el método keys() retorne los identificadores válidos para reglas de validación.
     */
    public function test_keys_method_returns_exact_list_of_service_identifiers(): void
    {
        $keys = ServiceCatalog::keys();

        $this->assertIsArray($keys);
        $this->assertSame(array_keys(ServiceCatalog::all()), $keys);

        $this->assertContains('laborales', $keys);
        $this->assertContains('crediticias', $keys);
        $this->assertContains('record', $keys);

        // Claves inválidas o inexistentes no deben estar presentes
        $this->assertNotContains('servicio_inexistente', $keys);
        $this->assertNotContains('reniec', $keys);
        $this->assertNotContains('', $keys);
    }
}

