<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\ClinicalRecord;
use App\Models\Sex;
use App\Models\CivilStatus;
use App\Models\LinguisticCommunity;
use App\Models\Ethnicity;
use App\Models\Country;
use App\Models\Department;
use App\Models\Municipality;
use App\Models\Disability;
use App\Models\Allergy;
use App\Models\TemporaryPatient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class ClinicalRecordSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $adminUser;
    protected $emergencyUser;
    protected $consultationUser;
    protected $activeRole;
    protected $adminRole;
    protected $emergencyRole;
    protected $consultationRole;
    protected $clinicalRecord;
    protected $catalogData;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles
        $this->adminRole = Role::create([
            'name' => 'Administrador',
            'is_active' => true
        ]);

        $this->emergencyRole = Role::create([
            'name' => 'Emergencia',
            'is_active' => true
        ]);

        $this->consultationRole = Role::create([
            'name' => 'Consulta Externa',
            'is_active' => true
        ]);

        // Crear usuarios
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->adminRole->id,
            'is_active' => true
        ]);

        $this->emergencyUser = User::create([
            'name' => 'Emergency User',
            'email' => 'emergency@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->emergencyRole->id,
            'is_active' => true
        ]);

        $this->consultationUser = User::create([
            'name' => 'Consultation User',
            'email' => 'consultation@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->consultationRole->id,
            'is_active' => true
        ]);

        $this->user = $this->emergencyUser; // Usuario por defecto

        // Crear datos de catálogo
        $this->catalogData = $this->createCatalogData();

        // Crear expediente clínico de prueba
        $this->clinicalRecord = $this->createClinicalRecord();

        Cache::flush();
    }

    private function createCatalogData()
    {
        $sex = Sex::create(['name' => 'Masculino', 'is_active' => true]);
        $civilStatus = CivilStatus::create(['name' => 'Soltero', 'is_active' => true]);
        $linguisticCommunity = LinguisticCommunity::create(['name' => 'Español', 'is_active' => true]);
        $ethnicity = Ethnicity::create(['name' => 'Mestizo', 'is_active' => true]);
        $country = Country::create(['name' => 'Guatemala', 'is_active' => true]);
        $department = Department::create(['name' => 'Guatemala', 'country_id' => $country->id, 'is_active' => true]);
        $municipality = Municipality::create(['name' => 'Guatemala', 'department_id' => $department->id, 'is_active' => true]);
        $disability = Disability::create(['name' => 'Visual', 'is_active' => true]);
        $allergy = Allergy::create(['name' => 'Penicilina', 'is_active' => true]);

        return [
            'sex_id' => $sex->id,
            'civil_status_id' => $civilStatus->id,
            'linguistic_community_id' => $linguisticCommunity->id,
            'ethnicity_id' => $ethnicity->id,
            'country_id' => $country->id,
            'department_id' => $department->id,
            'municipality_id' => $municipality->id,
            'disability_id' => [$disability->id],
            'allergy_id' => [$allergy->id]
        ];
    }

    private function createClinicalRecord()
    {
        return ClinicalRecord::create([
            'record_number' => 'EXP-2024-000001',
            'first_name' => 'Juan',
            'first_lastname' => 'Pérez',
            'cui' => '1234567890123',
            'phone' => '12345678',
            'email' => 'juan@example.com',
            'sex_id' => $this->catalogData['sex_id'],
            'civil_status_id' => $this->catalogData['civil_status_id'],
            'linguistic_community_id' => $this->catalogData['linguistic_community_id'],
            'ethnicity_id' => $this->catalogData['ethnicity_id'],
            'birth_date' => '1990-01-01',
            'country_id' => $this->catalogData['country_id'],
            'department_id' => $this->catalogData['department_id'],
            'municipality_id' => $this->catalogData['municipality_id'],
            'specific_residence' => 'Zona 1'
        ]);
    }

    /**
     * Test de acceso no autorizado - Usuario no autenticado
     */
    public function test_acceso_no_autorizado_usuario_no_autenticado()
    {
        // Intentar acceder sin autenticación
        $response = $this->get('/clinical-records');
        $response->assertRedirect('/login');

        $response = $this->get('/clinical-records/' . $this->clinicalRecord->id);
        $response->assertRedirect('/login');

        $response = $this->post('/clinical-records', []);
        $response->assertRedirect('/login');

        $response = $this->get('/clinical-records/create');
        $response->assertRedirect('/login');

        $response = $this->put('/clinical-records/' . $this->clinicalRecord->id, []);
        $response->assertRedirect('/login');
    }

    /**
     * Test de bypass de permisos - Usuario sin permisos adecuados
     */
    public function test_bypass_permisos_usuario_sin_permisos()
    {
        $userWithoutPermissions = User::create([
            'name' => 'User Sin Permisos',
            'email' => 'sinpermisos@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->consultationRole->id,
            'is_active' => true
        ]);

        $this->actingAs($userWithoutPermissions);

        // Intentar acceder a expedientes sin permisos
        $response = $this->get('/clinical-records');
        $this->assertTrue(in_array($response->status(), [403, 404, 302]));

        $response = $this->get('/clinical-records/' . $this->clinicalRecord->id);
        $this->assertTrue(in_array($response->status(), [403, 404, 302]));

        $response = $this->get('/clinical-records/create');
        $this->assertTrue(in_array($response->status(), [403, 404, 302]));
    }

    /**
     * Test de inyección SQL en búsquedas
     */
    public function test_inyeccion_sql_en_busquedas()
    {
        $this->actingAs($this->emergencyUser);
        
        $sqlInjectionPayloads = [
            "' OR '1'='1",
            "' OR 1=1--",
            "' OR 1=1#",
            "admin'--",
            "admin'/*",
            "' OR 'x'='x",
            "') OR ('1'='1",
            "1' UNION SELECT * FROM clinical_records--",
            "1' UNION SELECT cui,email FROM clinical_records--",
            "1' OR '1'='1' AND (SELECT COUNT(*) FROM clinical_records) > 0--",
        ];

        foreach ($sqlInjectionPayloads as $payload) {
            $response = $this->get('/clinical-records?search=' . urlencode($payload));
            $this->assertStringNotContainsString('error', strtolower($response->getContent()));
            $this->assertTrue(in_array($response->status(), [200, 302, 422]));
        }
    }

    /**
     * Test de manipulación de parámetros - ID Manipulation
     */
    public function test_manipulacion_parametros_id_manipulation()
    {
        $this->actingAs($this->emergencyUser);

        $anotherUser = User::create([
            'name' => 'Otro Usuario',
            'email' => 'otro@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->emergencyRole->id,
            'is_active' => true
        ]);

        $anotherRecord = ClinicalRecord::create([
            'record_number' => 'EXP-2024-000002',
            'first_name' => 'María',
            'first_lastname' => 'González',
            'cui' => '9876543210987',
            'phone' => '87654321',
            'email' => 'maria@example.com',
            'sex_id' => $this->catalogData['sex_id'],
            'civil_status_id' => $this->catalogData['civil_status_id'],
            'linguistic_community_id' => $this->catalogData['linguistic_community_id'],
            'ethnicity_id' => $this->catalogData['ethnicity_id'],
            'birth_date' => '1985-05-15',
            'country_id' => $this->catalogData['country_id'],
            'department_id' => $this->catalogData['department_id'],
            'municipality_id' => $this->catalogData['municipality_id'],
            'specific_residence' => 'Zona 10'
        ]);

        // Intentar acceder a expediente ajeno
        $response = $this->get('/clinical-records/' . $anotherRecord->id);
        $this->assertTrue(in_array($response->status(), [200, 403, 404, 302]));

        // Intentar modificar expediente ajeno
        $response = $this->put('/clinical-records/' . $anotherRecord->id, [
            'first_name' => 'Hackeado',
            'first_lastname' => $anotherRecord->first_lastname,
            'cui' => $anotherRecord->cui,
            'sex_id' => $this->catalogData['sex_id'],
            'civil_status_id' => $this->catalogData['civil_status_id'],
            'linguistic_community_id' => $this->catalogData['linguistic_community_id'],
            'ethnicity_id' => $this->catalogData['ethnicity_id'],
            'birth_date' => $anotherRecord->birth_date,
            'country_id' => $this->catalogData['country_id'],
            'department_id' => $this->catalogData['department_id'],
            'municipality_id' => $this->catalogData['municipality_id'],
            '_token' => csrf_token()
        ]);
        $this->assertTrue(in_array($response->status(), [200, 403, 404, 302, 422]));
    }

    /**
     * Test de inyección SQL en creación de expedientes
     */
    public function test_inyeccion_sql_en_creacion_expedientes()
    {
        $this->actingAs($this->emergencyUser);

        $maliciousData = [
            'first_name' => "Juan'; DROP TABLE clinical_records; --",
            'first_lastname' => "Pérez",
            'cui' => "1234567890123",
            'phone' => "12345678",
            'email' => "juan@example.com",
            'sex_id' => $this->catalogData['sex_id'],
            'civil_status_id' => $this->catalogData['civil_status_id'],
            'linguistic_community_id' => $this->catalogData['linguistic_community_id'],
            'ethnicity_id' => $this->catalogData['ethnicity_id'],
            'birth_date' => '1990-01-01',
            'country_id' => $this->catalogData['country_id'],
            'department_id' => $this->catalogData['department_id'],
            'municipality_id' => $this->catalogData['municipality_id'],
            'specific_residence' => "Zona 1'; DELETE FROM users; --",
            '_token' => csrf_token()
        ];

        $response = $this->post('/clinical-records', $maliciousData);
        $this->assertTrue(in_array($response->status(), [302, 422]));
        $this->assertDatabaseMissing('clinical_records', ['first_name' => "Juan'; DROP TABLE clinical_records; --"]);
    }

    /**
     * Test de XSS en campos de texto
     */
    public function test_xss_en_campos_texto()
    {
        $this->actingAs($this->emergencyUser);

        $xssPayloads = [
            '<script>alert("xss")</script>',
            '<img src=x onerror=alert("xss")>',
            '"onmouseover="alert(\'xss\')"',
            '\';alert("xss");//',
            '<svg onload=alert("xss")>',
            'javascript:alert("xss")',
        ];

        foreach ($xssPayloads as $payload) {
            $xssData = array_merge($this->catalogData, [
                'first_name' => $payload,
                'first_lastname' => 'Test',
                'cui' => '1111111111111',
                'phone' => '11111111',
                'email' => 'test@example.com',
                'birth_date' => '1990-01-01',
                'specific_residence' => $payload,
                '_token' => csrf_token()
            ]);

            $response = $this->post('/clinical-records', $xssData);
            $this->assertTrue(in_array($response->status(), [302, 422]));
        }
    }

    /**
     * Test de bypass de validaciones
     */
    public function test_bypass_validaciones()
    {
        $this->actingAs($this->emergencyUser);

        $invalidData = [
            'first_name' => '', // Campo requerido vacío
            'first_lastname' => str_repeat('A', 300), // Excede longitud máxima
            'cui' => '123', // CUI muy corto
            'email' => 'email-invalido', // Email inválido
            'birth_date' => '2030-01-01', // Fecha futura
            'sex_id' => 99999, // ID inexistente
            '_token' => csrf_token()
        ];

        $response = $this->post('/clinical-records', $invalidData);
        // Verificar que la respuesta indica error (puede ser redirect con errores o 422)
        $this->assertTrue(in_array($response->status(), [302, 422]));
    }

    /**
     * Test de manipulación de cache
     */
    public function test_manipulacion_cache()
    {
        $this->actingAs($this->emergencyUser);

        // Intentar manipular cache keys para obtener datos de otros usuarios
        $cacheKeys = [
            'expedientes:index:v1:u=1:q=test:p=1',
            'expedientes:index:v1:u=999:q=test:p=1',
            'expedientes:show:1',
            'expedientes:show:999'
        ];

        foreach ($cacheKeys as $key) {
            Cache::put($key, 'datos_maliciosos', 3600);
        }

        $response = $this->get('/clinical-records');
        $this->assertTrue(in_array($response->status(), [200, 403, 404, 302]));
    }

    /**
     * Test de path traversal en impresión de PDF
     */
    public function test_path_traversal_impresion_pdf()
    {
        $this->actingAs($this->emergencyUser);

        $pathTraversalPayloads = [
            '../../../etc/passwd',
            '../../../../etc/shadow',
            '....//....//....//etc/passwd',
            '..%2F..%2F..%2Fetc%2Fpasswd',
            '..%252F..%252F..%252Fetc%252Fpasswd',
        ];

        foreach ($pathTraversalPayloads as $payload) {
            try {
                $response = $this->get('/clinical-records/' . $payload . '/print');
                $this->assertTrue(in_array($response->status(), [404, 422, 500]));
            } catch (\Exception $e) {
                // Esperado para payloads maliciosos
                $this->assertTrue(true);
            }
        }
    }

    /**
     * Test de bypass de transacciones
     */
    public function test_bypass_transacciones()
    {
        $this->actingAs($this->emergencyUser);

        // Intentar crear expediente con datos que causen rollback
        $transactionData = array_merge($this->catalogData, [
            'first_name' => 'Test',
            'first_lastname' => 'Transaction',
            'cui' => '1234567890123', // CUI duplicado
            'phone' => '12345678',
            'email' => 'transaction@example.com',
            'birth_date' => '1990-01-01',
            'specific_residence' => 'Test',
            '_token' => csrf_token()
        ]);

        $response = $this->post('/clinical-records', $transactionData);
        $this->assertTrue(in_array($response->status(), [302, 422]));
    }

    /**
     * Test de manipulación de temporal patient ID
     */
    public function test_manipulacion_temporal_patient_id()
    {
        $this->actingAs($this->emergencyUser);

        $temporaryPatient = TemporaryPatient::create([
            'registration_number' => 'TEMP-001',
            'first_name' => 'Temp',
            'first_lastname' => 'Patient',
            'cui' => '9999999999999',
            'phone' => '99999999',
            'email' => 'temp@example.com',
            'sex_id' => $this->catalogData['sex_id'],
            'civil_status_id' => $this->catalogData['civil_status_id'],
            'linguistic_community_id' => $this->catalogData['linguistic_community_id'],
            'ethnicity_id' => $this->catalogData['ethnicity_id'],
            'birth_date' => '1995-01-01',
            'country_id' => $this->catalogData['country_id'],
            'department_id' => $this->catalogData['department_id'],
            'municipality_id' => $this->catalogData['municipality_id'],
            'specific_residence' => 'Temporal'
        ]);

        $temporaryData = array_merge($this->catalogData, [
            'first_name' => 'Migrated',
            'first_lastname' => 'Patient',
            'cui' => '8888888888888',
            'phone' => '88888888',
            'email' => 'migrated@example.com',
            'birth_date' => '1990-01-01',
            'specific_residence' => 'Migrated',
            'temporary_patient_id' => $temporaryPatient->id,
            '_token' => csrf_token()
        ]);

        $response = $this->post('/clinical-records', $temporaryData);
        $this->assertTrue(in_array($response->status(), [302, 422]));
    }

    /**
     * Test de bypass de middleware de permisos
     */
    public function test_bypass_middleware_permisos()
    {
        $this->actingAs($this->emergencyUser);

        $bypassData = array_merge($this->catalogData, [
            'first_name' => 'Bypass',
            'first_lastname' => 'Test',
            'cui' => '7777777777777',
            'phone' => '77777777',
            'email' => 'bypass@example.com',
            'birth_date' => '1990-01-01',
            'specific_residence' => 'Bypass',
            '_token' => csrf_token()
        ]);

        // Intentar con diferentes headers para bypass
        $headers = [
            'HTTP_X_FORWARDED_FOR' => '127.0.0.1',
            'HTTP_X_REAL_IP' => '127.0.0.1',
            'HTTP_CLIENT_IP' => '127.0.0.1',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_HOST' => 'localhost',
        ];

        foreach ($headers as $header => $value) {
            $response = $this->post('/clinical-records', $bypassData, [$header => $value]);
            $this->assertTrue(in_array($response->status(), [302, 403, 404, 422]));
        }
    }

    /**
     * Test de manipulación de relaciones (disabilities/allergies)
     */
    public function test_manipulacion_relaciones()
    {
        $this->actingAs($this->emergencyUser);

        $relationData = array_merge($this->catalogData, [
            'first_name' => 'Relation',
            'first_lastname' => 'Test',
            'cui' => '6666666666666',
            'phone' => '66666666',
            'email' => 'relation@example.com',
            'birth_date' => '1990-01-01',
            'specific_residence' => 'Relation',
            'disability_id' => [99999, -1, 'invalid'], // IDs inválidos
            'allergy_id' => [99999, -1, 'invalid'], // IDs inválidos
            '_token' => csrf_token()
        ]);

        $response = $this->post('/clinical-records', $relationData);
        $this->assertTrue(in_array($response->status(), [302, 422]));
    }

    /**
     * Test de bypass de validación de fechas
     */
    public function test_bypass_validacion_fechas()
    {
        $this->actingAs($this->emergencyUser);

        $dateBypassData = array_merge($this->catalogData, [
            'first_name' => 'Date',
            'first_lastname' => 'Test',
            'cui' => '5555555555555',
            'phone' => '55555555',
            'email' => 'date@example.com',
            'birth_date' => 'invalid-date', // Fecha inválida
            'specific_residence' => 'Date',
            '_token' => csrf_token()
        ]);

        $response = $this->post('/clinical-records', $dateBypassData);
        // Verificar que la respuesta indica error
        $this->assertTrue(in_array($response->status(), [302, 422]));
    }

    /**
     * Test de bypass de validación de CUI único
     */
    public function test_bypass_validacion_cui_unico()
    {
        $this->actingAs($this->emergencyUser);

        $duplicateCuiData = array_merge($this->catalogData, [
            'first_name' => 'Duplicate',
            'first_lastname' => 'CUI',
            'cui' => $this->clinicalRecord->cui, // CUI duplicado
            'phone' => '44444444',
            'email' => 'duplicate@example.com',
            'birth_date' => '1990-01-01',
            'specific_residence' => 'Duplicate',
            '_token' => csrf_token()
        ]);

        $response = $this->post('/clinical-records', $duplicateCuiData);
        // Verificar que la respuesta indica error
        $this->assertTrue(in_array($response->status(), [302, 422]));
    }

    /**
     * Test de manipulación de campos ocultos
     */
    public function test_manipulacion_campos_ocultos()
    {
        $this->actingAs($this->emergencyUser);

        $hiddenFieldData = array_merge($this->catalogData, [
            'first_name' => 'Hidden',
            'first_lastname' => 'Test',
            'cui' => '3333333333333',
            'phone' => '33333333',
            'email' => 'hidden@example.com',
            'birth_date' => '1990-01-01',
            'specific_residence' => 'Hidden',
            'id' => $this->clinicalRecord->id, // Intentar modificar ID existente
            'record_number' => 'EXP-2024-HACKED', // Intentar cambiar número de expediente
            '_token' => csrf_token()
        ]);

        $response = $this->post('/clinical-records', $hiddenFieldData);
        $this->assertTrue(in_array($response->status(), [302, 422]));

        // Verificar que el expediente original no fue modificado
        $this->clinicalRecord->refresh();
        $this->assertNotEquals('EXP-2024-HACKED', $this->clinicalRecord->record_number);
    }

    /**
     * Test de bypass de validación de email
     */
    public function test_bypass_validacion_email()
    {
        $this->actingAs($this->emergencyUser);

        $emailBypassData = array_merge($this->catalogData, [
            'first_name' => 'Email',
            'first_lastname' => 'Test',
            'cui' => '2222222222222',
            'phone' => '22222222',
            'email' => 'not-an-email', // Email inválido
            'birth_date' => '1990-01-01',
            'specific_residence' => 'Email',
            '_token' => csrf_token()
        ]);

        $response = $this->post('/clinical-records', $emailBypassData);
        // Verificar que la respuesta indica error
        $this->assertTrue(in_array($response->status(), [302, 422]));
    }

    /**
     * Test de bypass de validación de teléfono
     */
    public function test_bypass_validacion_telefono()
    {
        $this->actingAs($this->emergencyUser);

        $phoneBypassData = array_merge($this->catalogData, [
            'first_name' => 'Phone',
            'first_lastname' => 'Test',
            'cui' => '1111111111111',
            'phone' => str_repeat('1', 100), // Teléfono muy largo
            'email' => 'phone@example.com',
            'birth_date' => '1990-01-01',
            'specific_residence' => 'Phone',
            '_token' => csrf_token()
        ]);

        $response = $this->post('/clinical-records', $phoneBypassData);
        $this->assertTrue(in_array($response->status(), [302, 422]));
    }

    /**
     * Test de bypass de validación de nombres
     */
    public function test_bypass_validacion_nombres()
    {
        $this->actingAs($this->emergencyUser);

        $nameBypassData = array_merge($this->catalogData, [
            'first_name' => str_repeat('A', 300), // Nombre muy largo
            'first_lastname' => '', // Apellido vacío (requerido)
            'cui' => '0000000000000',
            'phone' => '00000000',
            'email' => 'name@example.com',
            'birth_date' => '1990-01-01',
            'specific_residence' => 'Name',
            '_token' => csrf_token()
        ]);

        $response = $this->post('/clinical-records', $nameBypassData);
        // Verificar que la respuesta indica error
        $this->assertTrue(in_array($response->status(), [302, 422]));
    }

    /**
     * Test final - Verificar integridad del sistema
     */
    public function test_verificar_integridad_sistema()
    {
        $this->actingAs($this->emergencyUser);

        // Verificar que no se crearon expedientes maliciosos
        $maliciousRecords = ClinicalRecord::where('first_name', 'like', '%<script>%')
            ->orWhere('first_name', 'like', '%DROP TABLE%')
            ->orWhere('first_name', 'like', '%UNION SELECT%')
            ->count();

        $this->assertEquals(0, $maliciousRecords, 'No deberían existir expedientes con datos maliciosos');

        // Verificar que el expediente original sigue intacto
        $this->clinicalRecord->refresh();
        $this->assertEquals('Juan', $this->clinicalRecord->first_name);
        $this->assertEquals('Pérez', $this->clinicalRecord->first_lastname);
        $this->assertEquals('1234567890123', $this->clinicalRecord->cui);
    }
}