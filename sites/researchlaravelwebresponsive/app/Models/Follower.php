<?php

namespace App\Models;
use Illuminate\Container\Container;

class Follower extends Base
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'followers';

    public $timestamps = false;
    
    
}
