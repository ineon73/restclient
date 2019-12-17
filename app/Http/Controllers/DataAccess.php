<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use \Datetime;
use DateInterval;
use App\TaskParameters;
use App\DonationModel as PaymentDataSource;

use App\Tasks\LeykaToBx24Proto;


use App\Connectors\Bx24Webhook\Bx24WebhookConnector;


use \App\Processors\ConditionCyrLatinNameParser as  ConditionCyrLatinNameParser;
use \App\Processors\TwoNameFieldsParser as  TwoNameFieldsParser;
use App\DonationModel as Donation;
use App\CampaignModel as Campaign;
use App\Test1 as Test1;
use App\Test2 as Test2;

class DataAccess extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();
        if ($data != null) {
            if (isset($data['type']) and $data['type'] == 'c') {
                $mod = new Campaign();
                dump($mod->get($data));
            } else {
                $mod = new Donation();
                dump($mod->get($data));
            }
        } else echo "требуется request параметры. Для campaign \"?type=c\"";
    }

    /* public function db()
     {
         $array = ['mysql1', 'wordpress'];
 $data['conn'] = 'test';

         $test2 = new Test2();
         $test2->get($data);
     }*/
    public function testParser(Request $request)
    {

        $element = $request->all();

        $parser = new TwoNameFieldsParser(['INPUT_FIELD_CYRLATIN'=>'fullname','INPUT_FIELD_LATIN'=>'cardholder']);

        $output = $parser->process($element);

        dd($output);

    }
    public function testSomething(Request $request)
    {

        $element = $request->all();

        $value = $opt = [

            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,

            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,

            \PDO::ATTR_EMULATE_PREPARES => false,

        ]; //config('database.connections.mysql.charset');

        print_r($value);
        dd($value);


    }
}
