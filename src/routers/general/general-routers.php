<?php

namespace EPOS_CRM\Src\Routers\General;

/**
 * Bookings General Router
 *
 *
 */

defined('ABSPATH') or die();

use EPOS_CRM\Src\Controllers\Admin\General_Controller;

use EPOS_CRM\Src\App\Models\General_Api_Model;

use EPOS_CRM\Src\Middleware\Admin\Epos_Crm_Permission;

class General_Routers
{

  protected static $_instance = null;

  /**
   * @return General_Routers
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
    add_action('rest_api_init', array($this, 'general_init_api'));
  }

  public function general_init_api()
  {
    register_rest_route(EPOS_CRM_API_NAMESPACE, '/update-options', array(
      'methods' => 'POST',
      'callback' => [General_Controller::class, 'update_option_configs'],
      'args' => General_Api_Model::update_option_args(),
      'permission_callback' => array(Epos_Crm_Permission::class, 'zippy_permission_callback'),

    ));

    register_rest_route(EPOS_CRM_API_NAMESPACE, '/get-options', array(
      'methods' => 'POST',
      'callback' => [General_Controller::class, 'get_option_configs'],
      'args' => General_Api_Model::get_option_args(),
      'permission_callback' => array(Epos_Crm_Permission::class, 'zippy_permission_callback'),

    ));

    register_rest_route(EPOS_CRM_API_NAMESPACE, '/auth', array(
      'methods' => 'POST',
      'callback' => [General_Controller::class, 'check_authentication'],
      'args' => General_Api_Model::get_option_args(),
      'permission_callback' => "__return_true",

    ));

    // register_rest_route(EPOS_CRM_API_NAMESPACE, '/zippy-register', array(
    //   'methods' => 'POST',
    //   'callback' => [General_Controller::class, 'register'],
    //   'args' => General_Api_Model::register_args(),
    //   'permission_callback' => "__return_true",
    // ));
  }
}
