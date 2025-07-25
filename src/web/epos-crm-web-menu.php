<?php

/**
 * Epos_crm_webs FontEnd Form
 *
 *
 */

namespace EPOS_CRM\Src\Web;

defined('ABSPATH') or die();

use EPOS_CRM\Utils\Woo_Session_Handler;
use EPOS_CRM\Utils\Utils_Core;

class Epos_Crm_Web_Menu
{

  protected static $_instance = null;
  const MENU_POST_NAME = 'epos_crm_menu';

  /**
   * @return Epos_Crm_Web_Menu
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
    if (is_admin()) {
      add_action('admin_head-nav-menus.php', array($this, 'add_nav_menu_meta_boxes'));
    }

    add_filter('walker_nav_menu_start_el', array($this, 'process_menu_item'), 50, 2);

    add_filter('megamenu_walker_nav_menu_start_el', array($this, 'process_menu_item'), 50, 2);
  }

  public function get_customer_portal_auto_login_url()
  {
    $session = new Woo_Session_Handler;

    $token = $session->get('epos_customer_token');

    $tanent_domain =  get_option('epos_be_url', null);

    if (empty($token) || empty($tanent_domain)) return;

    $query_string = EPOS_CRM_CUSTOMER_PORTAL_URL . '/api/auto-login?';

    $tenant_name = Utils_Core::get_subdomain($tanent_domain);

    return $query_string . build_query(array('token' => $token, 'tenant' => $tenant_name));
  }


  public function add_nav_menu_meta_boxes()
  {

    add_meta_box(
      'epos_crm_endpoints_nav_link',
      __('EPOS CRM', 'ajax-search-for-woocommerce'),
      array($this, 'nav_menu_links'),
      'nav-menus',
      'side',
      'low'
    );
  }

  public function process_menu_item($itemOutput, $item)
  {

    if (
      ! empty($itemOutput)
      && is_string($itemOutput)
      && strpos($item->post_name, self::MENU_POST_NAME) !== false
    ) {
      $customer_portal_url = $this->get_customer_portal_auto_login_url();
      $itemOutput = '';
      if (!empty($customer_portal_url) && !is_admin()) {
        $itemOutput = '<a href="' . $customer_portal_url . '" target="__blank" class="elementor-item ' . $item->classes[0] . '">' . $item->title . '</a>';
      }
    }

    return $itemOutput;
  }


  public function nav_menu_links()
  {
    echo Utils_Core::get_template('menu-auto-login.php', array('post_name' => self::MENU_POST_NAME), dirname(__FILE__), '/templates');
  }
}
