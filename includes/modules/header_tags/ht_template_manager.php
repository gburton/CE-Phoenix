<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  class ht_template_manager {
    var $code = 'ht_template_manager';
    var $group = 'header_tags';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function ht_template_manager() {
      $this->title = MODULE_HEADER_TAGS_TEMPLATE_MANAGER_TITLE;
      $this->description = MODULE_HEADER_TAGS_TEMPLATE_MANAGER_DESCRIPTION;

      if ( defined('MODULE_HEADER_TAGS_TEMPLATE_MANAGER_STATUS') ) {
        $this->sort_order = MODULE_HEADER_TAGS_TEMPLATE_MANAGER_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_TEMPLATE_MANAGER_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate;

    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_HEADER_TAGS_TEMPLATE_MANAGER_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable template manager', 'MODULE_HEADER_TAGS_TEMPLATE_MANAGER_STATUS', 'True', 'Standard templates when false.', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Active Template', 'TEMPLATE', '" . implode(';', $this->get_default_pages()) . "', 'The default template for your shop.', '6', '4', 'ht_template_manager_show_folders', 'ht_template_manager_select_folder(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_HEADER_TAGS_TEMPLATE_MANAGER_SORT_ORDER', '0', 'Sort order of call. Lowest is called first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_HEADER_TAGS_TEMPLATE_MANAGER_STATUS', 'TEMPLATE', 'MODULE_HEADER_TAGS_TEMPLATE_MANAGER_SORT_ORDER');
    }
    function get_default_pages() {
      return array('Internal');
    }	
  }
  function ht_template_manager_show_folders($text) {
    return nl2br(implode("\n", explode(';', $text)));
  }

  function ht_template_manager_select_folder($values, $key) {
    global $PHP_SELF;

 	  $default_array = array('Internal');
      $folders_array = glob(DIR_FS_CATALOG . DIR_WS_INCLUDES . "templates/*", GLOB_ONLYDIR|GLOB_NOSORT);
      $files_array = array_merge($default_array,$folders_array);

    print var_dump($files_array);

    $values_array = explode(';', $values);

    $output = '';
    foreach ($files_array as $file) {
      $output .= tep_draw_radio_field('ht_template_manager_folder[]', basename($file), in_array(basename($file), $values_array)) . '&nbsp;' . tep_output_string(basename($file)) . '<br />';
    }

    if (!empty($output)) {
      $output = '<br />' . substr($output, 0, -6);
    }

    $output .= tep_draw_hidden_field('configuration[' . $key . ']', '', 'id="htrn_files"');

    $output .= '<script>
                function htrn_update_cfg_value() {
                  var htrn_selected_files = \'\';

                  if ($(\'input[name="ht_template_manager_folder[]"]\').length > 0) {
                    $(\'input[name="ht_template_manager_folder[]"]:checked\').each(function() {
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

                  if ($(\'input[name="ht_template_manager_folder[]"]\').length > 0) {
                    $(\'input[name="ht_template_manager_folder[]"]\').change(function() {
                      htrn_update_cfg_value();
                    });
                  }
                });
                </script>';

    return $output;
  }  
?>
