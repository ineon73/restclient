<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataBridge as Model;
use DateTime;

class DataAccess extends Controller
{
    public function index($id)
    {
        $a = Model::get($id);
        var_dump($a);
        echo "<hr>";
        if ($a->post_date instanceof DateTime) {
            echo "это класс";
        } else {
            echo "это не класс";
        }

    }
}
