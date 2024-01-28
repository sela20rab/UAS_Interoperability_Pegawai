<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Pegawai extends Model{
    protected $primarykey = 'id';
    protected $table = "pegawai";
    protected $fillable = array ('nip', 'nama_pegawai', 'alamat',  'jenis_kelamin');
    public $timestamps = true;

    public function jabatan(){
        return $this->belongsTo('App\Models\Jabatan');
    }

    public function gaji(){
        return $this->HasMany('App\Model\Gaji');
    }
}

?>
