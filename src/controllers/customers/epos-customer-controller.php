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
use EPOS_CRM\Utils\EPOS_Helper;
use Exception;

defined('ABSPATH') or die();



class Epos_Customer_controller
{

  private static $client;
  private static $customer_key = 'epos_customer_data';
  private static $customer_id = 'epos_customer_id';
  private static $phone_number = 'epos_phone_number';
  private static $member_id = 'epos_member_id';
  private static $epos_customer_token = 'epos_customer_token';

  private static function init_client_internal()
  {
    if (self::$client === null) {
      self::$client = new Client([
        'base_uri' => get_option('epos_be_url'),
        'timeout'  => 10,
      ]);
    }
  }
  // Init instance Class
  private static function init_client_public()
  {
    if (self::$client === null) {
      self::$client = new Client([
        'base_uri' => EPOS_CRM_URL_SERVICE,
        'timeout'  => 10,
      ]);
    }
  }

  // Login Controller
  public static function login($request)
  {
    $response = self::loginAPI($request);

    if ($response['status'] == 'success') {
      $data = !empty($response['data']) ? $response['data'] : null;
      $rawToken = !empty($response['authorization']) ? $response['authorization'] : null;
      $email = !empty($data->attributes->email) ? $data->attributes->email : '';
      $phone = !empty($data->attributes->phone_number) ? $data->attributes->phone_number : '';
      $is_validEmail = EPOS_Helper::isValidEmail($email);
      $epos_customer_token = self::getTokenAutoLogin($rawToken);
      if ($is_validEmail) {
        $session = new Woo_Session_Handler;
        $session->init_session();
        $session->set(self::$customer_key, $data->attributes);
        $session->set(self::$customer_id, $data->id);
        $session->set(self::$member_id, $data->attributes->member_id);
        $session->set(self::$phone_number, $phone);
        $session->set(self::$epos_customer_token, $epos_customer_token);
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
        $session->init_session();
        $session->set(self::$customer_key, $data);
        $session->set(self::$customer_id, $data->member_id);
      }
    }

    return $response;
  }

  // Register Controller
  public static function register($request)
  {
    self::init_client_public();

    try {
      $validationRules = [
        "phone_number" => [
          "required" => true,
          "data_type" => "string",
          "validation" => "phone"
        ],
        "phone_code" => [
          "required" => true,
          "data_type" => "string"
        ],
        "email" => [
          "required" => true,
          "data_type" => "string",
          "validation" => "email"
        ],
        "password" => [
          "required" => true,
          "data_type" => "string",
          "min_length" => 8,

        ],
        "full_name" => [
          "required" => true,
          "data_type" => "string",
          "min_length" => 2
        ],
        "address_street_1" => [
          "required" => true,
          "data_type" => "string"
        ],
        "address_street_2" => [
          "required" => false,
          "data_type" => "string"
        ],
        "address_country" => [
          "required" => true,
          "data_type" => "string"
        ],
        "address_city" => [
          "required" => false,
          "data_type" => "string"
        ],
        "address_postal_code" => [
          "required" => true,
          "data_type" => "string"
        ],
      ];

      // Validate Request Fields
      $validationErrors = Zippy_Request_Validation::validate_request($validationRules, $request);

      if (!empty($validationErrors)) {
        return Zippy_Response_Handler::error($validationErrors);
      }

      $customer_data = [
        "full_name" => sanitize_text_field($request["full_name"]),
        "email" => sanitize_email($request["email"]),
        "phone_code" => sanitize_text_field($request["phone_code"]),
        "phone_number" => sanitize_text_field($request["phone_number"]),
        "password" => sanitize_text_field($request["password"]),
        "address_street_1" => sanitize_text_field($request["address_street_1"]),
        "address_street_2" => sanitize_text_field($request["address_street_2"]),
        "address_country" => sanitize_text_field($request["address_country"]),
        "address_city" => sanitize_text_field($request["address_city"]),
        "address_postal_code" => sanitize_text_field($request["address_postal_code"])
      ];

      $params = [
        "customer" => $customer_data
      ];

      $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => get_option('epos_crm_token_key')
      ];

      $options = [
        'headers' => $headers,
        'json' => $params,
      ];

      $login = self::$client->post("/api/v1/customers", $options);

      return [
        'status' => 'success',
        'message' => 'Register successfully',
        'data' => json_decode($login->getBody())
      ];
    } catch (ClientException $e) {
      $responseBody = $e->getResponse()->getBody()->getContents();
      $errors = json_decode($responseBody, true);
      $response = Handle_Response_Errors::V5_API_Error_Public($e, $errors);

      return $response;
    } catch (ConnectException $e) {
      return [
        'status' => 'error',
        'message' => 'Registration service is currently unavailable. Please try again later.',
        'error_code' => 'service_unavailable',
      ];
    } catch (Exception $e) {
      return [
        'status' => 'error',
        'message' => 'An unexpected error occurred during registration.',
        'error_code' => 'internal_error',
      ];
    }
  }

  // Get Controller
  public static function get($request)
  {
    $response = self::getCustomerInfor();

    if ($response['status'] == 'success') {
      $data = !empty($response['data']) ? $response['data']->customers[0] : null;
      $response['data'] = $data;
      if (!empty($data)) {
        $session = new Woo_Session_Handler;
        $session->set(self::$customer_key, $data);
        $session->set(self::$customer_id, $data->id);
        $session->set(self::$member_id, $data->member_id);
        $session->set(self::$phone_number, $data->phone_number);
      }
    }

    return $response;
  }

  // Logout Controller

  public static function logout($request)
  {
    try {
      $session = new Woo_Session_Handler;
      $session->destroy('epos_customer_data');
      $session->destroy('epos_customer_id');
    } catch (\Throwable $th) {
      return [
        'status' => 'error',
        'message' => 'An unexpected error occurred during logout.',
        'error_code' => 'internal_error',
      ];
    }
  }


  /**  API Callback  */
  private static function updateUserAPI($request)
  {
    self::init_client_internal();

    try {


      $validationRules = [
        "id" => [
          "required" => true,
          "data_type" => "string",
        ],
        "customer" => [
          "required" => true,
          "data_type" => "array",

        ],
      ];

      // Validate Request Fields
      $validate = Zippy_Request_Validation::validate_request($validationRules, $request);

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

      $options = [
        'headers' => $headers,
        'json' => $params,
      ];

      $update = self::$client->patch('/api/v1/customers/' . $id_user, $options);

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
    } catch (Exception $e) {
      return [
        'status' => 'error',
        'message' => 'An unexpected error occurred during registration.',
        'error_code' => 'internal_error',
      ];
    }
    return $response;
  }

  private static function loginAPI($request)
  {
    self::init_client_internal();

    try {
      $validationRules = [
        "phone_number" => [
          "required" => true,
          "data_type" => "string",
          "validation" => "phone"
        ],
        "password" => [
          "required" => true,
          "data_type" => "string",
          "min_length" => 8,

        ],
      ];

      // Validate Request Fields
      $validate = Zippy_Request_Validation::validate_request($validationRules, $request);

      if (!empty($validate)) {
        return Zippy_Response_Handler::error($validate);
      }

      $login_data = [
        'phone_number' => sanitize_text_field($request["phone_number"]),
        'password' => sanitize_text_field($request["password"]),
      ];

      $params = [
        "data" => [
          "attributes" => $login_data
        ]
      ];

      $headers = [
        'Content-Type' => 'application/vnd.api+json',
        'Accept' => 'application/vnd.api+json',
      ];

      $options = [
        'headers' => $headers,
        'json' => $params,
      ];

      $login = self::$client->post("api/modules/woocommerce/customers/login", $options);

      // var_dump($login);

      $response = array(
        'status' => 'success',
        'message' => 'Login successfully',
        'data' => json_decode($login->getBody())->data,
        'authorization' => $login->getHeaders()['Authorization']
      );

      return $response;
    } catch (ClientException $e) {
      $responseBody = $e->getResponse()->getBody()->getContents();
      $errors = json_decode($responseBody, true);
      $response = Handle_Response_Errors::V5_API_Error_Internal($e, $errors);
      return $response;
    } catch (ConnectException $e) {
      return [
        'status' => false,
        'message' => 'Login service is currently unavailable. Please try again later.',
        'error_code' => 'service_unavailable',
      ];
    } catch (Exception $e) {
      return [
        'status' => 'error',
        'message' => 'An unexpected error occurred during registration.',
        'error_code' => 'internal_error',
      ];
    }
  }

  private static function getCustomerInfor()
  {
    self::init_client_public();

    try {

      $session = new Woo_Session_Handler;

      $phone_number = $session->get(self::$phone_number);

      $params = [
        "phone_number" => sanitize_text_field($phone_number),
        "exclude_deleted" => 'true',
      ];

      $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => get_option('epos_crm_token_key'),
      ];

      $options = [
        'headers' => $headers,
        'query' => $params
      ];

      $customers = self::$client->get("/api/v1/customers", $options);

      return [
        'status' => 'success',
        'message' => 'Get Customer successfully',
        'data' => json_decode($customers->getBody())
      ];
    } catch (ClientException $e) {
      $responseBody = $e->getResponse()->getBody()->getContents();
      $errors = json_decode($responseBody, true);
      $response = Handle_Response_Errors::V5_API_Error_Public($e, $errors);

      return $response;
    } catch (ConnectException $e) {
      return [
        'status' => 'error',
        'message' => 'Customer service is currently unavailable. Please try again later.',
        'error_code' => 'service_unavailable',
      ];
    } catch (Exception $e) {
      return [
        'status' => 'error',
        'message' => 'An unexpected error occurred during get customer information.',
        'error_code' => 'internal_error',
      ];
    }
  }

  private static function getTokenAutoLogin($rawToken)
  {


    if (empty($rawToken)) return null;

    return array_shift(array_values(str_replace('Bearer ', '', $rawToken)));;
  }
}
