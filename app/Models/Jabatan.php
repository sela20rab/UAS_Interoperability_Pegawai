<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model{
    protected $primaryKey = 'id';
    protected $table = "jabatan";
    protected $fillable = array('jabatan');

    public $timestamps = true;

    public function Pegawai(){
        return $this->hasMany('App\Model\Pegawai');
    }
}