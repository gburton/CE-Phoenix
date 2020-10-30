<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_Cfg_verify_ssl {
    var $default = '1';
    var $title;
    var $description;
    var $sort_order = 300;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_verify_ssl_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_verify_ssl_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;
      
      $input = null;      
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="verifySslSelectionTrue" name="verify_ssl" value="1"' . (OSCOM_APP_PAYPAL_VERIFY_SSL == '1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="verifySslSelectionTrue">' . $OSCOM_PayPal->getDef('cfg_verify_ssl_true') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="verifySslSelectionFalse" name="verify_ssl" value="0"' . (OSCOM_APP_PAYPAL_VERIFY_SSL == '0' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="verifySslSelectionFalse">' . $OSCOM_PayPal->getDef('cfg_verify_ssl_false') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div id="verifySslSelection" class="mb-3">{$input}</div>
EOT;

      return $result;
    }
  }
?>
