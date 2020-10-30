<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_DP_Cfg_transaction_method {
    var $default = '1';
    var $title;
    var $description;
    var $sort_order = 300;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_dp_transaction_method_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_dp_transaction_method_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;

      $input = null;
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="transactionMethodSelectionAuthorize" name="transaction_method" value="0"' . (OSCOM_APP_PAYPAL_DP_TRANSACTION_METHOD == '0' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="transactionMethodSelectionAuthorize">' . $OSCOM_PayPal->getDef('cfg_dp_transaction_method_authorize') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="transactionMethodSelectionSale" name="transaction_method" value="1"' . (OSCOM_APP_PAYPAL_DP_TRANSACTION_METHOD == '1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="transactionMethodSelectionSale">' . $OSCOM_PayPal->getDef('cfg_dp_transaction_method_sale') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3" id="transactionMethodSelection">{$input}</div>
EOT;

      return $result;
    }
  }
?>
