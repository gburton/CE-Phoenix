<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  class bm_upcoming {
    var $code = 'bm_upcoming';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function bm_upcoming() {
      $this->title = MODULE_BOXES_UPCOMING_TITLE;
      $this->description = MODULE_BOXES_UPCOMING_DESCRIPTION;

      if ( defined('MODULE_BOXES_UPCOMING_STATUS') ) {
        $this->sort_order = MODULE_BOXES_UPCOMING_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_UPCOMING_STATUS == 'True');
		$this->group = 'boxes_content';
      }
    }

    function execute() {
      global $oscTemplate, $languages_id, $PHP_SELF, $current_category_id;
      if (tep_not_null(MODULE_BOXES_UPCOMING_PAGES)) {
        $pages_array = array();

        foreach (explode(';', MODULE_BOXES_UPCOMING_PAGES) as $page) {
          $page = trim($page);

          if (!empty($page)) {
            $pages_array[] = $page;
          }
        }
        if (in_array(basename($PHP_SELF), $pages_array)) {
		// Check if we are at index.php
          if ($page == basename($PHP_SELF)) {
            if ((((!isset($current_category_id)) || ($current_category_id == '0')) && MODULE_BOXES_UPCOMING_MAIN == 'True') || (($current_category_id != '0')) && MODULE_BOXES_UPCOMING_CATEGORIES == 'True'){
              $expected_query_raw = "select p.products_id, pd.products_name, products_date_available as date_expected from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where to_days(products_date_available) >= to_days(now()) and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by " . EXPECTED_PRODUCTS_FIELD . " " . EXPECTED_PRODUCTS_SORT . " limit " . MAX_DISPLAY_UPCOMING_PRODUCTS;
              $expected_query = tep_db_query($expected_query_raw );
              $num_rows =tep_db_num_rows($expected_query);
            } else {
		        $num_rows =0;
		        }
		      } else { // other page, show all new products
            $expected_query_raw = "select p.products_id, pd.products_name, products_date_available as date_expected from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where to_days(products_date_available) >= to_days(now()) and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by " . EXPECTED_PRODUCTS_FIELD . " " . EXPECTED_PRODUCTS_SORT . " limit " . MAX_DISPLAY_UPCOMING_PRODUCTS;
            $expected_query = tep_db_query($expected_query_raw );
            $num_rows =tep_db_num_rows($expected_query);
          }
          if ($num_rows> 0) {
            $data = '<!-- upcoming products -->' . "\n";
            $data .= '  <div class="panel panel-info">' . "\n";
            $data .= '    <div class="panel-heading">' . "\n";
            $data .= '      <div class="pull-left">' . TABLE_HEADING_UPCOMING_PRODUCTS . '</div>' . "\n";
            $data .= '      <div class="pull-right">' . TABLE_HEADING_DATE_EXPECTED . '</div>' . "\n";
            $data .= '      <div class="clearfix"></div>' . "\n";
            $data .= '    </div>' . "\n";
            $data .= '    <div class="panel-body">' . "\n";

            while ($expected = tep_db_fetch_array($expected_query)) {
              $data .= '      <div class="pull-left"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $expected['products_id']) . '">' . $expected['products_name'] . '</a></div>' . "\n" .
              '      <div class="pull-right">' . tep_date_short($expected['date_expected']) . '</div>' .
              '<div class="clearfix"></div>' . "\n".
              '    </div>' . "\n" .
              '  </div>' . "\n" . '<!-- end upcoming products -->' . "\n";

            }
          $oscTemplate->addBlock($data, $this->group);
          }
        }
      }
    }
	
    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_UPCOMING_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Front page message Module', 'MODULE_BOXES_UPCOMING_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Pages', 'MODULE_BOXES_UPCOMING_PAGES', '" . implode(';', $this->get_default_pages()) . "', 'The pages to add the message to.', '6', '0', 'bm_upcoming_show_pages', 'bm_upcoming_edit_pages(', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show on Main page', 'MODULE_BOXES_UPCOMING_MAIN', 'True', 'Do you want to display the module on main page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show on categories', 'MODULE_BOXES_UPCOMING_CATEGORIES', 'True', 'Do you want to display the module on categories page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_UPCOMING_SORT_ORDER', '1050', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_UPCOMING_STATUS', 'MODULE_BOXES_UPCOMING_PAGES', 'MODULE_BOXES_UPCOMING_MAIN', 'MODULE_BOXES_UPCOMING_CATEGORIES', 'MODULE_BOXES_UPCOMING_SORT_ORDER');
    }
    function get_default_pages() {
      return array('index.php'
                   );
    }
  }


  function bm_upcoming_show_pages($text) {
    return nl2br(implode("\n", explode(';', $text)));
  }

  function bm_upcoming_edit_pages($values, $key) {
    global $PHP_SELF;

    $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
    $files_array = array();
	  if ($dir = @dir(DIR_FS_CATALOG)) {
	    while ($file = $dir->read()) {
	      if (!is_dir(DIR_FS_CATALOG . $file)) {
	        if (substr($file, strrpos($file, '.')) == $file_extension) {
            $files_array[] = $file;
          }
        }
      }
      sort($files_array);
      $dir->close();
    }

    $values_array = explode(';', $values);

    $output = '';
    foreach ($files_array as $file) {
      $output .= tep_draw_checkbox_field('bm_upcoming_file[]', $file, in_array($file, $values_array)) . '&nbsp;' . tep_output_string($file) . '<br />';
    }

    if (!empty($output)) {
      $output = '<br />' . substr($output, 0, -6);
    }

    $output .= tep_draw_hidden_field('configuration[' . $key . ']', '', 'id="htrn_files"');

    $output .= '<script>
                function htrn_update_cfg_value() {
                  var htrn_selected_files = \'\';

                  if ($(\'input[name="bm_upcoming_file[]"]\').length > 0) {
                    $(\'input[name="bm_upcoming_file[]"]:checked\').each(function() {
                      htrn_selected_files += $(this).attr(\'value\') + \';\';
                    });

                    if (htrn_selected_files.length > 0) {
                      htrn_selected_files = htrn_selected_files.substring(0, htrn_selected_files.length - 1);
                    }
                  }

                  $(\'#htrn_files\').val(htrn_selected_files);
                }

                $(function() {
                  htrn_update_cfg_value();

                  if ($(\'input[name="bm_upcoming_file[]"]\').length > 0) {
                    $(\'input[name="bm_upcoming_file[]"]\').change(function() {
                      htrn_update_cfg_value();
                    });
                  }
                });
                </script>';

    return $output;
  }
?>