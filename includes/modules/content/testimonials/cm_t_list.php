<?php
/*
  Copyright (c) 2019, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

  class cm_t_list {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_TESTIMONIALS_LIST_TITLE;
      $this->description = MODULE_CONTENT_TESTIMONIALS_LIST_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_TESTIMONIALS_LIST_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_TESTIMONIALS_LIST_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_TESTIMONIALS_LIST_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $languages_id;
      
      $content_width = MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH;
      $item_width    = MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH_EACH;
      
      $testimonials_query_raw = "select t.testimonials_id, td.testimonials_text, t.date_added, t.customers_name from testimonials t, testimonials_description td where t.testimonials_id = td.testimonials_id ";
      if (MODULE_CONTENT_TESTIMONIALS_LIST_ALL == 'All') {
        $testimonials_query_raw .= "and td.languages_id = '" . (int)$languages_id . "' ";
      }
      $testimonials_query_raw .= "and testimonials_status = 1 order by t.testimonials_id DESC";
      
      $testimonials_split = new splitPageResults($testimonials_query_raw, MODULE_CONTENT_TESTIMONIALS_LIST_PAGING);
      
      if ($testimonials_split->number_of_rows > 0) {
        $testimonials_query = tep_db_query($testimonials_split->sql_query);

        ob_start();
        include('includes/modules/content/' . $this->group . '/templates/tpl_' . basename(__FILE__));
        $template = ob_get_clean(); 
      }
      else {
        $template = '<div class="col">';
          $template .= '<div class="alert alert-info" role="alert">';
            $template .= MODULE_CONTENT_TESTIMONIALS_LIST_NO_TESTIMONIALS;
          $template .= '</div>';
        $template .= '</div>';
      }
      
      $oscTemplate->addContent($template, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_TESTIMONIALS_LIST_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable List Module', 'MODULE_CONTENT_TESTIMONIALS_LIST_STATUS', 'True', 'Do you want to enable this module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH', '12', 'What width container should the content be shown in? (12 = full width, 6 = half width).', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('View Testimonials', 'MODULE_CONTENT_TESTIMONIALS_LIST_ALL', 'All', 'Do you want to show all Testimonials or language specific Testimonials?', '6', '1', 'tep_cfg_select_option(array(\'All\', \'Language Specific\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Number of Testimonials', 'MODULE_CONTENT_TESTIMONIALS_LIST_PAGING', '12', 'How many Testimonials to display per page.', '6', '5', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Item Width', 'MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH_EACH', '6', 'What width container should the each Testimonial be shown in? (12 = full width, 6 = half width).', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_TESTIMONIALS_LIST_SORT_ORDER', '200', 'Sort order of display. Lowest is displayed first.', '6', '5', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_CONTENT_TESTIMONIALS_LIST_STATUS', 'MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH', 'MODULE_CONTENT_TESTIMONIALS_LIST_ALL', 'MODULE_CONTENT_TESTIMONIALS_LIST_PAGING', 'MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH_EACH', 'MODULE_CONTENT_TESTIMONIALS_LIST_SORT_ORDER');
    }
  }
  