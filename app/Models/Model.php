<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Class Model
 *
 * @method static \App\Models\Model find()
 */
abstract class Model extends BaseModel
{
    const EVENT_CREATING = 'creating';

    const EVENT_CREATED = 'created';

    const EVENT_UPDATING = 'updating';

    const EVENT_UPDATED = 'updated';

    const EVENT_DELETING = 'deleting';

    const EVENT_DELETED = 'deleted';

    /**
     * The attributes that should be encode/decode with json
     *
     * @var array
     */
    protected $jsonFields = [];

    /**
     * The attributes that should be boolean
     *
     * @var array
     */
    protected $booleanFields = [];

    /**
     * @var array
     */
    public static $handleableEvents =  [
        self::EVENT_CREATING, self::EVENT_CREATED,
        self::EVENT_UPDATING, self::EVENT_UPDATING,
        self::EVENT_DELETING, self::EVENT_DELETED
    ];

    /**
     * get available fields
     *
     * @return array
     */
    public static function getAvailableFields()
    {
        static $fields = [];

        if (!isset($fields[get_called_class()])) {
            $fields[get_called_class()] = array_merge((new static)->fillable, ['id']);
        }

        return $fields[get_called_class()];
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        foreach (static::$handleableEvents as $event) {
            static::$event(function($model) use ($event) {
                /** @var Model $model */
                return $model->handleEvent($event);
            });
        }
    }


    /**
     * handle event
     *
     * @param string $event
     *
     * @return bool
     */
    protected function handleEvent($event)
    {
        return true;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if (in_array($key, $this->jsonFields)) {
            return json_decode($value, true);
        }
        return $value;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->jsonFields)) {
            $value = json_encode($value);
        }
        parent::setAttribute($key, $value);
    }

    /**
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        foreach ($this->jsonFields as $field) {
            $attributes[$field] = json_decode($attributes[$field], true);
        }
        foreach ($this->booleanFields as $field) {
            $attributes[$field] = boolval($attributes[$field]);
        }
        return $attributes;
    }
}