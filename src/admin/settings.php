<?php

namespace EPOS_CRM\Src\Admin;

use EPOS_CRM\Utils\Utils_Core;

class Settings
{

  /**
   * The single instance of the class.
   *
   * @var   Settings
   */
  protected static $_instance = null;

  /**
   * @return Settings
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

    if (!Utils_Core::check_exits_woocommerce()) {
      return;
    }
    add_filter('woocommerce_get_settings_pages', [$this, 'woo_setting_tab']);
    add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
  }

  public function woo_setting_tab($settings)
  {

    $settings[] = new Woo_Settings();
    return $settings;
  }

  public function admin_assets()
  {
    $version = time();
    $current_user_id = get_current_user_id();
    // Pass the user ID to the script
    wp_enqueue_script('admin-crm-js', EPOS_CRM_URL . '/assets/dist/js/admin.min.js', [], $version, true);
    wp_enqueue_style('epos-crm-css', EPOS_CRM_URL . '/assets/dist/css/admin.min.css', [], $version);
    wp_localize_script('current-id', 'admin_id', array(
      'userID' => $current_user_id,
    ));
  }
}
