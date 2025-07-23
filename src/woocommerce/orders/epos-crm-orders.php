<?php

/**
 * Woocommece Booking Settings
 *
 *
 */

namespace EPOS_CRM\Src\Woocommerce\Orders;

defined('ABSPATH') or die();

use WC_Order;
use EPOS_CRM\Utils\Utils_Core;
use EPOS_CRM\Src\Woocommerce\Orders\Epos_Crm_Orders_Payload;

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

      $meta_data = Epos_Crm_Orders_Payload::build_order_meta_data($epos_customer_id, $order);

      $order->update_meta_data('epos_crm', $meta_data, true);

      $order->save_meta_data();
    }
  }
}
