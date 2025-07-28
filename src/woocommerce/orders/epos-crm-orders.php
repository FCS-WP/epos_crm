<?php

/**
 * Woocommece Booking Settings
 *
 *
 */

namespace EPOS_CRM\Src\Woocommerce\Orders;

defined('ABSPATH') or die();

use WC_Order;
use EPOS_CRM\Src\Woocommerce\Checkout\Epos_Crm_Redeem_Process;
use EPOS_CRM\Src\Woocommerce\Orders\Epos_Crm_Orders_Payload;
use EPOS_CRM\Utils\Woo_Session_Handler;


class Epos_Crm_Orders
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
    //Add Epos Customer ID
    add_action('woocommerce_checkout_update_order_meta', array($this, 'add_order_meta_data'));

    add_action('woocommerce_order_status_completed', array($this, 'redeem_process')); // Process or Completed order status

    add_action('woocommerce_order_status_processing', array($this, 'redeem_process')); // Process or Completed order status

  }

  public function add_order_meta_data($order_id)
  {
    $epos_customer_id = isset($_POST['epos_customer_id']) ? sanitize_text_field($_POST['epos_customer_id']) : '';
    $epos_member_id = isset($_POST['epos_member_id']) ? sanitize_text_field($_POST['epos_member_id']) : '';
    $epos_order_id    = isset($_POST['epos_order_id']) ? sanitize_text_field($_POST['epos_order_id']) : '';
    $redeem_id    = isset($_POST['redeem_id']) ? sanitize_text_field($_POST['redeem_id']) : '';

    if (!empty($epos_customer_id)) {

      $order = new WC_Order($order_id);

      $meta_data = Epos_Crm_Orders_Payload::build_order_meta_data($epos_customer_id, $order, $epos_order_id, $redeem_id);

      $order->update_meta_data('epos_crm', $meta_data, true);

      $order->update_meta_data('epos_order_id', $epos_order_id, true);

      $order->update_meta_data('epos_customer_id', $epos_customer_id, true);
    }

    if ($this->is_apply_redeem()) {
      $order->update_meta_data('redeem_id', $redeem_id, true);
    }

    if (!empty($epos_member_id)) {
      $order->update_meta_data('epos_member_id', $epos_member_id, true);
    }

    $order->save_meta_data();
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

    $epos_member_id = $order->get_meta('epos_member_id');

    $redeem_api = new Epos_Crm_Redeem_Process;

    $transacted_at =  gmdate('Y-m-d\TH:i:s\Z');

    $session = new Woo_Session_Handler;

    $customer_data = $session->get('epos_customer_data');

    $request = array(
      'id' => $redeem_id,
      'order_id' => $epos_order_id,
      'member_id' => $epos_member_id,
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


  public function is_apply_redeem()
  {

    if (empty(WC()->session->get(('is_used_redeem')) || empty(WC()->session->get('points')))) return false;

    return true;
  }
}
