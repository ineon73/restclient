<?php

namespace App;


use Corcel\Model\Post as Corcel;
use DateTime;

class DataModel extends Corcel
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

}
