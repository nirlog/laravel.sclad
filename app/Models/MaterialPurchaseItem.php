<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaterialPurchaseItem extends Model { protected $fillable=['material_purchase_id','material_id','quantity','unit_price','total_price']; protected function casts(): array{return ['quantity'=>'decimal:3','unit_price'=>'decimal:2','total_price'=>'decimal:2'];} public function purchase(){return $this->belongsTo(MaterialPurchase::class,'material_purchase_id');} public function material(){return $this->belongsTo(Material::class);} public function movement(){return $this->morphOne(InventoryMovement::class,'source');} }
