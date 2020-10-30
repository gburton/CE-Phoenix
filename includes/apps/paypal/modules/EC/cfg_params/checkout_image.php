<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_EC_Cfg_checkout_image {
    var $default = '0';
    var $title;
    var $description;
    var $sort_order = 500;

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_ec_checkout_image_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_ec_checkout_image_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;

      $input = null;
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="checkoutImageSelectionStatic" name="checkout_image" value="0"' . (OSCOM_APP_PAYPAL_EC_CHECKOUT_FLOW == '0' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="checkoutImageSelectionStatic">' . $OSCOM_PayPal->getDef('cfg_ec_checkout_image_static') . '</label>';
      $input .= '</div>';
      $input .= '<div class="custom-control custom-radio custom-control-inline">';
        $input .= '<input type="radio" class="custom-control-input" id="checkoutImageSelectionDynamic" name="checkout_image" value="1"' . (OSCOM_APP_PAYPAL_EC_CHECKOUT_FLOW == '1' ? ' checked="checked"' : '') . '>';
        $input .= '<label class="custom-control-label" for="checkoutImageSelectionDynamic">' . $OSCOM_PayPal->getDef('cfg_ec_checkout_image_dynamic') . '</label>';
      $input .= '</div>';

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3" id="checkoutImageSelection">{$input}</div>
EOT;

      return $result;
    }
  }
?>
