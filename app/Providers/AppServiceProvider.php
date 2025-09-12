<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;
use App\Observers\CatalogObserver;
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\ScheduleType;
use App\Models\Sex;
use App\Models\CivilStatus;
use App\Models\LinguisticCommunity;
use App\Models\Ethnicity;
use App\Models\Disability;
use App\Models\Allergy;
use App\Models\LaboratoryTest;
use App\Models\Exam;
use App\Models\Medication;
use App\Models\Role;
use App\Models\Country;
use App\Models\Department;
use App\Models\Municipality;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        $this->registerPermissionGates();

        $this->registerCatalogObservers();
    }

    private function registerPermissionGates(): void
    {
        try {
            $permissions = Permission::all();
            
            foreach ($permissions as $permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermission($permission->slug);
                });
            }
        } catch (\Exception $e) {
        }
    }

    private function registerCatalogObservers(): void
    {
        try {
            Specialty::observe(CatalogObserver::class);
            Doctor::observe(CatalogObserver::class);
            ScheduleType::observe(CatalogObserver::class);
            Sex::observe(CatalogObserver::class);
            CivilStatus::observe(CatalogObserver::class);
            LinguisticCommunity::observe(CatalogObserver::class);
            Ethnicity::observe(CatalogObserver::class);
            Disability::observe(CatalogObserver::class);
            Allergy::observe(CatalogObserver::class);
            LaboratoryTest::observe(CatalogObserver::class);
            Exam::observe(CatalogObserver::class);
            Medication::observe(CatalogObserver::class);
            Role::observe(CatalogObserver::class);
            Country::observe(CatalogObserver::class);
            Department::observe(CatalogObserver::class);
            Municipality::observe(CatalogObserver::class);
        } catch (\Throwable $e) {
        }
    }
}
