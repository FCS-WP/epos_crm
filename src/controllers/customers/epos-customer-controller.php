<?php

/**
 * Admin Booking Controller
 *
 *
 */

namespace EPOS_CRM\Src\Controllers\Customers;

use EPOS_CRM\Utils\Utils_Core;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use EPOS_CRM\Src\App\Models\Zippy_Request_Validation;
use EPOS_CRM\Utils\Woo_Session_Handler;

defined('ABSPATH') or die();



class Epos_Customer_controller
{


  private static $client;
  private static $customer_key = 'epos_customer_data';

  private static function init_client()
  {
    if (self::$client === null) {
      self::$client = new Client([
        'base_uri' => get_option('epos_be_url'),
        'timeout'  => 6,
      ]);
    }
  }

  public static function login($request)
  {
    $response = self::loginAPI($request);

    if ($response['status'] == 'success') {
      $session = new Woo_Session_Handler;
      $session->set(self::$customer_key, $response['data']);
    }

    return $response;
  }

  private static function loginAPI($request)
  {
    self::init_client();

    try {
      $required_fields = [
        "phone_number" => ["required" => true, "data_type" => "string"],
        "password" => ["required" => true, "data_type" => "string"],
      ];

      // Validate Request Fields
      $validate = Zippy_Request_Validation::validate_request($required_fields, $request);

      if (!empty($validate)) {
        return Zippy_Response_Handler::error($validate);
      }

      $params = [
        "data" => [
          "attributes" => [
            'phone_number' => sanitize_text_field($request["phone_number"]),
            'password' => sanitize_text_field($request["password"]),
          ]
        ]
      ];

      $headers = [
        'Content-Type' => 'application/vnd.api+json',
        'Accept' => 'application/vnd.api+json',
      ];

      $login = self::$client->post("api/modules/woocommerce/customers/login", ['headers' => $headers, 'json' => $params]);

      $response = array(
        'status' => 'success',
        'message' => 'Login successfully',
        'data' => json_decode($login->getBody())->data
      );
    } catch (ClientException $e) {
      $response = array(
        'status' => $e->getResponse()->getStatusCode(),
        'message' => 'Login failed',
      );
    } catch (ConnectException $e) {
      $response = array(
        'status' => false,
        'message' => 'Login failed',
      );
    }
    return $response;
  }
}
