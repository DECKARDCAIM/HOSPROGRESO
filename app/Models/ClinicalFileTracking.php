<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalFileTracking extends Model
{
    use HasFactory;

    protected $table = 'clinical_file_tracking';

    protected $fillable = [
        'clinical_record_id',
        'is_printed',
        'printed_at',
        'printed_by',
        'is_archived',
        'archived_at',
        'archived_by',
        'notes'
    ];

    protected $casts = [
        'is_printed' => 'boolean',
        'is_archived' => 'boolean',
        'printed_at' => 'datetime',
        'archived_at' => 'datetime'
    ];

    /**
     * Relación con el expediente clínico
     */
    public function clinicalRecord()
    {
        return $this->belongsTo(ClinicalRecord::class);
    }

    /**
     * Usuario que imprimió
     */
    public function printedBy()
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    /**
     * Usuario que archivó
     */
    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    /**
     * Scope para expedientes no impresos
     */
    public function scopeNotPrinted($query)
    {
        return $query->where('is_printed', false);
    }

    /**
     * Scope para expedientes no archivados
     */
    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope para expedientes archivados
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    /**
     * Marcar como impreso
     */
    public function markAsPrinted($userId)
    {
        $this->update([
            'is_printed' => true,
            'printed_at' => now(),
            'printed_by' => $userId
        ]);
    }

    /**
     * Marcar como archivado
     */
    public function markAsArchived($userId, $notes = null)
    {
        $this->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => $userId,
            'notes' => $notes
        ]);
    }
}