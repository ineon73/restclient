<?php

namespace App;

use Corcel\Model\Post as Corsel;
use SebastianBergmann\CodeCoverage\Report\PHP;

class DataBridge extends Corsel
{
    public function get($data)
    {
        for ($i = 0; $i < 3; $i++) {
            try {
                $a = Corsel::query();
                if (!isset($data['id'])) {

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
                    $list = $a->get();
                    return $this->filterForDate($list);
                } else {
                    return $this->getSomeById($data);
                }
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
        return $this->filterForIds($data);
    }

    public function filterForIds($callback)
    {
        $relevant = [];
        foreach ($callback as $value => $item) {
            foreach ($item as $key) {
                $relevant['email'] = (Array)$key->leyka_donor_email;
                $relevant['full_name'] = (String)$key->leyka_donor_name;
                $relevant['status'] = (String)$key->post_status;
                $relevant['campaign_id'] = (Integer)$key->leyka_campaign_id;
                $relevant['site_id'] = (String)$key->slug;
                $relevant['acquirer_name'] = (String)$key->leyka_gateway;
                $relevant['currency_code'] = (String)$key->leyka_donation_currency;
                $relevant['summa_rur_gross'] = (Double)$key->leyka_donation_amount;
                $relevant['summa_rur_net'] = (Double)$key->leyka_donation_amount_total;
                $relevant['summa_cur_gross'] = (Double)$key->leyka_main_curr_amount;
                $relevant['recurring'] = (Bool)$key->leyka_payment_type;
                $gateway = unserialize($key->leyka_gateway_response);
                $relevant['bin'] = (String)$gateway['CardLastFour'];
                $relevant['CardExpDate'] = (String)$gateway['CardExpDate'];
                $relevant['acquirer_id'] = (String)$gateway['TransactionId'];
            }
        }
        return $relevant;
    }

    public function filterForDate($callback)
    {
        $relevant = [];
        foreach ($callback as $value => $item) {
            $relevant['email'] = (Array)$item->leyka_donor_email;
            $relevant['full_name'] = (String)$item->leyka_donor_name;
            $relevant['status'] = (String)$item->post_status;
            $relevant['campaign_id'] = (Integer)$item->leyka_campaign_id;
            $relevant['site_id'] = (String)$item->slug;
            $relevant['acquirer_name'] = (String)$item->leyka_gateway;
            $relevant['currency_code'] = (String)$item->leyka_donation_currency;
            $relevant['summa_rur_gross'] = (Double)$item->leyka_donation_amount;
            $relevant['summa_rur_net'] = (Double)$item->leyka_donation_amount_total;
            $relevant['summa_cur_gross'] = (Double)$item->leyka_main_curr_amount;
            $relevant['recurring'] = (Bool)$item->leyka_payment_type;
            $gateway = unserialize($item->leyka_gateway_response);
            $relevant['bin'] = (String)$gateway['CardLastFour'];
            $relevant['CardExpDate'] = (String)$gateway['CardExpDate'];
            $relevant['acquirer_id'] = (String)$gateway['TransactionId'];
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
