<?php

namespace App;

use Corcel\Model\Post as Corsel;

class CampaignModel extends Corsel
{
    protected $postType = 'leyka_campaign';

    public function get($data)
    {
        $a = Corsel::query();
        if (isset($data['modifiedTo'])) {
            $a->whereDate('post_modified', '<=', $data['modifiedTo']);
        }
        if (isset($data['modifiedFrom'])) {
            $a->whereDate('post_modified', '>=', $data['modifiedFrom']);
        }
        if (isset($data['limit'])) {
            $a->limit($data['limit']);
        }
        return $this->filterForData($a->get());
    }
    public function filterForData($callback) {
        $data = [];
        foreach ($callback as $value) {
            $data[$value->ID]['Id'] = $value->ID;
            $data[$value->ID]['name'] = $value->title;
        }
        return $data;
    }
}
