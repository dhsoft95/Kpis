<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppUser extends Model
{
    use HasFactory;
    protected $connection ='mysql_second';
    protected $table="users";



    protected $fillable = [
        'first_name',
        'last_name',
        'role_id',
        'email_verified_at',
        'password',
        'remember_token',
        'phone_number',
        'email',
        'identity_type',
        'identity_value',
        'birth_date',
        'gender',
        'address',
        'city',
        'state',
        'country',
        'status',
        'otp_pin',
        'phone_verified_at',
        'last_session_id',
        'is_active',
    ];


    public function customerFeedbacks(): HasMany
    {
        return $this->hasMany(CustomerFeedback::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(trans::class);
    }
}
