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

        for ($i = 0; $i < 3; $i++) {
            echo $i . PHP_EOL;
            try {
                if (!(DB::connection()->getDatabaseName())) {
                    sleep(2);
                    DB::reconnect();
                } else {
                    var_dump(Model::get($id));
                    break;
                }
            } catch
            (\PDOException $exception) {
                if ($i == 2) {
                    echo $exception;
                }
            }
        }

    }
}
