<?php

/**
 * Plugin Name: Thawani Gateway Woocommerce
 * Plugin URI: https://Alrisi.net
 * Author: Muhannad Alrisi
 * Author URI: https://alrisi.net
 * Description: Thawani Payments Gateway for Woocommerce.
 * Version: 1.0.5
 * License: GPL2
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: thawani
 *
 *
 *@package WooCommerce\Thawani
 */

require_once plugin_dir_path(__FILE__) . '/vendor/autoload.php';

use \Thawani\WC_Gateway_ThawaniGateway;
use \Thawani\AdminDashboard;
use Thawani\RestAPI;
use \Thawani\ThawaniAjax;

if (!defined('ABSPATH'))
    exit;

// define the path of the plugin 
define('THAWANI_GW_DIR', plugin_dir_path(__FILE__));

/**
 * Check if the WooCommerce plugin is active
 */
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
    return;

function add_thawani_gateway($gateways)
{
    $gateways[] = '\Thawani\WC_Gateway_ThawaniGateway';
    return $gateways;
}

add_filter('woocommerce_payment_gateways', 'add_thawani_gateway');

add_action('plugins_loaded', 'init_WC_Gateway_ThawaniGateway', 11);

function init_WC_Gateway_ThawaniGateway()
{
    if (class_exists('WC_Payment_Gateway')) {
        new AdminDashboard();
        new ThawaniAjax();
    }
}
/**
 * Enqueue a script in the WordPress admin on post.php.
 *
 * @param int $hook Hook suffix for the current admin page.
 */
function thawani_gw_enqueue_script($hook)
{
    if ('post.php' != $hook) {
        return;
    }
    wp_enqueue_script('my_custom_script', plugin_dir_url(__FILE__) . '/dist/thawani-generate.js', array('jquery'), '1.0');
}
add_action('admin_enqueue_scripts', 'thawani_gw_enqueue_script');
