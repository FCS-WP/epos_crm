<?php

/**
 * Admin Setting
 *
 * @package Shin
 */

namespace EPOS_CRM\Utils;

defined('ABSPATH') or die();

class EPOS_Helper
{
  public static function split_full_name($name)
  {
    if (empty($name)) return null;

    $name = trim($name);
    $parts = explode(' ', $name);

    $first_name = array_shift($parts);
    $last_name = implode(' ', $parts);

    return array(
      'first_name' => $first_name,
      'last_name' => $last_name
    );
  }
}
