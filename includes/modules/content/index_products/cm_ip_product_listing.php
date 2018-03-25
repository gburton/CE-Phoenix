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
      global $oscTemplate, $category, $cPath_array, $cPath, $current_category_id, $languages_id, $messageStack, $currencies, $PHP_SELF;
      
      $content_width  = MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH;
      //$category_width = MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH_EACH;

	  $OSCOM_Category = new osC_Category($current_category_id);
      $OSCOM_Products = new osC_Products($current_category_id);
      
	  if (!isset($_GET['manufacturers_id']) && isset($_GET['filter']) && is_numeric($_GET['filter']) && ($_GET['filter'] > 0) ) {
        $OSCOM_Products->setManufacturer($_GET['filter']);
      }
      if (isset($_GET['manufacturers_id']) && isset($_GET['filter']) && is_numeric($_GET['filter']) && ($_GET['filter'] > 0) ) {
        $OSCOM_Products = new osC_Products($_GET['filter']);
	  }

      if ( isset($_GET['sort']) && !empty($_GET['sort']) ) {
        if ( strpos($_GET['sort'], '|d') !== false ) {
          $OSCOM_Products->setSortBy(substr($_GET['sort'], 0, -3), '-');
        } else {
          $OSCOM_Products->setSortBy($_GET['sort']);
        }
      }		
		// optional Product List Filter
		if ( PRODUCT_LIST_FILTER > 0 ) {
			if ( isset($_GET['manufacturers_id']) && !empty($_GET['manufacturers_id']) ) {
                $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from products p, products_to_categories p2c, categories c, categories_description cd where p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "' order by cd.categories_name";
			} else {
				$filterlist_sql = "select distinct m.manufacturers_id as id, m.manufacturers_name as name from products p, products_to_categories p2c, manufacturers m where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = '" . $OSCOM_Category->getID() . "' order by m.manufacturers_name";
			}
			$filter_result = tep_db_query($filterlist_sql);			
			if (tep_db_num_rows($filter_result) > 1) {
				$filterlist = tep_db_fetch_array($filter_result);
				$filter_data = '<div class="btn-group" role="group">';
				if ( isset($_GET['manufacturers_id']) && !empty($_GET['manufacturers_id']) ) {
					$filter_data .= '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">' . TEXT_ALL_CATEGORIES . ' <span class="caret"></span></button>';
				} else {
					$filter_data .= '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">' . TEXT_ALL_MANUFACTURERS . ' <span class="caret"></span></button>';

				}
				$filter_data .= '<ul class="dropdown-menu">';

				if(isset($cPath)&& !empty($cPath) ){
					$filter_data .= '<li><a href="' . tep_href_link('index.php', 'cPath=' . $cPath . (isset($_GET['sort']) ? '&sort=' .$_GET['sort'] : null) ) . '">' . TEXT_ALL_MANUFACTURERS . '</a></li><li role="separator" class="divider"></li>';				
					foreach ( $filter_result as $f ) {
						$filter_data .= '<li><a href="' . tep_href_link('index.php', 'cPath=' . $cPath . '&filter=' . $f['id'] . (isset($_GET['sort']) ? '&sort=' .$_GET['sort'] : null) ) . '">' . $f['name'] . '</a></li>';				
					}
				}
				if(isset($_GET['manufacturers_id'])&& !empty($_GET['manufacturers_id']) ){
					$filter_data .= '<li><a href="' . tep_href_link('index.php', 'manufacturers_id=' . $_GET['manufacturers_id'] . (isset($_GET['sort']) ? '&sort=' .$_GET['sort'] : null) ) . '">' . TEXT_ALL_CATEGORIES . '</a></li><li role="separator" class="divider"></li>';				
					
					foreach ( $filter_result as $f ) {
						$filter_data .= '<li><a href="' . tep_href_link('index.php', 'manufacturers_id=' . $_GET['manufacturers_id'] . '&filter=' . $f['id'] . (isset($_GET['sort']) ? '&sort=' .$_GET['sort'] : null) ) . '">' . $f['name'] . '</a></li>';				
					}				
				}
				
				$filter_data .= '</ul>';
				$filter_data .= '</div>';
			}
		}

		if ( isset($_GET['manufacturers_id']) && !empty($_GET['manufacturers_id']) ) {
			$OSCOM_Products->setManufacturer($_GET['manufacturers_id']);
		}

		$listing_sql = $OSCOM_Products->execute();



      ob_start();
      include('includes/modules/product_listing.php');
      $output = ob_get_clean();
      $oscTemplate->addContent($output, $this->group);
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
//      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Category Width', 'MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH_EACH', '4', 'What width container should each Category be shown in?', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_IP_PRODUCT_LISTING_SORT_ORDER', '200', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
    
    function keys() {
      return array('MODULE_CONTENT_IP_PRODUCT_LISTING_STATUS', 'MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH', 'MODULE_CONTENT_IP_PRODUCT_LISTING_SORT_ORDER');
    }  
  }
  