<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataBridge as Model;

class DataAccess extends Controller
{
    public function index($id)
    {
        $array = [48,45];
        echo "<hr>";
        $mod = new Model();
       var_dump($mod->get($array));

    }
}
