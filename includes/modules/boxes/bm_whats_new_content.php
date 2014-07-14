<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  class bm_whats_new_content {
    var $code = 'bm_whats_new_content';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function bm_whats_new_content() {
      $this->title = MODULE_BOXES_WHATS_NEW_CONTENT_TITLE;
      $this->description = MODULE_BOXES_WHATS_NEW_CONTENT_DESCRIPTION;

      if ( defined('MODULE_BOXES_WHATS_NEW_CONTENT_STATUS') ) {
        $this->sort_order = MODULE_BOXES_WHATS_NEW_CONTENT_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_WHATS_NEW_CONTENT_STATUS == 'True');
		$this->group = 'boxes_content';
      }
    }

    function execute() {
      global $oscTemplate, $current_category_id, $currencies, $languages_id, $PHP_SELF;
      if (tep_not_null(MODULE_BOXES_WHATS_NEW_CONTENT_PAGES)) {
        $pages_array = array();

        foreach (explode(';', MODULE_BOXES_WHATS_NEW_CONTENT_PAGES) as $page) {
          $page = trim($page);

          if (!empty($page)) {
            $pages_array[] = $page;
          }
        }
        if (in_array(basename($PHP_SELF), $pages_array)) {
		// Check if we are at index.php
          if ($page == basename($PHP_SELF)) {
            if (((!isset($current_category_id)) || ($current_category_id == '0')) && MODULE_BOXES_WHATS_NEW_CONTENT_MAIN == 'True') {// Main page, active
            $new_products_query = tep_db_query( "select p.products_id, p.products_image, p.products_tax_class_id, pd.products_name, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
             $num_new_products = tep_db_num_rows($new_products_query);
             } elseif ((($current_category_id != '0')) && MODULE_BOXES_WHATS_NEW_CONTENT_CATEGORIES == 'True') {// Categories, active
             $new_products_query = tep_db_query( "select distinct p.products_id, p.products_image, p.products_tax_class_id, pd.products_name, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and c.parent_id = '" . (int)$current_category_id . "' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
             $num_new_products = tep_db_num_rows($new_products_query);
            } else {
		  $num_new_products =0;
		  }
		} else { // other page, show all new products
             $new_products_query = tep_db_query("select p.products_id, p.products_image, p.products_tax_class_id, pd.products_name, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
             $num_new_products = tep_db_num_rows($new_products_query);
          }

          if ($num_new_products > 0) {
            $data = NULL;
            $data = '  <!-- whats new -->' . "\n";
            $data .= '  <h3>' . TABLE_HEADING_NEW_PRODUCTS . '</h3>';
            $data .= '<div class="row">' . "\n";

            while ($new_products = tep_db_fetch_array($new_products_query)) {
              $data .= '<div class="col-sm-6 col-md-4">' . "\n";
              $data .= '  <div class="thumbnail">';
              $data .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $new_products['products_image'], $new_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>' . "\n";
              $data .= '    <div class="caption">' . "\n";;
              $data .= '       <p class="text-center"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . $new_products['products_name'] . '</a></p>' . "\n";
              $data .= '       <hr>' . "\n";
              $data .= '       <p class="text-center">' . $currencies->display_price($new_products['products_price'], tep_get_tax_rate($new_products['products_tax_class_id'])) . '</p>' . "\n";
              $data .= '      <div class="text-center">' . "\n";
              $data .= '          <div class="btn-group">';
              $data .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'products_id=' .   $new_products['products_id']) . '" class="btn btn-default" role="button">' . SMALL_IMAGE_BUTTON_VIEW . '</a>';
              $data .= '<a href="' . tep_href_link($PHP_SELF, tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' .  $new_products['products_id']) . '" class="btn btn-success" role="button">' . SMALL_IMAGE_BUTTON_BUY . '</a>';
              $data .= '</div>' . "\n";
              $data .= '      </div>' . "\n";
              $data .= '    </div>' . "\n";
              $data .= '  </div>' . "\n";
              $data .= '</div>' . "\n";
            }
            $data .= '</div>' . "\n";
            $data .= '  <!-- end whats new -->' . "\n";
            $oscTemplate->addBlock($data, $this->group);
          }
        }
      }
    }
	
    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_WHATS_NEW_CONTENT_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable What\'s New Module', 'MODULE_BOXES_WHATS_NEW_CONTENT_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Pages', 'MODULE_BOXES_WHATS_NEW_CONTENT_PAGES', '" . implode(';', $this->get_default_pages()) . "', 'The pages to add the What\'s new box to.', '6', '0', 'bm_whats_new_content_show_pages', 'bm_whats_new_content_edit_pages(', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show on Main page', 'MODULE_BOXES_WHATS_NEW_CONTENT_MAIN', 'True', 'Do you want to display the module on main page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show on categories', 'MODULE_BOXES_WHATS_NEW_CONTENT_CATEGORIES', 'True', 'Do you want to display the module on categories page?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_WHATS_NEW_CONTENT_SORT_ORDER', '1010', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_WHATS_NEW_CONTENT_STATUS', 'MODULE_BOXES_WHATS_NEW_CONTENT_PAGES', 'MODULE_BOXES_WHATS_NEW_CONTENT_MAIN', 'MODULE_BOXES_WHATS_NEW_CONTENT_CATEGORIES', 'MODULE_BOXES_WHATS_NEW_CONTENT_SORT_ORDER');
    }
    function get_default_pages() {
      return array('index.php'
                   );
    }
}
  function bm_whats_new_content_show_pages($text) {
    return nl2br(implode("\n", explode(';', $text)));
  }

  function bm_whats_new_content_edit_pages($values, $key) {
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
      $output .= tep_draw_checkbox_field('bm_whats_new_content_file[]', $file, in_array($file, $values_array)) . '&nbsp;' . tep_output_string($file) . '<br />';
    }

    if (!empty($output)) {
      $output = '<br />' . substr($output, 0, -6);
    }

    $output .= tep_draw_hidden_field('configuration[' . $key . ']', '', 'id="htrn_files"');

    $output .= '<script>
                function htrn_update_cfg_value() {
                  var htrn_selected_files = \'\';

                  if ($(\'input[name="bm_whats_new_content_file[]"]\').length > 0) {
                    $(\'input[name="bm_whats_new_content_file[]"]:checked\').each(function() {
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

                  if ($(\'input[name="bm_whats_new_content_file[]"]\').length > 0) {
                    $(\'input[name="bm_whats_new_content_file[]"]\').change(function() {
                      htrn_update_cfg_value();
                    });
                  }
                });
                </script>';

    return $output;
  }
?>
