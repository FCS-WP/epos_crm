<?php

namespace EPOS_CRM\Src\Woocommerce\Checkout;

defined('ABSPATH') or die();

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use EPOS_CRM\Src\Logs\EPOS_CRM_logger;
use EPOS_CRM\Src\App\Helper\Handle_Response_Errors;


class Epos_Crm_Redeem_Process
{
  private static $client;

  public function __construct()
  {

    $this->client = new Client([
      'base_uri' => EPOS_CRM_URL_SERVICE,
      'timeout'  => 10,
    ]);
  }


  // Register Controller
  public function API_redeem_process($request)
  {

    try {


      $redeem_request = [
        "id"              => sanitize_text_field($request["id"]),
        "order_id"        => sanitize_text_field($request["order_id"]),
        "member_id"       => sanitize_text_field($request["member_id"]),
        "points"          => (float)$request["points"],
        "transacted_at"   => sanitize_text_field($request["transacted_at"]),
        "tier_id"         => sanitize_text_field($request["tier_id"]),
        "conversion_rate" => (float) $request["conversion_rate"],
      ];

      $params = [
        "points_spend" => $redeem_request
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

      $redeem_response = $this->client->post("/api/v1/point_transactions/redemptions", $options);

      return [
        'status' => 'success',
        'message' => 'Redeem successfully',
        'data' => json_decode($redeem_response->getBody())
      ];
    } catch (ClientException $e) {
      $responseBody = $e->getResponse()->getBody()->getContents();
      $errors = json_decode($responseBody, true);
      $response = Handle_Response_Errors::V5_API_Error_Public($e, $errors);
      EPOS_CRM_logger::log_response("Redeem_Error", $response);
      return $response;
    } catch (ConnectException $e) {
      EPOS_CRM_logger::log_response("Redeem_Error", $e->getMessage());

      return [
        'status' => 'error',
        'message' => 'Membership service is currently unavailable. Please try again later.',
        'error_code' => 'service_unavailable',
      ];
    } catch (Exception $e) {
      EPOS_CRM_logger::log_response("Redeem_Error", 'internal_error');

      return [
        'status' => 'error',
        'message' => 'An unexpected error occurred during registration.',
        'error_code' => 'internal_error',
      ];
    }
  }
}
