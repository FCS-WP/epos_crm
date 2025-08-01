<?php

/**
 * Admin Setting
 *
 * @package Shin
 */

namespace EPOS_CRM\Utils;

defined('ABSPATH') or die();

class Utils_Core
{
  public static function check_exits_woocommerce()
  {
    if (!function_exists('is_plugin_active')) {

      include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    if (!is_plugin_active('woocommerce/woocommerce.php')) return false;

    return true;
  }

  public static function check_is_active_feature($key)
  {
    $is_active = '';

    $is_active = get_option($key);

    if (empty($is_active) || $is_active == 0) return false;

    return true;
  }
  public static function encrypt_data_input($input)
  {
    $encryption_key = EPOS_CRM_PREFIX;
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $input_data = openssl_encrypt($input, 'aes-256-cbc', $encryption_key, 0, $iv);
    $input_data_with_iv = base64_encode($iv . '::' . $input_data);
    return $input_data_with_iv;
  }

  public static function decrypt_data_input($data_encryption)
  {

    if (!isset($data_encryption) || empty($data_encryption)) return false;

    $encryption_key = EPOS_CRM_PREFIX;

    list($iv, $data) = explode('::', base64_decode($data_encryption), 2);

    $data_descypt = openssl_decrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);

    return $data_descypt;
  }

  /**
   * Recursive sanitation for an array
   *
   * @param $array
   *
   * @return mixed
   */
  public static function recursive_sanitize_text_field($array)
  {
    foreach ($array as $key => &$value) {
      if (is_array($value)) {
        $value = self::recursive_sanitize_text_field($value);
      } else {
        $value = sanitize_text_field($value);
      }
    }

    return $array;
  }

  public static function separator()
  {
    return [
      'title'       => '',
      'type'        => 'title',
      'description' => '<hr>'
    ];
  }

  public static function divider()
  {
    return array(
      'id'          => EPOS_CRM_PREFIX . '_divider',
      'name'       => __('', EPOS_CRM_PREFIX . 'woocommerce-settings-tab'),
      'type'        => 'title',
      'desc' => '<hr>'
    );
  }

  public static function get_subdomain($url)
  {
    $host = parse_url($url, PHP_URL_HOST);
    $parts = explode('.', $host);
    if (count($parts) > 2) {
      return $parts[0];
    }
    return null; // No subdomain found
  }


  public static function loadEnv($env_name)
  {
    $file = EPOS_CRM_DIR_PATH . '/.env';
    if (!file_exists($file)) {
      return false;
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
      if (strpos(trim($line), '#') === 0) continue;

      list($name, $value) = explode('=', $line, 2);
      $name = trim($name);
      $value = trim($value);

      // Remove surrounding quotes
      $value = trim($value, "'\"");

      $env[$name] = $value;
    }

    return isset($env[$env_name]) ?  $env[$env_name] : '';
  }
  /**
   * Gets the client IP
   *
   * @return string
   */
  public static function get_client_ip()
  {

    $ip = $_SERVER['REMOTE_ADDR'];

    if (empty($ip)) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }

    if (empty($ip)) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    return $ip; //'192.168.0.1'
  }

  /**
   * Get the Locale
   *
   * @return string
   */

  public static function get_locale()
  {
    return str_replace(['_informal', '_formal'], '', get_locale());
  }
  /**
   * Get the userAgent
   *
   * @return string
   */

  public static function get_user_agent()
  {
    return $_SERVER['HTTP_USER_AGENT'];
  }

  /**
   * Get the userAgent
   *
   * @return string
   */

  public static function get_http_accept()
  {
    return $_SERVER['HTTP_ACCEPT'];
  }

  /**
   * Get the userAgent
   *
   * @return string
   */

  public static function get_reference($order_id)
  {
    return $_SERVER['HTTP_HOST'] . '_' . $order_id;
  }

  public static function get_domain_name()
  {
    $original_url = "https://" . $_SERVER['SERVER_NAME'];
    $pieces = parse_url($original_url);
    $domain = isset($pieces['host']) ? $pieces['host'] : '';
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain,    $regs)) {
      $domain = strstr($regs['domain'], '.', true);
    }
    return $domain;
  }

  /**
   * Retrieves the shop domain used for generating origin keys.
   *
   * @return string
   */
  public static function get_origin_domain()
  {

    $incl_port = get_option('incl_server_port', 'yes');
    $protocol  = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'https://';
    $port      = in_array($_SERVER['SERVER_PORT'], ['80', '443']) ? '' : ':' . $_SERVER['SERVER_PORT'];
    $domain    = 'yes' === $incl_port ? $protocol . $_SERVER['HTTP_HOST'] . $port : $protocol . $_SERVER['HTTP_HOST'];

    return $domain;
  }

  public static function get_merchant_reference($order_key)
  {

    return md5($order_key . '_' . self::get_origin_domain());
  }

  /**
   * Gets content of a given path file.
   *
   * @param string $template_name - the end part of the file path including the template name
   * @param array $vars - arguments passed to the template
   * @param string $absolute_path - plugin's DIR_PATH
   * @param mixed $relative_path - relative path added between the absolute path and the template name
   * @return string $content
   */
  public static function get_template($template_name, $vars = array(), $absolute_path = '', $relative_path = '')
  {

    extract($vars);

    $content = '';

    $template_name = empty($absolute_path) && empty($relative_path) ? $template_name : trim($template_name, "/\\");
    $absolute_path = empty($absolute_path) ? '' : trailingslashit($absolute_path);
    $relative_path = empty($relative_path) ? '' : trailingslashit(trim($relative_path, "/\\"));

    $template = $absolute_path . $relative_path . $template_name;

    //check for template in plugin's folder `includes/`
    if (file_exists(EPOS_CRM_DIR_PATH . $relative_path . $template_name)) {
      $template = EPOS_CRM_DIR_PATH . $relative_path . $template_name;
    }

    $template = apply_filters_deprecated(EPOS_CRM_PREFIX . '\util\get_template\path_file', [$template, $vars], '1.0.0', EPOS_CRM_PREFIX . '\util\get_template\template');
    $template = apply_filters(EPOS_CRM_PREFIX . '\util\get_template\template', $template, $template_name, $absolute_path, $relative_path);

    if (file_exists($template)) {

      ob_start();

      include $template;

      $content = ob_get_clean();
    }

    return $content;
  }

  public static function create_guid()
  {
    return sprintf(
      '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      // 32 bits for "time_low"
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),

      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,

      // 48 bits for "node"
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff)
    );
  }
}
