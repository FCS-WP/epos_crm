<?php

namespace EPOS_CRM\Src\Woocommerce\Checkout;

defined('ABSPATH') or die();

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use EPOS_CRM\Src\Logs\EPOS_CRM_logger;
use EPOS_CRM\Src\App\Helper\Handle_Response_Errors;
use EPOS_CRM\Utils\Utils_Core;


class Epos_Crm_Refund_Process
{
  private static $client;

  public function __construct()
  {

    $this->client = new Client([
      'base_uri' => EPOS_CRM_URL_SERVICE,
      'timeout'  => 6,
    ]);
  }


  // Register Controller
  public function API_refund_process($request)
  {

    try {
      $id = Utils_Core::create_guid();

      $transacted_at =  gmdate('Y-m-d\TH:i:s\Z');

      $refund_request = [
        "id"         => $id,
        "redemption_id"         => sanitize_text_field($request["redemption_id"]),
        "member_id"       => sanitize_text_field($request["member_id"]),
        "order_id"        => sanitize_text_field($request["order_id"]),
        "transacted_at"        => $transacted_at
      ];

      $params = [
        "points_refund" => $refund_request
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

      $refund_response = $this->client->post("/api/v1/point_transactions/redemptions", $options);

      return [
        'status' => 'success',
        'message' => 'Refund successfully',
        'data' => json_decode($refund_response->getBody())
      ];
    } catch (ClientException $e) {
      $responseBody = $e->getResponse()->getBody()->getContents();
      $errors = json_decode($responseBody, true);
      $response = Handle_Response_Errors::V5_API_Error_Public($e, $errors);
      EPOS_CRM_logger::log_response("Refund_Error", $response);
      return $response;
    } catch (ConnectException $e) {
      EPOS_CRM_logger::log_response("Refund_Error", $e);

      return [
        'status' => 'error',
        'message' => 'Membership service is currently unavailable. Please try again later.',
        'error_code' => 'service_unavailable',
      ];
    } catch (Exception $e) {
      EPOS_CRM_logger::log_response("Refund_Error", 'internal_error');

      return [
        'status' => 'error',
        'message' => 'An unexpected error occurred during registration.',
        'error_code' => 'internal_error',
      ];
    }
  }
}
