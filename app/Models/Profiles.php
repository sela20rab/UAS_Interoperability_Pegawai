<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profiles extends Model{
    protected $primaryKey = 'id';
    protected $table = "profiles";
    protected $fillable = array('user_id', 'first_name', 'last_name', 'summary', 'image');

    public $timestamps = true;
}