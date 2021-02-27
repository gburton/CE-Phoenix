<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class specials {

////
// Sets the status of a special product
    public static function set_status($specials_id, $status) {
      return tep_db_query("UPDATE specials SET status = " . (int)$status . ", date_status_change = NOW() WHERE specials_id = " . (int)$specials_id);
    }

////
// Auto expire products on special
    public static function expire() {
      return tep_db_query("UPDATE specials SET status = 0, date_status_change = NOW() WHERE status = 1 AND NOW() >= expires_date AND expires_date > 0");
    }

  }
