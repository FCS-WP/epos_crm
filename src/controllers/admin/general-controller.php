<?php

/**
 * Admin Booking Controller
 *
 *
 */

namespace EPOS_CRM\Src\Controllers\Admin;

use WP_REST_Request;
use EPOS_CRM\Src\App\Zippy_Response_Handler;
use EPOS_CRM\Src\App\Models\Zippy_Request_Validation;

defined('ABSPATH') or die();



class General_Controller
{
  /**
   *
   * UPDATE CONFIG WORDPRESS OPTION
   *
   */

  public static function update_option_configs(WP_REST_Request $request)
  {
    // Define validation rules
    $required_fields = [
      "option_name" => ["data_type" => "array", "required" => true],
      "option_data" => ["data_type" => "array", "required" => true]
    ];

    // Validate request fields
    $validate = Zippy_Request_Validation::validate_request($required_fields, $request);

    if (!empty($validate)) {
      return Zippy_Response_Handler::error($validate);
    }

    // Get parameters and sanitize
    $query_param = [
      "option_name" => array_map('sanitize_text_field', $request->get_param('option_name')),
      "option_data" => array_map('maybe_serialize', $request->get_param('option_data')) // For complex data
    ];

    if (count($query_param['option_name']) !== count($query_param['option_data'])) {
      $message = 'Option names and Option values must have the same number of items.';
      return Zippy_Response_Handler::error($message);
    }

    $result = [];
    foreach ($query_param['option_name'] as $key => $name) {
      $value = $query_param['option_data'][$key];

      $update_result = update_option($name, $value, false);
      $result[$name] = $value;
    }

    // Prepare response
    $response = $result;
    $message = 'Update option successfully';

    return Zippy_Response_Handler::success($response, $message);
  }


  public static function get_option_configs(WP_REST_Request $request)
  {
    // Define validation rules
    $required_fields = [
      "option_name" => ["data_type" => "array", "required" => true],
    ];

    // Validate request fields
    $validate = Zippy_Request_Validation::validate_request($required_fields, $request);

    if (!empty($validate)) {
      return Zippy_Response_Handler::error($validate);
    }

    $option_names = array_map('sanitize_text_field', $request->get_param('option_name'));

    $data = [];

    foreach ($option_names as $option_name) {
      $data[$option_name] = get_option($option_name, null); // `null` as the default value
    }

    // Prepare response
    $response =  $data;
    return Zippy_Response_Handler::success($response);
  }

  public static function check_authentication(WP_REST_Request $request)
  {
    // Define validation rules
    $required_fields = [
      "option_name" => ["data_type" => "array", "required" => true],
    ];

    // Validate request fields
    $validate = Zippy_Request_Validation::validate_request($required_fields, $request);

    if (!empty($validate)) {
      return Zippy_Response_Handler::error($validate);
    }

    $option_names = array_map('sanitize_text_field', $request->get_param('option_name'));

    $data = [];

    foreach ($option_names as $option_name) {
      $data[$option_name] = get_option($option_name, null);
    }

    // Prepare response
    $response =  $data;
    return Zippy_Response_Handler::success($response);
  }
}
