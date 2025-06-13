<?php

namespace EPOS_CRM\Src\Admin;

use WC_Settings_Page;
use WC_Admin_Settings;
use EPOS_CRM\Utils\Utils_Core;
use EPOS_CRM\Src\Controllers\Auth\Epos_Auth_controller;

defined('ABSPATH') || exit;

class Woo_Settings extends WC_Settings_Page
{
  protected static $_instance = null;

  /**
   * Singleton instance
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
    $this->label = __('EPOS CRM', 'epos-crm-settings-tab');

    add_filter('woocommerce_settings_tabs_array', [$this, 'add_settings_tab'], 50);
    add_action('woocommerce_admin_field_epos_crm_des', [$this, 'renderSettingsSection']);
    add_action('woocommerce_settings_' . $this->id, [$this, 'output']);
    add_action('epos_crm_authentication', [$this, 'handleAuthenticationCallback']);
    add_action('admin_head', [$this, 'addInlineCSS']);
  }

  /**
   * Add the tab to WooCommerce settings
   */
  public function add_settings_tab($settings_tabs)
  {
    $settings_tabs[$this->id] = __('EPOS CRM', 'epos-crm-settings-tab');
    return $settings_tabs;
  }

  /**
   * Output settings fields
   */
  public function output()
  {
    $settings = $this->get_settings();
    WC_Admin_Settings::output_fields($settings);
  }

  /**
   * Get settings array
   */
  public function get_settings($section = null)
  {
    return apply_filters('wc_settings_tab_settings', [
      'section_title' => $this->getTitleDescription(),
      'divider' => Utils_Core::divider(),
      'epos_crm_des' => [
        'id'    => 'epos_crm_des',
        'title' => __('Authentication', 'epos-crm-settings-tab'),
        'type'  => 'epos_crm_des',
      ],
      'divider' => Utils_Core::divider(),
      'section_end' => [
        'type' => 'sectionend',
        'id'   => 'epos-crm-settings-tab_end',
      ],
    ], $section);
  }

  /**
   * Display either auth form or success message
   */
  public function renderSettingsSection()
  {
    $is_auth = $this->triggerAuthentication();
    if (isset($_GET['epos_error']) && $_GET['epos_error'] == 1) {
      $auth_error = get_option('epos_crm_auth_error');
      if ($auth_error) {
        echo '<div class="notice notice-error"><p>' . esc_html($auth_error) . '</p></div>';
      }
    }
    if (!$is_auth) {

      echo Utils_Core::get_template('auth-form.php', [], dirname(__FILE__), '/templates');
    } else {
      $epos_url = get_option('epos_be_url', '');
      echo Utils_Core::get_template('auth-success.php', ['epos_url' => esc_url($epos_url)], dirname(__FILE__), '/templates');
    }
  }

  /**
   * Trigger authentication callback
   */
  public function handleAuthenticationCallback($code)
  {

    $authController = new Epos_Auth_controller($code);
    $response = $authController->Auth();

    if (!empty($response['data']->access_token)) {
      $this->storeToken($response['data']->access_token);
      delete_option('epos_crm_auth_error');

      // Optional: redirect back to clean the URL
      wp_redirect(admin_url('admin.php?page=wc-settings&tab=' . EPOS_CRM_PREFIX));
      exit;
    } else {
      // Store the error message for later display
      update_option('epos_crm_auth_error', 'Failed to authenticate with EPOS. Please try again.');

      // Redirect back with error query param
      $error_url = admin_url('admin.php?page=wc-settings&tab=' . EPOS_CRM_PREFIX . '&epos_error=1');
      wp_redirect($error_url);
      exit;
    }
  }

  /**
   * Trigger custom authentication action
   */
  private function triggerAuthentication()
  {
    $token = get_option('epos_crm_token_key');

    if (!empty($token)) return true;

    if (!is_admin() || empty($_GET['code'])) {
      return false;
    }

    $code = sanitize_text_field($_GET['code']);

    do_action('epos_crm_authentication', $code);

    return true;
  }

  /**
   * Save access token
   */
  private function storeToken($token)
  {
    update_option('epos_crm_token_key', sanitize_text_field($token), true);
  }

  /**
   * Output inline CSS to hide the default save button
   */
  public function addInlineCSS()
  {
    if (!isset($_GET['tab']) || $_GET['tab'] !== EPOS_CRM_PREFIX) return;

    echo '<style>
            .form-table .titledesc { width: 280px; }
            .submit { display: none; }
        </style>';
  }

  /**
   * Section title and description
   */
  private function getTitleDescription()
  {
    return [
      'name' => __('EPOS CRM', 'epos-crm-settings-tab'),
      'type' => 'title',
      'desc' => __(
        'This configuration integrates with <strong>EPOS V5 Backend Customer Data</strong>.<br>
                <span style="color: #cc0000;">**Any adjustments must be made directly in the EPOS V5 Backend.</span>',
        'epos-crm-settings-tab'
      ),
      'id' => 'epos_crm_settings_title_section'
    ];
  }
}
