<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  if (defined('DIR_FS_SESSION') && DIR_FS_SESSION && is_dir(DIR_FS_SESSION) && is_writable(DIR_FS_SESSION)) {
    session_save_path(DIR_FS_SESSION);
  } else {
    // if we don't have a usable session directory defined,
    // use MySQL sessions
    // Note:  this is the default configuration in the normal install process.
    function _sess_open($save_path, $session_name) {
      return true;
    }

    function _sess_close() {
      return true;
    }

    function _sess_read($key) {
      $value_query = tep_db_query("SELECT value FROM sessions WHERE sesskey = '" . tep_db_input($key) . "'");
      $value = $value_query->fetch_assoc();

      return $value['value'] ?? '';
    }

    function _sess_write($key, $value) {
      return false !== tep_db_query("INSERT INTO sessions (sesskey, expiry, value) VALUES ('"
        . tep_db_input($key) . "', '" . tep_db_input(time()) . "', '" . tep_db_input($value)
        . "') ON DUPLICATE KEY UPDATE expiry = VALUES(expiry), value = VALUES(value)");
    }

    function _sess_destroy($key) {
      return false !== tep_db_query("DELETE FROM sessions WHERE sesskey = '" . tep_db_input($key) . "'");
    }

    function _sess_gc($maxlifetime) {
      return false !== tep_db_query("DELETE FROM sessions WHERE expiry < '" . (time() - $maxlifetime) . "'");
    }

    session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
  }

  function tep_session_start() {
    $sane_session_id = true;

    if ( isset($_GET[session_name()]) ) {
      if ( (SESSION_FORCE_COOKIE_USE == 'True') || (preg_match('/^[a-zA-Z0-9,-]+$/', $_GET[session_name()]) == false) ) {
        unset($_GET[session_name()]);

        $sane_session_id = false;
      }
    }

    if ( isset($_POST[session_name()]) ) {
      if ( (SESSION_FORCE_COOKIE_USE == 'True') || (preg_match('/^[a-zA-Z0-9,-]+$/', $_POST[session_name()]) == false) ) {
        unset($_POST[session_name()]);

        $sane_session_id = false;
      }
    }

    if ( isset($_COOKIE[session_name()]) ) {
      if ( preg_match('/^[a-zA-Z0-9,-]+$/', $_COOKIE[session_name()]) == false ) {
        $session_data = session_get_cookie_params();

        setcookie(session_name(), '', time()-42000, $session_data['path'], $session_data['domain']);
        unset($_COOKIE[session_name()]);

        $sane_session_id = false;
      }
    }

    if ($sane_session_id == false) {
      tep_redirect(tep_href_link('index.php', '', 'NONSSL', false));
    }

    register_shutdown_function('session_write_close');

    global $session_started;
    $session_started = session_start();
    return $session_started;
  }

  function tep_session_register($variable) {
    trigger_error('The tep_session_register function has been deprecated.', E_USER_DEPRECATED);
    global $session_started;

    if ($session_started === true) {
      if (!isset($GLOBALS[$variable])) {
        $GLOBALS[$variable] = null;
      }

      $_SESSION[$variable] =& $GLOBALS[$variable];
    }

    return false;
  }

  function tep_session_is_registered($variable) {
    trigger_error('The tep_session_is_registered function has been deprecated.', E_USER_DEPRECATED);
    return isset($_SESSION) && array_key_exists($variable, $_SESSION);
  }

  function tep_session_unregister($variable) {
    trigger_error('The tep_session_unregister function has been deprecated.', E_USER_DEPRECATED);
    unset($_SESSION[$variable]);
  }

  function tep_session_id($sessid = '') {
    if (empty($sessid)) {
      return session_id();
    }

    return session_id($sessid);
  }

  function tep_session_name($name = '') {
    trigger_error('The tep_session_name function has been deprecated.', E_USER_DEPRECATED);
    if (empty($name)) {
      return session_name();
    }

    return session_name($name);
  }

  function tep_session_close() {
    trigger_error('The tep_session_close function has been deprecated.', E_USER_DEPRECATED);
    return session_write_close();
  }

  function tep_session_destroy() {
    if ( isset($_COOKIE[session_name()]) ) {
      $session_data = session_get_cookie_params();

      setcookie(session_name(), '', time()-42000, $session_data['path'], $session_data['domain']);
      unset($_COOKIE[session_name()]);
    }

    return session_destroy();
  }

  function tep_session_save_path($path = '') {
    trigger_error('The tep_session_save_path function has been deprecated.', E_USER_DEPRECATED);
    if (empty($path)) {
      return session_save_path();
    }

    return session_save_path($path);
  }

  function tep_session_recreate() {
    if (SESSION_RECREATE !== 'True') {
      return;
    }

    $old_id = session_id();

    session_regenerate_id(true);

    if (!empty($GLOBALS['SID'])) {
      $GLOBALS['SID'] = session_name() . '=' . session_id();
    }

    whos_online::update_session_id($old_id, session_id());
  }

  function tep_reset_session_token() {
    $_SESSION['sessiontoken'] = md5(mt_rand() . mt_rand() . mt_rand() . mt_rand());
  }
