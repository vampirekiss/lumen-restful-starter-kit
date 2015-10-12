<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    const EVENT_CREATING = 'creating';

    const EVENT_CREATED = 'created';

    const EVENT_UPDATING = 'updating';

    const EVENT_UPDATED = 'updated';

    const EVENT_DELETING = 'deleting';

    const EVENT_DELETED = 'deleted';

    /**
     * @var array
     */
    public static $handleableEvents =  [
        self::EVENT_CREATING, self::EVENT_CREATED,
        self::EVENT_UPDATING, self::EVENT_UPDATING,
        self::EVENT_DELETING, self::EVENT_DELETED
    ];

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
}