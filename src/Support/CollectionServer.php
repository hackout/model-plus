<?php namespace DennisLui\ModelPlus\Support;

use \Illuminate\Support\Collection;

class CollectionServer {

	protected $collection;

	/**
	 * Create a new collection instance.
	 * @param Collection $collection
	 */
	public function __construct(Collection $collection) {
		$this->collection = $collection;
	}

	public static function getFunctions() {
		return ['reverse_keys'=>'reverse_keys'];
	}

	/**
	 * The collection reverse keys
	 * @param  string|null $keyName
	 * @param  string|null $otherKey
	 * @param  string|null $secondKey
	 * @return Collection
	 */
	public function reverse_keys(): Collection {
		list($keyName,$otherKey,$secondKey) = Collection::wrap(func_get_args()[0])->pad(0,3);
		if ($this->collection->isEmpty()) {
			return $this->collection;
		}

		if (!array_is_list($this->collection->toArray())) {
			if ($this->collection->has($keyName) && $this->collection->get($keyName)) {
				$parent = Collection::wrap($this->collection->pull($keyName));
			} else {
				$parent = Collection::wrap([]);
			}
			$children = $this->collection;
			if($parent->isNotEmpty() && array_is_list($parent->toArray()))
			{
				$parent = $parent->map(function($value,$key) use($children,$otherKey){
					$value[$otherKey] = $children;
					return $value;
				});
			}else{
				$parent->put($otherKey, $children);
			}
			return $parent;
		}

		$collection = Collection::wrap([]);
		$array = Collection::wrap([]);
		$children = Collection::wrap([]);
		$this->collection->each(function($value,$key) use ($secondKey,$keyName,$children,$array){
			$value = Collection::wrap($value);
			if($value->isNotEmpty() && array_is_list($value->toArray()))
			{
				$value->each(function($v,$k) use ($array,$keyName,$secondKey){
					$v = Collection::wrap($v);
					$primary = Collection::wrap($v->pull($keyName));
					if($primary->isNotEmpty() && array_is_list($primary->toArray()))
					{
						$primary->each(function($vv,$kk) use($children,$secondKey,$v,$array){
							$vv = Collection::wrap($vv);
							$primaryKey = !$vv->has($secondKey) ? md5($vv->toJson()) : $vv->pull($secondKey);
							$vv->put($secondKey,$primaryKey);
							$children->push($vv->toArray());
							$arr = !$array->has($primaryKey) ? [] : $array->pull($primaryKey);
							$arr[] = $v;
							$array->put($primaryKey,$arr);
						});
					}else{
						$primaryKey = !$primary->has($secondKey) ? md5($primary->toJson()) : $primary->pull($secondKey);
						$value->put($secondKey,$primaryKey);
						$children->push($v->toArray());
						$arr = !$array->has($primaryKey) ? [] : $array->pull($primaryKey);
						$arr[] = $primary->toArray();
						$array->put($primaryKey,$arr);
					}
				});
			}else{
				$primary = Collection::wrap($value->pull($keyName));
				if($primary->isNotEmpty() && array_is_list($primary->toArray()))
				{
					$primary->each(function($v,$k) use($children,$secondKey,$value,$array){
						$v = Collection::wrap($v);
						$primaryKey = !$v->has($secondKey) ? md5($v->toJson()) : $v->pull($secondKey);
						$v->put($secondKey,$primaryKey);
						$children->push($v->toArray());
						$arr = !$array->has($primaryKey) ? [] : $array->pull($primaryKey);
						$arr[] = $value->toArray();
						$array->put($primaryKey,$arr);
					});
				}else{
					$primaryKey = !$primary->has($secondKey) ? md5($primary->toJson()) : $primary->pull($secondKey);
					$value->put($secondKey,$primaryKey);
					$children->push($value->toArray());
					$arr = !$array->has($primaryKey) ? [] : $array->pull($primaryKey);
					$arr[] = $primary->toArray();
					$array->put($primaryKey,$arr);
				}
			}
		});
		$unique = $children->unique($secondKey);
		$unique->each(function($value,$key) use ($array,$secondKey,$otherKey,$collection)
		{
			$value = Collection::wrap($value);
			$primaryKey = $value->pull($secondKey);
			$value->put($secondKey,$primaryKey);
			$value->put($otherKey,$array->get($primaryKey));
			$collection->push($value);
		});

		return $collection;
	}
}
