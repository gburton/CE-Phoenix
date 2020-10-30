<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_EC_Cfg_incontext_button_shape {
    var $default = '1';
    var $title;
    var $description;
    var $sort_order = 230;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_ec_incontext_button_shape_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_ec_incontext_button_shape_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;

      $input = null;
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="incontextButtonShapePill" name="incontext_button_shape" value="1"' . (OSCOM_APP_PAYPAL_EC_INCONTEXT_BUTTON_SHAPE == '1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="incontextButtonShapePill">' . $OSCOM_PayPal->getDef('cfg_ec_incontext_button_shape_pill') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="incontextButtonShapeRect" name="incontext_button_shape" value="2"' . (OSCOM_APP_PAYPAL_EC_INCONTEXT_BUTTON_SHAPE == '2' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="incontextButtonShapeRect">' . $OSCOM_PayPal->getDef('cfg_ec_incontext_button_shape_rect') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3" id="incontextButtonShapeSelection">{$input}</div>
EOT;

      return $result;
    }
  }
?>
