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
		return [];
	}

	/**
	 * The collection reverse keys
	 * @param  string|null $keyName
	 * @param  string|null $otherKey
	 * @param  string|null $secondKey
	 * @return Collection
	 */
	public function reverse_keys($keyName = 'children', $otherKey = 'children', $secondKey = null): Collection {
		if ($this->collection->isEmpty()) {
			return $this->collection;
		}

		if (!array_is_list($this->collection->toArray())) {
			if ($this->collection->has($keyName) && $this->collection->get($keyName)) {
				$parent = $this->collection->pull($keyName);
			} else {
				$parent = Collection::wrap([]);
			}
			$children = $this->collection;
			if($parent->isNotEmpty() && array_is_list($parent->toArray()))
			{
				$parent->each(function($value,$key) use($children,$otherKey){
					$value->put($otherKey,$children);
				});
			}else{
				$parent->put($otherKey, $children);
			}
			return $parent;
		}

		$collection = $array = $children = Collection::wrap([]);

		$this->collection->each(function ($value, $key) use ($secondKey, $keyName, $children, $array) {
			if ($value->has($keyName) && $value->get($keyName)) {
				$parent = $value->pull($keyName);
			} else {
				$parent = Collection::wrap([]);
			}
			if($parent->isNotEmpty() && array_is_list($parent))
			{
				$parent->each(function($v,$k) use ($value,$array,$children,$secondKey){
					$mainKey = $secondKey ? $v->get($secondKey) : md5($v->toJson());
					if ($children->has($mainKey)) {
						$children->each(function ($vv, $kk) use ($mainKey, $value) {
							if ($kk == $mainKey) {
								$vv->push($value);
							}
						});
					} else {
						$children->put($mainKey, Collection::wrap([$value]));
					}
					$array->put($mainKey, $v);
				});
			}else{
				$mainKey = $secondKey ? $parent->get($secondKey) : md5($parent->toJson());
				if ($children->has($mainKey)) {
					$children->each(function ($v, $k) use ($mainKey, $value) {
						if ($k == $mainKey) {
							$v->push($value);
						}
					});
				} else {
					$children->put($mainKey, Collection::wrap([$value]));
				}
				$array->put($mainKey, $parent);
			}
		});

		$array->each(function ($value, $key) use ($children, $collection, $otherKey) {
			if ($value->has($otherKey)) {
				$value->each(function ($v, $k) use ($children, $key, $otherKey) {
					if ($k == $otherKey) {
						$v->push($children->get($key));
					}
				});
			} else {
				$value->put($otherKey, $children->get($key));
			}
			$collection->push($value);
		});

		return $collection;
	}
}
