<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class ht_product_opengraph {
    var $code = 'ht_product_opengraph';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function ht_product_opengraph() {
      $this->title = MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_TITLE;
      $this->description = MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_STATUS == 'True');
      }
    }

    function execute() {
      global $PHP_SELF, $HTTP_GET_VARS, $oscTemplate, $languages_id, $currencies, $currency, $product_check;

      if ( ($PHP_SELF == FILENAME_PRODUCT_INFO) && isset($HTTP_GET_VARS['products_id']) ) {
        if ($product_check['total'] > 0) {
          $product_info_query = tep_db_query("select p.products_id, COALESCE(NULLIF(pd.products_seo_title, ''), pd.products_name) as products_name, pd.products_description, p.products_image, p.products_price, p.products_quantity, p.products_tax_class_id, p.products_date_available from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");

          if ( tep_db_num_rows($product_info_query) === 1 ) {
            $product_info = tep_db_fetch_array($product_info_query);

            $data = array('og:type' => 'product',
                          'og:title' => $product_info['products_name'],
                          'og:site_name' => STORE_NAME);

            if ( tep_not_null(MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_APP_ID) ) {
              $data['fb:app_id'] = MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_APP_ID;
            }

            $product_description = substr(trim(preg_replace('/\s\s+/', ' ', strip_tags($product_info['products_description']))), 0, 197);

            if ( strlen($product_description) == 197 ) {
              $product_description .= ' ..';
            }

            $data['og:description'] = $product_description;

            $products_image = $product_info['products_image'];

            $pi_query = tep_db_query("select image from " . TABLE_PRODUCTS_IMAGES . " where products_id = '" . (int)$product_info['products_id'] . "' order by sort_order limit 1");

            if ( tep_db_num_rows($pi_query) === 1 ) {
              $pi = tep_db_fetch_array($pi_query);
              $products_image = $pi['image'];
            }

            $data['og:image'] = tep_href_link(DIR_WS_IMAGES . $products_image, '', 'NONSSL', false, false);

            if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
              $products_price = $this->format_raw($new_price);
            } else {
              $products_price = $this->format_raw($product_info['products_price']);
            }

            $data['product:price:amount'] = $products_price;
            $data['product:price:currency'] = $currency;

            $data['og:url'] = tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id'], 'NONSSL', false);

            $data['product:availability'] = ( $product_info['products_quantity'] > 0 ) ? MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_TEXT_IN_STOCK : MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_TEXT_OUT_OF_STOCK;

            $result = '';

            foreach ( $data as $key => $value ) {
              $result .= '<meta property="' . tep_output_string_protected($key) . '" content="' . tep_output_string_protected($value) . '" />' . "\n";
            }

            $oscTemplate->addBlock($result, $this->group);
          }
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Product Open Graph Meta Module', 'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_STATUS', 'True', 'Do you want to allow Open Graph Meta Tags (good for Facebook and Pinterest) to be added to your product page?  Note that your product thumbnails MUST be at least 200px by 200px.', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Facebook App ID', 'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_APP_ID', '', 'Your Facebook APP ID<br />Note: Not Required.<br><br><strong>Helper Links</strong><br /><small>https://developers.facebook.com/docs/opengraph/getting-started/<br />https://developers.facebook.com/docs/opengraph/using-objects/</small>', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_SORT_ORDER', '900', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_STATUS', 'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_APP_ID', 'MODULE_HEADER_TAGS_PRODUCT_OPENGRAPH_SORT_ORDER');
    }

    function format_raw($number, $currency_code = '', $currency_value = '') {
      global $currencies, $currency;

      if (empty($currency_code) || !$this->is_set($currency_code)) {
        $currency_code = $currency;
      }

      if (empty($currency_value) || !is_numeric($currency_value)) {
        $currency_value = $currencies->currencies[$currency_code]['value'];
      }

      return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '.', '');
    }
  }

