<?php

namespace DennisLui\ModelPlus\Relations;
use \Illuminate\Database\Eloquent\Builder as BaseBuilder;
use \Illuminate\Database\Eloquent\Model as BaseModel;

class AttachOne extends MorphOne {

	/**
	 * Create a new has many relationship instance.
	 * @return void
	 */
	public function __construct(BaseBuilder $query, BaseModel $parent,$localName, $type, $id, $localKey) {
		$query->where(explode('.',$type)[0].'.name',$localName);
		parent::__construct($query, $parent, $type, $id, $localKey);
	}
}
