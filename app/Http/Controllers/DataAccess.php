<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataBridge as Model;

class DataAccess extends Controller
{
    public function index(Request $request)
    {
      $data = $request->all();
        echo "<hr>";
        $mod = new Model();
        //dd($mod->get($array));
        dump($mod->get($data));
    }
}
