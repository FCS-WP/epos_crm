<?php

/**
 * Uninstaller
 *
 *
 */

namespace Zippy_Booking\Src\Admin;

defined('ABSPATH') or die();

class Zippy_Uninstaller
{

  public static function uninstall()
  {
    // global $wpdb;

    // // DROP tables

    // $table_names = [
    //   "bookings",
    //   "booking_configs",
    //   "products_booking",
    //   "zippy_booking_log",
    // ];

    // foreach ($table_names as $name) {
    //   $table_name = $wpdb->prefix . $name;
    //   $wpdb->query("DROP TABLE IF EXISTS $table_name");
    // }


    // DELETE options
    $options = [
      "epos_be_url",
      "consent_pdpa",
      "epos_crm_auth_error",
      "epos_crm_token_key",
    ];

    foreach ($options as $opt) {
      delete_option($opt);
    }
  }
}
