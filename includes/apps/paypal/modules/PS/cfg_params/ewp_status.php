<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_PS_Cfg_ewp_status {
    var $default = '-1';
    var $title;
    var $description;
    var $sort_order = 700;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_ps_ewp_status_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_ps_ewp_status_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;
      
      $input = null;      
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="ewpStatusSelectionTrue" name="ewp_status" value="1"' . (OSCOM_APP_PAYPAL_PS_EWP_STATUS == '1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="ewpStatusSelectionTrue">' . $OSCOM_PayPal->getDef('cfg_ps_ewp_status_true') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="ewpStatusSelectionFalse" name="ewp_status" value="0"' . (OSCOM_APP_PAYPAL_PS_EWP_STATUS == '0' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="ewpStatusSelectionFalse">' . $OSCOM_PayPal->getDef('cfg_ps_ewp_status_false') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3">{$input}</div>
EOT;

      return $result;
    }
  }
?>
