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
        'subscription_status',
        'pin',
        'security_question',
        'security_answer',
        'language',
        'veriffy_status',
        'fcm_token',
        'failed_logins',
        'last_session_id',
        'is_active',
        'wallet_status',
        'lock_status',
        'address_one',
        'address_two',
        'ward',
        'district',
        'customer_image',
        'card_image',
        'simba_tag',
        'account_no',
        'request_id',
    ];


    public function customerFeedbacks(): HasMany
    {
        return $this->hasMany(CustomerFeedback::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Trans::class); // Ensure Trans::class is correct
    }
}
