<?php
/*
Plugin Name: EPOS CRM
Plugin URI: https://zippy.sg/
Description: EPOS CRM is a smart, EPOS-connected Customer Relationship Management plugin for WooCommerce that syncs customer data, manages credit points, and places orders directly through your site.
Version: 1.0
Author: Zippy SG
Author URI: https://zippy.sg/
License: GNU General Public License v3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Domain Path: /languages
Text Domain: epos-crm
*/

namespace EPOS_CRM;

defined('ABSPATH') || exit;

/*-----------------------------------------------
 | Define Plugin Constants
 ------------------------------------------------*/
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

/*-----------------------------------------------
 | Autoload & Includes
 ------------------------------------------------*/
// Prevent autoload conflicts with other Composer plugins
// if (!class_exists(\Composer\Autoload\ClassLoader::class)) {
//   require_once EPOS_CRM_DIR_PATH . 'vendor/autoload.php';
// }

require_once EPOS_CRM_DIR_PATH . 'includes/autoload.php';
require_once EPOS_CRM_DIR_PATH . 'includes/constances.php';

/*-----------------------------------------------
 | Plugin Initialization
 ------------------------------------------------*/

use EPOS_CRM\Src\Admin\Settings;
use EPOS_CRM\Src\Routers\Epos_Crm_Routers;
use EPOS_CRM\Src\Web\Epos_Crm_Web;
use EPOS_CRM\Src\Woocommerce\Epos_Crm_Woocommerce;

add_action('plugins_loaded', function () {
  // Initialize Settings and Routers
  Settings::get_instance();
  Epos_Crm_Routers::get_instance();
  Epos_Crm_Web::get_instance();
  Epos_Crm_Woocommerce::get_instance();
});


