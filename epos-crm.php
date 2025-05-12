<?php
/*
Plugin Name: EPOS CRM
Plugin URI: https://zippy.sg/
Description: EPOS CRM is a smart, EPOS-connected Customer Relationship Management plugin for Woocommerce that lets you sync customer allowing you to sync customer data, manage credit points and place orders directly through your site.
Version: 1.0
Author: Zippy SG
Author URI: https://zippy.sg/
License: GNU General Public
License v3.0 License
URI: https://zippy.sg/
Domain Path: /languages

Copyright 2024

*/

namespace EPOS_CRM;


defined('ABSPATH') or die('°_°’');

/* ------------------------------------------
 // Constants
 ------------------------------------------------------------------------ */
/* Set plugin version constant. */

if (!defined('EPOS_CRM_VERSION')) {
  define('EPOS_CRM_VERSION', '4.0');
}

/* Set plugin name. */

if (!defined('EPOS_CRM_NAME')) {
  define('EPOS_CRM_NAME', 'EPOS CRM');
}

if (!defined('EPOS_CRM_PREFIX')) {
  define('EPOS_CRM_PREFIX', 'epos_crm');
}

if (!defined('EPOS_CRM_BASENAME')) {
  define('EPOS_CRM_BASENAME', plugin_basename(__FILE__));
}

/* Set constant path to the plugin directory. */

if (!defined('EPOS_CRM_DIR_PATH')) {
  define('EPOS_CRM_DIR_PATH', plugin_dir_path(__FILE__));
}

/* Set constant url to the plugin directory. */

if (!defined('EPOS_CRM_URL')) {
  define('EPOS_CRM_URL', plugin_dir_url(__FILE__));
}

/* Set constant enpoint to the plugin directory. */
if (!defined('EPOS_CRM_API_NAMESPACE')) {
  define('EPOS_CRM_API_NAMESPACE', 'epos-crm/v1');
}


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

/* ------------------------------------------
// Includes
 --------------------------- --------------------------------------------- */
require EPOS_CRM_DIR_PATH . '/includes/autoload.php';

use  EPOS_CRM\Src\Admin\Settings;

// use EPOS_CRM\Src\Routers\Zippy_Booking_Routers;

/**
 *
 * Init Zippy Booking
 */

 Settings::get_instance();

//  Zippy_Booking_Routers::get_instance();
