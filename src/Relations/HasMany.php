<?php

namespace DennisLui\ModelPlus\Relations;
use DennisLui\ModelPlus\Traits\RelationPlus;
use \Illuminate\Database\Eloquent\Builder as BaseBuilder;
use \Illuminate\Database\Eloquent\Model as BaseModel;

class HasMany extends \Illuminate\Database\Eloquent\Relations\HasMany {
	use RelationPlus;

	/**
	 * Create a new morph one or many relationship instance.
	 *
	 * @param  BaseBuilder  $query
	 * @param  BaseModel  $parent
	 * @param  string  $type
	 * @param  string  $id
	 * @param  string  $localKey
	 * @return void
	 */
	public function __construct(BaseBuilder $query, BaseModel $parent, $foreignKey, $localKey) {
		parent::__construct($query, $parent, $foreignKey, $localKey);
		$this->macroNext($query);
	}
}
