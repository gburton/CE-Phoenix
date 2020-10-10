<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  abstract class abstract_zoneable_module extends abstract_module {

    public function __construct() {
      parent::__construct();
      if ($this->enabled && isset($GLOBALS['order']->delivery['country']['id'])) {
        $this->update_status();
      }
    }

    public function update_status() {
      global $order;

      $zone_id = $this->base_constant('ZONE') ?? 0;
      if ( $this->enabled && isset($order->delivery['zone_id']) && ((int)$zone_id > 0) ) {
        $check_query = tep_db_query("SELECT zone_id FROM zones_to_geo_zones WHERE geo_zone_id = " . (int)$zone_id . " AND zone_country_id = " . (int)$order->delivery['country']['id'] . " ORDER BY zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if (($check['zone_id'] < 1) || ($check['zone_id'] == $order->delivery['zone_id'])) {
            return;
          }
        }

        $this->enabled = false;
      }
    }

  }

