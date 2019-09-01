<?php
/*
  $Id$

  2018 QTPro 6.0 Phoenix
  by @raiwa 
  info@oscaddons.com
  www.oscaddons.com

  Copyright (c) 2017 Rainer Schmied
  Based on prior works released under the GNU General Public License

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_qtpro_options {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));

      $this->title = MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_TITLE;
      $this->description = MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_DESCRIPTION;
      $this->description .= '<div class="secWarning"><a target="_blank" href="https://www.oscaddons.com/en/commercial-add-ons/qtpro-update-product-price-p-56.html"><u>GET QTpro Update Product Price</u></a><br>
                            - Dynamically update the shown Product Price depending on the selected options.<br>
                              <a target="_blank" href="https://www.oscaddons.com"><u>www.oscaddons.com</u></a>
                            </div>';
      if ( !defined('MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS') || (defined('MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS') && MODULE_HEADER_TAGS_QTPRO_STOCK_CHECK_STATUS != 'True') ) {
        $this->description .=   '<div class="secWarning">' . MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_HT_WARNING . '<br>
                                <a href="modules.php?set=header_tags&module=ht_qtpro_stock_check&action=install">' . MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_HT_INSTALL_NOW . '</a></div>';
      }
      $this->description .= '<div class="secWarning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';

      if ( defined('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_STATUS == 'True');
      }
    }

    function execute() {
      global $oscTemplate, $product_info, $languages_id;
      
      $content_width = (int)MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_CONTENT_WIDTH;

      $product_exists = false;
      if (method_exists('product_info','product_exists') && isset($product_info) && $product_info->product_exists() === true && $product_info->has_options() === true ) {
        $product_exists = true;
      } else {
        $products_attributes_query = tep_db_query("select count(*) as total from products_options popt, products_attributes patrib where patrib.products_id='" . (int)$_GET['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "'");
        $products_attributes = tep_db_fetch_array($products_attributes_query);
        if ($products_attributes['total'] > 0) 
          $product_exists = true;          
      }

      if ($product_exists === true) {
        $products_id=(preg_match("/^\d{1,10}(\{\d{1,10}\}\d{1,10})*$/", $_GET['products_id']) ? $_GET['products_id'] : (int)$_GET['products_id']); 
        require('includes/classes/pad_' . MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_PLUGIN . '.php');
        $class = 'pad_' . MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_PLUGIN;
        $pad = new $class($products_id);
        $qtpro_options = $pad->draw();
        
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
      return defined('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Current Version', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_VERSION_INSTALLED', '5.6 BS', 'Read only.', '6', '1', 'tep_version_readonly(', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable QTPRO Options Module', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_STATUS', 'True', 'Should this module be shown on the product info page?', '6', '2', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Width', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_CONTENT_WIDTH', '6', 'What width container should the content be shown in?', '6', '3', 'tep_cfg_select_option(array(\'12\', \'11\', \'10\', \'9\', \'8\', \'7\', \'6\', \'5\', \'4\', \'3\', \'2\', \'1\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_SORT_ORDER', '80', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Uninstall Removes Database entries', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_REMOVE_DATA', 'False', 'Do you want to remove the QT Pro database entries when uninstall the module?<br>All attribute quantities and other QT Pro database entries will be lost. Use only if you just installed QT Pro for testing purpose and you are sure not to use QT Pro any more.', '6', '5', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Product Info Attribute Display Plugin', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_PLUGIN', 'multiple_dropdowns', 'The plugin used for displaying attributes on the product information page', '6', '6', 'tep_cfg_select_option(array(\'multiple_dropdowns\', \'sequenced_dropdowns\', \'single_dropdown\', \'single_radioset\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Out of Stock Attributes', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_SHOW_OUT_OF_STOCK', 'True', 'If True:</strong> Attributes that are out of stock will be displayed.<br>If False: Attributes that are out of stock will <em>not</em> be displayed.<br>Has no effect for sequenced dropdowns.<br>Default is True.', '6', '7', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Mark Out of Stock Attributes', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_MARK_OUT_OF_STOCK', 'Right', 'Controls how out of stock attributes are marked as out of stock.', '6', '8', 'tep_cfg_select_option(array(\'None\', \'Right\', \'Left\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Out of Stock Message Line', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_OUT_OF_STOCK_MSGLINE', 'True', 'If True: If an out of stock attribute combination is selected by the customer, a message line informing on this will displayed.<br>Used for multiple dropdowns only.', '6', '9', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Prevent Adding Out of Stock to Cart', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_NO_ADD_OUT_OF_STOCK', 'True', 'If True: Customer will not be able to ad a product with an out of stock attribute combination to the cart. A javascript form will be displayed.', '6', '10', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Combine untracked options', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_COMBINE_NON_STOCKED', 'True', 'For single drop down or singel radio: combine also options which stock is not tracked.', '6', '11', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Use Actual Price Pull Downs', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_ACTUAL_PRICE_PULL_DOWN', 'False', 'NOTE: If you have more than one option per product, this can only be used with a satisfying result with single_dropdown or single_radioset.<br>If True: Option prices will displayed as a final product price.<br>Default is false.</strong>', '6', '12', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show default \'Please Select\' option', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_SHOW_PLEASE_SELECT', 'True', 'If True: \'Please Select\' will be shown by default option. (multiple and single drop-down only)<br> If no other option is selected, click on \'Add to Cart\' button will show an alert.', '6', '13', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      // BEGIN options images support
      if ( defined('MODULE_HEADER_TAGS_BOOTSTRAP_SELECT_STATUS') && MODULE_HEADER_TAGS_BOOTSTRAP_SELECT_STATUS == 'True' ) {
        // ADD FIELD for options images
        if (tep_db_num_rows(tep_db_query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='products_attributes' AND COLUMN_NAME LIKE 'option_image'")) != 1 ) {
          tep_db_query("AlTER TABLE products_attributes ADD option_image varchar(127) NULL default NULL");
        }
        // ADD FIELD for options sort order
        if (tep_db_num_rows(tep_db_query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='products_attributes' AND COLUMN_NAME LIKE 'sort_order'")) != 1 ) {
        tep_db_query("ALTER TABLE products_attributes ADD sort_order int(11) NOT NULL default 0");
        }
        // ADD FIELD for options values images
        if (tep_db_num_rows(tep_db_query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='products_options_values' AND COLUMN_NAME LIKE 'products_options_values_image'")) != 1 ) {
          tep_db_query("AlTER TABLE products_options_values ADD products_options_values_image varchar(127) NULL default NULL");
        }
        // Create image directory for options images
        if(!is_dir(DIR_FS_CATALOG_IMAGES . 'options/'))mkdir(DIR_FS_CATALOG_IMAGES . 'options/', 0755);
      }
      // END options images support
      // Check if option/attributes sort order is used and add configuration entry
      if (tep_db_num_rows(tep_db_query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='products_options' AND COLUMN_NAME LIKE 'products_options_sort_order'")) == 1 ) {
        tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Use Option/Attribute Sort Order', 'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_USE_OPT_ATTR_SORT_ORDER', 'True', 'Support for Options-Attributes Sort Order added by Ajax Attribute Manager or other add-on.<br>This option is only installed if the column \'products_options_sort_order\' is found in the database.', '6', '14', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      }
      include_once('includes/classes/language.php');
      $lng = new language;
      foreach($lng->catalog_languages as $id => $value) {
      	$key = strtoupper($value['directory']);
        switch ($key) {
        	case 'ESPANOL':        
        		tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('Aviso Admin Stock " . $key . "', 'MODULE_CONTENT_QTPRO_ADMIN_WARNING_" . $key . "', 'Atención: Hay %s productos enfermos en la base de datos. Por favor, visite <a href=\'%s\' class=\'headerLink\'>el QTPro doctor</a>', 'Definición de idioma usado en admin QT Pro Stock Warning', '6', '15', 'shorten_string', now())");
        		tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Botón Admin Stock " . $key . "', 'MODULE_CONTENT_QTPRO_STOCK_BUTTON_" . $key . "', 'Stock', 'Definición de idioma usado en el admin botón QT Pro Stock', '6', '16', now())");
        		tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Admin Control de Stock " . $key . "', 'MODULE_CONTENT_QTPRO_TRACK_STOCK_" . $key . "', 'Controlar Stock?', 'Definición de idioma usado en el admin atributos de producto', '6', '17', now())");
       		break;
        	case 'GERMAN':        
        		tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('Admin Lagerbestand Warnung " . $key . "', 'MODULE_CONTENT_QTPRO_ADMIN_WARNING_" . $key . "', 'Warnung: Es gibt %s kranke Produkte in der Datenbank. Bitte gehen Sie zum <a href=\'%s\' class=\'headerLink\'>QTPro doctor</a>', 'Sprachdefinition für die Admin QT Pro Lagerbestand Warnung', '6', '18', 'shorten_string', now())");
        		tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Admin Lagerbestand Button " . $key . "', 'MODULE_CONTENT_QTPRO_STOCK_BUTTON_" . $key . "', 'Lagerbestand', 'Sprachdefinition für den Admin QT Pro Lagerbestand Button', '6', '19', now())");
        		tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Admin Lagerbestand verfolgen " . $key . "', 'MODULE_CONTENT_QTPRO_TRACK_STOCK_" . $key . "', 'Lagerbestand konrollieren?', 'Sprachdefinition für den Admin QT Pro Lagerbestand Button', '6', '20', now())");
            break;
        	default:        
        		tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('Admin Stock Warning " . $key . "', 'MODULE_CONTENT_QTPRO_ADMIN_WARNING_" . $key . "', 'Warning: There are %s sick products in the database. Please visit <a href=\'%s\' class=\'headerLink\'>the QTPro doctor</a>', 'Language definition used in admin QT Pro Stock Warning', '6', '21', 'shorten_string', now())");
        		tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Admin Stock Button " . $key . "', 'MODULE_CONTENT_QTPRO_STOCK_BUTTON_" . $key . "', 'Stock', 'Language definition used in admin QT Pro Stock Button', '6', '22', now())");
        		tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Admin Track Stock " . $key . "', 'MODULE_CONTENT_QTPRO_TRACK_STOCK_" . $key . "', 'Track Stock?', 'Language definition used in admin QT Pro Stock Button', '6', '23', now())");
        }
      }

      // Add new column to products_options to indicate if stock should be tracked for an option
      if (tep_db_num_rows(tep_db_query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='products_options' AND COLUMN_NAME LIKE 'products_options_track_stock'")) != 1 ) {
        tep_db_query("ALTER TABLE `products_options` ADD COLUMN `products_options_track_stock` TINYINT(4) DEFAULT '0' NOT NULL AFTER `products_options_name`");
      }
      // Add new column to orders_products to track attributes to make it possible to delete an order and restock
      if (tep_db_num_rows(tep_db_query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='orders_products' AND COLUMN_NAME LIKE 'products_stock_attributes'")) != 1 ) {
        tep_db_query("ALTER TABLE `orders_products` ADD COLUMN `products_stock_attributes` VARCHAR(255) DEFAULT NULL AFTER `products_quantity`");
      }
      // Create new table to track stock for products attributes
      tep_db_query( "CREATE TABLE if not exists `products_stock` (`products_stock_id` INT(11) NOT NULL AUTO_INCREMENT,
                                                                  `products_id` INT(11) DEFAULT '0' NOT NULL ,
                                                                  `products_stock_attributes` VARCHAR(255) NOT NULL,
                                                                  `products_stock_quantity` INT(11) DEFAULT '0' NOT NULL ,
                                                                  PRIMARY KEY (products_stock_id),
                                                                  UNIQUE idx_products_stock_attributes (`products_id`,`products_stock_attributes`))");
      // Delete old configuration entries
      tep_db_query("DELETE FROM configuration_group WHERE configuration_group_title='Prod Info (QTPro)'");
      tep_db_query("DELETE FROM configuration WHERE configuration_key IN ('" . implode("', '", $this->old_keys()) . "')");      
    }

    function remove() {
      if ( defined('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_REMOVE_DATA') && MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_REMOVE_DATA == 'True' ) {
      	tep_db_query("ALTER TABLE `products_options` DROP `products_options_track_stock`");
      	tep_db_query("ALTER TABLE `orders_products` DROP `products_stock_attributes`");
      	// BEGIN options images support
        tep_db_query("ALTER TABLE products_options_values DROP COLUMN products_options_values_image");
        tep_db_query("ALTER TABLE `products_attributes` DROP COLUMN `option_image`");
        // END options images support
      	tep_db_query("DROP TABLE IF EXISTS `products_stock`");
      }
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      include_once('includes/classes/language.php');
      $lng = new language;
      $KeysArray =  array('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_VERSION_INSTALLED', 
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_STATUS', 
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_CONTENT_WIDTH', 
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_SORT_ORDER', 
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_REMOVE_DATA', 
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_PLUGIN', 
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_SHOW_OUT_OF_STOCK', 
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_MARK_OUT_OF_STOCK', 
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_OUT_OF_STOCK_MSGLINE', 
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_NO_ADD_OUT_OF_STOCK', 
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_COMBINE_NON_STOCKED',
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_ACTUAL_PRICE_PULL_DOWN', 
                          'MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_ATTRIBUTE_SHOW_PLEASE_SELECT'); 
      if (tep_db_num_rows(tep_db_query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='products_options' AND COLUMN_NAME LIKE 'products_options_sort_order'")) == 1 ) {
        $KeysArray = array_merge($KeysArray, array('MODULE_CONTENT_PRODUCT_INFO_QTPRO_OPTIONS_USE_OPT_ATTR_SORT_ORDER'));
      }
      foreach($lng->catalog_languages as $id => $value) {
      	$key = strtoupper($value['directory']);
      	array_push($KeysArray, 'MODULE_CONTENT_QTPRO_ADMIN_WARNING_' . $key);
      	array_push($KeysArray, 'MODULE_CONTENT_QTPRO_STOCK_BUTTON_' . $key);
      	array_push($KeysArray, 'MODULE_CONTENT_QTPRO_TRACK_STOCK_' . $key);
      }
      return $KeysArray;
    }

    function old_keys() {
      $KeysArray =  array('PRODINFO_ATTRIBUTE_PLUGIN', 
                          'PRODINFO_ATTRIBUTE_SHOW_OUT_OF_STOCK', 
                          'PRODINFO_ATTRIBUTE_MARK_OUT_OF_STOCK', 
                          'PRODINFO_ATTRIBUTE_OUT_OF_STOCK_MSGLINE', 
                          'PRODINFO_ATTRIBUTE_NO_ADD_OUT_OF_STOCK', 
                          'PRODINFO_ATTRIBUTE_ACTUAL_PRICE_PULL_DOWN', 
                          'PRODINFO_ATTRIBUTE_DISPLAY_STOCK_LIST');
      return $KeysArray;
    }
  } // end class

  function shorten_string($text) {
    return substr ($text, 0, 30);
  }
  
// function show the version read only  
  if(!function_exists('tep_version_readonly')) {
  	function tep_version_readonly($value){
  		$version_text = '<br>Version ' . $value;
      return $version_text;
    }
  }
