<?php
namespace App\Services;
class TagFilterService{public function apply($query,array $tagIds){return empty($tagIds)?$query:$query->whereHas('tags',fn($q)=>$q->whereIn('tags.id',$tagIds));}}
