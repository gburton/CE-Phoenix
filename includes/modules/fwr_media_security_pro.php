<?php
  /**
  *
  * Security Pro Version 2.1
  *
  * 
  * @package SECURITY_PRO
  * @license http://www.opensource.org/licenses/gpl-2.0.php GNU Public License
  * @link http://www.fwrmedia.co.uk
  * @copyright Copyright 2008-2009 FWR Media
  * @copyright Portions Copyright 2005 ( rewrite uri concept ) Bobby Easland
  * @author Robert Fisher, FWR Media, http://www.fwrmedia.co.uk 
  * @lastdev $Author:: FWR Media                                        $:  Author of last commit
  * @lastmod $Date:: 2012-07-19 18:51:37 +0100 (Thu, 19 Jul 2012)       $:  Date of last commit
  * @version $Rev:: 11                                                  $:  Revision of last commit
  * @Id $Id:: fwr_media_security_pro.php 11 2012-07-19 17:51:37Z FWR Me#$:  Full Details   
  */
  
  /**
  * Security Pro Querystring whitelist protection against hacking.
  * 
  * @package SECURITY_PRO
  */
  class Fwr_Media_Security_Pro {
    // Array of files to be excluded from cleansing, these can also be added in application_top.php if preferred using Fwr_Media_Security_Pro::addExclusion()
    var $_excluded_from_cleansing = array(); 
    var $_enabled = true; // Turn on or off - bool true / false
    var $_basename;
    var $_cleanse_keys; // Turn on or off - bool true / false
    /**
    * Constructor
    * 
    * @uses defined()
    * @param bool $cleanse_keys
    */
    function Fwr_Media_Security_Pro( $cleanse_keys = false ) {
      if ( $cleanse_keys ) $this->_cleanse_keys = true;
      $this->addExclusions( array( defined ( 'FILENAME_PROTX_PROCESS' )  ? FILENAME_PROTX_PROCESS  : 'protx_process.php' ) );
    } // end constructor
    /**
    * Add file exclusions - these files will NOT have the querystring cleansed
    * 
    * @uses in_array()
    * @param string $file_to_exclude - file to exclude from cleansing
    * 
    * @access public
    * @return object Fwr_Media_Security_Pro - allows chaining
    */
    function addExclusion( $file_to_exclude = '' ) {
      if ( !in_array ( $file_to_exclude, $this->_excluded_from_cleansing ) ) {
        $this->_excluded_from_cleansing[] = (string)$file_to_exclude;
      }
      return $this;
    } // end method
    /**
    * Add multiple file exclusions as an array
    * 
    * @uses foreach()
    * @param array $args - files to exclude from cleansing
    * 
    * @access public
    * @return void
    */
    function addExclusions( array $args = array() ) {
      if ( empty ( $args ) ) return;
      foreach ( $args as $index => $exclusion_file ) {
        $this->addExclusion( $exclusion_file );  
      }
    } // end method
    /**
    * Called from application_top.php here we instigate the cleansing of the querystring
    * 
    * @uses in_array()
    * @uses function_exists()
    * @uses ini_get()
    * @see Fwr_Media_Security_Pro::cleanGlobals()
    * @param array $HTTP_GET_VARS - long array
    * @param string $PHP_SELF - base filename from osCommerce application_top.php
    * 
    * @access public
    * @return void
    */
    function cleanse( $PHP_SELF = '' ) {
      if ( false === $this->_enabled ) {
        return;
      }
      if ( empty( $PHP_SELF ) ) {
        return;
      }
      $this->_basename = $PHP_SELF;
      if ( in_array ( $this->_basename, $this->_excluded_from_cleansing ) ) {
        return;
      }
      $this->cleanseGetRecursive( $_GET );
      $_REQUEST = $_GET + $_POST; // $_REQUEST now holds the cleansed $_GET and unchanged $_POST. $_COOKIE has been removed.
      if ( !function_exists ( 'ini_get' ) || ini_get ( 'register_globals' ) != false ) {
        $this->cleanGlobals();
      }
    } // end method
    /**
    * Recursively cleanse _GET values and optionally keys as well if Fwr_Media_Security_Pro::cleanse_keys === true
    * 
    * @uses is_array()
    * @param array $get
    * 
    * @access public
    * @return void
    */
    function cleanseGetRecursive( &$get ) {
      foreach ( $get as $key => &$value ) {
        // If cleanse keys is set to on we unset array keys if they don't conform to expectations
        if ( $this->_cleanse_keys && ( $this->cleanseKeyString( $key ) != $key ) ) {
          unset ( $get[$key] );
          continue;
        }
        if ( is_array ( $value ) ) {
            // We have an array so well run it through again
            $this->cleanseGetRecursive( $value );
        // We have a string value so we'll cleanse it
        } else $value = $this->cleanseValueString( $value );
      } 
    } // end method
    /**
    * Cleanse array keys
    * 
    * Initially set as the same as values this may need to be made less strict
    * 
    * @uses urldecode()
    * @uses preg_replace()
    * 
    * @access public
    * @return string - cleansed key string
    */
    function cleanseKeyString( $string ) {
      $banned_string_pattern = '@GLOBALS|_REQUEST|base64_encode|UNION|%3C|%3E@i';
      // Apply the whitelist
      $cleansed = preg_replace ( "/[^\s{}a-z0-9_\.\-]/i", "", urldecode ( $string ) );
      // Remove banned words
      $cleansed = preg_replace ( $banned_string_pattern, '', $cleansed );
      // Ensure that a clever hacker hasn't gained himself a naughty double hyphen -- after our cleansing
      return preg_replace ( '@[-]+@', '-', $cleansed );  
    } // end method
    /**
    * Cleanse array values
    * 
    * @uses urldecode()
    * @uses preg_replace()
    * 
    * @access public
    * @return string - cleansed value string
    */
    function cleanseValueString( $string ) {
      $banned_string_pattern = '@GLOBALS|_REQUEST|base64_encode|UNION|%3C|%3E@i';
      // Apply the whitelist
      $cleansed = preg_replace ( "/[^\s{}a-z0-9_\.\-@]/i", "", urldecode ( $string ) );
      // Remove banned words
      $cleansed = preg_replace ( $banned_string_pattern, '', $cleansed );
      // Ensure that a clever hacker hasn't gained himself a naughty double hyphen -- after our cleansing
      return preg_replace ( '@[-]+@', '-', $cleansed );  
    } // end method
    /**
    * With register globals set to on we need to ensure that GLOBALS are cleansed
    * 
    * @uses array_key_exists()
    * 
    * @access public
    * @return void
    */
    function cleanGlobals() {
      foreach ( $_GET as $key => $value ) {
        if ( array_key_exists ( $key, $GLOBALS ) ) {
          $GLOBALS[$key] = $value;
        }
      }
    } // end method
  } // end class