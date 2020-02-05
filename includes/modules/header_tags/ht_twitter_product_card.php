<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  class ht_twitter_product_card {
    var $code = 'ht_twitter_product_card';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_TITLE;
      $this->description = MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_STATUS == 'True');
      }
    }

    function execute() {
      global $PHP_SELF, $oscTemplate, $languages_id, $currencies, $currency;

      if ( ($PHP_SELF == 'product_info.php') && isset($_GET['products_id']) ) {
        $product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, p.products_image from products p, products_description pd where p.products_id = '" . (int)$_GET['products_id'] . "' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");

        if ( tep_db_num_rows($product_info_query) === 1 ) {
          $product_info = tep_db_fetch_array($product_info_query);

          $data = array('card' => MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_TYPE,
                        'title' => $product_info['products_name']);

          if ( tep_not_null(MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SITE_ID) ) {
            $data['site'] = MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SITE_ID;
          }

          if ( tep_not_null(MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_USER_ID) ) {
            $data['creator'] = MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_USER_ID;
          }

          $product_description = substr(trim(preg_replace('/\s\s+/', ' ', strip_tags($product_info['products_description']))), 0, 197);

          if ( strlen($product_description) == 197 ) {
            $product_description .= ' ..';
          }

          $data['description'] = $product_description;

          $products_image = $product_info['products_image'];

          $pi_query = tep_db_query("select image from products_images where products_id = '" . (int)$product_info['products_id'] . "' order by sort_order limit 1");
  
          if ( tep_db_num_rows($pi_query) === 1 ) {
            $pi = tep_db_fetch_array($pi_query);

            $products_image = $pi['image'];
          }

          $data['image'] = tep_href_link('images/' . $products_image, '', 'NONSSL', false, false);

          $result = '';

          foreach ( $data as $key => $value ) {
            $result .= '<meta name="twitter:' . tep_output_string_protected($key) . '" content="' . tep_output_string_protected($value) . '" />' . "\n";
          }

          $oscTemplate->addBlock($result, $this->group);
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Twitter Card Module', 'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_STATUS', 'True', 'Do you want to allow Twitter Card tags to be added to your product information pages?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Choose Twitter Card Type', 'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_TYPE', 'summary_large_image', 'Choose Summary or Summary Large Image.  Note that your product images MUST be at least h120px by w120px (Summary) or h150px x w280px (Summary Large Image).', '6', '1', 'tep_cfg_select_option(array(\'summary\', \'summary_large_image\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Twitter Author @username', 'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_USER_ID', '', 'Your @username at Twitter', '6', '0', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Twitter Shop @username', 'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SITE_ID', '', 'Your shops @username at Twitter (or leave blank if it is the same as your @username above).', '6', '0', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_STATUS', 'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_TYPE', 'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_USER_ID', 'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SITE_ID', 'MODULE_HEADER_TAGS_TWITTER_PRODUCT_CARD_SORT_ORDER');
    }
  }
  