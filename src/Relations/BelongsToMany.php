<?php

namespace DennisLui\ModelPlus\Relations;
use DennisLui\ModelPlus\Traits\RelationPlus;
use \Illuminate\Database\Eloquent\Builder as BaseBuilder;
use \Illuminate\Database\Eloquent\Model as BaseModel;

class BelongsToMany extends \Illuminate\Database\Eloquent\Relations\BelongsToMany {
	use RelationPlus;

    /**
     * Create a new belongs to many relationship instance.
     *
     * @param  BaseBuilder  $query
     * @param  BaseModel  $parent
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relationName
     * @return void
     */
	public function __construct(BaseBuilder $query, BaseModel $parent, $table, $foreignPivotKey,
		$relatedPivotKey, $parentKey, $relatedKey, $relationName = null) {
		parent::__construct($query, $parent, $table, $foreignPivotKey,
			$relatedPivotKey, $parentKey, $relatedKey, $relationName);
		$this->macroNext($query);
	}

}
