<?php

namespace DennisLui\ModelPlus\Eloquent;

use ReflectionClass;
use ReflectionMethod;
use \Illuminate\Database\Eloquent\Builder as BaseBuilder;

/**
 * @property-read HigherOrderBuilderProxy $orWhere
 *
 * @mixin \Illuminate\Database\Query\Builder
 */
class Builder extends BaseBuilder {

	/**
	 * the relation keyname from this model
	 * @var array
	 */
	protected $relationAttributes = [];

	protected $afterActionBuilder = [];

	/**
	 * make the attributes
	 * @return void
	 */
	protected function makeRelationAttributes() {
		$model = new ReflectionClass($this->getModel());
		$methods = $model->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $r) {
			if ($r->class == get_class($this->getModel()) && !$r->getParameters()) {
				if ($this->getModel()->{$r->getName()}() instanceof \DennisLui\ModelPlus\Relations\HasMany ||
					$this->getModel()->{$r->getName()}() instanceof \DennisLui\ModelPlus\Relations\HasOne) {
					$this->relationAttributes[$r->getName()] = $this->getModel()->{$r->getName()}();
				}
			}
		}
	}

	/**
	 * Save a new model and return the instance.
	 *
	 * @param  array  $attributes
	 * @return \Illuminate\Database\Eloquent\Model|$this
	 */
	public function create(array $attributes = []) {
		$this->makeRelationAttributes();
		if ($attributes) {
			$attributesKey = array_keys($attributes);
			$isMultiple = is_numeric($attributesKey[0]);
			if ($isMultiple) {
				$multiples = [];
				foreach ($attributes as $attribute) {
					$multiples[] = tap($this->newModelInstance($attribute), function ($instance) {
						$instance->save();
					});
				}
				return collection($multiples);
			}
			$attribute = [];
			foreach ($attributes as $key => $rs) {
				if (array_key_exists($key, $this->relationAttributes)) {
					$this->afterActionBuilder[$key] = $rs;
				} else {
					$attribute[$key] = $rs;
				}
			}
			$modelCollection = tap($this->newModelInstance($attribute), function ($instance) {
				$instance->save();
			});
			if ($this->afterActionBuilder) {
				foreach ($this->afterActionBuilder as $key => $attribute) {
					if (array_key_exists($key, $this->relationAttributes)) {
						$primaryKeyName = $this->relationAttributes[$key]->getForeignKeyName();
						$primaryKey = $modelCollection->{$this->relationAttributes[$key]->getLocalKeyName()};
						$relationModel = $this->relationAttributes[$key]->getModel();
						if (is_numeric(array_keys($attribute)[0])) {
							foreach ($attribute as $attr) {
								$attr[$primaryKeyName] = $primaryKey;
								$relationModel->create($attribute);
							}
						} else {
							$attribute[$primaryKeyName] = $primaryKey;
							$relationModel->create($attribute);
						}
					}
				}
			}
			return $modelCollection;
		}
		return tap($this->newModelInstance($attributes), function ($instance) {
			$instance->save();
		});
	}

}
