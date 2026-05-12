<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Contractor extends Model { protected $fillable=['project_id','name','phone','email','comment','is_active']; protected function casts(): array{return ['is_active'=>'boolean'];} public function project(){return $this->belongsTo(Project::class);} public function serviceEntries(){return $this->hasMany(ServiceEntry::class);} }
