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

    if ($session->get('epos_customer_data')) return;

    $site_name = get_option('blogname') ?? "EPOS";

    return '<div id="epos_crm_login_form" data-site-name="' . $site_name . '"></div>';
  }

  public function render_login_form()
  {
    if (!is_checkout()) return;
    echo do_shortcode('[epos_crm_login_form]');
    $session = new Woo_Session_Handler;

    $session->destroy('epos_customer_data');
  }
}
