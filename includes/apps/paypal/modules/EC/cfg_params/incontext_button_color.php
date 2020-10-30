<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_EC_Cfg_incontext_button_color {
    var $default = '1';
    var $title;
    var $description;
    var $sort_order = 210;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_ec_incontext_button_color_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_ec_incontext_button_color_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;
      
      $input = null;
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="incontextButtonColorSelectionGold" name="incontext_button_color" value="1"' . (OSCOM_APP_PAYPAL_EC_INCONTEXT_BUTTON_COLOR == '1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="incontextButtonColorSelectionGold">' . $OSCOM_PayPal->getDef('cfg_ec_incontext_button_color_gold') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="incontextButtonColorSelectionBlue" name="incontext_button_color" value="2"' . (OSCOM_APP_PAYPAL_EC_INCONTEXT_BUTTON_COLOR == '2' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="incontextButtonColorSelectionBlue">' . $OSCOM_PayPal->getDef('cfg_ec_incontext_button_color_blue') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="incontextButtonColorSelectionSilver" name="incontext_button_color" value="3"' . (OSCOM_APP_PAYPAL_EC_INCONTEXT_BUTTON_COLOR == '3' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="incontextButtonColorSelectionSilver">' . $OSCOM_PayPal->getDef('cfg_ec_incontext_button_color_silver') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3" id="incontextButtonColorSelection">{$input}</div>
EOT;

      return $result;
    }
  }
?>
