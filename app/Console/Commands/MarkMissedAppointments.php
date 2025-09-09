<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;

class MarkMissedAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:mark-missed {--dry-run : Show what would be updated without actually updating} {--force : Execute without confirmation prompts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca automáticamente como perdidas las citas que pasaron su fecha/hora y no fueron atendidas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $isForced = $this->option('force');
        
        $this->info('🔍 Buscando citas vencidas...');
        
        // NUEVA LÓGICA: Marcar únicamente al día siguiente a las 00:00
        // Se consideran perdidas las citas en estado PENDIENTE cuya fecha/hora
        // sea estrictamente anterior al inicio del día actual (no durante el mismo día de la cita)
        $cutoffTime = Carbon::now()->startOfDay();

        $missedAppointments = Appointment::where('status', 'pendiente')
            ->where('appointment_date', '<', $cutoffTime)
            ->with(['clinicalRecord', 'doctor', 'specialty'])
            ->get();
        
        if ($missedAppointments->isEmpty()) {
            $this->info('✅ No se encontraron citas perdidas.');
            return 0;
        }
        
        $this->info("📋 Se encontraron {$missedAppointments->count()} citas perdidas:");
        
        $table = [];
        foreach ($missedAppointments as $appointment) {
            $table[] = [
                'Cita' => $appointment->appointment_number,
                'Paciente' => $appointment->clinicalRecord->full_name ?? 'N/A',
                'Doctor' => $appointment->doctor->full_name ?? 'N/A',
                'Fecha/Hora' => $appointment->appointment_date->format('d/m/Y H:i'),
                'Estado Actual' => ucfirst($appointment->status),
                'Horas Vencidas' => $appointment->appointment_date->diffInHours(now(), false)
            ];
        }
        
        $this->table([
            'Cita', 'Paciente', 'Doctor', 'Fecha/Hora', 'Estado Actual', 'Horas Vencidas'
        ], $table);
        
        if ($isDryRun) {
            $this->warn('🧪 MODO DRY-RUN: No se realizarán cambios.');
            $this->info('💡 Para ejecutar los cambios reales, ejecuta: php artisan appointments:mark-missed --force');
            return 0;
        }
        
        // Solo pedir confirmación si no es automático (--force)
        if (!$isForced && !$this->confirm('¿Deseas marcar estas citas como perdidas?')) {
            $this->info('❌ Operación cancelada.');
            return 0;
        }
        
        if ($isForced) {
            $this->info('🤖 Ejecución automática: Marcando citas como perdidas...');
        }
        
        $updated = 0;
        foreach ($missedAppointments as $appointment) {
            try {
                $appointment->markAsMissed('Marcada automáticamente como perdida por el sistema');
                $updated++;
                
                $this->info("✅ {$appointment->appointment_number} - {$appointment->clinicalRecord->full_name}");
                
            } catch (\Exception $e) {
                $this->error("❌ Error al actualizar {$appointment->appointment_number}: " . $e->getMessage());
            }
        }
        
        $this->info("🎉 Proceso completado: {$updated} citas marcadas como perdidas.");
        
        if ($updated > 0) {
            $this->info('📧 Tip: Considera enviar notificaciones a los pacientes para reagendar sus citas.');
        }
        
        return 0;
    }
}
