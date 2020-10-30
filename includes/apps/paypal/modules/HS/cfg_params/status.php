<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_HS_Cfg_status {
    var $default = '1';
    var $title;
    var $description;
    var $sort_order = 100;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_hs_status_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_hs_status_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;

      $input = null;
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="statusSelectionLive" name="status" value="1"' . (OSCOM_APP_PAYPAL_HS_STATUS == '1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="statusSelectionLive">' . $OSCOM_PayPal->getDef('cfg_hs_status_live') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="statusSelectionSandbox" name="status" value="0"' . (OSCOM_APP_PAYPAL_HS_STATUS == '0' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="statusSelectionSandbox">' . $OSCOM_PayPal->getDef('cfg_hs_status_sandbox') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="statusSelectionDisabled" name="status" value="-1"' . (OSCOM_APP_PAYPAL_HS_STATUS == '-1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="statusSelectionDisabled">' . $OSCOM_PayPal->getDef('cfg_hs_status_disabled') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3" id="statusSelection">{$input}</div>
EOT;

      return $result;
    }
  }
?>
