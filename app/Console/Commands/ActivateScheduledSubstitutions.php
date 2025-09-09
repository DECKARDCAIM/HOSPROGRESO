<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DoctorSubstitution;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivateScheduledSubstitutions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'substitutions:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activa las sustituciones programadas que deben iniciar hoy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        $scheduledSubstitutions = DoctorSubstitution::where('status', DoctorSubstitution::STATUS_SCHEDULED)
            ->whereDate('start_date', $today)
            ->get();

        if ($scheduledSubstitutions->isEmpty()) {
            $this->info('No hay sustituciones programadas para activar hoy.');
            return;
        }

        $this->info("Activando {$scheduledSubstitutions->count()} sustituciones programadas...");

        foreach ($scheduledSubstitutions as $substitution) {
            DB::beginTransaction();
            try {
                // Activar la sustitución
                $substitution->update(['status' => DoctorSubstitution::STATUS_ACTIVE]);

                // Reasignar citas
                $this->reassignAppointments($substitution);

                // Desactivar doctor original si la razón es vacaciones o despido
                if (in_array($substitution->reason, [DoctorSubstitution::REASON_VACATION, DoctorSubstitution::REASON_TERMINATION])) {
                    $substitution->originalDoctor->update(['is_active' => false]);
                }

                DB::commit();
                
                $this->info("✓ Sustitución activada: {$substitution->originalDoctor->full_name} → {$substitution->substituteDoctor->full_name}");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("✗ Error al activar sustitución ID {$substitution->id}: {$e->getMessage()}");
            }
        }

        $this->info('Proceso completado.');
    }

    /**
     * Reasignar citas a la sustitución
     */
    private function reassignAppointments(DoctorSubstitution $substitution)
    {
        // Reasignar citas futuras
        $appointments = \App\Models\Appointment::where('doctor_id', $substitution->original_doctor_id)
            ->where('appointment_date', '>=', $substitution->start_date)
            ->where('appointment_date', '<=', $substitution->end_date)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->update([
                'doctor_id' => $substitution->substitute_doctor_id,
                'substitution_id' => $substitution->id,
                'is_substituted' => true,
                'substituted_at' => now()
            ]);
        }

        // Reasignar consultas médicas futuras
        $consultations = \App\Models\MedicalConsultation::where('doctor_id', $substitution->original_doctor_id)
            ->where('consultation_date', '>=', $substitution->start_date)
            ->where('consultation_date', '<=', $substitution->end_date)
            ->where('status', 'abierta')
            ->get();

        foreach ($consultations as $consultation) {
            $consultation->update([
                'doctor_id' => $substitution->substitute_doctor_id,
                'substitution_id' => $substitution->id,
                'is_substituted' => true,
                'substituted_at' => now()
            ]);
        }
    }
}