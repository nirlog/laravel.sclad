<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Material extends Model
{ protected $fillable=['project_id','unit_id','name','sku','description','is_active']; protected function casts(): array{return ['is_active'=>'boolean'];} public function project(){return $this->belongsTo(Project::class);} public function unit(){return $this->belongsTo(Unit::class);} public function inventoryMovements(){return $this->hasMany(InventoryMovement::class);} public function purchaseItems(){return $this->hasMany(MaterialPurchaseItem::class);} }
