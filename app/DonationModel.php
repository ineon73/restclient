<?php

namespace App;

use Corcel\Model\Post as Corsel;
use App\BxLog as Log;

class DonationModel extends Corsel
{
    public function get($data)
    {
        $log = new Log();
        for ($i = 0; $i < 3; $i++) {
            try {
                $a = Corsel::query()->type('leyka_donation');
                if (isset($data['modifiedTo'])) {
                    $a->whereDate('post_modified', '<=', $data['modifiedTo']);
                }
                if (isset($data['modifiedFrom'])) {
                    $a->whereDate('post_modified', '>=', $data['modifiedFrom']);
                }
                if (isset($data['gateway'])) {
                    $a->hasMeta(['leyka_gateway' => $data['gateway']]);
                }
                if (isset($data['method'])) {
                    $a->hasMeta(['leyka_payment_method' => $data['method']]);
                }
                if (isset($data['status'])) {
                    $a->where('post_status', '=', $data['status']);
                }
                if (isset($data['id'])) {
                    $a->whereIn('id', (array)$data['id']);
                }
                if (isset($data['limit'])) {
                    $a->limit($data['limit']);
                }
                if (isset($data['campaign_id'])) {
                    $a->hasMeta(['leyka_campaign_id' => $data['campaign_id']]);
                }

                $log->log_debug(json_encode($a->getQuery()));
                return $this->filterForData($a->orderBy('post_modified', 'asc')->get());
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

    public function filterForData($callback)
    {
        $relevant = [];
        foreach ($callback as $value) {
            $relevant[$value->ID]['email'] = "";
            $relevant[$value->ID]['email'] = (Array)$value->leyka_donor_email;
            $relevant[$value->ID]['full_name'] = "";
            $relevant[$value->ID]['full_name'] = (String)$value->leyka_donor_name;
            $relevant[$value->ID]['status'] = "";
            $relevant[$value->ID]['status'] = (String)$value->post_status;
            $relevant[$value->ID]['campaign_id'] = "";
            $relevant[$value->ID]['campaign_id'] = (Integer)$value->leyka_campaign_id;
            $relevant[$value->ID]['site_id'] = "";
            $relevant[$value->ID]['site_id'] = (String)$value->slug;
            $relevant[$value->ID]['acquirer_code'] = "";
            $relevant[$value->ID]['acquirer_code'] = (String)$value->leyka_gateway;
            $relevant[$value->ID]['payment_method'] = "";
            $relevant[$value->ID]['payment_method'] = (String)$value->leyka_payment_method;
            $relevant[$value->ID]['payment_type'] = "";
            $relevant[$value->ID]['payment_type'] = (String)$value->leyka_payment_type;
            $relevant[$value->ID]['currency_code'] = "";
            $relevant[$value->ID]['currency_code'] = (String)$value->leyka_donation_currency;
            $relevant[$value->ID]['summa_rur_gross'] = "";
            $relevant[$value->ID]['summa_rur_gross'] = (Double)$value->leyka_donation_amount;
            $relevant[$value->ID]['summa_rur_net'] = "";
            $relevant[$value->ID]['summa_rur_net'] = (Double)$value->leyka_donation_amount_total;
            $relevant[$value->ID]['summa_cur_gross'] = "";
            $relevant[$value->ID]['summa_cur_gross'] = (Double)$value->leyka_main_curr_amount;
            $relevant[$value->ID]['recurring'] = "";
            if ($value->leyka_payment_type == 'rebill') {
                $relevant[$value->ID]['recurring'] = true;
            }
            $relevant[$value->ID]['recurring_id'] = "";
            $relevant[$value->ID]['recurring_id'] = (String)$value->_cp_recurring_id;
            $relevant[$value->ID]['subscribe'] = "";
            $relevant[$value->ID]['subscribe'] = $value->leyka_donor_subscribed;
            $relevant[$value->ID]['bin'] = "";
            $relevant[$value->ID]['CardExpDate'] = "";
            $relevant[$value->ID]['acquirer_id'] = "";
            $relevant[$value->ID]['cardholder'] = "";
            $gateway = @unserialize($value->leyka_gateway_response);
            $payment_log = @unserialize($value->_paypal_payment_log);
            if (is_array($gateway) and isset($gateway['InvId'])) {
                $relevant[$value->ID]['acquirer_id'] = $gateway['InvId'];
            }
            if (is_array($gateway) and isset($gateway['Reason'])) {
                $relevant[$value->ID]['Reason'] = $gateway['Reason'];
            }
            if (is_array($gateway) and isset($gateway['CardLastFour'])) {
                $relevant[$value->ID]['bin'] = (String)$gateway['CardLastFour'];
                $relevant[$value->ID]['CardExpDate'] = (String)$gateway['CardExpDate'];
                $relevant[$value->ID]['acquirer_id'] = (String)$gateway['TransactionId'];
                $relevant[$value->ID]['cardholder'] = (String)$gateway['Name'];
            }
            if (isset($payment_log['1'])) {
                $relevant[$value->ID]['cardholder'] = $payment_log['1']['result']['FIRSTNAME'] . ' ' . $payment_log['1']['result']['LASTNAME'];
            }
            //if (isset($value->meta->_paypal_sale_id)) $relevant[$value->ID]['acquirer_id'] = $value->meta->_paypal_sale_id;
            /*if (isset($gateway['_expiryYear'])) {
                $relevant[$value->ID]['CardExpDate'] = (String)$gateway['_expiryYear'] . "/" . (String)substr($gateway['_expiryMonth'], 1, 2);
                $relevant[$value->ID]['bin'] = (String)$gateway['_last4'];
            }*/
            //$gateway=$this->fixObject($gateway);
            //$gateway= $this->casttoclass('YandexCheckout\Request\Payments\PaymentResponse',$gateway);
            //dd($gateway);
            //$relevant[$value->ID]['all'] = $value->toArray();
            $relevant[$value->ID]['post_date'] = "";
            $relevant[$value->ID]['post_date'] = $value->post_date;
            $relevant[$value->ID]['post_date_gmt'] = "";
            $relevant[$value->ID]['post_date_gmt'] = $value->post_date_gmt;
            $relevant[$value->ID]['post_modified'] = "";
            $relevant[$value->ID]['post_modified'] = $value->post_modified;
            $relevant[$value->ID]['post_modified_gmt'] = "";
            $relevant[$value->ID]['post_modified_gmt'] = $value->post_modified_gmt;
            $relevant[$value->ID]['comment'] = "";
            $relevant[$value->ID]['comment'] = (String)$value->title;
            $relevant[$value->ID]['raw'] = "";
            $relevant[$value->ID]['raw'] = $value->toJson(JSON_UNESCAPED_UNICODE);
            $debug = json_encode($relevant[$value->ID]['all']);
            $log = new Log();
            $log->log_debug($debug);
        }

        return $relevant;
    }

    function casttoclass($class, $object)
    {
        return unserialize(preg_replace('/^O:\d+:"[^"]++"/', 'O:' . strlen($class) . ':"' . $class . '"', serialize($object)));
    }

    function fixObject(&$object)
    {
        if (!is_object($object) && gettype($object) == 'object')
            return ($object = unserialize(serialize($object)));
        return $object;
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
        'post_modified' => 'datetime',
        'post_modified_gmt' => 'datetime',
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
