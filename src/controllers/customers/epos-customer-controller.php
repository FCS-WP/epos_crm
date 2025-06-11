<?php

/**
 * Admin Booking Controller
 *
 *
 */

namespace EPOS_CRM\Src\Controllers\Customers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use EPOS_CRM\Src\App\Models\Zippy_Request_Validation;
use EPOS_CRM\Src\App\Zippy_Response_Handler;
use EPOS_CRM\Utils\Woo_Session_Handler;
use EPOS_CRM\Src\App\Helper\Handle_Response_Errors;

defined('ABSPATH') or die();



class Epos_Customer_controller
{

  private static $client;
  private static $customer_key = 'epos_customer_data';
  private static $customer_id = 'epos_customer_id';

  private static function init_client_internal()
  {
    if (self::$client === null) {
      self::$client = new Client([
        'base_uri' => get_option('epos_be_url'),
        'timeout'  => 6,
      ]);
    }
  }
  // Init instance Class
  private static function init_client_public()
  {
    if (self::$client === null) {
      self::$client = new Client([
        'base_uri' => 'https://livedevs.com',
        'timeout'  => 6,
      ]);
    }
  }

  // Login Controller
  public static function login($request)
  {
    $response = self::loginAPI($request);

    if ($response['status'] == 'success') {
      $data = !empty($response['data']) ? $response['data'] : null;
      if (!empty($data->attributes->email)) {
        $session = new Woo_Session_Handler;
        $session->set(self::$customer_key, $data->attributes);
        $session->set(self::$customer_id, $data->id);
      }
    }

    return $response;
  }

  // Update Controller
  public static function update($request)
  {
    $response = self::updateUserAPI($request);

    if ($response['status'] == 'success') {
      $data = !empty($response['data']) ? $response['data'] : null;
      if (!empty($data)) {
        $session = new Woo_Session_Handler;
        $session->set(self::$customer_key, $data);
        $session->set(self::$customer_id, $data->id);

      }
    }

    return $response;
  }

  // Register Controller
  public static function register($request)
  {
    self::init_client_public();

    try {
      $required_fields = [
        "phone_number" => ["required" => true, "data_type" => "string"],
        "phone_code" => ["required" => true, "data_type" => "string"],
        "email" => ["required" => true, "data_type" => "string"],
        "password" => ["required" => true, "data_type" => "string"],
        "full_name" => ["required" => true, "data_type" => "string"],
        "address_street_1" => ["required" => false, "data_type" => "string"],
      ];

      // Validate Request Fields
      $validate = Zippy_Request_Validation::validate_request($required_fields, $request);

      if (!empty($validate)) {
        return Zippy_Response_Handler::error($validate);
      }

      $params = [
        "customer" => [
          "full_name" => sanitize_text_field($request["full_name"]),
          "email" => sanitize_text_field($request["email"]),
          "phone_code" => sanitize_text_field($request["phone_code"]),
          "phone_number" => sanitize_text_field($request["phone_number"]),
          "address_street_1" => sanitize_text_field($request["address_street_1"]),
          "password" => sanitize_text_field($request["password"])
        ]
      ];

      $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => get_option('epos_crm_token_key')
      ];

      $login = self::$client->post("/api/v1/customers", ['headers' => $headers, 'json' => $params]);

      $response = array(
        'status' => 'success',
        'message' => 'Register successfully',
        'data' => json_decode($login->getBody())
      );
    } catch (ClientException $e) {
      $responseBody = $e->getResponse()->getBody()->getContents();
      $errors = json_decode($responseBody, true);
      $response = Handle_Response_Errors::V5_API_Error_Public($e, $errors);
    } catch (ConnectException $e) {
      $response = array(
        'status' => false,
        'message' => 'Register failed',
      );
    }
    return $response;
  }


  /**  API Callback  */
  private static function updateUserAPI($request)
  {
    self::init_client_internal();

    try {
      $required_fields = [
        "id" => ["required" => true, "data_type" => "string"],
        "customer" => ["required" => true, "data_type" => "array"],
      ];

      // Validate Request Fields
      $validate = Zippy_Request_Validation::validate_request($required_fields, $request);

      if (!empty($validate)) {
        return Zippy_Response_Handler::error($validate);
      }

      $params = [
        "customer" => $request['customer']
      ];
      $id_user = sanitize_text_field($request["id"]);
      $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => get_option('epos_crm_token_key'),
      ];

      $update = self::$client->patch('/api/v1/customers/' . $id_user, ['headers' => $headers, 'json' => $params]);
      $response = array(
        'status' => 'success',
        'message' => 'Update successfully',
        'data' => json_decode($update->getBody())->customer
      );
    } catch (ClientException $e) {
      $responseBody = $e->getResponse()->getBody()->getContents();
      $errors = json_decode($responseBody, true);
      $response = Handle_Response_Errors::V5_API_Error_Public($e, $errors);
    } catch (ConnectException $e) {
      $response = array(
        'status' => false,
        'message' => 'Update failed',
      );
    }
    return $response;
  }

  private static function loginAPI($request)
  {
    self::init_client_internal();

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
      $responseBody = $e->getResponse()->getBody()->getContents();
      $errors = json_decode($responseBody, true);
      $response = Handle_Response_Errors::V5_API_Error_Internal($e, $errors);
    } catch (ConnectException $e) {
      $response = array(
        'status' => false,
        'message' => 'Login failed',
      );
    }
    return $response;
  }
}
