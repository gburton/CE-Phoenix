<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  abstract class abstract_payment_module extends abstract_module {

    function __construct() {
      parent::__construct();
      $this->public_title = self::get_constant(static::CONFIG_KEY_BASE . 'TEXT_PUBLIC_TITLE')
                         ?? self::get_constant(static::CONFIG_KEY_BASE . 'PUBLIC_TITLE');

      $this->order_status = (int)(self::get_constant(static::CONFIG_KEY_BASE . 'ORDER_STATUS_ID') ?? 0);
      $this->order_status = ($this->order_status > 0) ? $this->order_status : 0;
      if ( ( $this->enabled === true ) && isset($GLOBALS['order']) && $GLOBALS['order'] instanceof order ) {
        $this->update_status();
      }
    }

    public function update_status() {
      global $order;

      $zone_id = constant(static::CONFIG_KEY_BASE . 'ZONE');
      if ( $this->enabled && ((int)$zone_id > 0) ) {
        $check_query = tep_db_query("SELECT zone_id FROM zones_to_geo_zones WHERE geo_zone_id = " . (int)$zone_id . " AND zone_country_id = " . (int)$order->delivery['country']['id'] . " ORDER BY zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if (($check['zone_id'] < 1) || ($check['zone_id'] == $order->delivery['zone_id'])) {
            return;
          }
        }

        $this->enabled = false;
      }
    }

    public function javascript_validation() {
      return false;
    }

    public function selection() {
      return [
        'id' => $this->code,
        'module' => $this->public_title ?? $this->title,
      ];
    }

    public function pre_confirmation_check() {
      return false;
    }

    public function confirmation() {
      return false;
    }

    public function process_button() {
      return false;
    }

    public function before_process() {
      return false;
    }

    public function after_process() {
      return false;
    }

    public function get_error() {
      return false;
    }

    public static function ensure_order_status($constant_name, $order_status_name) {
      if (defined($constant_name)) {
        return constant($constant_name);
      }

      $check_sql = "SELECT orders_status_id FROM orders_status WHERE orders_status_name = '" . tep_db_input($order_status_name) . "' LIMIT 1";
      $check_query = tep_db_query($check_sql);

      if (tep_db_num_rows($check_query) < 1) {
        $column_names = '';
        $column_values = '';
        $flags_query = tep_db_query("DESCRIBE orders_status public_flag");
        if (tep_db_num_rows($flags_query) === 1) {
          $column_names = ', public_flag, downloads_flag';
          $column_values = ', 0 AS public_flag, 0 AS downloads_flag';
        }

        $next_id = tep_db_fetch_array(tep_db_query("SELECT MAX(orders_status_id) + 1 AS next_id FROM orders_status"))['next_id'] ?? 1;

        tep_db_query(sprintf(<<<'EOSQL'
INSERT INTO orders_status (orders_status_id, language_id, orders_status_name%s)
 SELECT %d AS orders_status_id,
   l.languages_id AS language_id,
   '%s' AS orders_status_name%s
 FROM languages l
 ORDER BY l.sort_order
EOSQL
          , $column_names, (int)$next_id, tep_db_input($order_status_name), $column_values));

        $check_query = tep_db_query($check_sql);
      }

      $check = tep_db_fetch_array($check_query);
      return $check['orders_status_id'];
    }

  }
