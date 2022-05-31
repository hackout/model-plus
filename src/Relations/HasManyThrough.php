<?php

namespace DennisLui\ModelPlus\Relations;
use DennisLui\ModelPlus\Traits\RelationPlus;
use \Illuminate\Database\Eloquent\Builder as BaseBuilder;
use \Illuminate\Database\Eloquent\Model as BaseModel;

class HasManyThrough extends \Illuminate\Database\Eloquent\Relations\HasManyThrough {
	use RelationPlus;
	/**
	 * Create a new has many through relationship instance.
	 *
	 * @param  BaseBuilder  $query
	 * @param  BaseModel  $farParent
	 * @param  BaseModel  $throughParent
	 * @param  string  $firstKey
	 * @param  string  $secondKey
	 * @param  string  $localKey
	 * @param  string  $secondLocalKey
	 * @return void
	 */
	public function __construct(BaseBuilder $query, BaseModel $farParent, BaseModel $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey) {
		parent::__construct($query, $farParent, $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey);
		$this->macroNext($query);
	}
}
