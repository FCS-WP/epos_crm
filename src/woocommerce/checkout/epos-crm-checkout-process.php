<?php

namespace EPOS_CRM\Src\Woocommerce\Checkout;

defined('ABSPATH') or die();

use EPOS_CRM\Utils\Utils_Core;


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

    // add_action('woocommerce_checkout_process', array($this, 'validate_custom_discount_input'));

    add_action('woocommerce_payment_complete', 'epos_redeem_process_for_complete'); // Process or Completed order status
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

  private function epos_redeem_process_for_complete() {

  }

  private function validate_custom_discount_input()
  {
    if (!empty($_POST['custom_discount_amount']) && floatval($_POST['custom_discount_amount']) < 0) {
      wc_add_notice(__('Discount must be a positive number.'), 'error');
    }
  }
}
