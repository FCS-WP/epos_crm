<?php

/**
 * WooCommerce Session Handler
 *
 * @package Shin
 */

namespace EPOS_CRM\Utils;

defined('ABSPATH') || exit;

use WC_Session_Handler;

class Woo_Session_Handler
{


  /**
   * Initialize WooCommerce session.
   */
  public function init_session()
  {
    // if (!WC()->session) {
    WC()->session = new WC_Session_Handler();
    WC()->session->init();
    WC()->session->set_customer_session_cookie(true);

    // var_dump(WC()->session);
    // }
  }

  /**
   * Set session data.
   *
   * @param string $key
   * @param mixed $value
   */
  public function set($key, $value)
  {
    WC()->session->__set($key, $value);
  }

  /**
   * Get session data.
   *
   * @param string $key
   * @param mixed $default Optional default value if not found.
   * @return mixed|null
   */
  public function get($key, $default = null)
  {
    $value = WC()->session->__get($key);
    return $value !== null ? $value : $default;
  }

  /**
   * Delete a specific session key.
   *
   * @param string $key
   */
  public function destroy($key)
  {
    if (WC()->session) {
      WC()->session->__unset($key);
    }
  }

  /**
   * Delete the entire customer session from cache and DB.
   */
  public function delete_session()
  {
    if (WC()->session) {
      // var_dump($this->_customer_id);
      WC()->session->__unset('epos_customer_data');
      WC()->session->__unset('epos_customer_id');
    }
  }
}
