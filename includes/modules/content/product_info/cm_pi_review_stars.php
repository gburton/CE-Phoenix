<?php
/*
  Copyright (c) 2020, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

  class cm_pi_review_stars {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));
      
      $this->title = MODULE_CONTENT_PI_REVIEW_STARS_TITLE;
      $this->description = MODULE_CONTENT_PI_REVIEW_STARS_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_PI_REVIEW_STARS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_PI_REVIEW_STARS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_PI_REVIEW_STARS_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $product_info;
      
      $pid = (int)$product_info['products_id'];
      $review_stars_array = array();

      $review_average_query = tep_db_query("select AVG(reviews_rating) as average, COUNT(reviews_rating) as count from reviews where products_id = '" . $pid . "' and reviews_status = 1");
      $review_average = tep_db_fetch_array($review_average_query);

      if ($review_average['count'] > 0) {        
        $review_stars_array[] = tep_draw_stars((int)$review_average['average']);

        if ((int)$review_average['count'] == 1) {
          $review_stars_array[] = sprintf(MODULE_CONTENT_PI_REVIEW_STARS_COUNT_ONE, (int)$review_average['count']);
        }
        else {
          $review_stars_array[] = sprintf(MODULE_CONTENT_PI_REVIEW_STARS_COUNT, (int)$review_average['count']);
        }

        $review_stars_array['border-left ml-2 pl-3']  = '<a href="' . tep_href_link('ext/modules/content/reviews/write.php', 'products_id=' . $pid) . '">' . MODULE_CONTENT_PI_REVIEW_STARS_DO_REVIEW . '</a>';
      }
      else {
        $review_stars_array[] = sprintf(MODULE_CONTENT_PI_REVIEW_STARS_COUNT, 0);
        
        $review_stars_array['border-left ml-2 pl-3']  = '<a href="' . tep_href_link('ext/modules/content/reviews/write.php', 'products_id=' . $pid) . '">' . MODULE_CONTENT_PI_REVIEW_STARS_DO_FIRST_REVIEW . '</a>';
      }      
      
      $content_width = (int)MODULE_CONTENT_PI_REVIEW_STARS_CONTENT_WIDTH;
      
      $tpl_data = ['group' => $this->group, 'file' => __FILE__];
      include 'includes/modules/content/cm_template.php';
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_PI_REVIEW_STARS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Review Stars/Link Module', 'MODULE_CONTENT_PI_REVIEW_STARS_STATUS', 'True', 'Should this module be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_PI_REVIEW_STARS_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_PI_REVIEW_STARS_SORT_ORDER', '55', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_PI_REVIEW_STARS_STATUS', 'MODULE_CONTENT_PI_REVIEW_STARS_CONTENT_WIDTH', 'MODULE_CONTENT_PI_REVIEW_STARS_SORT_ORDER');
    }
  }
  