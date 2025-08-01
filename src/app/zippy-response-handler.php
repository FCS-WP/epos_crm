<?php

/**
 * API ResponHandler
 *
 * @package Shin
 */

namespace EPOS_CRM\Src\App;

defined('ABSPATH') or die();

use WP_REST_Response;

class Zippy_Response_Handler
{
  // Handle success responses
  public static function success($data, $message = EPOS_CRM_SUCCESS, $status_code = 200)
  {
    return new WP_REST_Response(array(
      'status' => 'success',
      'message' => $message,
      "data" => $data
    ), $status_code);
  }

  // Handle error responses
  public static function error($message = EPOS_CRM_ERROR, $status_code = 400)
  {
    return new WP_REST_Response(array(
      'status' => 'error',
      'message' => $message,
    ), $status_code);
  }

  // Handle custom responses
  public static function custom($status, $message, $data = null, $status_code = 200)
  {
    $response = array(
      'status' => $status,
      'message' => $message,
    );

    if (!is_null($data)) {
      $response['data'] = $data;
    }

    return new WP_REST_Response($response, $status_code);
  }
}
