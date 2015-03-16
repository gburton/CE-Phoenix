<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_review_button {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function cm_pi_review_button() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_TITLE;
      $this->description = MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_DESCRIPTION;

      if ( defined('MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $HTTP_GET_VARS, $languages_id;
      
      $content_width   = (int)MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_CONTENT_WIDTH;
      $review_button   = NULL;
      
      $reviews_query = tep_db_query("select count(*) as count, avg(reviews_rating) as avgrating from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and r.reviews_id = rd.reviews_id and rd.languages_id = '" . (int)$languages_id . "' and reviews_status = 1");
      $reviews = tep_db_fetch_array($reviews_query);

      if ($reviews['count'] > 0) {
        $review_button .= '<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"><meta itemprop="ratingValue" content="' . $reviews['avgrating'] . '" /><meta itemprop="ratingCount" content="' . $reviews['count'] . '" /></span>';
      }
      
      $review_button .= tep_draw_button(IMAGE_BUTTON_REVIEWS . (($reviews['count'] > 0) ? ' (' . $reviews['count'] . ')' : ''), 'glyphicon glyphicon-comment', tep_href_link('product_reviews.php', tep_get_all_get_params()));
        
      ob_start();
      include(DIR_WS_MODULES . 'content/' . $this->group . '/templates/review_button.php');
      $template = ob_get_clean();

      $oscTemplate->addContent($template, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Product Reviews Button Module', 'MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_STATUS', 'True', 'Should the product reviews button block be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_CONTENT_WIDTH', '6', 'What width container should the content be shown in?', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_STATUS', 'MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_CONTENT_WIDTH', 'MODULE_CONTENT_PRODUCT_INFO_REVIEW_BUTTON_SORT_ORDER');
    }
  }

