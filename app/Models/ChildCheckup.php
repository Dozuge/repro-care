<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildCheckup extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'checkup_date',
        'weight',
        'height',
        'head_circumference',
        'developmental_milestones',
        'vaccinations_given',
        'notes',
        'conducted_by_id',
    ];

    protected $casts = [
        'checkup_date' => 'date',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'head_circumference' => 'decimal:2',
        'vaccinations_given' => 'array',
    ];

    public function child()
    {
        return $this->belongsTo(ChildRecord::class, 'child_id');
    }

    public function conductedBy()
    {
        return $this->belongsTo(User::class, 'conducted_by_id');
    }
}
