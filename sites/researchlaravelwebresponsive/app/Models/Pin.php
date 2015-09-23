<?php

namespace App\Models;
use Illuminate\Container\Container;

class Pin extends Base
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pin';

    public $timestamps = false;

    /**
     * Check the current user in system pin the product or not
     *
     * @return boolean
     */
    public function isPinned() {

        $auth = Container::getInstance()->make('Illuminate\Contracts\Auth\Guard');
        if ($auth->guest()) {
            return false;
        }
        $userId   = $auth->user()->id;
        $pinUsers = json_decode($this->user_id, true);

        return isset($pinUsers[$userId]);
    }
}
