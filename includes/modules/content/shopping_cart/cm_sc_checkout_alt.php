<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class cm_sc_checkout_alt {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_SC_CHECKOUT_ALT_TITLE;
      $this->description = MODULE_CONTENT_SC_CHECKOUT_ALT_DESCRIPTION;

      if ( defined('MODULE_CONTENT_SC_CHECKOUT_ALT_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_SC_CHECKOUT_ALT_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_SC_CHECKOUT_ALT_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $cart;

      $content_width = (int)MODULE_CONTENT_SC_CHECKOUT_ALT_CONTENT_WIDTH;

	    if ($cart->count_contents() > 0) {
      	if (!class_exists('payment')) {
      		include(DIR_WS_CLASSES . 'payment.php');
      	}
	    	$payment_modules = new payment;

	    	$initialize_checkout_methods = $payment_modules->checkout_initialization_method();

	    	if (!empty($initialize_checkout_methods)) {
	    		
	    		$sc_alt_checkout = '<div class="buttonSet">';

	    		reset($initialize_checkout_methods);

					switch (MODULE_CONTENT_SC_CHECKOUT_ALT_TEXT_POSITION) {
              case 'top':
              	$sc_alt_checkout .= '<p>' .  TEXT_ALTERNATIVE_CHECKOUT_METHODS . '</p>';
							 	while (list(, $value) = each($initialize_checkout_methods)) {
							 		$sc_alt_checkout .= '<p>' .  $value . '</p>';
							 	}
              	break;
              case 'bottom':
							 	while (list(, $value) = each($initialize_checkout_methods)) {
							 		$sc_alt_checkout .= '<p>' .  $value . '</p>';
							 	}
              	$sc_alt_checkout .= '<p>' .  TEXT_ALTERNATIVE_CHECKOUT_METHODS . '</p>';
              	break;
              case 'left':
              	$sc_alt_checkout .= '<p>' .  TEXT_ALTERNATIVE_CHECKOUT_METHODS . '   ';
							 	reset($initialize_checkout_methods);
							 	while (list(, $value) = each($initialize_checkout_methods)) {
							 		$sc_alt_checkout .= $value . '</p>';
							 	}
              	break;
              case 'right':
							 	while (list(, $value) = each($initialize_checkout_methods)) {
							 		$sc_alt_checkout .= '<p>' .  $value . '   ';
							 	}
              	$sc_alt_checkout .= TEXT_ALTERNATIVE_CHECKOUT_METHODS . '</p>';
              	break;
              case 'none':
							 	while (list(, $value) = each($initialize_checkout_methods)) {
							 		$sc_alt_checkout .= '<p>' .  $value . '   ';
							 	}
            } // end switch
	    		$sc_alt_checkout .= '</div>'; // end button set
		  
	    		ob_start();
	    		include(DIR_WS_MODULES . 'content/' . $this->group . '/templates/checkout_alt.php');
	    		$template = ob_get_clean();

	    		$oscTemplate->addContent($template, $this->group);
				} // end if (!empty($initialize_checkout_methods)) {
			} // end if $cart->count_contents() > 0
    }

    function  isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_SC_CHECKOUT_ALT_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Shopping Cart Alternative Checkout Button', 'MODULE_CONTENT_SC_CHECKOUT_ALT_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");	
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_SC_CHECKOUT_ALT_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Alt text position', 'MODULE_CONTENT_SC_CHECKOUT_ALT_TEXT_POSITION', 'top', 'The position of the alternative text (\"or\") relative to the button.', '6', '1', 'tep_cfg_select_option(array(\'top\', \'bottom\', \'left\', \'right\', \'none\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_SC_CHECKOUT_ALT_SORT_ORDER', '400', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_SC_CHECKOUT_ALT_STATUS', 'MODULE_CONTENT_SC_CHECKOUT_ALT_CONTENT_WIDTH', 'MODULE_CONTENT_SC_CHECKOUT_ALT_TEXT_POSITION', 'MODULE_CONTENT_SC_CHECKOUT_ALT_SORT_ORDER');
    }
  }
?>
