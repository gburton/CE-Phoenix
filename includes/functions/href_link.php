<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

////
// The HTML href link wrapper function
  function tep_href_link($page = '', $parameters = '', $connection = null, $add_session_id = true, $search_engine_safe = true) {
    global $SID;

    $page = tep_output_string($page);

    if (!tep_not_null($page)) {
      die('<h5>Error!</h5><p>Unable to determine the page link!</p>');
    }

    $link = HTTP_SERVER . DIR_WS_CATALOG . $page;

    if (tep_not_null($parameters)) {
      $link .= '?' . tep_output_string($parameters);
      $separator = '&';
    } else {
      $separator = '?';
    }

    $link = rtrim($link, '&?');

// Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
    if ( $add_session_id && $GLOBALS['session_started'] && isset($SID) && (SESSION_FORCE_COOKIE_USE == 'False') && tep_not_null($SID)) {
      $_sid = $SID;
      if (isset($_sid)) {
        $link .= $separator . tep_output_string($_sid);
      }
    }


    while (strpos($link, '&&') !== false) $link = str_replace('&&', '&', $link);
    
    $link = str_replace('&', '&amp;', $link);

    return $link;
  }
