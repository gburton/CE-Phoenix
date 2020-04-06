<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2018 osCommerce; http://www.oscommerce.com
 * @license GNU General Public License; http://www.oscommerce.com/gpllicense.txt
 */

class manufacturer {
  public $_data = array();

  function __construct($mID) {
    $this->buildManufacturer($mID);
  }

  function buildManufacturer($mID) {
    $manufacturer_query = tep_db_query("select m.*, mi.* from manufacturers m, manufacturers_info mi where m.manufacturers_id = " . (int)$mID . " and m.manufacturers_id = mi.manufacturers_id and mi.languages_id = " . (int)$_SESSION['languages_id']);

    if ( tep_db_num_rows($manufacturer_query) === 1 ) {
      $manufacturer = tep_db_fetch_array($manufacturer_query);

      $this->_data = $manufacturer;
    }
  }
  
  function getData($key) {
    return $this->_data[$key];
  }

  function showImage() {
    return tep_image('images/' . $this->_data['manufacturers_image'], $this->_data['manufacturers_name']);
  }
  
  function buildManufacturerArray() {
    return $this->_data;
  }
  
}
