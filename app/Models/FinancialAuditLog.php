<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get the model that was audited (polymorphic-like)
    public function auditable()
    {
        if ($this->model_type && $this->model_id) {
            $modelClass = "App\\Models\\{$this->model_type}";
            if (class_exists($modelClass)) {
                return $modelClass::find($this->model_id);
            }
        }
        return null;
    }

    // Helper to log financial activity
    public static function logActivity($action, $model, $oldValues = null, $newValues = null, $description = null)
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => class_basename($model),
            'model_id' => $model->id ?? null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
        ]);
    }
}
