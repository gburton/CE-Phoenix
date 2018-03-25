<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  class ht_product_schema {
    var $code = 'ht_product_schema';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_HEADER_TAGS_PRODUCT_SCHEMA_TITLE;
      $this->description = MODULE_HEADER_TAGS_PRODUCT_SCHEMA_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_PRODUCT_SCHEMA_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_PRODUCT_SCHEMA_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_PRODUCT_SCHEMA_STATUS == 'True');
      }
    }
    
    function execute() {
      global $PHP_SELF, $oscTemplate, $product_check, $languages_id, $currency, $currencies;
      
      if (MODULE_HEADER_TAGS_PRODUCT_SCHEMA_PLACEMENT != 'Header') {
        $this->group = 'footer_scripts';
      }

      if ($product_check['total'] > 0) {        
        $product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, p.products_model, p.manufacturers_id, p.products_image, p.products_price, p.products_quantity, p.products_tax_class_id, p.products_date_available, p.products_gtin from products p, products_description pd where p.products_id = '" . (int)$_GET['products_id'] . "' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");

        if ( tep_db_num_rows($product_info_query) ) {
          $product_info = tep_db_fetch_array($product_info_query);  
          
          $products_image = $product_info['products_image'];
          $pi_query = tep_db_query("select image from products_images where products_id = '" . (int)$product_info['products_id'] . "' order by sort_order limit 1");
          if ( tep_db_num_rows($pi_query) ) {
            $pi = tep_db_fetch_array($pi_query);
            $products_image = $pi['image'];
          }
          
          $schema_product = array("@context"    => "http://schema.org",
                                  "@type"       => "Product",
                                  "name"        => tep_db_output($product_info['products_name']),
                                  "image"       => tep_href_link('images/' . $products_image, '', 'NONSSL', false, false),
                                  "url"         => tep_href_link('product_info.php', 'products_id=' . $product_info['products_id'], 'NONSSL', false, false),
                                  "description" => substr(trim(preg_replace('/\s\s+/', ' ', strip_tags($product_info['products_description']))), 0, 197) . '...');
                                  
          if (tep_not_null($product_info['products_model'])) {
            $schema_product['mpn'] = tep_db_output($product_info['products_model']);
          }
          
          if (tep_not_null($product_info['products_gtin']) && defined('MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH')) {
            $schema_product['gtin' .  MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH] = tep_db_output(substr($product_info['products_gtin'], 0-MODULE_CONTENT_PRODUCT_INFO_GTIN_LENGTH));
          }
          
          $schema_product['offers'] = array("@type"         => "Offer",
                                            "priceCurrency" => $currency);
                                            
          if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
            $products_price = $currencies->display_raw($new_price, tep_get_tax_rate($product_info['products_tax_class_id']));
          } else {
            $products_price = $currencies->display_raw($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
          }          
          
          $schema_product['offers']['price'] = $products_price;
          
          $specials_expiry_query = tep_db_query("select expires_date from specials where products_id = '" . (int)$product_info['products_id'] . "' and status = 1");
          if (tep_db_num_rows($specials_expiry_query)) {
            $specials_expiry = tep_db_fetch_array($specials_expiry_query); 
            
            $schema_product['offers']['priceValidUntil'] = $specials_expiry['expires_date'];
          }
          
          $availability = ( $product_info['products_quantity'] > 0 ) ? MODULE_HEADER_TAGS_PRODUCT_SCHEMA_TEXT_IN_STOCK : MODULE_HEADER_TAGS_PRODUCT_SCHEMA_TEXT_OUT_OF_STOCK;
          $schema_product['offers']['availability'] = $availability;          
                                            
          $schema_product['offers']['seller'] = array("@type" => "Organization",
                                                      "name"  => STORE_NAME);
                                                                              
          $manufacturers_name_query = tep_db_query("select manufacturers_name from manufacturers where manufacturers_id='" . (int)$product_info['manufacturers_id'] . "'");
          if (tep_db_num_rows($manufacturers_name_query)) {
            $manufacturers_name = tep_db_fetch_array($manufacturers_name_query);
            $schema_product['manufacturer'] = array("@type" => "Organization",
                                                    "name"  => tep_db_output($manufacturers_name['manufacturers_name']));
          }
                                        
          $average_query = tep_db_query("select AVG(r.reviews_rating) as average, COUNT(r.reviews_rating) as count from reviews r where r.products_id = '" . (int)$product_info['products_id'] . "' and r.reviews_status = 1");
          $average = tep_db_fetch_array($average_query);
          if ($average['count'] > 0) {
            $schema_product['aggregateRating'] = array("@type"       => "AggregateRating",
                                                       "ratingValue" => (int)$average['average'],
                                                       "reviewCount" => (int)$average['count']);
          }
          
          $reviews_query = tep_db_query("select rd.reviews_text, r.reviews_rating, r.reviews_id, r.customers_name, r.date_added, r.reviews_read from reviews r, reviews_description rd where r.products_id = '" . (int)$_GET['products_id'] . "' and r.reviews_id = rd.reviews_id and rd.languages_id = '" . (int)$languages_id . "' and r.reviews_status = '1' order by r.reviews_rating DESC");

          if (tep_db_num_rows($reviews_query) > 0) {
            $schema_product['review'] = array();
            while($reviews = tep_db_fetch_array($reviews_query)) {
              $schema_product['review'][] = array("@type"         => "Review",
                                                  "author"        => tep_db_output($reviews['customers_name']),
                                                  "datePublished" => tep_db_output($reviews['date_added']),
                                                  "description"   => tep_db_output($reviews['reviews_text']),
                                                  "name"          => tep_db_output($product_info['products_name']),
                                                  "reviewRating"  => array("@type"       => "Rating",
                                                                           "bestRating"  => "5",
                                                                           "ratingValue" => (int)$reviews['reviews_rating'],
                                                                           "worstRating" => "1"));
            }
          }
          
          $data = json_encode($schema_product);

          $oscTemplate->addBlock('<script type="application/ld+json">' . $data . '</script>', $this->group);
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_PRODUCT_SCHEMA_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Product Schema Module', 'MODULE_HEADER_TAGS_PRODUCT_SCHEMA_STATUS', 'True', 'Do you want to allow product schema to be added to your product page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Placement', 'MODULE_HEADER_TAGS_PRODUCT_SCHEMA_PLACEMENT', 'Header', 'Where should the code be placed?', '6', '1', 'tep_cfg_select_option(array(\'Header\', \'Footer\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_PRODUCT_SCHEMA_SORT_ORDER', '950', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_PRODUCT_SCHEMA_STATUS', 'MODULE_HEADER_TAGS_PRODUCT_SCHEMA_PLACEMENT', 'MODULE_HEADER_TAGS_PRODUCT_SCHEMA_SORT_ORDER');
    }
  }
  