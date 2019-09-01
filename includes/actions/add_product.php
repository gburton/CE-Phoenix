<?php
/*
  $Id$

  Modified for:
  QTpro
  Version 5.6 BS 
  by @raiwa 
  info@oscaddons.com
  www.oscaddons.com

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/
	
	class osC_Actions_add_product {
		public static function execute() {
			global $PHP_SELF, $messageStack, $cart, $goto, $parameters;
      
      if (isset($_POST['products_id'])) {       
//++++ QT Pro: Begin Changed code
        $attributes=array();
        if (isset($_POST['attrcomb']) && (preg_match("/^\d{1,10}-\d{1,10}(,\d{1,10}-\d{1,10})*$/",$_POST['attrcomb']))) {
          $attrlist=explode(',',$_POST['attrcomb']);
          foreach ($attrlist as $attr) {
            list($oid, $oval)=explode('-',$attr);
            if (is_numeric($oid) && $oid==(int)$oid && is_numeric($oval) && $oval==(int)$oval)
              $attributes[$oid]=$oval;
          }
        }
        if (isset($_POST['id']) && is_array($_POST['id'])) {
          foreach ($_POST['id'] as $key=>$val) {
            if (is_numeric($key) && $key==(int)$key && is_numeric($val) && $val==(int)$val)
              $attributes=$attributes + $_POST['id'];
          }
        }
        $cart->add_cart($_POST['products_id'], $cart->get_quantity(tep_get_uprid($_POST['products_id'], $attributes))+1, $attributes);
//++++ QT Pro: End Changed Code
        
        $messageStack->add_session('product_action', sprintf(PRODUCT_ADDED, tep_get_products_name((int)$_POST['products_id'])), 'success');
      }
      
      tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));      
		}
	}
