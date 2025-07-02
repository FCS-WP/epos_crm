<?php

/**
 * Bookings FontEnd Form
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

    add_action('wp_footer', array($this, 'render_login_form'));

    add_action('woocommerce_payment_complete',  array($this, 'action_payment_complete'));

    /* Booking Assets  */
    add_action('wp_enqueue_scripts', array($this, 'booking_assets'));
  }

  public function function_init()
  {
    return;
  }

  public function booking_assets()
  {
    // if (!is_archive() && !is_single() && !is_checkout()) return;
    $version = time();

    $current_user_id = get_current_user_id();
    $user_info = get_userdata($current_user_id);
    // Form Assets
    wp_enqueue_script('booking-js', EPOS_CRM_URL . '/assets/dist/js/web.min.js', [], $version, true);
    wp_enqueue_style('booking-css', EPOS_CRM_URL . '/assets/dist/css/web.min.css', [], $version);
    wp_localize_script('booking-js', 'admin_data', array(
      'userID' => $current_user_id,
      'user_email' => $user_info->user_email
    ));
  }

  public function epos_crm_login_form_callback($atts)
  {
    $session = new Woo_Session_Handler;

    // if ($session->get('epos_customer_data')) return;
    $is_login = !empty($session->get('epos_customer_data')) ? 'true' : 'false';

    $site_name = get_option('blogname') ?? "EPOS";

    $logo_url = $this->get_logo_url();

    $is_checkout = is_checkout() ? 'true' : 'false';

    return '<div id="epos_crm_login_form" data-login="' . $is_login . '" data-checkout="' . $is_checkout . '" data-site-name="' . $site_name . '" data-site-logo="' . $logo_url . '"></div>';
  }

  public function render_login_form()
  {
    if (empty(get_option('epos_crm_token_key'))) return;

    echo do_shortcode('[epos_crm_login_form]');
  }

  function action_payment_complete($order_id, $order)
  {
    $session = new Woo_Session_Handler;
    $session->destroy('epos_customer_data');
    $session->destroy('epos_customer_id');
  }

  private function get_logo_url()
  {
    $logo_id = get_theme_mod('custom_logo');

    $image = wp_get_attachment_image_src($logo_id, 'full');

    $logo_url = !empty($image[0]) ? $image[0] : '/wp-content/plugins/epos_crm/assets/web/icons/eposLogo.png';

    return $logo_url;
  }
}
