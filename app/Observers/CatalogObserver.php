<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CatalogObserver
{
    public function created(Model $model): void   { $this->invalidateCache($model); }
    public function updated(Model $model): void   { $this->invalidateCache($model); }
    public function deleted(Model $model): void   { $this->invalidateCache($model); }
    public function restored(Model $model): void  { $this->invalidateCache($model); }
    public function forceDeleted(Model $model): void { $this->invalidateCache($model); }

    private function invalidateCache(Model $model): void
    {
        [$tags, $prefix, $version] = $this->resolveTagsAndPrefix($model);

        if ($tags) {
            $tagsToFlush = array_values(array_unique(array_diff($tags, [$prefix])));
            if (!empty($tagsToFlush)) {
                Cache::tags($tagsToFlush)->flush();
            }
        }

        if ($prefix && isset($model->id)) {
            Cache::tags([$prefix])->forget("{$prefix}:show:{$version}:{$model->id}");
        }
    }

    private function resolveTagsAndPrefix(Model $model): array
    {
        $class = get_class($model);

        $map = [
            'App\\Models\\Specialty'          => [['especialidades', 'catalogos', 'listados', 'especialidades'], 'especialidades', 'v2'],
            'App\\Models\\Doctor'             => [['doctores', 'catalogos', 'listados', 'doctores'], 'doctores', 'v1'],
            'App\\Models\\ScheduleType'       => [['tipos_horario', 'catalogos', 'listados', 'schedule-types'], 'schedule-types', 'v1'],
            'App\\Models\\ControlType'        => [['tipos_control', 'catalogos', 'listados', 'control-types'], 'control-types', 'v1'],
            'App\\Models\\Sex'                => [['sexos', 'catalogos', 'listados', 'sexes'], 'sexes', 'v1'],
            'App\\Models\\CivilStatus'        => [['estados_civiles', 'catalogos', 'listados', 'civil-statuses'], 'civil-statuses', 'v1'],
            'App\\Models\\LinguisticCommunity'=> [['comunidades_linguisticas', 'catalogos', 'listados', 'linguistic-communities'], 'linguistic-communities', 'v1'],
            'App\\Models\\Ethnicity'          => [['etnias', 'catalogos', 'listados', 'ethnicities'], 'ethnicities', 'v1'],
            'App\\Models\\Disability'         => [['discapacidades', 'catalogos', 'listados', 'disabilities'], 'disabilities', 'v1'],
            'App\\Models\\Allergy'            => [['alergias', 'catalogos', 'listados', 'allergies'], 'allergies', 'v1'],
            'App\\Models\\LaboratoryTest'     => [['pruebas_laboratorio', 'catalogos', 'listados', 'laboratory-tests'], 'laboratory-tests', 'v1'],
            'App\\Models\\Exam'               => [['examenes', 'catalogos', 'listados', 'exams'], 'exams', 'v1'],
            'App\\Models\\Medication'         => [['medicamentos', 'catalogos', 'listados', 'medications'], 'medications', 'v1'], // ↑ pon 'v2' si ya migraste

            'App\\Models\\Role'               => [['roles', 'catalogos', 'listados', 'roles'], 'roles', 'v2'], // ← alinea con tus controladores
            'App\\Models\\Permission'         => [['permisos', 'catalogos', 'listados', 'permisos'], 'permisos', 'v1'],

            'App\\Models\\Country'            => [['paises', 'catalogos', 'listados', 'paises'], 'paises', 'v1'],
            'App\\Models\\Department'         => [['departamentos', 'catalogos', 'listados', 'departamentos'], 'departamentos', 'v1'],
            'App\\Models\\Municipality'       => [['municipios', 'catalogos', 'listados', 'municipios'], 'municipios', 'v1'],
        ];

        if (isset($map[$class])) {
            [$tags, $prefix, $version] = $map[$class];
            if ($prefix && !in_array($prefix, $tags, true)) {
                $tags[] = $prefix;
            }
            return [$tags, $prefix, $version];
        }

        return [[], null, 'v1'];
    }
}