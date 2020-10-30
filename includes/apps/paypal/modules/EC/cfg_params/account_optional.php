<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_EC_Cfg_account_optional {
    var $default = '0';
    var $title;
    var $description;
    var $sort_order = 300;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_ec_account_optional_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_ec_account_optional_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;
      
      $input = null;      
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="accountOptionalSelectionTrue" name="account_optional" value="1"' . (OSCOM_APP_PAYPAL_EC_ACCOUNT_OPTIONAL == '1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="accountOptionalSelectionTrue">' . $OSCOM_PayPal->getDef('cfg_ec_account_optional_true') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="accountOptionalSelectionFalse" name="account_optional" value="0"' . (OSCOM_APP_PAYPAL_EC_ACCOUNT_OPTIONAL == '0' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="accountOptionalSelectionFalse">' . $OSCOM_PayPal->getDef('cfg_ec_account_optional_false') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3" id="accountOptionalSelection">{$input}</div>
EOT;

      return $result;
    }
  }
?>
