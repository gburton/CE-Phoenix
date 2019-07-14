<?php
/*
  Copyright (c) 2018, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

  class cm_pi_gallery {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_PI_GALLERY_TITLE;
      $this->description = MODULE_CONTENT_PI_GALLERY_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_PI_GALLERY_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_PI_GALLERY_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_PI_GALLERY_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $product_info;
      
      $content_width = (int)MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH;
      
      if (tep_not_null($product_info['products_image'])) {
        $photoset_layout = (int)MODULE_HEADER_TAGS_PRODUCT_COLORBOX_LAYOUT;
        $photoset_limit  = array_sum(str_split(MODULE_HEADER_TAGS_PRODUCT_COLORBOX_LAYOUT));

        $pi_query = tep_db_query("select image, htmlcontent from products_images where products_id = '" . (int)$product_info['products_id'] . "' order by sort_order limit " . (int)$photoset_limit);
        $pi_total = tep_db_num_rows($pi_query);

        $pi_output = null;
        
        if ($pi_total > 0) {
          $pi_counter = 0;
          $pi_html = array();
          
          $pi_output .= '<div class="piGal" data-imgcount="' . $photoset_layout . '">';
          while ($pi = tep_db_fetch_array($pi_query)) {
            $pi_counter++;

            if (tep_not_null($pi['htmlcontent'])) {
              $pi_html[] = '<div id="piGalDiv_' . $pi_counter . '">' . $pi['htmlcontent'] . '</div>';
            }

            $pi_output .= tep_image('images/' . $pi['image'], '', '', '', 'id="piGalImg_' . $pi_counter . '"');
          }          
          $pi_output .= '</div>';
          
          if ( !empty($pi_html) ) {
            $pi_output .= '<div hidden>' . implode('', $pi_html) . '</div>';
          }          
        }
        else {
          $pi_output .= '<div class="piGal">';
          $pi_output .= tep_image('images/' . $product_info['products_image'], htmlspecialchars($product_info['products_name']));
          $pi_output .= '</div>';          
        }

        ob_start();
        include('includes/modules/content/' . $this->group . '/templates/tpl_' . basename(__FILE__));
        $template = ob_get_clean();

        $oscTemplate->addContent($template, $this->group);
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_PI_GALLERY_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Gallery Module', 'MODULE_CONTENT_PI_GALLERY_STATUS', 'True', 'Should this module be shown on the product info page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH', '4', 'What width container should the content be shown in?', '6', '1', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_PI_GALLERY_SORT_ORDER', '65', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_PI_GALLERY_STATUS', 'MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH', 'MODULE_CONTENT_PI_GALLERY_SORT_ORDER');
    }
  }
  