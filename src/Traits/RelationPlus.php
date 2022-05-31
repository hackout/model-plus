<?php

namespace DennisLui\ModelPlus\Traits;

use ReflectionClass;
use ReflectionMethod;
use \Illuminate\Database\Eloquent\Builder as BaseBuilder;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Database\Eloquent\Relations\Relation as BaseRelation;

trait RelationPlus
{
    /**
     * Contact macro functions
     * 
     * @param  BaseBuilder $query
     * @return void
     */
    protected function macroNext(BaseBuilder $query)
    {
        $model = new ReflectionClass($query->getModel());
        $methods = $model->getMethods(ReflectionMethod::IS_PUBLIC);
        $macros = [];
        foreach($methods as $r)
        {
            if($r->class == get_class($query->getModel()) && !$r->getParameters())
            {
                $macros[] = $r;
            }
        }
        foreach($macros as $macro)
        {
            self::macro($macro->getName(),function() use($query,$macro){
                $args = func_get_args();
                $type = $query->getModel()->{$macro->getName()}();
                $macroKey = $this->pluckParentId($query,$type);
                if(!$macroKey) return ;
                if($args) return $this->nextRelationCollection($type,$macroKey);
                return $this->nextRelationQuery($type,$macroKey);
            });
        }
    }

    /**
     * Return parent id
     * 
     * @param  BaseBuilder $query
     * @param  baseRelation $type
     * @return array
     */
    protected function pluckParentId(BaseBuilder $query,BaseRelation $type) : array
    {
        return $query->get()->pluck($type->getRelated()->getKeyName())->toArray() ?: [];
    }

    /**
     * Return new builder query.
     * 
     * @param  BaseRelation $type         
     * @param  array        $macroKey
     * @return BaseBuilder
     */
    protected function nextRelationQuery(BaseRelation $type,(array) $macroKey) : BaseBuilder
    {
        return $type->getRelated()->whereIn($type->getRelated()->getKeyName(),$macroKey);
    }

    /**
     * Return related collection.
     * 
     * @param  BaseRelation $type
     * @param  array  $macroKey
     * @return Collection
     */
    protected function nextRelationCollection(BaseRelation $type,(array) $macroKey) : Collection
    {
        return $this->nextRelationQuery($type,$macroKey)->get();
    }
}
