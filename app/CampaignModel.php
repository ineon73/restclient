<?php

namespace App;

use Corcel\Model\Post as Corsel;

class CampaignModel extends Corsel
{
    protected $postType = 'leyka_campaign'; //leyka_campaign';

    public function get($data)
    {
        $a = Corsel::query();   // type('leyka_campaign')->take(3)->get(); //::
        if (isset($data['modifiedTo'])) {
            $a->whereDate('post_modified', '<=', $data['modifiedTo']);
        }
        if (isset($data['modifiedFrom'])) {
            $a->whereDate('post_modified', '>=', $data['modifiedFrom']);
        }
        if (isset($data['limit'])) {
            $a->limit($data['limit']);
        }
        if (isset($data['limit'])) {
            $a->limit($data['limit']);
        }
        if (isset($data['post_status'])) {
            $a->where('post_status', $data['post_status']);
        }
        if (isset($data['id'])) {
            foreach ((array)$data['id'] as $id) {
                $a->orwhere('id', '=', $id);
            }
        }
        //dd($a);
        $a->where('post_type', 'leyka_campaign');
        return $this->filterForData($a->orderBy('post_modified', 'asc')->get());
    }

    public function filterForData($callback)
    {
        $data = [];
        foreach ($callback as $value) {
            $data[$value->ID]['Id'] = $value->ID;
            $data[$value->ID]['name'] = $value->title;
            $data[$value->ID]['payment_title'] = $value->payment_title;
            //echo 'value<pre>';print_r($value);echo '</pre>';
        }
        // dd($callback->toArray());
        return $data;
    }
}
