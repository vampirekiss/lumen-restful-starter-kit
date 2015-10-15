<?php

namespace App\Models;


/**
 * Class Client
 *
 * @method static \Illuminate\Database\Eloquent\Builder enabled()
 */
class Client extends Model
{

    const TYPE_USER = 'User';

    const TYPE_ADMIN = 'Admin';

    const TYPE_BACKEND_APP = 'BackendApp';

    /**
     * @var array
     */
    public static $allTypes = [
        self::TYPE_USER, self::TYPE_ADMIN, self::TYPE_BACKEND_APP
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'clients';

    /**
     * @var array
     */
    protected $fillable = ['name', 'type', 'scopes'];

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

    /**
     * @param \App\Models\Token $token
     *
     * @return bool
     */
    public function tokenIsExpired(Token $token)
    {
        $expires_at = $token->getAttribute('expires_at');
        if ($expires_at <= 0) {
            return false;
        }

        return time() <= $expires_at;
    }

}