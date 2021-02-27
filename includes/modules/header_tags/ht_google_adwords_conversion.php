<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class ht_google_adwords_conversion extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_';

    public function __construct() {
      parent::__construct(__FILE__);

      if (static::get_constant('MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_JS_PLACEMENT') === 'Footer') {
        $this->group = 'footer_scripts';
      }
    }

    function execute() {
      global $lng;

      if ( ($GLOBALS['PHP_SELF'] == 'checkout_success.php') && isset($_SESSION['customer_id']) ) {
        $order_query = tep_db_query("SELECT orders_id, currency, currency_value FROM orders WHERE customers_id = " . (int)$_SESSION['customer_id'] . " ORDER BY date_purchased DESC LIMIT 1");

        if (mysqli_num_rows($order_query) == 1) {
          $order = $order_query->fetch_assoc();

          $order_subtotal_query = tep_db_query("SELECT value FROM orders_total WHERE orders_id = " . (int)$order['orders_id'] . " AND class='ot_subtotal'");
          $order_subtotal = $order_subtotal_query->fetch_assoc();

          if (!isset($lng) || !($lng instanceof language)) {
            $lng = new language();
          }

          $language_code = 'en';

          foreach ($lng->catalog_languages as $lkey => $lvalue) {
            if ($lvalue['id'] == $_SESSION['languages_id']) {
              $language_code = $lkey;
              break;
            }
          }

          $conversion_id = (int)MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_ID;
          $conversion_language = htmlspecialchars($language_code);
          $conversion_format = (int)MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_FORMAT;
          $conversion_color = htmlspecialchars(MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_COLOR);
          $conversion_label = htmlspecialchars(MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_LABEL);
          $conversion_value = $this->format_raw($order_subtotal['value'], $order['currency'], $order['currency_value']);

          $output = <<<EOD
<script>
/* <![CDATA[ */
var google_conversion_id = {$conversion_id};
var google_conversion_language = "{$conversion_language}";
var google_conversion_format = "{$conversion_format}";
var google_conversion_color = "{$conversion_color}";
var google_conversion_label = "{$conversion_label}";
var google_conversion_value = {$conversion_value};
/* ]]> */
</script>
<script src="//www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/{$conversion_id}/?value={$conversion_value}&amp;label={$conversion_label}&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
EOD;

          $GLOBALS['oscTemplate']->addBlock($output, $this->group);
        }
      }
    }

    function format_raw($number, $currency_code = '', $currency_value = '') {
      global $currencies;

      if (empty($currency_code) || !$currencies->is_set($currency_code)) {
        $currency_code = $_SESSION['currency'];
      }

      if (empty($currency_value) || !is_numeric($currency_value)) {
        $currency_value = $currencies->currencies[$currency_code]['value'];
      }

      return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '.', '');
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_STATUS' => [
          'title' => 'Enable Google AdWords Conversion Module',
          'value' => 'True',
          'desc' => 'Do you want to allow the Google AdWords Conversion Module on your checkout success page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_ID' => [
          'title' => 'Conversion ID',
          'value' => '',
          'desc' => 'The Google AdWords Conversion ID',
        ],
        'MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_FORMAT' => [
          'title' => 'Tracking Notification Layout',
          'value' => '1',
          'desc' => 'A small message will appear on your site telling customers that their visits on your site are being tracked. We recommend you use it.',
          'set_func' => 'tep_cfg_google_adwords_conversion_set_format(',
          'use_func' => 'tep_cfg_google_adwords_conversion_get_format',
        ],
        'MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_COLOR' => [
          'title' => 'Page Background Color',
          'value' => 'ffffff',
          'desc' => 'Enter a HTML color to match the color of your website background page.',
        ],
        'MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_LABEL' => [
          'title' => 'Conversion Label',
          'value' => '',
          'desc' => 'The alphanumeric code generated by Google for your AdWords Conversion',
        ],
        'MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_JS_PLACEMENT' => [
          'title' => 'Javascript Placement',
          'value' => 'Footer',
          'desc' => 'Should the Google AdWords Conversion javascript be loaded in the header or footer?',
          'set_func' => "tep_cfg_select_option(['Header', 'Footer'], ",
        ],
        'MODULE_HEADER_TAGS_GOOGLE_ADWORDS_CONVERSION_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

  function tep_cfg_google_adwords_conversion_set_format($key_value, $field_key) {
    $format = ['1' => 'Single Line', '2' => 'Two Lines', '3' => 'No Indicator'];

    $string = '';

    foreach ( $format as $key => $value ) {
      $string .= '<br><input type="radio" name="configuration[' . $field_key . ']" value="' . $key . '"';

      if ($key_value == $key) $string .= ' checked="checked"';

      $string .= ' /> ' . $value;
    }

    return $string;
  }

  function tep_cfg_google_adwords_conversion_get_format($value) {
    $format = ['1' => 'Single Line', '2' => 'Two Lines', '3' => 'No Indicator'];

    return $format[$value];
  }
