<?php

namespace EPOS_CRM\Src\Woocommerce\Orders;

defined('ABSPATH') or die();

use EPOS_CRM\Utils\Utils_Core;
use EPOS_CRM\Utils\Woo_Session_Handler;

class Epos_Crm_Orders_Payload
{

  public static function build_order_meta_data($epos_customer_id, $order, $order_id, $redeem_id)
  {


    $point_consumption = self::handle_build_point_payment($order, $redeem_id);
    $woo_payment = self::handle_build_woocommerce_payment($order);
    $redeem_point = self::handle_get_redeem_point($order);
    $grand_total = self::handle_get_total_order($order) + $redeem_point;
    $meta_data = array(
      "order_id" => $order_id,
      "customer_id" => $epos_customer_id,
      "use_billing_info" => self::handle_get_ship_to_destination(),
      "grand_total" => $grand_total,
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
      "value" => $order->get_total(),
      "transacted_at" => $order->get_date_created()->format('Y-m-d H:m:s')
    );
  }


  public static function handle_build_point_payment($order, $redeem_id)
  {

    $session = new Woo_Session_Handler;
    $customer_data = $session->get('epos_customer_data');

    if (empty($customer_data->active_member)) return array();

    $redeem_point = self::handle_get_redeem_point($order);

    if (empty($redeem_point) || $redeem_point == 0) return;

    $value = $customer_data->point_conversion_rate * $redeem_point;

    return  array(
      "id" => Utils_Core::create_guid(),
      "strategy" => "point_consumption",
      "points_used" => $redeem_point,
      "conversion_rate" => $customer_data->point_conversion_rate,
      "value" => $value,
      "transacted_at" => $order->get_date_created()->format('Y-m-d H:m:s'),
      "redemption_id" => $redeem_id
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
