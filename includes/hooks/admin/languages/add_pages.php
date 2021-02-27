<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

class hook_admin_languages_add_pages {

  public function listen_insertAction() {
    global $lID, $languages_id;
    
    tep_db_query("INSERT INTO pages_description (pages_id, languages_id) SELECT pages_id, " . (int)$lID . " FROM pages_description WHERE languages_id = " . (int)$languages_id);
  }
  
  public function listen_deleteConfirmAction() {
    global $lID;
    
    tep_db_query("DELETE FROM pages_description WHERE languages_id = '" . (int)$lID . "'");
  }

}
