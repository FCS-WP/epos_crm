<?php

/**
 * Bookings Admin Settings
 *
 *
 */

namespace EPOS_CRM\Src\Database;

defined('ABSPATH') or die();


class Epos_Crm_Databases
{
  protected static $_instance = null;

  /**
   * @return Epos_Crm_Databases
   */

  public static function get_instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }


  public function __construct()
  {

    register_activation_hook(EPOS_CRM_BASENAME, array($this, 'create_redeem_log_table'));
  }


  public function create_redeem_log_table()
  {

    global $wpdb;
    $table_name = $wpdb->prefix . 'epos_crm_redeem_logs';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
    id INT NOT NULL AUTO_INCREMENT,
    customer_id VARCHAR(100) NOT NULL,
    epos_order_id VARCHAR(100) NOT NULL,
    redemption_id VARCHAR(100) NOT NULL,
    points VARCHAR(20) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    transacted_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
    ) $charset;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}
