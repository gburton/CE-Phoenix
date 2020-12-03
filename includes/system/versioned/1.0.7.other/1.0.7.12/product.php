<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  class product {
    protected $_data = ['products_status' => 0];

    public function __construct($pid) {

      if ( !empty($pid) ) {
        $id = (int)$pid;
        
        $product_query = tep_db_query(<<<'EOSQL'
SELECT p.products_id, p.manufacturers_id, p.products_image, p.products_price, p.products_tax_class_id, pd.products_name, p.products_status,  
 IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, 
 IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_quantity as in_stock, 
 IF(s.status, 1, 0) as is_special 
 FROM products_description pd, products p left join specials s on p.products_id = s.products_id 
 WHERE p.products_status = '1' AND p.products_id = pd.products_id AND p.products_id =
EOSQL
            . $id . " AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
        
        if (tep_db_num_rows($product_query)) {
          $this->_data = tep_db_fetch_array($product_query);
          
          $attributes_query = tep_db_query(<<<'EOSQL'
SELECT COUNT(*) AS count 
 FROM products_attributes
 WHERE products_id =
EOSQL
            . $id);
          $attributes = tep_db_fetch_array($attributes_query);
          
          $this->_data['has_attributes'] = ($attributes['count'] > 0) ? 1 : 0;
        }
      }
    }

    public function get($key = null) {
      return (!is_null($key)) ? $this->_data[$key] : $this->_data;
    }

  }
  