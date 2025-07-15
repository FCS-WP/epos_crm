<?php

namespace EPOS_CRM\Src\Woocommerce\Orders;

defined('ABSPATH') or die();

use EPOS_CRM\Utils\Utils_Core;

class Epos_Crm_Orders_Payload
{

  public static function build_order_meta_data($epos_customer_id, $order)
  {

    $meta_data = array(
      "order_id" => Utils_Core::create_guid(),
      "customer_id" => $epos_customer_id,
      "use_billing_info" => self::handle_get_ship_to_destination(),
      "payments" => array(
        array(
          "id" => Utils_Core::create_guid(),
          "strategy" => "point_consumption",
          "points_used" => "100",
          "conversion_rate" => "1",
          "value" => "100",
          "transacted_at" => "2020-03-11T19:56:05",
          "redemption_id" => Utils_Core::create_guid()
        ),
        array(
          "id" => Utils_Core::create_guid(),
          "strategy" => "cash",
          "value" => "500",
          "transacted_at" => "2020-03-11T19:56:05"
        )
      )
    );

    return json_encode($meta_data);
  }

  public static function handle_get_ship_to_destination()
  {
    if (get_option('woocommerce_ship_to_destination') == 'shipping') return 'false';

    return 'true';
  }
}
