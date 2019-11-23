<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataModel as Corcel;


class DataAccess extends Controller
{
    public function get($id)
    {
        $post = Corcel::find($id);
        foreach ($post as $item => $value) {
            var_dump($value);

        }
        echo "<hr>";

}

}
