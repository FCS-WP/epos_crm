<?php

/**
 * App Helper
 *
 * @package Shin
 */

namespace EPOS_CRM\Src\App\Helper;


defined('ABSPATH') or die();

class Handle_Response_Errors
{
  public static function V5_API_Error_Public($e, $errors)
  {
    $firstError = 'An error occurred';

    if (isset($errors['errors']) && is_array($errors['errors'])) {
      foreach ($errors['errors'] as $errorGroup) {
        if (is_array($errorGroup)) {
          foreach ($errorGroup as $error) {
            if (isset($error['detail'])) {
              $firstError = $error['detail'];
              break 2; // Break both loops
            }
          }
        }
      }
    }
    $response = array(
      'status' => $e->getResponse()->getStatusCode(),
      'errors' => $firstError,
    );
    return $response;
  }

  public static function V5_API_Error_Internal($e, $errors)
  {
    $firstError = 'An error occurred';

      if (isset($errors['errors']) && is_array($errors['errors'])) {
        foreach ($errors['errors'] as $errorGroup) {
          if (isset($errorGroup['detail'])) {
            $firstError = $errorGroup['detail'];
            break;
          }
        }
      }
      $response = array(
        'status' => $e->getResponse()->getStatusCode(),
        'errors' => $firstError,
      );
    return $response;
  }
}
