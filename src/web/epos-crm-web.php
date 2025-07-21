<?php

/**
 * Epos_crm_webs FontEnd Form
 *
 *
 */

namespace EPOS_CRM\Src\Web;

defined('ABSPATH') or die();

use EPOS_CRM\Utils\Woo_Session_Handler;

class Epos_Crm_Web
{
  protected static $_instance = null;

  /**
   * @return Epos_Crm_Web
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

    /* Init Function */
    add_shortcode('epos_crm_login_form', array($this, 'epos_crm_login_form_callback'));

    add_shortcode('epos_crm_login', array($this, 'epos_crm_login_callback'));

    add_action('wp_footer', array($this, 'render_login_form'));

    add_action('woocommerce_before_checkout_form', array($this, 'render_point_information'));

    add_action('woocommerce_thankyou',  array($this, 'action_payment_complete'));

    /* Epos_crm_web Assets  */
    add_action('wp_enqueue_scripts', array($this, 'epos_crm_web_assets'));
  }

  public function function_init()
  {
    return;
  }

  public function epos_crm_web_assets()
  {
    $version = time();

    $current_user_id = get_current_user_id();
    $user_info = get_userdata($current_user_id);
    // Form Assets
    wp_enqueue_script('epos_crm_web-js', EPOS_CRM_URL . '/assets/dist/js/web.min.js', [], $version, true);
    wp_enqueue_style('epos_crm_web-css', EPOS_CRM_URL . '/assets/dist/css/web.min.css', [], $version);
  }

  public function epos_crm_login_form_callback($atts)
  {
    $session = new Woo_Session_Handler;

    $is_login = !empty($session->get('epos_customer_data')) ? 'true' : 'false';

    $site_name = get_option('blogname') ?? "EPOS";

    $logo_url = $this->get_logo_url();

    $is_checkout = is_checkout() ? 'true' : 'false';

    return '<div id="epos_crm_login_form" data-login="' . $is_login . '" data-checkout="' . $is_checkout . '" data-site-name="' . $site_name . '" data-site-logo="' . $logo_url . '"></div>';
  }

  public function epos_crm_login_callback($atts)
  {
    $session = new Woo_Session_Handler;

    $session_user_data = !empty($session->get('epos_customer_data')) ? $session->get('epos_customer_data') : '';

    $name = $session_user_data->full_name ?? '';

    return '<div id="epos_crm_user_name" data-customer-name="' . $name . '"><span>' . $name . '</span></div>';
  }

  public function render_login_form()
  {
    if (empty(get_option('epos_crm_token_key')) || is_wc_endpoint_url('order-received')) return;

    echo do_shortcode('[epos_crm_login_form]');
  }

  public function render_point_information()
  {
    $session = new Woo_Session_Handler;

    $customer_data = $session->get('epos_customer_data');
    if (empty($customer_data->active_member)) return;

    echo '<div id="epos_crm_point_information"
      data-active-member="' . $customer_data->active_member . '"
      data-points="' . $customer_data->point_balance . '"
      data-point-rate="' . $customer_data->point_conversion_rate . '"
      >
      </div>';
  }

  public function action_payment_complete($order_id)
  {
    $session = new Woo_Session_Handler;
    $session->delete_session();
  }

  private function get_logo_url()
  {
    $logo_id = get_theme_mod('custom_logo');

    $image = wp_get_attachment_image_src($logo_id, 'full');

    $logo_url = !empty($image[0]) ? $image[0] : '/wp-content/plugins/epos_crm/assets/web/icons/eposLogo.png';

    return $logo_url;
  }
}
