<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerFeedback extends Model
{
    use HasFactory;

    protected $connection ='mysql_second';

    protected $table="customer_feedbacks";

    protected $fillable = ['transaction_id', 'type', 'score', 'comment'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'score' => 'integer',
    ];

    /**
     * Get the transaction that this feedback is for.
     */


    /**
     * Get the user that gave the feedback.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(AppUser::class);
    }


}
