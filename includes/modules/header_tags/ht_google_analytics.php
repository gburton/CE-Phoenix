<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ht_google_analytics extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_GOOGLE_ANALYTICS_';

    public function __construct() {
      parent::__construct(__FILE__);

      if (static::get_constant('MODULE_HEADER_TAGS_GOOGLE_ANALYTICS_JS_PLACEMENT') !== 'Header') {
        $this->group = 'footer_scripts';
      }
    }

    function execute() {
      global $currencies;

      if (Text::is_empty(MODULE_HEADER_TAGS_GOOGLE_ANALYTICS_ID)) {
        return;
      }

      $header = '<script>
  var _gaq = _gaq || [];
  _gaq.push([\'_setAccount\', \'' . Text::output(MODULE_HEADER_TAGS_GOOGLE_ANALYTICS_ID) . '\']);
  _gaq.push([\'_trackPageview\']);' . "\n";

      if ( (MODULE_HEADER_TAGS_GOOGLE_ANALYTICS_EC_TRACKING === 'True') && (basename($GLOBALS['PHP_SELF']) === 'checkout_success.php') && isset($_SESSION['customer_id']) ) {
        $order_query = tep_db_query("SELECT orders_id, billing_city, billing_state, billing_country FROM orders WHERE customers_id = " . (int)$_SESSION['customer_id'] . " ORDER BY date_purchased DESC LIMIT 1");

        if (mysqli_num_rows($order_query) == 1) {
          $order = $order_query->fetch_assoc();

          $totals = [];

          $order_totals_query = tep_db_query("SELECT value, class FROM orders_total WHERE orders_id = " . (int)$order['orders_id']);
          while ($order_totals = $order_totals_query->fetch_assoc()) {
            $totals[$order_totals['class']] = $order_totals['value'];
          }

          $header .= '  _gaq.push([\'_addTrans\',
    \'' . (int)$order['orders_id'] . '\', // order ID - required
    \'' . Text::output(STORE_NAME) . '\', // store name
    \'' . (isset($totals['ot_total']) ? $currencies->format_raw($totals['ot_total'], DEFAULT_CURRENCY) : 0) . '\', // total - required
    \'' . (isset($totals['ot_tax']) ? $currencies->format_raw($totals['ot_tax'], DEFAULT_CURRENCY) : 0) . '\', // tax
    \'' . (isset($totals['ot_shipping']) ? $currencies->format_raw($totals['ot_shipping'], DEFAULT_CURRENCY) : 0) . '\', // shipping
    \'' . htmlspecialchars($order['billing_city']) . '\', // city
    \'' . htmlspecialchars($order['billing_state']) . '\', // state or province
    \'' . htmlspecialchars($order['billing_country']) . '\' // country
  ]);' . "\n";

          $order_products_query = tep_db_query("SELECT op.products_id, pd.products_name, op.final_price, op.products_quantity FROM orders_products op, products_description pd, languages l WHERE op.orders_id = " . (int)$order['orders_id'] . " AND op.products_id = pd.products_id AND l.code = '" . tep_db_input(DEFAULT_LANGUAGE) . "' AND l.languages_id = pd.language_id");
          while ($order_products = $order_products_query->fetch_assoc()) {
            $category_query = tep_db_query("SELECT cd.categories_name FROM categories_description cd, products_to_categories p2c, languages l WHERE p2c.products_id = " . (int)$order_products['products_id'] . " AND p2c.categories_id = cd.categories_id AND l.code = '" . tep_db_input(DEFAULT_LANGUAGE) . "' AND l.languages_id = cd.language_id LIMIT 1");
            $category = $category_query->fetch_assoc();

            $header .= '  _gaq.push([\'_addItem\',
    \'' . (int)$order['orders_id'] . '\', // order ID - required
    \'' . (int)$order_products['products_id'] . '\', // SKU/code - required
    \'' . Text::output($order_products['products_name']) . '\', // product name
    \'' . Text::output($category['categories_name']) . '\', // category
    \'' . $currencies->format_raw($order_products['final_price']) . '\', // unit price - required
    \'' . (int)$order_products['products_quantity'] . '\' // quantity - required
  ]);' . "\n";
          }

          $header .= '  _gaq.push([\'_trackTrans\']); //submits transaction to the Analytics servers' . "\n";
        }
      }

      $header .= '  (function() {
    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>' . "\n";

      $GLOBALS['oscTemplate']->addBlock($header, $this->group);
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_GOOGLE_ANALYTICS_STATUS' => [
          'title' => 'Enable Google Analytics Module',
          'value' => 'True',
          'desc' => 'Do you want to add Google Analytics to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_GOOGLE_ANALYTICS_ID' => [
          'title' => 'Google Analytics ID',
          'value' => '',
          'desc' => 'The Google Analytics profile ID to track.',
        ],
        'MODULE_HEADER_TAGS_GOOGLE_ANALYTICS_EC_TRACKING' => [
          'title' => 'E-Commerce Tracking',
          'value' => 'True',
          'desc' => 'Do you want to enable e-commerce tracking? (E-Commerce tracking must also be enabled in your Google Analytics profile settings)',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_GOOGLE_ANALYTICS_JS_PLACEMENT' => [
          'title' => 'Javascript Placement',
          'value' => 'Header',
          'desc' => 'Should the Google Analytics javascript be loaded in the header or footer?',
          'set_func' => "tep_cfg_select_option(['Header', 'Footer'], ",
        ],
        'MODULE_HEADER_TAGS_GOOGLE_ANALYTICS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
