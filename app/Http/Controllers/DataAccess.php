<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataBridge as Model;
use App\Test1 as Test1;
use App\Test2 as Test2;

class DataAccess extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();
        echo "<hr>";
        $mod = new Model();
        //dd($mod->get($array));
        if ($data != null) {
            dump($mod->get($data));
        } else echo "требуется request параметры";
    }

    public function db()
    {
        $array = ['mysql1', 'wordpress'];
$data['conn'] = 'test';

        $test2 = new Test2();
        $test2->get($data);
    }
}
