<?php

/* Set constant url to the plugin directory. */

if (!defined('EPOS_CRM_URL')) {
  define('EPOS_CRM_URL', plugin_dir_url(__FILE__));
}

// API Response Message
if (!defined('EPOS_CRM_SUCCESS')) {
  define('EPOS_CRM_SUCCESS', 'Operation Successful!');
}
if (!defined('EPOS_CRM_NOT_FOUND')) {
  define('EPOS_CRM_NOT_FOUND', 'Nothing Found!');
}
if (!defined('EPOS_CRM_ERROR')) {
  define('EPOS_CRM_ERROR', 'An Error Occurred!');
}
