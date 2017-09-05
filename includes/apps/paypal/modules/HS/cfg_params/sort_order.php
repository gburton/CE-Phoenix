<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_HS_Cfg_sort_order {
    var $default = '0';
    var $title;
    var $description;
    var $app_configured = false;

    function OSCOM_PayPal_HS_Cfg_sort_order() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_hs_sort_order_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_hs_sort_order_desc');
    }

    function getSetField() {
      $input = tep_draw_input_field('sort_order', OSCOM_APP_PAYPAL_HS_SORT_ORDER, 'id="inputHsSortOrder"');

      $result = <<<EOT
<div>
  <p>
    <label for="inputHsSortOrder">{$this->title}</label>

    {$this->description}
  </p>

  <div>
    {$input}
  </div>
</div>
EOT;

      return $result;
    }
  }
?>
