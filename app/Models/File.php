<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use DateTimeInterface;

class File extends Model
{
    protected $table = 'file';

    protected $fillable = [
        'user_id', 'file_name', 'file_path'
    ];

    protected $appends = [
        'download_url'
    ];

    use HasFactory;


    public function getDownloadUrlAttribute()
    {
        $value = '';
        if ($this->file_path) {
            $value = Storage::disk('public')->url($this->file_path);
        }
        return $value;
    }

    public function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


}
