<?php

namespace EPOS_CRM\Src\Routers\Orders;

/**
 * EPOS Order Router
 *
 *
 */

defined('ABSPATH') or die();

use EPOS_CRM\Src\Controllers\Orders\Epos_Orders_Controller;

use EPOS_CRM\Src\App\Models\Order_Api_Model;

use EPOS_CRM\Src\Middleware\Admin\Epos_Crm_Permission;

class Orders_Routers
{

  protected static $_instance = null;

  /**
   * @return Orders_Routers
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
    add_action('rest_api_init', array($this, 'order_init_api'));
  }

  public function order_init_api()
  {
    register_rest_route(EPOS_CRM_API_NAMESPACE, '/epos-redeem', array(
      'methods' => 'POST',
      'callback' => [Epos_Orders_Controller::class, 'epos_redeem'],
      'args' => Order_Api_Model::redeem_args(),
      'permission_callback' => array(Epos_Crm_Permission::class, 'zippy_permission_callback'),

    ));
  }
}
