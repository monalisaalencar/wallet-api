<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $dateFormat = 'Y-m-d G:i:s';
    protected $dates = ['updated_at', 'created_at','deleted_at'];
    protected $guarded = ['_id'];
    protected $fillable = [
        'payee_id',
        'payer_id',
        'total_value',
        'created_at',
        'updated_at',
    ];

    public function payer()
    {
        return $this->belongsTo(Client::class);
    }

    public function payee()
    {
        return $this->belongsTo(Client::class);
    }

}
