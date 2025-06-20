<?php

/**
 * Woocommerce Session Handler
 *
 * @package Shin
 */

namespace EPOS_CRM\Utils;

defined('ABSPATH') || exit;

use WC_Session_Handler;

class Woo_Session_Handler
{
  public function __construct()
  {
    if (!WC()->session) {
      WC()->session = new WC_Session_Handler();
      WC()->session->init();
    }
  }

  /**
   * Set session data
   *
   * @param string $key
   * @param mixed $value
   */
  public function set($key, $value)
  {
    WC()->session->set($key, $value);
  }

  /**
   * Get session data
   *
   * @param string $key
   * @param mixed $default Optional default value if not found.
   * @return mixed|null
   */
  public function get($key, $default = null)
  {
    $value = WC()->session->get($key);
    return $value !== null ? $value : $default;
  }

  /**
   * Destroy a session key
   *
   * @param string $key
   */
  public function destroy($key)
  {
    WC()->session->__unset($key);
  }
}
