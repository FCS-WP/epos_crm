<?php

namespace EPOS_CRM\Src\Routers;

/**
 * Bookings General Router
 *
 *
 */

defined('ABSPATH') or die();

use EPOS_CRM\Src\Routers\General\General_Routers;
use EPOS_CRM\Src\Routers\Customers\Customer_Routers;
use EPOS_CRM\Src\Routers\Orders\Orders_Routers;


class Epos_Crm_Routers
{
  protected static $_instance = null;

  /**
   * @return Epos_Crm_Routers
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
    General_Routers::get_instance();
    Customer_Routers::get_instance();
    Orders_Routers::get_instance();
  }
}
