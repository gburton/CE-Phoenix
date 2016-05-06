<?php
/*
	$Id: eubanktransfer.php,v 1.9.1 2006/07/04 12:00:00 jb_gfx

	Thanks to all the developers from the EU-Standard Bank Transfer module

	osCommerce, Open Source E-Commerce Solutions
	http://www.oscommerce.com

	Released under the GNU General Public License
*/

	class eubanktransfer {
		var $code, $title, $description, $enabled;

		// begin: class constructor
		function __construct() {
			global $order;

			$this->code = 'eubanktransfer';
			$this->title = MODULE_PAYMENT_EU_BANKTRANSFER_TEXT_TITLE;
			$this->description = MODULE_PAYMENT_EU_BANKTRANSFER_TEXT_DESCRIPTION;
			$this->sort_order = MODULE_PAYMENT_EU_BANKTRANSFER_SORT_ORDER;
			$this->enabled = ((MODULE_PAYMENT_EU_BANKTRANSFER == 'True') ? true : false);

			if ((int)MODULE_PAYMENT_EU_BANKTRANSFER_ID > 0) {
				$this->order_status = MODULE_PAYMENT_EU_BANKTRANSFER_ORDER_STATUS_ID;
			}

			if (is_object($order)) {
				$this->update_status();
			}
			$this->email_footer = MODULE_PAYMENT_EU_BANKTRANSFER_TEXT_EMAIL_FOOTER;
		} // end: class constructor

		// begin: class methods
		function update_status() {
			global $order;

			if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_EU_BANKTRANSFER_ZONE > 0) ) {
				$check_flag = false;
				$check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_EU_BANKTRANSFER_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
				while ($check = tep_db_fetch_array($check_query)) {
					if ($check['zone_id'] < 1) {
						$check_flag = true;
						break;
					} elseif ($check['zone_id'] == $order->billing['zone_id']) {
						$check_flag = true;
						break;
					}
				}

				if ($check_flag == false) {
					$this->enabled = false;
				}
			}

			// disable the module if the order only contains virtual products
			if ( ($this->enabled == true) && ($order->content_type == 'virtual') ) {
				$this->enabled = false;
			}
		}

		function javascript_validation() {
			return false;
		}

		function selection() {
			return array(
				'id' => $this->code,
				'module' => $this->title);
		}

		function pre_confirmation_check() {
			return false;
		}

		function confirmation() {
			return array('title' => MODULE_PAYMENT_EU_BANKTRANSFER_TEXT_DESCRIPTION);
		}

		function process_button() {
			return false;
		}

		function before_process() {
			return false;
		}

		function after_process() {
			return false;
		}

		function get_error() {
			return false;
		}

		function check() {
			if (!isset($this->check)) {
				$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_EU_BANKTRANSFER'");
				$this->check = tep_db_num_rows($check_query);
			}
			return $this->check;
		}

		function install() {
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Allow Bank Transfer Payment', 'MODULE_PAYMENT_EU_BANKTRANSFER', 'True', 'Do you want to accept bank transfers?', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());");
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Bank Name', 'MODULE_PAYMENT_EU_BANKNAME', 'Bank name, city', 'Bank name and city', '6', '1', now());");
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Account Name', 'MODULE_PAYMENT_EU_ACCOUNT_HOLDER', 'Name', 'Name which is registered for the account', '6', '1', now());");
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Account IBAN', 'MODULE_PAYMENT_EU_IBAN', 'AC00 0000 0000 0000 0000 0000 000', 'Your account IBAN', '6', '1', now());");
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Account BIC / SWIFT Code', 'MODULE_PAYMENT_EU_BIC', 'ABCDEFGHIJK', 'Your account BIC / SWIFT code', '6', '1', now());");
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_EU_BANKTRANSFER_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_EU_BANKTRANSFER_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Module Sort order of display.', 'MODULE_PAYMENT_EU_BANKTRANSFER_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
		}

		function remove() {
			tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
		}

		function keys() {
			return $keys = array(
				'MODULE_PAYMENT_EU_BANKTRANSFER',
				'MODULE_PAYMENT_EU_BANKNAME',
				'MODULE_PAYMENT_EU_ACCOUNT_HOLDER',
				'MODULE_PAYMENT_EU_IBAN',
				'MODULE_PAYMENT_EU_BIC',
				'MODULE_PAYMENT_EU_BANKTRANSFER_ZONE',
				'MODULE_PAYMENT_EU_BANKTRANSFER_ORDER_STATUS_ID',
				'MODULE_PAYMENT_EU_BANKTRANSFER_SORT_ORDER');
		}
	} // end: class methods
?>
