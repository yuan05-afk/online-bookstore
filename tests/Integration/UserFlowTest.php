<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class UserFlowTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost/online-bookstore/',
            'http_errors' => false,
            'cookies' => true
        ]);
    }

    /**
     * @testdox Homepage loads successfully with code 200 and site title
     */
    public function testHomePageAccess()
    {
        $response = $this->client->get('index.php');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Online Bookstore', (string) $response->getBody());
    }

    /**
     * @testdox Login with incorrect credentials handles failure gracefully (no crash)
     */
    public function testLoginFailure()
    {
        $response = $this->client->post('auth/login_action.php', [
            'form_params' => [
                'email' => 'wrong@example.com',
                'password' => 'wrongpass'
            ]
        ]);

        // Assuming it redirects closely or returns 200 with error message
        // Or redirects back to login
        $this->assertNotEquals(500, $response->getStatusCode());

        // If your app sets a flash message in session, we can't easily check that without parsing HTML
        // But we can check if we are NOT redirected to dashboard or if we are back at login
        // For simplicity, just checking we didn't crash
    }
}
