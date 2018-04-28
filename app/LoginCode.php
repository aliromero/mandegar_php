<?php

namespace App;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;

class LoginCode extends Model
{
    protected $table = 'login_codes';
    public $timestamps = true;


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
