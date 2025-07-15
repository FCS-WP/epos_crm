<?php

/**
 * Woocommece Booking Settings
 *
 *
 */

namespace EPOS_CRM\Src\Woocommerce\Orders;

defined('ABSPATH') or die();

use WC_Order;

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
    //Make the Phone Number is Require
    add_filter('woocommerce_billing_fields', array($this, 'make_phone_is_require'), 10, 1);
  }


  public function make_phone_is_require($address_fields)
  {
    $address_fields['billing_phone']['required'] = false;
    return $address_fields;
  }

  public function add_order_meta_data($order_id)
  {
    $epos_customer_id = $_POST['epos_customer_id'];
    if (! empty($epos_customer_id) && !empty($order_id)) {
      $order = new WC_Order($order_id);

      $meta_data = $this->build_order_meta_data($epos_customer_id);

      $order->update_meta_data('epos_crm', $meta_data, true);

      $order->save_meta_data();
    }
  }
  private function build_order_meta_data($epos_customer_id)
  {

    $meta_data = array(
      "order_id" => com_create_guid(),
      "customer_id" => $epos_customer_id,
      "use_billing_info" => $this->handle_get_ship_to_destination(),
      "payments" => array(
        array(
          "id" => com_create_guid(),
          "strategy" => "point_consumption",
          "points_used" => "100",
          "conversion_rate" => "1",
          "value" => "100",
          "transacted_at" => "2020-03-11T19:56:05",
          "redemption_id" => com_create_guid()
        ),
        array(
          "id" => com_create_guid(),
          "strategy" => "cash",
          "value" => "500",
          "transacted_at" => "2020-03-11T19:56:05"
        )
      )
    );

    return json_encode($meta_data);
  }

  private function handle_get_ship_to_destination()
  {
    if (get_option('woocommerce_ship_to_destination') == 'shipping') return 'false';

    return 'true';
  }
}
