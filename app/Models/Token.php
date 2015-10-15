<?php

namespace App\Models;


/**
 * Class Token
 *
 * @method static \Illuminate\Database\Eloquent\Builder ofToken()
 */
class Token extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tokens';

    /**
     * @var array
     */
    protected $fillable = ['client_id', 'uid', 'value', 'expires_at'];

    /**
     * generate unique token
     *
     * @return string
     */
    public static function uniqueToken()
    {
        return md5(uniqid() . mt_rand(0, 10000)) . md5(strval(microtime(true)));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $token
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfToken($query, $token)
    {
        return $query->where('value', '=', $token);
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        $expires_at = $this->getAttribute('expires_at');

        if ($expires_at <= 0) {
            return false;
        }

        return $expires_at - time() <= 0;
    }
}