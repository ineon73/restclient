<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataBridge as Model;
use DateTime;
use Illuminate\Support\Facades\DB;


class DataAccess extends Controller
{
    public function index($id)
    {
        echo "<hr>";
        $mod = new Model();
        var_dump($mod->get($id));

    }
}
