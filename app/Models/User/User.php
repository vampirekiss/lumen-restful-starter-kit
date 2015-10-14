<?php

namespace App\Models\User;

use App\Models\Model;

/**
 * Class User
 *
 * @method static \Illuminate\Database\Eloquent\Builder ofAccount()
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cellphone', 'password'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $hidden = ['password', 'salt'];

    /**
     * handle event
     *
     * @param string $event
     *
     * @return bool
     */
    protected function handleEvent($event)
    {
        if ($event == self::EVENT_CREATING) {
            $this->setAttribute('salt', $this->_makeSalt());
            $this->setAttribute('password', $this->_encryptPassword());
        } elseif ($event == self::EVENT_UPDATING) {
            if ($this->isDirty(['password'])) {
                $this->setAttribute('password', $this->_encryptPassword());
            }
        }

        return parent::handleEvent($event);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $account
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfAccount($query, $account)
    {
        return $query->where('account', '=', $account, 'or')
            ->where('cellphone', '=', $account, 'or')
            ->where('email', '=', $account, 'or');
    }

    /**
     * @param $password
     *
     * @return bool
     */
    public function passwordIsCorrected($password)
    {
        return $this->_encryptPassword($password) == $this->getAttribute('password');
    }

    /**
     * @return string
     */
    private function _makeSalt()
    {
        return substr(uniqid(), 0, 10);
    }

    /**
     * @param string|null $password
     *
     * @return string
     */
    private function _encryptPassword($password = null)
    {
        $password = $password ?: $this->getAttribute('password');
        $salt = $this->getAttribute('salt');

        return sha1($salt . $password . $salt);
    }

}