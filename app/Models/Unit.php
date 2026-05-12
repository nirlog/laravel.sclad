<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Unit extends Model { protected $fillable=['name','short_name']; public function materials(){return $this->hasMany(Material::class);} }
