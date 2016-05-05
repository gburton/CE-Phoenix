<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  ////////////////////////////////////////////////////////////////////////////////////////////////
  //
  // Function    : tep_validate_email
  //
  // Arguments   : email   email address to be checked
  //
  // Return      : true  - valid email address
  //               false - invalid email address
  //
  // Description : function for validating email address that conforms to RFC 822 specs
  //
  //              This function will first attempt to validate the Email address using the filter
  //              extension for performance. If this extension is not available it will
  //              fall back to a regex based validator which doesn't validate all RFC822
  //              addresses but catches 99.9% of them. The regex is based on the code found at
  //              http://www.regular-expressions.info/email.html
  //
  //              Optional validation for validating the domain name is also valid is supplied
  //              and can be enabled using the administration tool.
  //
  // Sample Valid Addresses:
  //
  //    first.last@host.com
  //    firstlast@host.to
  //    first-last@host.com
  //    first_last@host.com
  //
  // Invalid Addresses:
  //
  //    first last@host.com
  //    first@last@host.com
  //
  ////////////////////////////////////////////////////////////////////////////////////////////////
  function tep_validate_email($email) {
    $email = trim($email);

    if ( strlen($email) > 255 ) {
      $valid_address = false;
    } elseif ( function_exists('filter_var') && defined('FILTER_VALIDATE_EMAIL') ) {
     $valid_address = (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    } else {
      if ( substr_count( $email, '@' ) > 1 ) {
        $valid_address = false;
      }

      if ( preg_match("/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i", $email) ) {
        $valid_address = true;
      } else {
        $valid_address = false;
      }
    }

    if ($valid_address && ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
      $domain = explode('@', $email);

      if ( !checkdnsrr($domain[1], "MX") && !checkdnsrr($domain[1], "A") ) {
        $valid_address = false;
      }
    }

    return $valid_address;
  }

  function tep_valida_nif_cif_nie($cif) {
  	//returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok, -1 = NIF bad, -2 = CIF bad, -3 = NIE bad, 0 = ??? bad
  	//función creada por David Vidal Serra, Copyleft 2005
        $cif=strtoupper($cif);
        if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/',$cif)) {return 0;}
        for ($i=0;$i<9;$i++) {$num[$i]=substr($cif,$i,1);}
        $suma=$num[2]+$num[4]+$num[6];
        for ($i=1;$i<8;$i+=2) {$suma+=substr((2*$num[$i]),0,1)+substr((2*$num[$i]),1,1);}
        $n=10-substr($suma,strlen($suma)-1,1);
        if (preg_match('/^[ABCDEFGHNPQSJ]{1}/',$cif)) {
                if ($num[8]==chr(64+$n) || $num[8]==substr($n,strlen($n)-1,1)){return 2;} else {return -2;}}
        if (preg_match('/^[KLM]{1}/',$cif)) {
                if ($num[8]==chr(64+$n)) {return 2;} else {return -2;}}
				//calcula NIE con letra X
        if (preg_match('/^[TX]{1}/',$cif)) {
                if ($num[8]==substr('TRWAGMYFPDXBNJZSQVHLCKE',substr(preg_replace('/X/','0',$cif),0,8)%23,1) || preg_match('/^[T]{1}[A-Z0-9]{8}$/',$cif)) {return 3;} else {return -3;}}
				//calcula NIE con letra Y
        if (preg_match('/^[TY]{1}/',$cif)) {
                if ($num[8]==substr('TRWAGMYFPDXBNJZSQVHLCKE',substr(preg_replace('/Y/','1',$cif),0,8)%23,1) || preg_match('/^[T]{1}[A-Z0-9]{8}$/',$cif)) {return 3;} else {return -3;}}
				//calcula NIE con letra Z
        if (preg_match('/^[TY]{1}/',$cif)) {
                if ($num[8]==substr('TRWAGMYFPDXBNJZSQVHLCKE',substr(preg_replace('/Y/','2',$cif),0,8)%23,1) || preg_match('/^[T]{1}[A-Z0-9]{8}$/',$cif)) {return 3;} else {return -3;}}
        if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/',$cif)) {
                if ($num[8]==substr('TRWAGMYFPDXBNJZSQVHLCKE',substr($cif,0,8)%23,1)) {return 1;} else {return -1;}}
        return 0;
  }
?>