<?php namespace DennisLui\ModelPlus\Models;

use DennisLui\ModelPlus\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Factories\HasFactory;
use \Illuminate\Support\Arr;
use \Illuminate\Support\Facades\Config;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships as DeepRelations;

class Model extends \Illuminate\Database\Eloquent\Model {
	use HasFactory, DeepRelations, Traits\Purgeable;


	protected $couldHost = '';

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

	protected function couldHostInfo()
	{
		return Config::get("modelplus.hosts")[$this->cloudHost];
	}

	public function getCouldHost()
	{
		return $this->cloudHostInfo();
	}


    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return App\Libs\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
    	$this->cloudHost = Config::get('modelplus.host','local');
        return new Builder($query);
    }
}
