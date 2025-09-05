<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'name',
        'original_name',
        'mime_type',
        'google_drive_id',
        'google_drive_url',
        'size'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}