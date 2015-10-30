<?php

namespace App\Models;


/**
 * Class Client
 *
 * @method static \Illuminate\Database\Eloquent\Builder enabled($id)
 */
class Client extends Model
{

    const DEFAULT_EXPIRES_IN = 7200;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'clients';

    /**
     * @var array
     */
    protected $jsonFields = ['scopes'];

    /**
     * @var array
     */
    protected $booleanFields = ['disabled'];

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled($query, $id = null)
    {
        if ($id) {
            $query->where('id', '=', $id);
        }

        return $query->where('disabled', '=', false);
    }

    /**
     * @return int
     */
    public function getTokenExpiresAt()
    {
        $expires_in = $this->getAttribute('expires_in');
        if ($expires_in <= 0) {
            return 0;
        }

        return time() + $expires_in;
    }

}