<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const TYPES = [
        'call' => ['label' => 'Call', 'icon' => 'phone'],
        'meeting' => ['label' => 'Meeting', 'icon' => 'users'],
        'task' => ['label' => 'Task', 'icon' => 'clock'],
        'deadline' => ['label' => 'Deadline', 'icon' => 'flag'],
        'email' => ['label' => 'Email', 'icon' => 'mail'],
        'lunch' => ['label' => 'Lunch', 'icon' => 'utensils'],
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'done' => 'boolean',
            'completed_at' => 'datetime',
            'is_sample' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function scopeTodo(Builder $query): Builder
    {
        return $query->where('done', false);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('done', false)->whereNotNull('due_date')->whereDate('due_date', '<', now()->toDateString());
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type]['label'] ?? ucfirst($this->type);
    }

    public function getIconAttribute(): string
    {
        return self::TYPES[$this->type]['icon'] ?? 'clock';
    }

    public function getSubjectCleanAttribute(): string
    {
        return preg_replace('/^\[Sample\]\s*/', '', $this->subject);
    }

    public function getIsOverdueAttribute(): bool
    {
        return ! $this->done && $this->due_date && $this->due_date->isBefore(now()->startOfDay());
    }
}
