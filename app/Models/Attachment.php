<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Attachment extends Model { protected $fillable=['project_id','attachable_type','attachable_id','file_path','original_name','mime_type','size']; public function project(){return $this->belongsTo(Project::class);} public function attachable(){return $this->morphTo();} }
