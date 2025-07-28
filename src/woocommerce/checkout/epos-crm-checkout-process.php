<?php

namespace EPOS_CRM\Src\Woocommerce\Checkout;

defined('ABSPATH') or die();

use EPOS_CRM\Utils\Utils_Core;
use EPOS_CRM\Utils\Woo_Session_Handler;
use EPOS_CRM\Src\Woocommerce\Checkout\Epos_Crm_Redeem_Process;
use DateTimeZone;
use DateTime;
use WC_Order;


class Epos_Crm_Checkout_Process
{

  protected static $_instance = null;

  /**
   * @return Epos_Crm_Orders
   */


  public static function get_instance()
  {

    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }


  public function __construct()
  {
    add_action('woocommerce_cart_calculate_fees', array($this, 'apply_redeem_point'));

    add_action('woocommerce_order_status_completed', array($this, 'redeem_process')); // Process or Completed order status

    add_action('woocommerce_order_status_processing', array($this, 'redeem_process')); // Process or Completed order status


  }


  public function apply_redeem_point($cart)
  {
    if (is_admin() && !defined('DOING_AJAX')) return;

    $is_used_redeem = WC()->session->get('is_used_redeem');
    $point_used = WC()->session->get('point_used');
    if ($is_used_redeem && $point_used > 0) {
      $discount = $point_used * -1;
      $cart->add_fee(EPOS_CRM_REDEEM, $discount);
    }
  }

  public function epos_redeem_process_for_complete($order_id)
  {
    $session = new Woo_Session_Handler;

    $session->delete_session();
  }


  public function redeem_process($order_id)
  {

    $is_used_redeem = WC()->session->get('is_used_redeem');

    $point_used = WC()->session->get('points');

    if (empty($is_used_redeem) || empty($point_used)) {
      return;
    }

    $order = new WC_Order($order_id);

    $redeem_id = $order->get_meta('redeem_id');

    $epos_order_id = $order->get_meta('epos_order_id');

    $epos_customer_id = $order->get_meta('epos_customer_id');

    $redeem_api = new Epos_Crm_Redeem_Process();

    $transacted_at =  gmdate('Y-m-d\TH:i:s\Z');

    $session = new Woo_Session_Handler;

    $customer_data = $session->get('epos_customer_data');

    $request = array(
      'id' => $redeem_id,
      'order_id' => $epos_order_id,
      'member_id' => $epos_customer_id,
      'points' => $point_used,
      'transacted_at' => $transacted_at,
      'conversion_rate' =>  $customer_data->point_conversion_rate,
    );

    $response = $redeem_api->API_redeem_process($request);

    if (!isset($response['status']) || $response['status'] !== "success") {
      error_log('Redeem API failed: ' . print_r($response, true));
      $order->add_order_note(__('Redeem API failed. Check logs.'), false);
    } else {
      $order->add_order_note(__('Points redeemed successfully.' . $point_used . ''), false);
      //Unset after done
      $session->delete_redeem_session();
    }
  }
}
