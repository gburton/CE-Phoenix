<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_EC_Cfg_instant_update {
    var $default = '1';
    var $title;
    var $description;
    var $sort_order = 400;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_ec_instant_update_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_ec_instant_update_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;

      $input = null;
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="instantUpdateSelectionEnabled" name="instant_update" value="1"' . (OSCOM_APP_PAYPAL_EC_INSTANT_UPDATE == '1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="instantUpdateSelectionEnabled">' . $OSCOM_PayPal->getDef('cfg_ec_instant_update_enabled') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="instantUpdateSelectionDisabled" name="instant_update" value="0"' . (OSCOM_APP_PAYPAL_EC_INSTANT_UPDATE == '0' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="instantUpdateSelectionDisabled">' . $OSCOM_PayPal->getDef('cfg_ec_instant_update_disabled') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3" id="instantUpdateSelection">{$input}</div>
EOT;

      return $result;
    }
  }
?>
