<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataBridge as Model;

class DataAccess extends Controller
{
    public function index($id)
    {
        $array[] = 48;
        echo "<hr>";
        $mod = new Model();
       dd($mod->get($array));

    }
}
