<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'name',
        'cpf',
        'birth_date',
        'gender',
        'email',
        'phone',
    ];

    public function appointment(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
