<?php
/*
  $Id: password_funcs_create_account.php,v 1.00 2007/08/25 04:32:05 hpdl Exp $
  
  same file as password_funcs.php, v 1.10 2003/02/11 01:31:02 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

////
// This function validates a plain text password with an
// encrpyted password
  function tep_validate_password($plain, $encrypted) {
    if (tep_not_null($plain) && tep_not_null($encrypted)) {
// split apart the hash / salt
      $stack = explode(':', $encrypted);

      if (sizeof($stack) != 2) return false;

      if (md5($stack[1] . $plain) == $stack[0]) {
        return true;
      }
    }

    return false;
  }

////
// This function makes a new password from a plaintext password. 
  function tep_encrypt_password_for_create_account($plain) {
    $password = '';

    for ($i=0; $i<10; $i++) {
      $password .= tep_rand();
    }

    $salt = substr(md5($password), 0, 2);

    $password = md5($salt . $plain) . ':' . $salt;

    return $password;
  }
?>
