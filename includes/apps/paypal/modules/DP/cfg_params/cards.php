<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class OSCOM_PayPal_DP_Cfg_cards {
    var $default = 'visa;mastercard;discover;amex;maestro';
    var $title;
    var $description;
    var $sort_order = 200;
    var $cards = ['visa' => 'Visa', 'mastercard' => 'MasterCard', 'discover' => 'Discover Card', 'amex' => 'American Express', 'maestro' => 'Maestro'];

    function __construct() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_dp_cards_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_dp_cards_desc');
    }

    function getSetField() {
      $active = explode(';', OSCOM_APP_PAYPAL_DP_CARDS);

      $input = null;

      foreach ( $this->cards as $key => $value ) {
        $input .= '<div class="custom-control custom-checkbox custom-control-inline">';
          $input .= '<input type="checkbox" class="custom-control-input" id="cardsSelection' . ucfirst($key) . '" value="' . $key . '"' . (in_array($key, $active) ? ' checked="checked"' : '') . '>';
          $input .= '<label class="custom-control-label" for="cardsSelection' . ucfirst($key) . '">' . $value . '</label>';
        $input .= '</div>';
      }

      $result = <<<EOT
<h5>{$this->title}</h5>
<p>{$this->description}</p>

<div class="mb-3" id="cardsSelection">
  {$input}
  <input type="hidden" name="cards" value="" />
</div>

<script>
$(function() {
  $('form[name="paypalConfigure"]').submit(function() {
    $('input[name="cards"]').val($('input[name="card_types[]"]:checked').map(function() {
      return this.value;
    }).get().join(';'));
  });
});
</script>
EOT;

      return $result;
    }
  }
?>
