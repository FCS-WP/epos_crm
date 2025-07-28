<?php

namespace EPOS_CRM\Src\Woocommerce\Checkout;

defined('ABSPATH') or die();

use EPOS_CRM\Utils\Utils_Core;
use EPOS_CRM\Utils\Woo_Session_Handler;
use DateTimeZone;
use DateTime;


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

    add_action('woocommerce_after_checkout_billing_form', array($this, 'epos_order_custom_fields'));

    //Make the Phone Number is Require
    add_filter('woocommerce_billing_fields', array($this, 'make_phone_is_require'), 10, 1);
  }


  public function make_phone_is_require($address_fields)
  {
    $address_fields['billing_phone']['required'] = false;
    return $address_fields;
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

  public function epos_order_custom_fields($checkout)
  {

    $value = Utils_Core::create_guid();

    $redeem_id = Utils_Core::create_guid();

    woocommerce_form_field('epos_order_id', array(
      'type'  => 'hidden',
      'class'         => array('epos_order_id form-row-wide'),
      'placeholder'   => __('epos_order_id'),
    ), $value);

    woocommerce_form_field('redeem_id', array(
      'type'  => 'hidden',
      'class'         => array('redeem_id form-row-wide'),
      'placeholder'   => __('redeem_id'),
    ), $redeem_id);
  }
}
