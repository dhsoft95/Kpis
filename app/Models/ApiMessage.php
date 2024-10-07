<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiMessage extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql_second';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_messages';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message_key',
        'message_text',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'message_key' => 'string',
        'message_text' => 'string',
    ];
}
