<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'description',
        'is_recurring',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con el usuario que creó el día festivo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope para días festivos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para días festivos recurrentes
     */
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    /**
     * Verificar si una fecha es día festivo
     */
    public static function isHoliday($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        
        // Verificar días festivos específicos
        $specificHoliday = self::active()
            ->where('date', $date)
            ->exists();
            
        if ($specificHoliday) {
            return true;
        }
        
        // Verificar días festivos recurrentes (mismo día y mes cada año)
        $recurringHoliday = self::active()
            ->recurring()
            ->whereRaw('DAY(date) = ? AND MONTH(date) = ?', [
                Carbon::parse($date)->day,
                Carbon::parse($date)->month
            ])
            ->exists();
            
        return $recurringHoliday;
    }

    /**
     * Obtener todos los días festivos para un rango de fechas
     */
    public static function getHolidaysInRange($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        
        $holidays = collect();
        
        // Días festivos específicos en el rango
        $specificHolidays = self::active()
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();
            
        $holidays = $holidays->merge($specificHolidays);
        
        // Días festivos recurrentes
        $recurringHolidays = self::active()
            ->recurring()
            ->get();
            
        foreach ($recurringHolidays as $holiday) {
            $holidayDate = Carbon::parse($holiday->date);
            
            // Generar fechas recurrentes para cada año en el rango
            for ($year = $startDate->year; $year <= $endDate->year; $year++) {
                $recurringDate = $holidayDate->copy()->year($year);
                
                if ($recurringDate->between($startDate, $endDate)) {
                    $holidayCopy = $holiday->replicate();
                    $holidayCopy->date = $recurringDate->format('Y-m-d');
                    $holidays->push($holidayCopy);
                }
            }
        }
        
        return $holidays->unique('date');
    }

    /**
     * Obtener el próximo día laborable después de una fecha
     */
    public static function getNextWorkingDay($date)
    {
        $date = Carbon::parse($date);
        
        do {
            $date->addDay();
        } while (self::isHoliday($date) || $date->isWeekend());
        
        return $date;
    }

    /**
     * Obtener el día laborable anterior a una fecha
     */
    public static function getPreviousWorkingDay($date)
    {
        $date = Carbon::parse($date);
        
        do {
            $date->subDay();
        } while (self::isHoliday($date) || $date->isWeekend());
        
        return $date;
    }
}
