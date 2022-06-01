<?php namespace DennisLui\ModelPlus\Models;

use DennisLui\ModelPlus\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Factories\HasFactory;
use \Illuminate\Support\Arr;
use \Illuminate\Support\Facades\Config;
use \Illuminate\Support\Collection;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships as DeepRelations;

class Model extends \Illuminate\Database\Eloquent\Model {
	use HasFactory, DeepRelations, \DennisLui\ModelPlus\Traits\Purgeable, \DennisLui\ModelPlus\Traits\HasRelationships;

	/**
	 * The model connects another host
	 * @var string
	 */
	protected $cloudHost = "localhost";

	/**
	 * The isCloud is a switch off / on for other host connect.
	 * @var boolean
	 */
	protected $isCloud = false;

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $fillable = [];

	protected $appends = [];

	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
	];

	protected $dateFormat = 'Y-m-d H:i:s';

	/**
	 * Concat saving hooks
	 *
	 * @method beforeSave()
	 * @method beforeCreate()
	 * @method beforeUpdate()
	 * @method beforeDelete()
	 * @method afterSave()
	 * @method afterCreate()
	 * @method afterUpdate()
	 * @method afterDelete()
	 * 
	 * @return void
	 */
	public static function boot() {
		parent::boot();
		$myself = get_called_class();
		$hooks = array('before' => 'ing', 'after' => 'ed');
		$radicals = array('sav', 'creat', 'updat', 'delet');
		foreach ($radicals as $rad) {
			foreach ($hooks as $hook => $event) {
				$method = $hook . ucfirst($rad) . 'e';
				if (method_exists($myself, $method)) {
					$eventMethod = $rad . $event;
					self::$eventMethod(function ($model) use ($method) {
						return $model->$method($model);
					});
				}
			}
		}
	}

	/**
	 * 翻转模型
	 * @param  string|null $keyName  
	 * @param  string|null $otherKey 
	 * @param  string|null $secondKey
	 * @return Collection
	 */
	public function reverse_keys($keyName = null,$otherKey = null,$secondKey = null) : Collection
	{
		return Collection::wrap($this->toArray())->reverse_keys($keyName,$otherKey,$secondKey);
	}

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return App\Libs\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }
}
