<?php

namespace EPOS_CRM\Src\Routers\Customers;

/**
 * Bookings General Router
 *
 *
 */

defined('ABSPATH') or die();

use EPOS_CRM\Src\Controllers\Customers\Epos_Customer_controller;

use EPOS_CRM\Src\App\Models\Customer_Api_Model;

use EPOS_CRM\Src\Middleware\Admin\Epos_Crm_Permission;

class Customer_Routers
{

  protected static $_instance = null;

  /**
   * @return Customer_Routers
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
    add_action('rest_api_init', array($this, 'customer_init_api'));
  }

  public function customer_init_api()
  {
    register_rest_route(EPOS_CRM_API_NAMESPACE, '/customers/login', array(
      'methods' => 'POST',
      'callback' => [Epos_Customer_controller::class, 'login'],
      'args' => Customer_Api_Model::login_args(),
      'permission_callback' => array(Epos_Crm_Permission::class, 'zippy_permission_callback'),

    ));
  }
}
