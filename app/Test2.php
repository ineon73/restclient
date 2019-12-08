<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test2 extends Model
{
    protected $connection = 'wordpress';
    protected $table = 'posts';

    public function get($id) {
        self::on($id['conn'])->insert(['post_title'=>'test3']);
    }
}
