<?php

namespace DennisLui\ModelPlus\Relations;

use DennisLui\ModelPlus\Traits\RelationPlus;
use \Illuminate\Database\Eloquent\Builder as BaseBuilder;
use \Illuminate\Database\Eloquent\Model as BaseModel;

class BelongsTo extends \Illuminate\Database\Eloquent\Relations\BelongsTo {
	use RelationPlus;

	/**
	 * Create a new belongs to relationship instance.
	 *
	 * @param  BaseBuilder  $query
	 * @param  BaseModel  $child
	 * @param  string  $foreignKey
	 * @param  string  $ownerKey
	 * @param  string  $relation
	 * @return void
	 */
	public function __construct(BaseBuilder $query, BaseModel $child, $foreignKey, $ownerKey, $relation) {
		parent::__construct($query, $child, $foreignKey, $ownerKey, $relation);
		$this->macroNext($query);
	}
}
