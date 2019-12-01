<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataBridge as Model;

class DataAccess extends Controller
{
    public function index($id, $id1 = null, $id2 = null)
    {
        $array[] = $id;
        $array[] = $id1;
        $array[] = $id2;
        echo "<hr>";
        $mod = new Model();
        //dd($mod->get($array));
        $test = '29.11.2019';
        dd($mod->getSomeById($array));
    }
}
