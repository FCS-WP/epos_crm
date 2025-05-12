<?php

/**
 * CRM WOO ADMIN TAB
 *
 *
 */

namespace EPOS_CRM\Src\Admin;

use WC_Settings_Page;

// use WC_Admin_Settings;

use EPOS_CRM\Utils\Utils_Core;

defined('ABSPATH') or die();

class Woo_Settings extends WC_Settings_Page
{
  protected static $_instance = null;

  /**
   * @return Woo_Settings
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

    $this->id    = EPOS_CRM_PREFIX;
    add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
  }

  /**
   * Add plugin options tab
   *
   * @return array
   */
  public function add_settings_tab($settings_tabs)
  {
    $settings_tabs[$this->id] = __('EPOS CRM',  'epos-settings-tab');
    return $settings_tabs;
  }

  public function admin_assets()
  {
    $version = time();
    $current_user_id = get_current_user_id();
    // Pass the user ID to the script
    wp_enqueue_script('admin-booking-js', EPOS_CRM_URL . '/assets/dist/js/admin.min.js', [], $version, true);
    wp_enqueue_style('booking-css', EPOS_CRM_URL . '/assets/dist/css/admin.min.css', [], $version);
    wp_localize_script('booking-js-current-id', 'admin_id', array(
      'userID' => $current_user_id,
    ));
  }

  public function admin_page()
  {
    add_menu_page('Zippy Add-ons', 'Zippy Add-ons', 'manage_options', 'zippy-addons', array($this, 'dashboard_render'), 'dashicons-list-view', 6);
    // SubPage
    // add_submenu_page('zippy-bookings', 'Settings', 'Settings', 'manage_options', 'settings', array($this, 'settings_render'));
  }


  public function render()
  {
    echo Utils_Core::get_template('booking-dashboard.php', [], dirname(__FILE__), '/templates');
  }
  public function settings_render()
  {
    echo Utils_Core::get_template('settings.php', [], dirname(__FILE__), '/templates');
  }

  public function dashboard_render()
  {
    echo Utils_Core::get_template('index.php', [], dirname(__FILE__), '/templates');
  }

  public function remove_default_stylesheets($handle)
  {
    $apply_urls = [
      'toplevel_page_zippy-addons',
    ];

    if (in_array($handle, $apply_urls)) {
      // Deregister the 'forms' stylesheet
      wp_deregister_style('forms');

      add_action('admin_head', function () {
        $admin_url = get_admin_url();
        $styles_to_load = [
          'dashicons',
          'admin-bar',
          'common',
          'admin-menu',
          'dashboard',
          'list-tables',
          'edit',
          'revisions',
          'media',
          'themes',
          'about',
          'nav-menus',
          'wp-pointer',
          'widgets',
          'site-icon',
          'l10n',
          'buttons',
          'wp-auth-check'
        ];

        $wp_version = get_bloginfo('version');

        // Generate the styles URL
        $styles_url = $admin_url . '/load-styles.php?c=0&dir=ltr&load=' . implode(',', $styles_to_load) . '&ver=' . $wp_version;

        // Enqueue the stylesheet
        echo '<link rel="stylesheet" href="' . esc_url($styles_url) . '" media="all">';
      });
    }
  }
}
