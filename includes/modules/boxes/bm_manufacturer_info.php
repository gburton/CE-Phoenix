<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class bm_manufacturer_info {
    var $code = 'bm_manufacturer_info';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_BOXES_MANUFACTURER_INFO_TITLE;
      $this->description = MODULE_BOXES_MANUFACTURER_INFO_DESCRIPTION;

      if ( defined('MODULE_BOXES_MANUFACTURER_INFO_STATUS') ) {
        $this->sort_order = MODULE_BOXES_MANUFACTURER_INFO_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_MANUFACTURER_INFO_STATUS == 'True');
        
        $this->group = ((MODULE_BOXES_MANUFACTURER_INFO_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    function execute() {
      if (isset($_GET['products_id'])) {
        $manufacturer_query = tep_db_query("select manufacturers_id from products where products_id = '" . (int)$_GET['products_id'] . "' and manufacturers_id is not null");
        $manufacturer = tep_db_fetch_array($manufacturer_query);
        
        if ((int)$manufacturer['manufacturers_id'] > 0) {
          $bm_brand = new manufacturer((int)$manufacturer['manufacturers_id']);

          $_brand = $bm_brand->getData('manufacturers_name');
          $_image = $bm_brand->getData('manufacturers_image');
          $_url   = $bm_brand->getData('manufacturers_url');
          $_id    = $bm_brand->getData('manufacturers_id');

          $box_image = $box_title = NULL;
          
          // title
          $box_title = '<a href="' . tep_href_link('index.php', 'manufacturers_id=' . (int)$_id) . '">' . $_brand . '</a>';
          // image
          if (tep_not_null($_image)) $box_image = '<a href="' . tep_href_link('index.php', 'manufacturers_id=' . (int)$_id) . '">' . tep_image('images/' . $_image, htmlspecialchars($_brand), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, '', true, 'card-img-top') . '</a>';
          // link to urls
          $box_url = '<a class="list-group-item list-group-item-action text-muted" href="' . tep_href_link('index.php', 'manufacturers_id=' . (int)$_id) . '">' . MODULE_BOXES_MANUFACTURER_INFO_BOX_OTHER_PRODUCTS . '</a>';
          if (tep_not_null($_url)) $box_url .= '<a class="list-group-item list-group-item-action text-muted" href="' . tep_href_link('redirect.php', 'action=manufacturer&manufacturers_id=' . (int)$_id) . '" target="_blank">' . sprintf(MODULE_BOXES_MANUFACTURER_INFO_BOX_HOMEPAGE, $_brand) . '</a>';

          $tpl_data = ['group' => $this->group, 'file' => __FILE__];
          include 'includes/modules/block_template.php';
        }
      }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_MANUFACTURER_INFO_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Manufacturer Info Module', 'MODULE_BOXES_MANUFACTURER_INFO_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_BOXES_MANUFACTURER_INFO_CONTENT_PLACEMENT', 'Right Column', 'Should the module be loaded in the left or right column?', '6', '1', 'tep_cfg_select_option(array(\'Left Column\', \'Right Column\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_MANUFACTURER_INFO_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_MANUFACTURER_INFO_STATUS', 'MODULE_BOXES_MANUFACTURER_INFO_CONTENT_PLACEMENT', 'MODULE_BOXES_MANUFACTURER_INFO_SORT_ORDER');
    }
  }
  