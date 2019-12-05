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
                  return  $a->get();
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
        return $this->filterData($data);
    }

    public function filterData($callback)
    {
        $relevant['email'] = (Array) $callback['id']->leyka_donor_email;
        $relevant['full_name'] = (String) $callback['id']->leyka_donor_name;
        $relevant['status'] = (String) $callback['id']->post_status;
        $relevant['campaign_id'] = (Integer) $callback['id']->leyka_campaign_id;
        $relevant['site_id'] = (String) $callback['id']->slug;
        $relevant['acquirer_name'] = (String) $callback['id']->leyka_gateway;
        $relevant['currency_code'] = (String) $callback['id']->leyka_donation_currency;
        $relevant['summa_rur_gross'] = (Double) $callback['id']->leyka_donation_amount;
        $relevant['summa_rur_net'] = (Double) $callback['id']->leyka_donation_amount_total;
        $relevant['summa_cur_gross'] = (Double) $callback['id']->leyka_main_curr_amount;
        $relevant['recurring'] = (Bool) $callback['id']->leyka_payment_type;
        $gateway = unserialize($callback['id']->leyka_gateway_response);
        $relevant['bin'] = (String) $gateway['CardLastFour'];
        $relevant['CardExpDate'] = (String) $gateway['CardExpDate'];
        $relevant['acquirer_id'] = (String) $gateway['TransactionId'];
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
