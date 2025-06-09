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
    if (empty($name)) return;
    $name = trim($name);
    $first_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
    $last_name = trim(preg_replace('#' . preg_quote($first_name, '#') . '#', '', $name));
    return array(
      'first_name' => $first_name,
      'last_name' => $last_name
    );
  }
}
