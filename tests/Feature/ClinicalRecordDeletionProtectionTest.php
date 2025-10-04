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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ClinicalRecordDeletionProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $adminUser;
    protected $regularUser;
    protected $adminRole;
    protected $regularRole;
    protected $catalogData;
    protected $clinicalRecord;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles
        $this->adminRole = Role::create([
            'name' => 'Administrador',
            'is_active' => true
        ]);

        $this->regularRole = Role::create([
            'name' => 'Usuario',
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

        $this->regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->regularRole->id,
            'is_active' => true
        ]);

        $this->user = $this->regularUser;

        // Crear datos de catálogo
        $this->catalogData = $this->createCatalogData();

        // Crear expediente clínico de prueba
        $this->clinicalRecord = $this->createClinicalRecord();
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

        return [
            'sex_id' => $sex->id,
            'civil_status_id' => $civilStatus->id,
            'linguistic_community_id' => $linguisticCommunity->id,
            'ethnicity_id' => $ethnicity->id,
            'country_id' => $country->id,
            'department_id' => $department->id,
            'municipality_id' => $municipality->id,
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
     * Test de protección contra eliminación - Usuario no autenticado
     */
    public function test_proteccion_eliminacion_usuario_no_autenticado()
    {
        // Intentar eliminar sin autenticación
        $response = $this->delete('/clinical-records/' . $this->clinicalRecord->id);
        $response->assertRedirect('/login');

        // Verificar que el expediente sigue existiendo
        $this->assertDatabaseHas('clinical_records', [
            'id' => $this->clinicalRecord->id
        ]);
    }

    /**
     * Test de protección contra eliminación - Usuario sin permisos
     */
    public function test_proteccion_eliminacion_usuario_sin_permisos()
    {
        $userWithoutPermissions = User::create([
            'name' => 'User Sin Permisos',
            'email' => 'sinpermisos@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->regularRole->id,
            'is_active' => true
        ]);

        $this->actingAs($userWithoutPermissions);

        // Intentar eliminar sin permisos
        $response = $this->delete('/clinical-records/' . $this->clinicalRecord->id);
        
        // Verificar que la respuesta no es exitosa (no debe eliminar)
        $this->assertFalse($response->isSuccessful());

        // Verificar que el expediente sigue existiendo
        $this->assertDatabaseHas('clinical_records', [
            'id' => $this->clinicalRecord->id
        ]);
    }

    /**
     * Test de protección contra eliminación - Usuario regular
     */
    public function test_proteccion_eliminacion_usuario_regular()
    {
        $this->actingAs($this->regularUser);

        // Intentar eliminar como usuario regular
        $response = $this->delete('/clinical-records/' . $this->clinicalRecord->id);
        
        // Verificar que la respuesta no es exitosa (no debe eliminar)
        $this->assertFalse($response->isSuccessful());

        // Verificar que el expediente sigue existiendo
        $this->assertDatabaseHas('clinical_records', [
            'id' => $this->clinicalRecord->id
        ]);
    }

    /**
     * Test de protección contra eliminación - Administrador
     */
    public function test_proteccion_eliminacion_administrador()
    {
        $this->actingAs($this->adminUser);

        // Intentar eliminar como administrador
        $response = $this->delete('/clinical-records/' . $this->clinicalRecord->id);
        
        // Verificar que la respuesta no es exitosa (no debe eliminar)
        $this->assertFalse($response->isSuccessful());

        // Verificar que el expediente sigue existiendo
        $this->assertDatabaseHas('clinical_records', [
            'id' => $this->clinicalRecord->id
        ]);
    }

    /**
     * Test de protección contra eliminación por ID inexistente
     */
    public function test_proteccion_eliminacion_id_inexistente()
    {
        $this->actingAs($this->adminUser);

        // Intentar eliminar expediente inexistente
        $response = $this->delete('/clinical-records/99999');
        
        // Verificar que la respuesta no es exitosa
        $this->assertFalse($response->isSuccessful());
    }

    /**
     * Test de protección contra eliminación por inyección SQL
     */
    public function test_proteccion_eliminacion_inyeccion_sql()
    {
        $this->actingAs($this->adminUser);

        $sqlInjectionPayloads = [
            "1'; DROP TABLE clinical_records; --",
            "1 OR 1=1",
            "1 UNION SELECT * FROM clinical_records",
            "1'; DELETE FROM clinical_records WHERE 1=1; --",
            "1 OR (SELECT COUNT(*) FROM clinical_records) > 0",
        ];

        foreach ($sqlInjectionPayloads as $payload) {
            try {
                $response = $this->delete('/clinical-records/' . $payload);
                
                // Verificar que la respuesta no es exitosa
                $this->assertFalse($response->isSuccessful());
            } catch (\Exception $e) {
                // Esperado para payloads maliciosos
                $this->assertTrue(true);
            }
        }

        // Verificar que el expediente sigue existiendo
        $this->assertDatabaseHas('clinical_records', [
            'id' => $this->clinicalRecord->id
        ]);
    }

    /**
     * Test de verificación final - Integridad de datos
     */
    public function test_verificacion_final_integridad_datos()
    {
        $this->actingAs($this->adminUser);

        // Intentar eliminación directa
        $response = $this->delete('/clinical-records/' . $this->clinicalRecord->id);
        
        // Verificar que la respuesta no es exitosa
        $this->assertFalse($response->isSuccessful());

        // Verificar que el expediente original sigue intacto
        $this->clinicalRecord->refresh();
        $this->assertEquals('Juan', $this->clinicalRecord->first_name);
        $this->assertEquals('Pérez', $this->clinicalRecord->first_lastname);
        $this->assertEquals('1234567890123', $this->clinicalRecord->cui);
        $this->assertEquals('EXP-2024-000001', $this->clinicalRecord->record_number);

        // Verificar que el expediente sigue en la base de datos
        $this->assertDatabaseHas('clinical_records', [
            'id' => $this->clinicalRecord->id,
            'first_name' => 'Juan',
            'first_lastname' => 'Pérez'
        ]);
    }
}