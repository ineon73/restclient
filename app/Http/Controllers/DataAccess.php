<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataModel as Corcel;


class DataAccess extends Controller
{
    public function get($id)
    {
        Corcel::connect($id);

}

}
