<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'frequency', 
        'color', 
        'is_active', 
        'user_id'
    ];

    // Constantes pour éviter les erreurs de frappe dans ton code
    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the current status of the habit (active/inactive)
     */
    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'active' : 'inactive';
    }

    /**
     * Scope to get only active habits
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

}
