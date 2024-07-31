<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class trans extends Model
{
    use HasFactory;
    protected $table="tbl_transactions";
    protected $connection = 'mysql_second';


    public function customerFeedback(): HasOne
    {
        return $this->hasOne(CustomerFeedback::class, 'transaction_id');
    }

    public function needsFeedback(): bool
    {
        if ($this->customerFeedback()->exists()) {
            return false;
        }
        return $this->status == 3; // Assuming status 3 means successful
    }

}
