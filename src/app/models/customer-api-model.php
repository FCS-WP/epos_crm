<?php

/**
 * API Args Handler
 *
 * @package Shin
 */

namespace EPOS_CRM\Src\App\Models;

defined('ABSPATH') or die();

class Customer_Api_Model
{


  public static function login_args()
  {
    return array(
      'phone_number' => array(
        'required' => true,
        'validate_callback' => function ($param) {
          return is_string($param) && preg_match('/^\d{10,15}$/', $param); // basic phone validation
        },
      ),
      'password' => array(
        'required' => true,
        'validate_callback' => function ($param) {
          return is_string($param) && strlen($param) >= 6;
        },
      ),
    );
  }
}
