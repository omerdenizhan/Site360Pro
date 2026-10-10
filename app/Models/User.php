<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const SECURITY_QUESTIONS = [
        'İlk evcil hayvanınızın adı nedir?',
        'İlkokul öğretmeninizin soyadı nedir?',
        'Doğduğunuz şehir neresidir?',
        'Annenizin kızlık soyadı nedir?',
        'Çocukluğunuzda yaşadığınız sokağın adı nedir?',
        'İlk çalıştığınız iş yerinin adı nedir?',
        'En sevdiğiniz yemek nedir?',
        'İlk aracınızın markası nedir?',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'site_id',
        'is_active',
        'security_question',
        'security_answer',
        'failed_attempts',
        'locked_until',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }
}
