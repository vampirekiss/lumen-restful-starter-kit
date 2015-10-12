<?php

namespace App\Models\User;

use App\Models\Model;

class User extends Model
{
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
            $this->setAttribute('salt', $salt = $this->_makeSalt());
            $this->setAttribute('password', $this->_makePassword($salt));
        }

        return parent::handleEvent($event);
    }

    /**
     * @return string
     */
    private function _makeSalt()
    {
        return substr(uniqid(), 0, 10);
    }

    /**
     * @param $salt
     *
     * @return string
     */
    private function _makePassword($salt)
    {
        return sha1($salt . $this->getAttribute('password') . $salt);
    }

}