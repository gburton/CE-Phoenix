<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

// set the session name and save path
  session_name('ceid');

// set the session cookie parameters
  Cookie::save_session_parameters();

// set the session ID if it exists
  if ( SESSION_FORCE_COOKIE_USE == 'False' ) {
    @ini_set('session.use_only_cookies', 0);

    if ( isset($_GET[session_name()]) && (($_COOKIE[session_name()] ?? null) !== $_GET[session_name()]) ) {
      tep_session_id($_GET[session_name()]);
    } elseif ( isset($_POST[session_name()]) && (($_COOKIE[session_name()] ?? null) !== $_POST[session_name()]) ) {
      tep_session_id($_POST[session_name()]);
    }
  }

// start the session
  $session_started = false;
  if (SESSION_FORCE_COOKIE_USE == 'True') {
    @ini_set('session.use_only_cookies', 1);

    Cookie::save('cookie_test', 'please_accept_for_session');

    if (isset($_COOKIE['cookie_test'])) {
      tep_session_start();
      $session_started = true;
    }
  } elseif (SESSION_BLOCK_SPIDERS == 'True') {
    $user_agent = strtolower(getenv('HTTP_USER_AGENT'));
    $spider_flag = false;

    if (tep_not_null($user_agent)) {
      foreach (file('includes/spiders.txt') as $spider) {
        if (tep_not_null($spider) && is_integer(strpos($user_agent, trim($spider)))) {
          $spider_flag = true;
          break;
        }
      }
    }

    if (!$spider_flag) {
      tep_session_start();
      $session_started = true;
    }
  } else {
    tep_session_start();
    $session_started = true;
  }

  if ($session_started) {
    // register session variables globally
    extract($_SESSION, EXTR_OVERWRITE+EXTR_REFS);
  }

// initialize a session token
  if (!isset($_SESSION['sessiontoken'])) {
    tep_reset_session_token();
  }

// set SID once, even if empty
  $SID = (defined('SID') ? SID : '');
