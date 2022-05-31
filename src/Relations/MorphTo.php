<?php

namespace DennisLui\ModelPlus\Relations;

use DennisLui\ModelPlus\Traits\RelationPlus;
use \Illuminate\Database\Eloquent\Builder as BaseBuilder;
use \Illuminate\Database\Eloquent\Model as BaseModel;

class MorphTo extends \Illuminate\Database\Eloquent\Relations\MorphTo {
	use RelationPlus;
	/**
	 * Create a new morph to relationship instance.
	 *
	 * @param  BaseBuilder  $query
	 * @param  BaseModel  $parent
	 * @param  string  $foreignKey
	 * @param  string  $ownerKey
	 * @param  string  $type
	 * @param  string  $relation
	 * @return void
	 */
	public function __construct(BaseBuilder $query, BaseModel $parent, $foreignKey, $ownerKey, $type, $relation) {
		parent::__construct($query, $parent, $foreignKey, $ownerKey, $type, $relation);
		$this->macroNext($query);
	}

}
