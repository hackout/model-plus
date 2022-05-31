<?php

namespace DennisLui\ModelPlus\Relations;

use DennisLui\ModelPlus\Traits\RelationPlus;
use \Illuminate\Database\Eloquent\Builder as BaseBuilder;
use \Illuminate\Database\Eloquent\Model as BaseModel;

class MorphMany extends \Illuminate\Database\Eloquent\Relations\MorphMany {
	use RelationPlus;
	/**
	 * Create a new morph one or many relationship instance.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @param  \Illuminate\Database\Eloquent\Model  $parent
	 * @param  string  $type
	 * @param  string  $id
	 * @param  string  $localKey
	 * @return void
	 */
	public function __construct(BaseBuilder $query, BaseModel $parent, $type, $id, $localKey) {

		parent::__construct($query, $parent, $type, $id, $localKey);
		$this->macroNext($query);
	}
}
