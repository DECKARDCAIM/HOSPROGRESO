<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class CatalogObserver
{
    public function created($model): void
    {
        $this->invalidateCache($model);
    }

    public function updated($model): void
    {
        $this->invalidateCache($model);
    }

    public function deleted($model): void
    {
        $this->invalidateCache($model);
    }

    public function restored($model): void
    {
        $this->invalidateCache($model);
    }

    private function invalidateCache($model): void
    {
        [$tags, $prefix, $version] = $this->resolveTagsAndPrefix($model);

        if (!empty($tags)) {
            Cache::tags($tags)->flush();
        }

        if ($prefix && isset($model->id)) {
            Cache::tags([$prefix])->forget("{$prefix}:show:{$version}:{$model->id}");
        }
    }

    private function resolveTagsAndPrefix($model): array
    {
        $class = get_class($model);

        // Mapa de clases a tags y prefijos de claves
        $map = [
            // Catálogos médicos y afines
            'App\\Models\\Specialty' => [['especialidades', 'catalogos', 'listados'], 'especialidades', 'v2'],
            'App\\Models\\Doctor' => [['doctores', 'listados'], 'doctores', 'v1'],
            'App\\Models\\ScheduleType' => [['tipos_horario', 'catalogos', 'listados'], 'schedule-types', 'v1'],
            'App\\Models\\ControlType' => [['tipos_control', 'catalogos', 'listados'], 'control-types', 'v1'],
            'App\\Models\\Sex' => [['sexos', 'catalogos', 'listados'], 'sexes', 'v1'],
            'App\\Models\\CivilStatus' => [['estados_civiles', 'catalogos', 'listados'], 'civil-statuses', 'v1'],
            'App\\Models\\LinguisticCommunity' => [['comunidades_linguisticas', 'catalogos', 'listados'], 'linguistic-communities', 'v1'],
            'App\\Models\\Ethnicity' => [['etnias', 'catalogos', 'listados'], 'ethnicities', 'v1'],
            'App\\Models\\Disability' => [['discapacidades', 'catalogos', 'listados'], 'disabilities', 'v1'],
            'App\\Models\\Allergy' => [['alergias', 'catalogos', 'listados'], 'allergies', 'v1'],
            'App\\Models\\LaboratoryTest' => [['pruebas_laboratorio', 'catalogos', 'listados'], 'laboratory-tests', 'v1'],
            'App\\Models\\Exam' => [['examenes', 'catalogos', 'listados'], 'exams', 'v1'],
            'App\\Models\\Medication' => [['medicamentos', 'catalogos', 'listados'], 'medications', 'v1'],

            // Administración
            'App\\Models\\Role' => [['roles', 'catalogos', 'listados'], 'roles', 'v1'],
            'App\\Models\\Permission' => [['permisos', 'catalogos', 'listados'], 'permisos', 'v1'],

            // Ubicación
            'App\\Models\\Country' => [['paises', 'catalogos', 'listados'], 'paises', 'v1'],
            'App\\Models\\Department' => [['departamentos', 'catalogos', 'listados'], 'departamentos', 'v1'],
            'App\\Models\\Municipality' => [['municipios', 'catalogos', 'listados'], 'municipios', 'v1'],
        ];

        if (isset($map[$class])) {
            return $map[$class];
        }

        return [[], null, 'v1'];
    }
}


