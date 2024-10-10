<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimbaTransaction extends Model
{
    use HasFactory;



    protected $table = 'simba_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trx_id',
        'third_party_trx_id',
        'user_id',
        'txn_source',
        'credit_amount',
        'debit_amount',
        'sender_currency',
        'receiver_currency',
        'charges',
        'txn_destination',
        'receiver_fullname',
        'partner_charges',
        'transaction_type',
        'biller_code',
        'biller_ref',
        'tax',
        'exchange_rate',
        'partner_exchange_rate',
        'partner_name',
        'reason',
        'account_no',
        'network_type',
        'status',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
