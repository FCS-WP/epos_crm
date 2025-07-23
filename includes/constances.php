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

// EPOS CRM

if (!defined('EPOS_CRM_REDEEM')) {
  define('EPOS_CRM_REDEEM', 'Membership Point Redemption');
}

if (!defined('EPOS_CRM_CUSTOMER_PORTAL_URL')) {
  define('EPOS_CRM_CUSTOMER_PORTAL_URL', 'https://myprofile.livedevs.com');
}
if (!defined('EPOS_CRM_URL_SERVICE')) {
  define('EPOS_CRM_URL_SERVICE', 'https://livedevs.com');
}
if (!defined('EPOS_CRM_MEMBER_PORTAL_TEXT')) {
  define('EPOS_CRM_MEMBER_PORTAL_TEXT', "Members' Portal");
}
if (!defined('EPOS_CRM_MENU_ITEM')) {
  define('EPOS_CRM_MENU_ITEM', "EPOS_CRM_MENU");
}
