<?php

namespace Tests\Unit;

use App\Support\DniHelper;
use App\Support\DocumentFiles;
use PHPUnit\Framework\TestCase;

class DocumentValidationTest extends TestCase
{
    /**
     * Valida que las extensiones permitidas (PDF, JPG, PNG) se reconozcan y devuelvan su MIME type correcto.
     */
    public function test_pdf_and_allowed_document_extensions_validation(): void
    {
        $this->assertSame('application/pdf', DocumentFiles::mimeType('informe.pdf'));
        $this->assertSame('application/pdf', DocumentFiles::mimeType('EXPEDIENTE.PDF')); // insensible a mayúsculas
        $this->assertSame('image/jpeg', DocumentFiles::mimeType('identidad.jpg'));
        $this->assertSame('image/jpeg', DocumentFiles::mimeType('foto.jpeg'));
        $this->assertSame('image/png', DocumentFiles::mimeType('constancia.png'));

        $this->assertTrue(DocumentFiles::isAllowedExtension('reporte_verificacion.pdf'));
        $this->assertTrue(DocumentFiles::isAllowedExtension('recibo.png'));
    }

    /**
     * Valida que extensiones ejecutables o no soportadas sean rechazadas para evitar vulnerabilidades de seguridad.
     */
    public function test_disallowed_and_dangerous_file_extensions_are_rejected(): void
    {
        $this->assertNull(DocumentFiles::mimeType('shell.php'));
        $this->assertNull(DocumentFiles::mimeType('exploit.exe'));
        $this->assertNull(DocumentFiles::mimeType('script.sh'));
        $this->assertNull(DocumentFiles::mimeType('archivo.docx'));
        $this->assertNull(DocumentFiles::mimeType(''));
        $this->assertNull(DocumentFiles::mimeType(null));

        $this->assertFalse(DocumentFiles::isAllowedExtension('script.php'));
        $this->assertFalse(DocumentFiles::isAllowedExtension('malware.exe'));
    }

    /**
     * Valida la sanitización del DNI eliminando espacios en blanco, puntos y caracteres no numéricos.
     */
    public function test_dni_sanitization_removes_spaces_and_non_digit_characters(): void
    {
        $this->assertSame('71234567', DniHelper::sanitize('71234567'));
        $this->assertSame('71234567', DniHelper::sanitize('  71234567  '));
        $this->assertSame('71234567', DniHelper::sanitize('71.234.567'));
        $this->assertSame('71234567', DniHelper::sanitize('71-23-45-67'));
        $this->assertSame('71234567', DniHelper::sanitize('DNI: 71234567'));
        $this->assertSame('', DniHelper::sanitize(null));
    }

    /**
     * Valida que el DNI cumpla estrictamente con el estándar peruano de 8 dígitos numéricos.
     */
    public function test_dni_validation_strictly_enforces_eight_numeric_digits(): void
    {
        $this->assertTrue(DniHelper::isValid('71234567'));
        $this->assertTrue(DniHelper::isValid(' 71234567 ')); // con espacios válidos después de sanitizar
        $this->assertTrue(DniHelper::isValid('71.234.567'));

        $this->assertFalse(DniHelper::isValid('1234567'));   // 7 dígitos (incompleto)
        $this->assertFalse(DniHelper::isValid('123456789')); // 9 dígitos (excede longitud)
        $this->assertFalse(DniHelper::isValid('abcdefgh'));  // letras
        $this->assertFalse(DniHelper::isValid('7123456a'));  // alfanumérico inválido
        $this->assertFalse(DniHelper::isValid(''));
        $this->assertFalse(DniHelper::isValid(null));
    }
}

