<?php

namespace EPOS_CRM\Src\Woocommerce\Webhooks;

defined('ABSPATH') or die();


class Epos_Crm_Woocommerce_Webhook
{

  protected static $_instance = null;

  /**
   * @return Epos_Crm_Orders
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
    add_filter('woocommerce_webhook_payload', array($this, 'unset_redeem_on_webhook'), 10, 4);
  }

  public function unset_redeem_on_webhook($payload, $resource = '', $resource_id = 0, $webhook = null)
  {
    if ($resource === 'order') {
      // Remove from fee_lines
      if (isset($payload['fee_lines']) && is_array($payload['fee_lines'])) {
        foreach ($payload['fee_lines'] as $key => $fee) {
          if (!empty($fee['name']) && strcasecmp($fee['name'], EPOS_CRM_REDEEM) === 0) {
            unset($payload['fee_lines'][$key]);
          }
        }
        $payload['fee_lines'] = array_values($payload['fee_lines']);
      }

    }

    return $payload;
  }
}
