<?php

namespace App\Models;


/**
 * Class Token
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Model ofToken($token)
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
     * generate unique token
     *
     * @return string
     */
    public static function uniqueToken()
    {
        return sha1(uniqid() . mt_rand(0, 10000) . strval(microtime(true)));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $token
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfToken($query, $token)
    {
        return $query->where('token', '=', $token);
    }


    /**
     * @return bool
     */
    public function isExpired()
    {
        $expires_at = $this->getAttribute('expires_at');
        return $expires_at - time() <= 0;
    }
}