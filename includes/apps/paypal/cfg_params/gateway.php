<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_Cfg_gateway {
    var $default = '1';
    var $title;
    var $description;
    var $sort_order = 100;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_gateway_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_gateway_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;
      
      $input = null;      
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="gatewaySelectionPayPal" name="gateway" value="1"' . (OSCOM_APP_PAYPAL_GATEWAY == '1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="gatewaySelectionPayPal">' . $OSCOM_PayPal->getDef('cfg_gateway_paypal') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="gatewaySelectionPayflow" name="gateway" value="0"' . (OSCOM_APP_PAYPAL_GATEWAY == '0' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="gatewaySelectionPayflow">' . $OSCOM_PayPal->getDef('cfg_gateway_payflow') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div id="gatewaySelection" class="mb-3">{$input}</div>
EOT;

      return $result;
    }
  }
?>
