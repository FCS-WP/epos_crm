<?php


namespace EPOS_CRM\Src\Woocommerce\Jobs;

defined('ABSPATH') or die();

use EPOS_CRM\Src\Woocommerce\Checkout\Epos_Crm_Refund_Process;
use EPOS_CRM\Src\Logs\EPOS_CRM_logger;


class Epos_Crm_Bg_Jobs
{
  const HOOK_NAME = 'epos_crm_auto_refund';

  public static function schedule_point_refund($redemption_id)
  {

    if (!wp_next_scheduled(self::HOOK_NAME, [$redemption_id])) {
      $held_duration = get_option('woocommerce_hold_stock_minutes');
      $cancel_unpaid_interval = apply_filters('woocommerce_cancel_unpaid_orders_interval_minutes', absint($held_duration));
      wp_schedule_event(time() + (absint($cancel_unpaid_interval) * 60), 'epos_crm_auto_refund_scheduled', 'epos_crm_auto_refund', [$redemption_id]);
    }
  }

  public static function epos_crm_auto_refund_callback($redemption_id)
  {
    global $wpdb;
    $table = $wpdb->prefix . 'epos_crm_redeem_logs';

    $log = $wpdb->get_row($wpdb->prepare(
      "SELECT * FROM $table WHERE redemption_id = %s AND status = 'pending'",
      $redemption_id
    ));

    if ($log) {
      $orders = wc_get_orders([
        'meta_key'   => 'redeem_id',
        'meta_value' => $redemption_id,
        'limit'      => 1,
      ]);

      if (empty($orders)) {
        $refund_api = new Epos_Crm_Refund_Process;

        $request = array(
          "redemption_id"         => $log->redemption_id,
          "member_id"       => $log->customer_id,
          "order_id"        => $log->epos_order_id,
        );
        $response = $refund_api->API_refund_process($request);

        if (!isset($response['status']) || $response['status'] !== "success") {
          EPOS_CRM_logger::log_response('Cannot refund for order', $request);
        } else {
          $wpdb->update($table, ['status' => 'refunded'], ['id' => $log->id]);
        }
      }
    }

    wp_clear_scheduled_hook(self::HOOK_NAME, [$redemption_id]);
  }
}
