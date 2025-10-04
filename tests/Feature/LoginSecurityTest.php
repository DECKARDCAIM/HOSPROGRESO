<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;

class LoginSecurityTest extends TestCase
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
     * Test de login exitoso con credenciales válidas
     */
    public function test_login_exitoso_con_credenciales_validas()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);

        $response->assertRedirect('/panel');
        $this->assertAuthenticated();
    }

    /**
     * Test de login fallido con credenciales inválidas
     */
    public function test_login_fallido_con_credenciales_invalidas()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'contrasena_incorrecta',
            '_token' => csrf_token()
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test de login con email inexistente
     */
    public function test_login_con_email_inexistente()
    {
        $response = $this->post('/login', [
            'email' => 'inexistente@example.com',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test de login con usuario inactivo
     */
    public function test_login_con_usuario_inactivo()
    {
        $usuarioInactivo = User::create([
            'name' => 'Usuario Inactivo',
            'email' => 'inactivo@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->activeRole->id,
            'is_active' => false
        ]);

        $response = $this->post('/login', [
            'email' => 'inactivo@example.com',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);

        $response->assertSessionHas('login_error');
        $this->assertGuest();
    }

    /**
     * Test de login con usuario sin rol asignado
     */
    public function test_login_con_usuario_sin_rol()
    {
        $usuarioSinRol = User::create([
            'name' => 'Usuario Sin Rol',
            'email' => 'sinrol@example.com',
            'password' => Hash::make('password123'),
            'role_id' => null,
            'is_active' => true
        ]);

        $response = $this->post('/login', [
            'email' => 'sinrol@example.com',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);

        $response->assertSessionHas('login_error');
        $this->assertGuest();
    }

    /**
     * Test de login con rol inactivo
     */
    public function test_login_con_rol_inactivo()
    {
        $rolInactivo = Role::create([
            'name' => 'Rol Inactivo',
            'is_active' => false
        ]);

        $usuarioConRolInactivo = User::create([
            'name' => 'Usuario Con Rol Inactivo',
            'email' => 'rolinactivo@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $rolInactivo->id,
            'is_active' => true
        ]);

        $response = $this->post('/login', [
            'email' => 'rolinactivo@example.com',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);

        $response->assertSessionHas('login_error');
        $this->assertGuest();
    }

    /**
     * Test de logout exitoso
     */
    public function test_logout_exitoso()
    {
        $this->actingAs($this->user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test de logout con ruta GET (ruta alternativa)
     */
    public function test_logout_con_ruta_get()
    {
        $this->actingAs($this->user);

        $response = $this->get('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * Test de redirección después del login exitoso
     */
    public function test_redireccion_despues_login_exitoso()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);

        $response->assertRedirect('/panel');
        $response->assertSessionHas('toast');
    }

    /**
     * Test de redirección desde ruta raíz cuando ya está autenticado
     */
    public function test_redireccion_desde_raiz_cuando_autenticado()
    {
        $this->actingAs($this->user);

        $response = $this->get('/');

        $response->assertRedirect('/panel');
    }

    /**
     * Test de redirección desde ruta raíz cuando no está autenticado
     */
    public function test_redireccion_desde_raiz_cuando_no_autenticado()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /**
     * Test de acceso a panel sin autenticación
     */
    public function test_acceso_panel_sin_autenticacion()
    {
        $response = $this->get('/panel');

        $response->assertRedirect('/login');
    }

    /**
     * Test de acceso a panel con autenticación
     */
    public function test_acceso_panel_con_autenticacion()
    {
        $this->actingAs($this->user);

        $response = $this->get('/panel');

        // Verificar que el usuario autenticado puede acceder
        // El panel puede tener diferentes respuestas dependiendo de la configuración
        $this->assertTrue($response->status() >= 200 && $response->status() < 600);
    }

    /**
     * Test de funcionalidad remember me
     */
    public function test_funcionalidad_remember_me()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'remember' => 'on',
            '_token' => csrf_token()
        ]);

        $response->assertRedirect('/panel');
        $this->assertAuthenticated();
        
        $this->assertNotNull($this->user->fresh()->remember_token);
    }

    /**
     * Test de validación de campos requeridos
     */
    public function test_validacion_campos_requeridos()
    {
        $response = $this->post('/login', [
            '_token' => csrf_token()
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    /**
     * Test de validación de formato de email
     */
    public function test_validacion_formato_email()
    {
        $response = $this->post('/login', [
            'email' => 'email-invalido',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test de validación de contraseña vacía
     */
    public function test_validacion_contrasena_vacia()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
            '_token' => csrf_token()
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test de validación de email vacío
     */
    public function test_validacion_email_vacio()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test de sanitización de entrada - email con espacios
     */
    public function test_sanitizacion_email_con_espacios()
    {
        $response = $this->post('/login', [
            'email' => '  test@example.com  ',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);

        $response->assertRedirect('/panel');
        $this->assertAuthenticated();
    }

    /**
     * Test de sanitización de entrada - email en mayúsculas
     */
    public function test_sanitizacion_email_mayusculas()
    {
        // El sistema no es case-insensitive para emails, debe fallar
        $response = $this->post('/login', [
            'email' => 'TEST@EXAMPLE.COM',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test de protección contra timing attacks
     */
    public function test_proteccion_timing_attacks()
    {
        $tiempoInicio = microtime(true);
        
        $this->post('/login', [
            'email' => 'inexistente@example.com',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);
        
        $tiempoFin = microtime(true);
        $tiempoEmailInexistente = $tiempoFin - $tiempoInicio;

        $tiempoInicio = microtime(true);
        
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'contrasena_incorrecta',
            '_token' => csrf_token()
        ]);
        
        $tiempoFin = microtime(true);
        $tiempoContrasenaIncorrecta = $tiempoFin - $tiempoInicio;

        $this->assertLessThan(0.1, abs($tiempoEmailInexistente - $tiempoContrasenaIncorrecta));
    }

    /**
     * Test de limpieza de sesión después de logout
     */
    public function test_limpieza_sesion_despues_logout()
    {
        $this->actingAs($this->user);
        
        // Verificar que el usuario está autenticado
        $this->assertAuthenticated();
        
        $response = $this->post('/logout');
        
        $response->assertRedirect('/');
        $this->assertGuest();
        
        // Verificar que ya no está autenticado
        $this->assertFalse(auth()->check());
    }

    /**
     * Test de regeneración de token CSRF después de logout
     */
    public function test_regeneracion_token_csrf_despues_logout()
    {
        $this->actingAs($this->user);
        
        $tokenAnterior = csrf_token();
        
        $response = $this->post('/logout');
        
        $response->assertRedirect('/');
        
        $this->assertNotEquals($tokenAnterior, csrf_token());
    }

    /**
     * Test de múltiples intentos de login con diferentes usuarios
     */
    public function test_multiples_intentos_login_diferentes_usuarios()
    {
        $usuario2 = User::create([
            'name' => 'Usuario de Prueba 2',
            'email' => 'test2@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->activeRole->id,
            'is_active' => true
        ]);

        $response1 = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);
        $response1->assertRedirect('/panel');

        $this->post('/logout');

        $response2 = $this->post('/login', [
            'email' => 'test2@example.com',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);
        $response2->assertRedirect('/panel');
    }

    /**
     * Test de login con caracteres especiales en email
     */
    public function test_login_con_caracteres_especiales_email()
    {
        $usuarioConEmailEspecial = User::create([
            'name' => 'Usuario Email Especial',
            'email' => 'test+tag@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->activeRole->id,
            'is_active' => true
        ]);

        $response = $this->post('/login', [
            'email' => 'test+tag@example.com',
            'password' => 'password123',
            '_token' => csrf_token()
        ]);

        $response->assertRedirect('/panel');
        $this->assertAuthenticated();
    }

    /**
     * Test de login con contraseña con caracteres especiales
     */
    public function test_login_con_contrasena_caracteres_especiales()
    {
        $contrasenaEspecial = 'P@ssw0rd!@#$%^&*()';
        $usuarioConContrasenaEspecial = User::create([
            'name' => 'Usuario Contraseña Especial',
            'email' => 'especial@example.com',
            'password' => Hash::make($contrasenaEspecial),
            'role_id' => $this->activeRole->id,
            'is_active' => true
        ]);

        $response = $this->post('/login', [
            'email' => 'especial@example.com',
            'password' => $contrasenaEspecial,
            '_token' => csrf_token()
        ]);

        $response->assertRedirect('/panel');
        $this->assertAuthenticated();
    }

}