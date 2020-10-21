<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

class hook_admin_languages_add_adverts {

  public function listen_insertAction() {
    global $lID, $languages_id;
    
    tep_db_query("INSERT INTO advert_info (advert_id, languages_id) SELECT advert_id, " . (int)$lID . " FROM advert_info WHERE languages_id = " . (int)$languages_id);
  }
  
  public function listen_deleteConfirmAction() {
    global $lID;
    
    tep_db_query("DELETE FROM advert_info WHERE languages_id = '" . (int)$lID . "'");
  }

}
