<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ServiceEntry extends Model { use ConcernsTags; protected $fillable=['project_id','contractor_id','date','name','pricing_type','hours','quantity','unit_name','unit_price','hourly_rate','total_amount','payment_status','paid_amount','comment']; protected function casts(): array{return ['date'=>'date','hours'=>'decimal:2','quantity'=>'decimal:3','unit_price'=>'decimal:2','hourly_rate'=>'decimal:2','total_amount'=>'decimal:2','paid_amount'=>'decimal:2'];} public function project(){return $this->belongsTo(Project::class);} public function contractor(){return $this->belongsTo(Contractor::class);} public function attachments(){return $this->morphMany(Attachment::class,'attachable');} }
