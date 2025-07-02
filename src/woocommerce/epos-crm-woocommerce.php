<?php

/**
 * Woocommece Booking Settings
 *
 *
 */

namespace EPOS_CRM\Src\Woocommerce;

defined('ABSPATH') or die();

use EPOS_CRM\Utils\EPOS_Helper;

use EPOS_CRM\Utils\Woo_Session_Handler;
use EPOS_CRM\Src\Woocommerce\Orders\Epos_Crm_Orders;

class Epos_Crm_Woocommerce
{
  protected static $_instance = null;

  /**
   * @return Epos_Crm_Woocommerce
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
    if (!function_exists('is_plugin_active')) {

      include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    if (!is_plugin_active('woocommerce/woocommerce.php')) return;

    $this->set_hooks();

    Epos_Crm_Orders::get_instance();
    /* Update Checkout After Applied Coupon */
    add_action('trigger_update_checkout', array($this, 'trigger_update_checkout_callback'), 1, 3);
    // EPOS Prefill Customer Infor
    add_action('woocommerce_checkout_get_value', array($this, 'prefill_checkout_fields'), 10, 2);
    add_filter('woocommerce_checkout_fields', array($this, 'custom_override_checkout_fields'));
  }

  protected function set_hooks()
  {
    add_filter('wc_get_template_part', array($this, 'override_woocommerce_template_part'), 1, 3);
    add_filter('woocommerce_locate_template', array($this, 'override_woocommerce_template'), 1, 3);
  }

  /**
   * Template Part's
   *
   * @param  string $template Default template file path.
   * @param  string $slug     Template file slug.
   * @param  string $name     Template file name.
   * @return string           Return the template part from plugin.
   */
  public function override_woocommerce_template_part($template, $slug, $name)
  {

    $template_directory = untrailingslashit(plugin_dir_path(__FILE__)) . "/templates/";
    if ($name) {
      $path = $template_directory . "{$slug}-{$name}.php";
    } else {
      $path = $template_directory . "{$slug}.php";
    }
    return file_exists($path) ? $path : $template;
  }
  /**
   * Template File
   *
   * @param  string $template      Default template file  path.
   * @param  string $template_name Template file name.
   * @param  string $template_path Template file directory file path.
   * @return string                Return the template file from plugin.
   */
  public function override_woocommerce_template($template, $template_name, $template_path)
  {

    $template_directory = untrailingslashit(plugin_dir_path(__FILE__)) . "/templates/";

    $path = $template_directory . $template_name;
    // echo 'template: ' . $path . '<br/>';

    return file_exists($path) ? $path : $template;
  }

  public function custom_override_checkout_fields($fields)
  {
    $fields['billing']['epos_customer_id'] = '';
    unset($fields['billing']['billing_company']);
    return $fields;
  }

  public function trigger_update_checkout_callback()
  {
    echo '<script>jQuery( "body" ).trigger( "update_checkout" ); </script>';
  }

  public function prefill_checkout_fields($value, $input)
  {

    $session = new Woo_Session_Handler;

    $session_user_data = !empty($session->get('epos_customer_data')) ? $session->get('epos_customer_data') : '';
    $session_user_id = !empty($session->get('epos_customer_id')) ? $session->get('epos_customer_id') : '';

    if (!$session_user_data || !is_object($session_user_data)) {
      return $value;
    }

    switch ($input) {
      case 'billing_first_name':
        return EPOS_Helper::split_full_name($session_user_data->full_name)['first_name']  ?? '';
      case 'billing_last_name':
        return  EPOS_Helper::split_full_name($session_user_data->full_name)['last_name'] ?? '';
      case 'billing_email':
        return $session_user_data->email ?? '';
      case 'epos_customer_id':
        return $session_user_id ?? '';
      case 'billing_phone':
        return $session_user_data->phone_number ?? '';
      case 'billing_address_1':
        return $session_user_data->address_street_1 ?? '';
      case 'billing_address_2':
        return $session_user_data->address_street_2 ?? '';
      case 'billing_postcode':
        return $session_user_data->address_postal_code ?? '';
      case 'billing_city':
        return $session_user_data->address_city ?? '';
      case 'billing_country':
        return $session_user_data->address_country ?? '';
      default:
        return '';
    }
  }
}
