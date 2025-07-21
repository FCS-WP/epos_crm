<?php

/**
 * API Args Handler
 *
 * @package Shin
 */

namespace EPOS_CRM\Src\App\Models;

defined('ABSPATH') or die();

use WP_REST_Response;

class Order_Api_Model
{


  public static function redeem_args()
  {
    return array(
      'point_use' => array(
        'required' => true,
        'validate_callback' => function ($param, $request, $key) {
          return is_numeric($param);
        },
      ),
      // 'option_data' => array(
      //   'required' => true,
      //   'validate_callback' => function ($param, $request, $key) {
      //     return is_array($param);
      //   },
      // ),
    );
  }
}
