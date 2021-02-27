<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class whos_online {

    public static function update() {
      if (isset($_SESSION['customer_id'])) {
        $wo_customer_id = $_SESSION['customer_id'];
        $wo_full_name = $GLOBALS['customer']->get('name');
      } else {
        $wo_customer_id = 0;
        $wo_full_name = 'Guest';
      }

      $current_time = time();
      static::expire($current_time);

      tep_db_query(<<<'EOSQL'
INSERT INTO whos_online (customer_id, full_name, session_id, ip_address, time_entry, time_last_click, last_page_url) VALUES (
EOSQL
        . (int)$wo_customer_id
        . ", '" . tep_db_input($wo_full_name)
        . "', '" . tep_db_input(session_id())
        . "', '" . tep_db_input(tep_get_ip_address())
        . "', '" . tep_db_input($current_time)
        . "', '" . tep_db_input($current_time)
        . "', '" . tep_db_input(tep_db_prepare_input(getenv('REQUEST_URI'))) . <<<'EOSQL'
') ON DUPLICATE KEY UPDATE
  customer_id = VALUES(customer_id),
  full_name = VALUES(full_name),
  ip_address = VALUES(ip_address),
  time_last_click = VALUES(time_last_click),
  last_page_url = VALUES(last_page_url)
EOSQL
        );
    }

    protected static function expire($current_time) {
      tep_db_query("DELETE FROM whos_online WHERE time_last_click < " . (int)($current_time - 900));
    }

    public static function update_session_id($old_id, $new_id) {
      tep_db_query(sprintf(<<<'EOSQL'
INSERT INTO whos_online (customer_id, full_name, session_id, ip_address, time_entry, time_last_click, last_page_url)
 SELECT wo.customer_id, wo.full_name, '%s', wo.ip_address, wo.time_entry, wo.time_last_click, wo.last_page_url
   FROM whos_online wo
   WHERE wo.session_id = '%s'
 ON DUPLICATE KEY UPDATE
   customer_id = VALUES(customer_id),
   full_name = VALUES(full_name),
   ip_address = VALUES(ip_address),
   time_entry = VALUES(time_entry),
   time_last_click = VALUES(time_last_click),
   last_page_url = VALUES(last_page_url)
EOSQL
        , tep_db_input($new_id), tep_db_input($old_id)));
      tep_db_query("DELETE FROM whos_online WHERE session_id = '" . tep_db_input($old_id) . "' OR time_last_click < " . (int)(time() - 900));
    }

  }
