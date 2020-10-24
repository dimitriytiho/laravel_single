<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends App
{
    use HasFactory;

    // Связь один ко многим
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
