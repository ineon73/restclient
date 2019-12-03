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
                    return $a->get();
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
        $fields = [
            'email' => 'Array',
            'phone' => 'Array',
            'bin' => 'String',
            'ewallet' => 'String',
            'campaign_id' => 'integer',
            'campaign_name' => 'String',
            'acquirer_id' => 'String',
            'site_id' => 'String',
            'full_name' => 'String',
            'acquirer_name' => 'String',
            'comment' => 'String',
            'summa_cur_net' => 'double',
            'summa_cur_gross' => 'double',
            'currency_code' => 'String',
            'summa_rur_gross' => 'double',
            'summa_rur_net' => 'double',
            'recurring' => 'bool',
            'recurring_id' => 'String',
            'recurring_first' => 'integer',
            'utm_campaign' => 'String',
            'utm_source' => 'String',
            'status' => 'String',
            'address' => 'String',
            'signout' => 'bool',
            'raw json' => 'json',
            'reject_reason' => 'String',
            'warning_message' => 'String',
            'upload_id' => 'String',
        ];

echo "<hr>";

    echo $relevant['email']  = $callback['id']->leyka_donor_email;
     echo   $relevant['full_name']  = $callback['id']->leyka_donor_name;
     echo   $relevant['recurring_id'] = $callback['id']->cp_recurring_id;
        echo   $relevant['status'] = $callback['id']->post_status;
        echo   $relevant['campaign_id'] = $callback['id']->leyka_campaign_id;
        echo   $relevant['currency_code '] = $callback['id']->leyka_donation_currency;
        echo   $relevant['recurring_id'] = $callback['id']->cp_recurring_id;
        echo   $relevant['recurring_id'] = $callback['id']->cp_recurring_id;
        echo   $relevant['recurring_id'] = $callback['id']->cp_recurring_id;
        echo   $relevant['recurring_id'] = $callback['id']->cp_recurring_id;
        echo   $relevant['recurring_id'] = $callback['id']->cp_recurring_id;
        echo   $relevant['recurring_id'] = $callback['id']->cp_recurring_id;
        echo   $relevant['recurring_id'] = $callback['id']->cp_recurring_id;
        echo   $relevant['recurring_id'] = $callback['id']->cp_recurring_id;
        echo   $relevant['recurring_id'] = $callback['id']->cp_recurring_id;

    }



    protected  $casts = [
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
