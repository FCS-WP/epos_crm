<?php

/**
 * Admin Booking Controller
 *
 *
 */

namespace EPOS_CRM\Src\Controllers\Auth;

use EPOS_CRM\Utils\Utils_Core;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

defined('ABSPATH') or die();



class Epos_Auth_controller
{


  /**
   * @var Client
   */
  private static $client;
  private static $code;

  /**
   * ZIPPY_Settings_Api constructor.
   */

  public function __construct($code)
  {
    self::$client = new Client([
      'base_uri' => EPOS_CRM_URL_SERVICE,
      'timeout'  => 6,
    ]);
    self::$code = $code;
  }
  public static function Auth()
  {
    $domain = get_option('epos_be_url', null);
    $params = array(
      "code" => self::$code,
      "subdomain" => Utils_Core::get_subdomain($domain),
      "client_secret" => Utils_Core::loadEnv('EPOS_CLIENT_KEY')
    );

    if (self::$client === null) {
      new self(self::$code);
    }
    try {

      $auth = self::$client->post("connect/token/", ['json' => $params]);

      $response = array(
        'status' => true,
        'message' => 'Authentication successfully',
        'data' => json_decode($auth->getBody())
      );
    } catch (ClientException $e) {
      $response = array(
        'status' => $e->getResponse()->getStatusCode(),
        'message' => 'Authenticationfailed',
      );
    } catch (ConnectException $e) {
      $response = array(
        'status' => false,
        'message' => 'Authentication failed',
      );
    }
    return $response;
  }
}
