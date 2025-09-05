<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'ai_analysis',
        'due_date'
    ];

    protected $casts = [
        'due_date' => 'datetime'
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}