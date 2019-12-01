<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataBridge as Model;

class DataAccess extends Controller
{
    public function index($id, $id1 = null, $id2 = null)
    {
        $date[] = $id;
        $id1?:$date[] = $id1;
        $id2?:$date[] = $id2;
        echo "<hr>";
        $mod = new Model();
        //dd($mod->get($array));
        $test = '2019/11/15';
        var_dump($mod->get($date));
    }
}
