<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/security.php';

// Mock constant if config not included
if (!defined('CSRF_TOKEN_EXPIRE')) {
    define('CSRF_TOKEN_EXPIRE', 3600);
}

class SecurityTest extends TestCase
{
    /**
     * @testdox Hashing a password produces a secure hash distinct from the plain text
     */
    public function testHashPassword()
    {
        $password = 'secret123';
        $hash = hashPassword($password);

        $this->assertNotEquals($password, $hash);
        $this->assertTrue(verifyPassword($password, $hash));
        $this->assertFalse(verifyPassword('wrongpass', $hash));
    }

    /**
     * @testdox Sanitizing input trims validation whitespace
     */
    public function testSanitizeInput()
    {
        $input = '  <script>alert("xss")</script>  ';
        $cleaned = sanitizeInput($input);

        // sanitizeInput in security.php only performs trim() and stripslashes()
        // It does NOT remove tags. That is handled by escapeHTML().
        $this->assertEquals('<script>alert("xss")</script>', $cleaned);
    }

    /**
     * @testdox Escaping HTML converts special characters to entities to prevent XSS
     */
    public function testEscapeHTML()
    {
        $input = '<script>alert("xss")</script>';
        $escaped = escapeHTML($input);

        // This function handles XSS prevention
        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
    }

    /**
     * @runInSeparateProcess
     * @testdox Generates a valid CSRF token and verifies it correctly
     */
    public function testCsrfTokenGeneration()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $token = generateCSRFToken();
        $this->assertNotEmpty($token);
        $this->assertEquals($token, $_SESSION['csrf_token']);

        $this->assertTrue(validateCSRFToken($token));
        $this->assertFalse(validateCSRFToken('invalid_token'));
    }
}
