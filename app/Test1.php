<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test1 extends Model
{
    protected $connection = 'mysql1';
    protected $table = 'posts';

}
