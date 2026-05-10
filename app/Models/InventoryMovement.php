<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InventoryMovement extends Model { use ConcernsTags; protected $fillable=['project_id','material_id','date','type','quantity','unit_price','amount','source_type','source_id','comment']; protected function casts(): array{return ['date'=>'date','quantity'=>'decimal:3','unit_price'=>'decimal:2','amount'=>'decimal:2'];} public function project(){return $this->belongsTo(Project::class);} public function material(){return $this->belongsTo(Material::class);} public function source(){return $this->morphTo();} }
