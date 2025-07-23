<?php

namespace EPOS_CRM\Src\Woocommerce\Orders;

defined('ABSPATH') or die();

use EPOS_CRM\Utils\Utils_Core;
use EPOS_CRM\Utils\Woo_Session_Handler;

class Epos_Crm_Orders_Payload
{

  public static function build_order_meta_data($epos_customer_id, $order)
  {


    $point_consumption = self::handle_build_point_payment($order);
    $woo_payment = self::handle_build_woocommerce_payment($order);

    $meta_data = array(
      "order_id" => Utils_Core::create_guid(),
      "customer_id" => $epos_customer_id,
      "use_billing_info" => self::handle_get_ship_to_destination(),
      "payments" => array(
        $point_consumption,
        $woo_payment

      )
    );

    return json_encode($meta_data);
  }

  public static function handle_get_ship_to_destination()
  {
    if (get_option('woocommerce_ship_to_destination') == 'shipping') return 'false';

    return 'true';
  }


  public static function handle_build_woocommerce_payment($order)
  {

    return  array(
      "id" => Utils_Core::create_guid(),
      "strategy" => $order->get_payment_method_title(),
      "value" => "500",
      "transacted_at" => $order->get_date_created()
    );
  }

  public static function handle_build_point_payment($order)
  {

    $session = new Woo_Session_Handler;
    $customer_data = $session->get('epos_customer_data');

    if (empty($customer_data->active_member)) return array();

    $redeem_point = self::handle_get_redeem_point($order);

    return  array(
      "id" => Utils_Core::create_guid(),
      "strategy" => "point_consumption",
      "points_used" => $customer_data->point_balance,
      "conversion_rate" => $customer_data->point_conversion_rate,
      "value" => $redeem_point,
      "transacted_at" => $order->get_date_created(),
      "redemption_id" => Utils_Core::create_guid()
    );
  }

  public static function handle_get_redeem_point($order)
  {
    if (empty($order)) return 0;

    foreach ($order->get_items('fee') as $item) {
      if ($item->get_name() === EPOS_CRM_REDEEM) {
        return $item->get_total() * -1; //Due to discount fee on woo is Negative Number
      }
    }

    return 0;
  }

  public static function handle_get_total_order($order)
  {
    if (empty($order)) return;

    return $order->get_total();
  }
}
