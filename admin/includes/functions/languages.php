<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  function tep_get_languages_directory($code) {
    trigger_error('The tep_get_languages_directory function has been deprecated.', E_USER_DEPRECATED);

    $language_query = tep_db_query("select languages_id, directory from languages where code = '" . tep_db_input($code) . "'");
    if (tep_db_num_rows($language_query)) {
      $language = tep_db_fetch_array($language_query);
      $_SESSION['languages_id'] = $language['languages_id'];
      return $language['directory'];
    } else {
      return false;
    }
  }
