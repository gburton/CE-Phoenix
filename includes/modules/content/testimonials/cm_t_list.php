<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_t_list extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_TESTIMONIALS_LIST_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      $content_width = MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH;
      $item_width    = MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH_EACH;

      $testimonials_query_raw = "SELECT t.*, td.* FROM testimonials t, testimonials_description td WHERE t.testimonials_id = td.testimonials_id";
      if (MODULE_CONTENT_TESTIMONIALS_LIST_ALL != 'All') {
        $testimonials_query_raw .= " AND td.languages_id = " . (int)$_SESSION['languages_id'];
      }
      $testimonials_query_raw .= " AND t.testimonials_status = 1 order by t.testimonials_id DESC";

      $testimonials_split = new splitPageResults($testimonials_query_raw, MODULE_CONTENT_TESTIMONIALS_LIST_PAGING);

      if ($testimonials_split->number_of_rows > 0) {
        $testimonials_query = tep_db_query($testimonials_split->sql_query);
      }

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_TESTIMONIALS_LIST_STATUS' => [
          'title' => 'Enable List Module',
          'value' => 'True',
          'desc' => 'Do you want to enable this module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_TESTIMONIALS_LIST_ALL' => [
          'title' => 'View Testimonials',
          'value' => 'All',
          'desc' => 'Do you want to show all Testimonials or language specific Testimonials?',
          'set_func' => "tep_cfg_select_option(['All', 'Language Specific'], ",
        ],
        'MODULE_CONTENT_TESTIMONIALS_LIST_PAGING' => [
          'title' => 'Number of Testimonials',
          'value' => '12',
          'desc' => 'How many Testimonials to display per page.',
        ],
        'MODULE_CONTENT_TESTIMONIALS_LIST_CONTENT_WIDTH_EACH' => [
          'title' => 'Item Width',
          'value' => '6',
          'desc' => 'What width container should the each Testimonial be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_TESTIMONIALS_LIST_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '200',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
