<?php

namespace Thawani;

use Thawani\RestAPI;
use Thawani\WC_Gateway_ThawaniGateway;

class ThawaniAjax extends WC_Gateway_ThawaniGateway
{


    private $ajax_id  = 'thawani_gw';

    private $api = null;
    protected $is_save_cards = null;
    /**
     * initialize wordpress do_action hook 
     * 
     */
    public function __construct()
    {
        $this->init();
        $secret_key = $this->get_option('secret_key');
        $publishable_key = $this->get_option('publishable_key');
        $environment = $this->get_option('environment');
        // disabled for now maybe activate this later 
        // $this->is_save_cards = $this->get_option('save_cards');
        $this->api = new RestAPI($secret_key, $publishable_key, $environment);
        add_action('wp_ajax_' . $this->ajax_id . '_get_all_sessions', [$this, 'get_all_sessions']);
        add_action('wp_ajax_' . $this->ajax_id . '_get_all_customers', [$this, 'get_all_customers']);
        add_action('wp_ajax_' . $this->ajax_id . '_get_customer_payment', [$this, 'get_customer_payment']);
        add_action('wp_ajax_' . $this->ajax_id . '_get_checkout', [$this, 'get_checkout_url']);
    }


    public function get_all_customers()
    {
        if (!isset($_POST['skip'])) {
            $response = $this->api->get_all_customers();

            wp_send_json($response['body'], 200);
            wp_die();
        } else {
            $response = $this->api->get_all_customers([
                'skip' => $_POST['skip'],
                'limit' => $_POST['limit']
            ]);

            wp_send_json($response['body'], 200);
            wp_die();
        }
    }

    public function get_all_sessions()
    {

        if (!isset($_POST['skip'])) {
            $response = $this->api->get_all_sessions();
            wp_send_json($response['body'], 200);
            wp_die();
        } else {
            $response = $this->api->get_all_sessions([
                'skip' => $_POST['skip'],
                'limit' => $_POST['limit']
            ]);

            wp_send_json($response['body'], 200);
            wp_die();
        }
        //
    }

    public function get_checkout_url()
    {
        if (isset($_POST['order_id'])) {
            // now get the products  
            // $products = $this->prepare_products($_POST['order_id']);
            $order  = wc_get_order($_POST['order_id']);
            // un-comment this when the feature of the customer saving account is active
            // if ($order->get_user_ID() != 0 && ($this->is_save_cards != 'no')) {
            //     $customer_id = $this->get_customer_key($order->get_user_ID());
            //     $payload  = $this->payload($order, $customer_id);
            // } else
            $payload = $this->payload($order);

            $response  = $this->api->create_session($payload);
            $parsed_response  = json_decode($response['body']);
            if ($parsed_response->success) {

                $this->set_session_token($parsed_response->data->session_id, $order->get_id());
                $order->update_status('wc-pending', __('waiting to complete the payment by thawani', 'thawani'));
                $checkout_link = $this->api->get_redirect_uri($parsed_response->data->session_id);
                wp_send_json([
                    'checkout' => $checkout_link,
                    'session_id' => $parsed_response->data->session_id
                ], 200);
            }

            wp_send_json('something went wrong', 200);
        }
    }

    protected function get_customer_key($customer_id)
    {
        $customer = $this->api->get_customer_instance();

        $customer_meta  = $customer->get_customer_meta();
        if ($customer_meta == null) {

            $get_response = $customer->create($customer_id);
            $response  = json_decode($get_response);

            $customer->set_customer_meta($response->id, $customer_id);

            return $response->id;
        }

        return  $customer_meta;
    }

    public function get_customer_payment()
    {
        if (isset($_POST['customer_token'])) {
            $response  = $this->api->get_payment($_POST['customer_token']);

            wp_send_json($response['body'], 200);
            wp_die();
        }
        wp_send_json('hello', 200);
    }
}
