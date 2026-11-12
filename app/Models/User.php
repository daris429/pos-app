<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];
    protected $hidden = ['password'];
    protected $casts = ['is_active' => 'boolean'];

    public function purchases() { return $this->hasMany(Purchase::class); }
    public function sales() { return $this->hasMany(Sale::class); }
}