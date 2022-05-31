<?php

namespace DennisLui\ModelPlus\Relations;

use DennisLui\ModelPlus\Traits\RelationPlus;
use \Illuminate\Database\Eloquent\Builder as BaseBuilder;
use \Illuminate\Database\Eloquent\Model as BaseModel;

class MorphToMany extends \Illuminate\Database\Eloquent\Relations\MorphToMany {
	use RelationPlus;
	/**
	 * Create a new morph to many relationship instance.
	 *
	 * @param  BaseBuilder  $query
	 * @param  BaseModel  $parent
	 * @param  string  $name
	 * @param  string  $table
	 * @param  string  $foreignPivotKey
	 * @param  string  $relatedPivotKey
	 * @param  string  $parentKey
	 * @param  string  $relatedKey
	 * @param  string  $relationName
	 * @param  bool  $inverse
	 * @return void
	 */
	public function __construct(BaseBuilder $query, BaseModel $parent, $name, $table, $foreignPivotKey,
		$relatedPivotKey, $parentKey, $relatedKey, $relationName = null, $inverse = false) {

		parent::__construct($query, $parent, $name, $table, $foreignPivotKey,
			$relatedPivotKey, $parentKey, $relatedKey, $relationName, $inverse);
		$this->macroNext($query);
	}
}
