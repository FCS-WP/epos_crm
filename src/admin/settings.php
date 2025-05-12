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
  }

  public function woo_setting_tab($settings)
  {

      $settings[] = new Woo_Settings();
      return $settings;
  }
}
