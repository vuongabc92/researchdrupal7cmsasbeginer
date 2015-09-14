<?php

namespace App\Models;

class Comment extends Base
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'comments';

    public $timestamps = false;

    /**
     * Get user
     *
     * @return App\Models\User
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
