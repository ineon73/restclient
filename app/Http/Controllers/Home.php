<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Leyka as Leyka;
class Home extends Controller
{
    public function index() {

        $posts = Leyka::find(19);
        $ho['ty'] = $posts->meta;
        $posts->saveMeta([
            'is_finished' => '0'
        ]);
        return view('welcome', $ho);
    }
}
