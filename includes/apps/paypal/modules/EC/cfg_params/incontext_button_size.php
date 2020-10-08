<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_EC_Cfg_incontext_button_size {
    var $default = '2';
    var $title;
    var $description;
    var $sort_order = 220;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_ec_incontext_button_size_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_ec_incontext_button_size_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;

      $input = null;
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="incontextButtonSizeSmall" name="incontext_button_size" value="2"' . (OSCOM_APP_PAYPAL_EC_INCONTEXT_BUTTON_SIZE == '2' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="incontextButtonSizeSmall">' . $OSCOM_PayPal->getDef('cfg_ec_incontext_button_size_small') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="incontextButtonSizeTiny" name="incontext_button_size" value="1"' . (OSCOM_APP_PAYPAL_EC_INCONTEXT_BUTTON_SIZE == '1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="incontextButtonSizeTiny">' . $OSCOM_PayPal->getDef('cfg_ec_incontext_button_size_tiny') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="incontextButtonSizeMedium" name="incontext_button_size" value="3"' . (OSCOM_APP_PAYPAL_EC_INCONTEXT_BUTTON_SIZE == '3' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="incontextButtonSizeMedium">' . $OSCOM_PayPal->getDef('cfg_ec_incontext_button_size_medium') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3" id="incontextButtonSizeSelection">{$input}</div>
EOT;

      return $result;
    }
  }
?>
