<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaterialWriteOff extends Model { use ConcernsTags; protected $fillable=['project_id','material_id','date','quantity','unit_price','total_amount','comment']; protected function casts(): array{return ['date'=>'date','quantity'=>'decimal:3','unit_price'=>'decimal:2','total_amount'=>'decimal:2'];} public function project(){return $this->belongsTo(Project::class);} public function material(){return $this->belongsTo(Material::class);} public function movement(){return $this->morphOne(InventoryMovement::class,'source');} }
