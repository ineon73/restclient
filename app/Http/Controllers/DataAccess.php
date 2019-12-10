<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataBridge as Donation;
use App\CampaignModel as Campaign;
use App\Test1 as Test1;
use App\Test2 as Test2;

class DataAccess extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();
        if ($data != null) {
            if ($data['type'] == 'd') {
                $mod = new Donation();
                dump($mod->get($data));
            } else {
                $mod = new Campaign();
                dump($mod->get($data));
            }
        } else echo "требуется request параметры. Для donation \"?type=d\"";
    }

    /* public function db()
     {
         $array = ['mysql1', 'wordpress'];
 $data['conn'] = 'test';

         $test2 = new Test2();
         $test2->get($data);
     }*/
}
