<?php

/**
 * Epos_crm_webs FontEnd Form
 *
 *
 */

namespace EPOS_CRM\Src\Web;

defined('ABSPATH') or die();

use EPOS_CRM\Utils\Woo_Session_Handler;
use EPOS_CRM\Utils\EPOS_CRM_Cart_Handler;
use EPOS_CRM\Utils\Utils_Core;
use EPOS_CRM\Src\Web\Epos_Crm_Web_Menu;

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

    /* Init Menu */
    Epos_Crm_Web_Menu::get_instance();
    // Login form
    add_shortcode('epos_crm_login_form', array($this, 'epos_crm_login_form_callback'));

    // Login icon
    add_shortcode('epos_crm_login', array($this, 'epos_crm_login_icon_callback'));

    add_action('wp_footer', array($this, 'render_login_form'));

    add_action('woocommerce_before_checkout_form', array($this, 'render_point_information'));

    add_action('woocommerce_thankyou',  array($this, 'action_payment_complete'));

    add_action('woocommerce_after_order_details',  array($this, 'render_button_auto_login'));

    /* Epos_crm_web Assets  */
    add_action('wp_enqueue_scripts', array($this, 'epos_crm_web_assets'));
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

    echo Utils_Core::get_template('form-login.php', ['is_login' => $is_login, 'site_name' => $site_name, 'logo_url' => $logo_url, 'is_checkout' => $is_checkout], dirname(__FILE__), '/templates');
  }

  public function epos_crm_login_icon_callback($atts)
  {
    $session = new Woo_Session_Handler;

    $session_user_data = !empty($session->get('epos_customer_data')) ? $session->get('epos_customer_data') : '';

    $name = $session_user_data->full_name ?? '';

    echo Utils_Core::get_template('login-icon.php', ['name' => $name], dirname(__FILE__), '/templates');
  }

  public function render_login_form()
  {
    if (empty(get_option('epos_crm_token_key')) || is_wc_endpoint_url('order-received')) return;

    echo do_shortcode('[epos_crm_login_form]');
  }

  public function render_point_information()
  {
    $session = new Woo_Session_Handler;

    $cart  =  new EPOS_CRM_Cart_Handler;

    $total = $cart->get_cart_sub_total();

    $customer_data = $session->get('epos_customer_data');

    $applied_points = $session->get('point_used') ?? 0;
    if (empty($customer_data->active_member)) return;

    echo Utils_Core::get_template('point-infomation.php', ['customer_data' => $customer_data, 'total' => $total, 'applied_points' => $applied_points], dirname(__FILE__), '/templates');
  }

  public function action_payment_complete($order_id)
  {
    $session = new Woo_Session_Handler;

    $session->destroy('is_used_redeem');
    $session->destroy('point_used');
  }

  public function render_button_auto_login($order)
  {

    $session = new Woo_Session_Handler;

    $token = $session->get('epos_customer_token');


    $tanent_domain =  get_option('epos_be_url', null);

    if (empty($token) || empty($tanent_domain)) return;

    $query_string = EPOS_CRM_CUSTOMER_PORTAL_URL . '/api/auto-login?';

    $tenant_name = Utils_Core::get_subdomain($tanent_domain);

    $customer_portal_url = $query_string . build_query(array('token' => $token, 'tenant' => $tenant_name));

    $id_member = $session->get('epos_member_id');
    if (!empty($id_member)) {
    echo Utils_Core::get_template('member_auto-login.php', ['customer_portal_url' => $customer_portal_url], dirname(__FILE__), '/templates');

    } else {
      echo Utils_Core::get_template('button-auto-login.php', ['customer_portal_url' => $customer_portal_url], dirname(__FILE__), '/templates');
    }
  }

  private function get_logo_url()
  {
    $logo_id = get_theme_mod('custom_logo');

    $image = wp_get_attachment_image_src($logo_id, 'full');

    $logo_url = !empty($image[0]) ? $image[0] : '/wp-content/plugins/epos_crm/assets/web/icons/eposLogo.png';

    return $logo_url;
  }
}
