<?php

namespace App;

use Corcel\Model\Post as Corsel;

class DataBridge extends Corsel
{
    public function get($data)
    {
        for ($i = 0; $i < 3; $i++) {
            try {
                $a = Corsel::query();
                if (isset($data['modifiedTo'])) {
                    $a->whereDate('post_modified', '<=', $data['modifiedTo']);
                }
                if (isset($data['modifiedFrom'])) {
                    $a->whereDate('post_modified', '>=', $data['modifiedFrom']);
                }
                if (isset($data['gateway'])) {
                    $a->hasMeta(['leyka_gateway' => $data['gateway']]);
                }
                if (isset($data['status'])) {
                    $a->where('post_status', '=', $data['status']);
                }
                if (isset($data['id'])) {
                    foreach ($data['id'] as $id) {
                    $a->orwhere('id', '=', $id); }
                }
                return $this->filterForData($a->get());
            } catch
            (\PDOException $exception) {
                sleep(2);
                if ($i < 2) {
                    echo $exception;
                } else {
                    throw $exception;
                }
            }
        }
    }

    public
    function getSomeById($ids)
    {
        $data = [];
        foreach ($ids as $key => $id) {
            $data[$key] = Corsel::find($id);
        }
        return $data;
    }


    public function filterForData($callback)
    {
        $relevant = [];
        $i = 0;
        foreach ($callback as $value) {
            $relevant[$i]["slug"] = ($value->slug);
            $relevant[$i]['email'] = (Array)$value->leyka_donor_email;
            $relevant[$i]['full_name'] = (String)$value->leyka_donor_name;
            $relevant[$i]['status'] = (String)$value->post_status;
            $relevant[$i]['campaign_id'] = (Integer)$value->leyka_campaign_id;
            $relevant[$i]['site_id'] = (String)$value->slug;
            $relevant[$i]['acquirer_code'] = (String)$value->leyka_gateway;
            $relevant[$i]['currency_code'] = (String)$value->leyka_donation_currency;
            $relevant[$i]['summa_rur_gross'] = (Double)$value->leyka_donation_amount;
            $relevant[$i]['summa_rur_net'] = (Double)$value->leyka_donation_amount_total;
            $relevant[$i]['summa_cur_gross'] = (Double)$value->leyka_main_curr_amount;
            $relevant[$i]['recurring'] = (Bool)$value->_rebilling_is_active;
            $gateway = unserialize($value->leyka_gateway_response);
            $relevant[$i]['bin'] = (String)$gateway['CardLastFour'];
            $relevant[$i]['CardExpDate'] = (String)$gateway['CardExpDate'];
            $relevant[$i]['acquirer_id'] = (String)$gateway['TransactionId'];
            $i++;
        }
        return $relevant;
    }

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
        'meta_id' => 'integer',
        'post_id' => 'integer',
        'meta_key' => 'string',
        'meta_value' => 'string',
        'id' => 'integer',
        'date' => 'datetime',
        'date_gmt' => 'datetime',
        'modified' => 'datetime',
        'modified_gmt' => 'datetime',
        'slug' => 'string',
        'status' => 'string',
        'type' => 'string',
        'link' => 'string',
        'template' => 'integer',
        '_edit_last' => 'string',
        'leyka_donor_name' => 'string',
        'leyka_donor_email' => 'string',
        'leyka_payment_type' => 'string',
        '_edit_lock' => 'string',
        'leyka_donation_amount' => 'double',
        'leyka_payment_method' => 'string',
        'leyka_gateway' => 'string',
        'leyka_donation_amount_total' => 'double',
        'leyka_donation_currency' => 'string',
        'leyka_main_curr_amount' => 'double',
        'leyka_campaign_id' => 'integer',
        '_leyka_donor_email_date' => 'integer',
        '_leyka_managers_emails_date' => 'date',
        'leyka_recurrents_cancel_date' => 'date',
        '_leyka_donation_id_on_gateway_response' => 'integer',
    ];


}
