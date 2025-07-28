<?php

/**
 * Admin Booking Controller
 */

namespace EPOS_CRM\Src\Controllers\Orders;

use Exception;
use EPOS_CRM\Src\App\Models\Zippy_Request_Validation;
use EPOS_CRM\Src\App\Zippy_Response_Handler;
use EPOS_CRM\Utils\Woo_Session_Handler;
use EPOS_CRM\Src\Logs\EPOS_CRM_logger;

defined('ABSPATH') || exit;

class Epos_Orders_Controller
{
  /**
   * Handle point redemption request
   *
   * @param array $request
   * @return array
   */
  public static function epos_redeem($request)
  {
    try {
      $validationRules = [
        "point_used" => [
          "required" => true,
          "data_type" => "float",
        ],
      ];

      $errors = Zippy_Request_Validation::validate_request($validationRules, $request);

      if (!empty($errors)) {
        return Zippy_Response_Handler::error($errors, 'Validation failed.');
      }

      $point_used = sanitize_text_field($request['point_used'] ?? 0);
      $is_used_redeem = sanitize_text_field($request['is_used'] ?? false);
      $points = number_format((float)sanitize_text_field($request['points'] ?? 0), 2, '.', '');


      $session = new Woo_Session_Handler;
      $session->set('is_used_redeem', $is_used_redeem);
      $session->set('point_used', $point_used);
      $session->set('points', $points);

      return Zippy_Response_Handler::success([
        'message' => 'Point redeem applied successfully.',
        'data' => [
          'is_used' => $is_used_redeem,
          'point_used' => $point_used,
          'points' => $points,
        ]
      ]);
    } catch (Exception $e) {
      EPOS_CRM_logger::log_response($e->getMessage(), $request);

      return Zippy_Response_Handler::error(
        ['message' => 'An unexpected error occurred during redeem.'],
        'internal_error'
      );
    }
  }
}
