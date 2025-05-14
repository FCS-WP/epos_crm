<?php

/**
 * CRM WOO ADMIN TAB
 *
 *
 */

namespace EPOS_CRM\Src\Admin;

use WC_Settings_Page;

use WC_Admin_Settings;

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
    $this->label = __('EPOS CRM',  'epos-crm-settings-tab');

    add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
    add_action('woocommerce_admin_field_epos_crm_des', array($this, 'epos_crm_des_setting'));
    // add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
    add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
    // add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
    add_action('epos_crm_authentication' . $this->id, array($this, 'epos_crm_authentication_callback'));
    add_action('admin_head', array($this, 'crm_inline_css'));
  }

  /**
   * Add plugin options tab
   *
   * @return array
   */
  public function add_settings_tab($settings_tabs)
  {
    $settings_tabs[$this->id] = __('EPOS CRM',  'epos-crm-settings-tab');
    return $settings_tabs;
  }
  /**
   * Add css inline to tab
   *
   * @return array
   */
  public function crm_inline_css()
  {
    if (isset($_GET['tab']) && $_GET['tab'] != EPOS_CRM_PREFIX) return;
    echo '<style>.form-table .titledesc{width:280px}.submit{display:none;}</style>';
  }

  public function get_settings($section = null)
  {
    $settings = array(
      'section_title' => $this->show_warning_message(),
      'divider' => Utils_Core::divider(),
      'epos_crm_des'         => array(
        'id'       => 'epos_crm_des',
        'title'   => __('Payment methods', 'epos-crm-settings-tab'),
        'type'      => 'epos_crm_des',
      ),

      'divider' => Utils_Core::divider(),
      'section_end' => array(
        'type' => 'sectionend',
        'id' => 'epos-crm-settings-tab_end'
      )
    );
    return apply_filters('wc_settings_tab_settings', $settings, $section);
  }

  /**
   * Get sections
   *
   * @return array
   */
  public function get_sections()
  {
    //Init Tab 
    $sections = array(
      ''                      => __('General', 'epos-crm-settings-tab'),
    );
    return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
  }

  /**
   * Output the settings
   */
  public function output()
  {
    global $current_section;
    $settings = $this->get_settings($current_section);
    WC_Admin_Settings::output_fields($settings);
  }

  /**
   * Save settings
   */
  public function save()
  {
    global $current_section;
    $settings = $this->get_settings($current_section);


    $has_error = false;

    // Validate 'auth_epos_crm'
    if (isset($_POST['epos_be_url']) && empty($_POST['epos_be_url'])) {
      WC_Admin_Settings::add_error(__('EPOS Backend URL is required.', 'woocommerce'));
      $has_error = true;
    }

    // Validate 'epos_crm_term'
    if (isset($_POST['epos_crm_term']) && empty($_POST['epos_crm_term'])) {
      WC_Admin_Settings::add_error(__('Please agree with the term and conditions.', 'woocommerce'));
      $has_error = true;
    }

    if (!$has_error) {
      WC_Admin_Settings::save_fields($settings);
      $this->save_settings_for_current_section();
      $this->do_update_options_action();
      $this->do_epos_crm_authentication();
    }
  }
  private function do_epos_crm_authentication($section_id = null)
  {
    //Get Backend URL
    $be_url = get_option('epos_be_url');

    if (empty($be_url)) WC_Admin_Settings::add_error(__('Authentication failed !.', 'woocommerce'));

    do_action('epos_crm_authentication');
  }

  function epos_crm_des_setting($current_section = '')
  {
    echo Utils_Core::get_template('index.php', [], dirname(__FILE__), '/templates');
  }

  private function show_warning_message()
  {
    $settings_title = array(
      'name'     => __('EPOS CRM', 'epos-crm-settings-tab'),
      'type'     => 'title',
      'desc'     => __('This configuration Integrates with <span style="color: #000;">EPOS V5 Backend Customer Data </span><br>
      <span style="color: #cc0000;display: block;">**Any adjustments must update directly in EPOS V5 Backend </span>', 'epos-crm-settings-tab'),
      'id'       => 'zippy_settings_tab_title_section'
    );
    return $settings_title;
  }

  function epos_crm_authentication_callback()
  {
    var_dump('shin');
  }
  function output_sections()
  {
    echo Utils_Core::get_template('index.php', [], dirname(__FILE__), '/templates');
  }
}
