<?php
	/*		
		osCommerce, Open Source E-Commerce Solutions
		http://www.oscommerce.com
		
		Copyright (c) 2018 osCommerce
        Released under the GNU General Public License
	*/


  class Specials {
    protected $_specials = array();

    public function activateAll() {

		$Qspecials_query = tep_db_query("select specials_id from specials where status = 0 and now() >= start_date and start_date > 0 and now() < expires_date");
		
		while ($Qspecials = tep_db_fetch_array($Qspecials_query)) {

        $this->_setStatus($Qspecials['specials_id'], true);
      }
    }

    public function expireAll() {

		$Qspecials_query = tep_db_query("select specials_id from specials where status = 1 and now() >= expires_date and expires_date > 0");

		while ( $Qspecials->fetch() ) {
			$this->_setStatus($Qspecials['specials_id'], false);
		}
    }

    public function isActive($id) {
      if ( !isset($this->_specials[$id]) ) {
        $this->_specials[$id] = $this->getPrice($id);
      }

      return is_numeric($this->_specials[$id]);
    }

    public function getPrice($id) {

      if ( !isset($this->_specials[$id]) ) {
        $Qspecials_query = tep_db_query("select specials_new_products_price from specials where products_id = '". (int)$id ."' and status = 1");

        if ( tep_db_num_rows($Qspecials_query) > 0 ) {
          $result = tep_db_fetch_array($Qspecials_query);
          $this->_specials[$id] = $result['specials_new_products_price'];
        } else {
          $this->_specials[$id] = null;
        }
      }

      return $this->_specials[$id];
    }
    
	public function getStatus($id) {

		$Qspecials_query = tep_db_query("select status from specials where products_id = '". (int)$id ."'");
		
		if ( tep_db_num_rows($Qspecials_query) > 0 ) {	  
			
			$result = tep_db_fetch_array($Qspecials_query);
			
			return $result['status'];
		}
	}
	
    public static function getListing() {

      $result = array();

      $Qspecials = $OSCOM_PDO->prepare('select SQL_CALC_FOUND_ROWS p.products_id, p.products_price, p.products_tax_class_id, pd.products_name, pd.products_keyword, s.specials_new_products_price, i.image from :table_products p left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag), :table_products_description pd, :table_specials s where p.products_status = 1 and s.products_id = p.products_id and p.products_id = pd.products_id and pd.language_id = :language_id and s.status = 1 order by s.specials_date_added desc limit :batch_pageset, :batch_max_results; select found_rows();');
      $Qspecials->bindInt(':default_flag', 1);
      $Qspecials->bindInt(':language_id', $OSCOM_Language->getID());
      $Qspecials->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom((isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1), MAX_DISPLAY_SPECIAL_PRODUCTS));
      $Qspecials->bindInt(':batch_max_results', MAX_DISPLAY_SPECIAL_PRODUCTS);
      $Qspecials->execute();

      $result['entries'] = $Qspecials->fetchAll();

      $Qspecials->nextRowset();

      $result['total'] = $Qspecials->fetchColumn();

      return $result;
    }

    protected function _setStatus($id, $status) {

      $Qspecials_query = tep_db_query("update specials set status = '" . (int)$status . "', date_status_change = now() where specials_id = '" . (int)$id . "'");

    }
  }
?>
