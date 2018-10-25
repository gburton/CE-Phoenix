<?php
/*
  $Id: 

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  class cm_ip_product_listing {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_IP_PRODUCT_LISTING_TITLE;
      $this->description = MODULE_CONTENT_IP_PRODUCT_LISTING_DESCRIPTION;
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_IP_PRODUCT_LISTING_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_IP_PRODUCT_LISTING_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_IP_PRODUCT_LISTING_STATUS == 'True');
      }      
    }

    function execute() {
      global $oscTemplate, $category, $cPath_array, $cPath, $current_category_id, $languages_id, $messageStack, $currencies, $currency, $PHP_SELF;
      
      $content_width  = MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH;
      $item_width     = MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH_EACH;
      
// create column list
			$define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
													 'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
													 'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
													 'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
													 'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
													 'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
													 'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
													 'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW);
	
			asort($define_list);
	
			$column_list = array();
			foreach($define_list as $key => $value) {
				if ($value > 0) $column_list[] = $key;
			}
	
// show the products of a specified manufacturer
			if (isset($_GET['manufacturers_id']) && !empty($_GET['manufacturers_id'])) {
				if (isset($_GET['filter_id']) && tep_not_null($_GET['filter_id'])) {
// We are asked to show only a specific category
					$listing_sql = "select p.products_model, pd.products_name, m.manufacturers_name, p.products_quantity, p.products_image, p.products_weight, p.products_id, pd.products_description, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_quantity as in_stock, if(s.status, 1, 0) as is_special from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$_GET['filter_id'] . "'";
				} else {
// We show them all
					$listing_sql = "select p.products_model, pd.products_name, m.manufacturers_name, p.products_quantity, p.products_image, p.products_weight, p.products_id, pd.products_description, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_quantity as in_stock, if(s.status, 1, 0) as is_special from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m where p.products_status = '1' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "'";
				}
			} else {
// show the products in a given categorie
				if (isset($_GET['filter_id']) && tep_not_null($_GET['filter_id'])) {
// We are asked to show only specific catgeory
					$listing_sql = "select p.products_model, pd.products_name, m.manufacturers_name, p.products_quantity, p.products_image, p.products_weight, p.products_id, pd.products_description, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_quantity as in_stock, if(s.status, 1, 0) as is_special from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$_GET['filter_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$current_category_id . "'";
				} else {
// We show them all
					$listing_sql = "select p.products_model, pd.products_name, m.manufacturers_name, p.products_quantity, p.products_image, p.products_weight, p.products_id, pd.products_description, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_quantity as in_stock, if(s.status, 1, 0) as is_special from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_status = '1' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$current_category_id . "'";
				}
			}
	
			if ( (!isset($_GET['sort'])) || (!preg_match('/^[1-8][ad]$/', $_GET['sort'])) || (substr($_GET['sort'], 0, 1) > sizeof($column_list)) ) {
				for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
					if ($column_list[$i] == 'PRODUCT_LIST_NAME') {
						$_GET['sort'] = $i+1 . 'a';
						$listing_sql .= " order by pd.products_name";
						break;
					}
				}
			} else {
				$sort_col = substr($_GET['sort'], 0 , 1);
				$sort_order = substr($_GET['sort'], 1);
	
				switch ($column_list[$sort_col-1]) {
					case 'PRODUCT_LIST_MODEL':
						$listing_sql .= " order by p.products_model " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
						break;
					case 'PRODUCT_LIST_NAME':
						$listing_sql .= " order by pd.products_name " . ($sort_order == 'd' ? 'desc' : '');
						break;
					case 'PRODUCT_LIST_MANUFACTURER':
						$listing_sql .= " order by m.manufacturers_name " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
						break;
					case 'PRODUCT_LIST_QUANTITY':
						$listing_sql .= " order by p.products_quantity " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
						break;
					case 'PRODUCT_LIST_IMAGE':
						$listing_sql .= " order by pd.products_name";
						break;
					case 'PRODUCT_LIST_WEIGHT':
						$listing_sql .= " order by p.products_weight " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
						break;
					case 'PRODUCT_LIST_PRICE':
						$listing_sql .= " order by final_price " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
						break;
				}
			}
			
			$template = $output = null;
	
// optional Product List Filter
			if (PRODUCT_LIST_FILTER > 0) {
				if (isset($_GET['manufacturers_id']) && !empty($_GET['manufacturers_id'])) {
					$filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' order by cd.categories_name";
				} else {
					$filterlist_sql= "select distinct m.manufacturers_id as id, m.manufacturers_name as name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_MANUFACTURERS . " m where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "' order by m.manufacturers_name";
				}
	
				$filterlist_query = tep_db_query($filterlist_sql);
				if (tep_db_num_rows($filterlist_query) > 1) {
					$output .= '<div class="filter-list">' . PHP_EOL;
					$output .= tep_draw_form('filter', 'index.php', 'get') . PHP_EOL;
					if (isset($_GET['manufacturers_id']) && !empty($_GET['manufacturers_id'])) {
						$output .= tep_draw_hidden_field('manufacturers_id', $_GET['manufacturers_id']);
						$options = array(array('id' => '', 'text' => TEXT_ALL_CATEGORIES));
					} else {
						$output .= tep_draw_hidden_field('cPath', $cPath);
						$options = array(array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS));
					}
					$output .= tep_draw_hidden_field('sort', $_GET['sort']);
					while ($filterlist = tep_db_fetch_array($filterlist_query)) {
						$options[] = array('id' => $filterlist['id'], 'text' => $filterlist['name']);
					}
					$output .= tep_draw_pull_down_menu('filter_id', $options, (isset($_GET['filter_id']) ? $_GET['filter_id'] : ''), 'onchange="this.form.submit()" class="form-control input-sm"');
					$output .= tep_hide_session_id() . PHP_EOL;
					$output .= '</form>' . PHP_EOL;
					$output .= '</div><br class="d-block d-sm-none">' . PHP_EOL;
				}
			}

      ob_start();
      include('includes/modules/product_listing.php');
      $output .= ob_get_clean();
      
      $content_width = MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH;

      $template .= '<div class="col-sm-' . $content_width . ' cm-ip-product-listing">' . PHP_EOL;
        $template .= $output;
      $template .= '</div>' . PHP_EOL;

      $oscTemplate->addContent($template, $this->group);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_IP_PRODUCT_LISTING_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Product Listing Module', 'MODULE_CONTENT_IP_PRODUCT_LISTING_STATUS', 'True', 'Should this module be enabled?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH', '12', 'What width container should the content be shown in?', '6', '2', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Item Width', 'MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH_EACH', '4', 'What width container should each Item be shown in?', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_IP_PRODUCT_LISTING_SORT_ORDER', '200', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
    
    function keys() {
      return array('MODULE_CONTENT_IP_PRODUCT_LISTING_STATUS', 'MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH', 'MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH_EACH', 'MODULE_CONTENT_IP_PRODUCT_LISTING_SORT_ORDER');
    }  
  }
  