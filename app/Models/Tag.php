<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Tag extends Model { protected $fillable=['project_id','name','slug','color']; public function project(){return $this->belongsTo(Project::class);} }
