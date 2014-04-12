<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  class bm_reviews {
    var $code = 'bm_reviews';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function bm_reviews() {
      $this->title = MODULE_BOXES_REVIEWS_TITLE;
      $this->description = MODULE_BOXES_REVIEWS_DESCRIPTION;

      if ( defined('MODULE_BOXES_REVIEWS_STATUS') ) {
        $this->sort_order = MODULE_BOXES_REVIEWS_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_REVIEWS_STATUS == 'True');

        switch (MODULE_BOXES_REVIEWS_CONTENT_PLACEMENT) {
          case 'Left Column':
          $this->group = 'boxes_column_left';
          break;
          case 'Product Page':
          $this->group = 'boxes_product_page';
          break;
          default:
          $this->group = 'boxes_column_right';
        }
      }
    }

    function execute() {
      global $languages_id, $HTTP_GET_VARS, $currencies, $oscTemplate;

      $random_select = "select r.reviews_id, r.reviews_rating, p.products_id, p.products_image, pd.products_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = r.products_id and r.reviews_id = rd.reviews_id and rd.languages_id = '" . (int)$languages_id . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and r.reviews_status = 1";
      if (isset($HTTP_GET_VARS['products_id'])) {
        $random_select .= " and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "'";
      }
      $random_select .= " order by r.reviews_id desc limit " . MAX_RANDOM_SELECT_REVIEWS;
      $random_product = tep_random_select($random_select);

      $reviews_box_contents = '';

      if ($random_product) {
// display random review box
        $rand_review_query = tep_db_query("select substring(reviews_text, 1, 60) as reviews_text from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . (int)$random_product['reviews_id'] . "' and languages_id = '" . (int)$languages_id . "'");
        $rand_review = tep_db_fetch_array($rand_review_query);

        $rand_review_text = tep_break_string(tep_output_string_protected($rand_review['reviews_text']), 15, '-<br />');

        $reviews_box_contents .= '<div class="text-center"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS, 'products_id=' . $random_product['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $random_product['products_image'], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></div><div><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS, 'products_id=' . $random_product['products_id']) . '">' . $rand_review_text . '</a>...</div><div class="text-center" title="' .  sprintf(MODULE_BOXES_REVIEWS_BOX_TEXT_OF_5_STARS, $random_product['reviews_rating']) . '">' . tep_draw_stars($random_product['reviews_rating']) . '</div>';
      } elseif (isset($HTTP_GET_VARS['products_id'])) {
// display 'write a review' box
        $reviews_box_contents .= '<span class="glyphicon glyphicon-thumbs-up"></span> <a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $HTTP_GET_VARS['products_id']) . '">' . MODULE_BOXES_REVIEWS_BOX_WRITE_REVIEW .'</a>';
      } else {
// display 'no reviews' box
        $reviews_box_contents .= '<p>' . MODULE_BOXES_REVIEWS_BOX_NO_REVIEWS . '</p>';
      }

      $data = '<div class="panel panel-default">' .
              '  <div class="panel-heading"><a href="' . tep_href_link(FILENAME_REVIEWS) . '">' . MODULE_BOXES_REVIEWS_BOX_TITLE . '</a></div>' .
              '  <div class="panel-body">' . $reviews_box_contents . '</div>' .
              '</div>';

      if (($this->group == 'boxes_product_page') && (isset($HTTP_GET_VARS['products_id']))) {
        $review_query = tep_db_query("select SUBSTRING_INDEX(rd.reviews_text, ' ', 20) as reviews_text, r.reviews_rating, r.reviews_id, r.customers_name, r.date_added, r.reviews_read, p.products_id, p.products_price, p.products_tax_class_id, p.products_image, p.products_model, pd.products_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where r.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and r.reviews_id = rd.reviews_id and rd.languages_id = '" . (int)$languages_id . "' and r.products_id = p.products_id and p.products_status = '1' and r.reviews_status = '1' and p.products_id = pd.products_id and pd.language_id = '". (int)$languages_id . "' order by r.reviews_rating DESC limit " . (int)MODULE_BOXES_REVIEWS_LISTING_LIMIT);

        $data = NULL;

        if (tep_db_num_rows($review_query) > 0) {
          $data .= '<div class="reviews col-xs-12">';
          $data .= '<h2>' . REVIEWS_TEXT_TITLE . '</h2><hr>';
          while ($review = tep_db_fetch_array($review_query)) {
            $data .=  '<blockquote class="col-sm-6">';
            $data .=   '  <p>' . tep_output_string_protected($review['reviews_text']) . ' ... </p>';
            $review_name = tep_output_string_protected($review['customers_name']);
            $data .=   '  <footer>' . sprintf(REVIEWS_TEXT_RATED, tep_draw_stars($review['reviews_rating']), $review_name, $review_name) . '</footer>';
            $data .=   '</blockquote>';
          }
          $data .= '</div>';
          $data .= '<div class="clearfix"></div>';
        }
      }

      $oscTemplate->addBlock($data, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_REVIEWS_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Reviews Module', 'MODULE_BOXES_REVIEWS_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_BOXES_REVIEWS_CONTENT_PLACEMENT', 'Product Page', 'Should the module be loaded in the left/right column or directly in the Product Page?', '6', '1', 'tep_cfg_select_option(array(\'Left Column\', \'Right Column\', \'Product Page\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Display', 'MODULE_BOXES_REVIEWS_LISTING_LIMIT', '4', 'Maximum Number of Reviews to Display if Content Placement is in Product Page', '6', '1', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_REVIEWS_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '1', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_REVIEWS_STATUS', 'MODULE_BOXES_REVIEWS_CONTENT_PLACEMENT', 'MODULE_BOXES_REVIEWS_LISTING_LIMIT', 'MODULE_BOXES_REVIEWS_SORT_ORDER');
    }
  }
?>
