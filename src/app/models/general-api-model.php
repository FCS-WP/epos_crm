<?php

/**
 * API Args Handler
 *
 * @package Shin
 */

namespace EPOS_CRM\Src\App\Models;

defined('ABSPATH') or die();

use WP_REST_Response;

class General_Api_Model
{

  public static function update_option_args()
  {
    return array(
      'option_name' => array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
          return is_array($param);
        },
      ),
      'option_data' => array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
          return is_array($param);
        },
      ),
    );
  }

  public static function get_option_args()
  {
    return array(
      'option_name' => array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
          return is_array($param);
        },
      ),
    );
  }
}
