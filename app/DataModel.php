<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Corcel\Model\Post as Corcel;
use DateTime;

interface DataAccess
{
    public function get($id);
}

class DataModel extends Corcel implements DataAccess
{

    protected $casts = [
        'post_author' => 'integer',
        'post_content' => 'string',
        'post_title' => 'string',
        'post_excerpt' => 'string',
        'post_status' => 'string',
        'comment_status' => 'string',
        'ping_status' => 'string',
        'post_password' => 'string',
        'post_name' => 'string',
        'to_ping' => 'string',
        'pinged' => 'string',
        'post_content_filtered' => 'string',
        'post_parent' => 'integer',
        'guid' => 'string',
        'menu_order' => 'integer',
        'post_type' => 'string',
        'post_mime_type' => 'string',
        'comment_count' => 'integer',
    ];

    public function get($id)
    {
        return Corcel::find($id);
    }

    public function getPostDateAttribute($date)
    {
        $date = new DateTime($date);
        return $date->format('d.m.Y');
    }

    public function getPostDateGmtAttribute($date)
    {
        $date = new DateTime($date);
        return $date->format('d.m.Y');
    }

    public function getPostModifiedAttribute($date)
    {
        $date = new DateTime($date);
        return $date->format('d.m.Y');
    }

    public function getPostModifiedGmtAttribute($date)
    {
        $date = new DateTime($date);
        return $date->format('d.m.Y');
    }

    public static function connect($config)
    {

        $is_connect = false;
        $i = 0;
        try {
            do {
                $is_connect = DB::connection()->getPdo();
                $i++;
                sleep(3);
            } while ($i < 3 xor $is_connect == true);
        } catch (\Exception $e) {
            echo "connect error ";
        }
        if ($is_connect == true) {
            echo "connect";
        }
    }


}
