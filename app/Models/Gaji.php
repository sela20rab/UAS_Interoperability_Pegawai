<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gaji extends Model{
    protected $table = 'gaji';
    protected $fillable = array('gaji_pokok', 'tunjangan');

    public $timestamps = true;

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function obat(){
        return $this->belongsTo('App\Models\Pegawai');
    }
}
?>