<?php
  /**
  * KISS Image Thumbnailer
  * Creates image thumbnails where the image size requested differs from the actual image size.
  * Ensures that the browser does not have to resize images resulting in far greater loading speeds.
  * Once thumbnails have been created the system has been designed to use very minimal resources.
  *  
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU Public License)
  * @package KISS Image Thumbnailer
  * @link http://www.fwrmedia.co.uk
  * @copyright Copyright 2008-2009 FWR Media ( Robert Fisher )
  * @author Robert Fisher, FWR Media, http://www.fwrmedia.co.uk 
  * @lastdev $Author:: FWR Media                                        $:  Author of last commit
  * @lastmod $Date:: 2012-07-14 09:19:13 +0100 (Sat, 14 Jul 2012)       $:  Date of last commit
  * @version $Rev:: 9                                                   $:  Revision of last commit
  * @Id $Id:: Image_Helper.php 9 2012-07-14 08:19:13Z FWR Media         $:  Full Details
  */
  require_once DIR_WS_MODULES . 'kiss_image_thumbnailer/classes/Image.php';
  /**
  * Helper class to create valid thumbnails on the fly within the tep_image() wrapper
  *  
  * 
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU Public License)
  * @author     Robert fisher - FWR Media ( www.fwrmedia.co.uk )
  * @version     1.0
  */
  class Image_Helper extends ArrayObject {
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $_valid_mime = array( 'image/png', 'image/jpeg', 'image/jpg', 'image/gif' );
    /**
    * put your comment there...
    * 
    */
    public function __construct( $input ) {
      parent::__construct( $input, parent::ARRAY_AS_PROPS );  
    } // end constructor
    /**
    * put your comment there...
    * 
    */
    public function assemble() {
      $image_check = $this->_checkImage();
      // Image check is bad so we pass control back to the old OsC image wrapper
      if ( 'abort' == $image_check ) {
        return false;
      }
      // If we have to only we generate a thumb .. this is very resource intensive
      if ( 'no_thumb_required' !== $image_check ) {
        $this->_generateThumbnail();
      }
      $this->_build();
      return (string)$this;  
    } // end method
    /**
    * put your comment there...
    *  // end method
    * @param mixed $attribs
    */
    protected function _checkImage() {
      if ( !is_file ( $this->src ) ) {
        $this->src = $this->default_missing_image;  
      }
      $image_path_parts = pathinfo ( $this->src );
      $this->_image_name = $image_path_parts['basename'];
      $this->_thumb_filename = $this->attributes['width'] . 'x' . $this->attributes['height'] . '_' . $this->_image_name;
      $this->_thumb_src = $this->thumbs_dir_path . $this->_thumb_filename;
      if ( is_readable ( $this->_thumb_src ) ) {
        $this->_calculated_width = $this->attributes['width'];
        $this->_calculated_height = $this->attributes['height'];
        $this->src = $this->_thumb_src;
        return 'no_thumb_required';
      }
      if ( !$this->_original_image_info = getimagesize ( $this->src ) ) {
        return 'abort';
      } 
      if (!in_array ( $this->_original_image_info['mime'], $this->_valid_mime ) ) {
        return 'abort';
      }
    } // end method
    /**
    * put your comment there...
    * 
    */
    protected function _generateThumbnail() {
      if ( $this->attributes['width'] == $this->_original_image_info[0] && $this->attributes['height'] == $this->_original_image_info[1] ) {
        $this->_calculated_width = $this->attributes['width'];
        return $this->_calculated_height = $this->attributes['height'];
      }
      if ( $this->attributes['width'] == 0 || $this->attributes['height'] == 0 ) {
        $this->_calculated_width =  $this->_original_image_info[0];
        return $this->_calculated_height = $this->_original_image_info[1]; 
      }
      //make sure the thumbnail directory exists. 
      if ( !is_writable ( $this->thumbs_dir_path ) ) { 
        trigger_error ( 'Cannot detect a writable thumbs directory!', E_USER_NOTICE );
      }
      if ( is_readable ( $this->_thumb_src ) ) {
        $this->_calculated_width =  (int)$this->attributes['width'];
        $this->_calculated_height = (int)$this->attributes['height'];
        return $this->src = $this->_thumb_src;  
      }
      // resize image
      $image = new Image();
      $image->open( $this->src, $this->thumb_background_rgb )
            ->resize( (int)$this->attributes['width'], (int)$this->attributes['height'] )
            ->save( $this->_thumb_src, (int)$this->thumb_quality );
      $this->_thumbnail = $image;
      $this->_calculated_width = $this->_thumbnail->getWidth();
      $this->_calculated_height = $this->_thumbnail->getHeight();
      $this->src = $this->_thumb_src;
    } // end method
    /**
    * put your comment there...
    *  // end method
    */
    protected function _build() {
      $alt_title = $this->isXhtml ? tep_output_string_protected( str_replace ( '&amp;', '&', $this->attributes['alt'] ) ) : tep_output_string( $this->attributes['alt'] );
      $parameters = tep_not_null( $this->parameters ) ? tep_output_string( $this->parameters ) : false;
      $width = (int)$this->_calculated_width;
      $height = (int)$this->_calculated_height;
      $this->_html = '<img width="' . $width . '" height="' . $height . '" src="' . $this->src . '" title="' . $alt_title . '" alt="' . $alt_title . '"';
      if ( false !== $parameters ) $this->_html .= ' ' . tep_output_string( $parameters );
      $this->_html .= $this->isXhtml ? ' />' : '>';  
    } // end method
    /**
    * put your comment there...
    * 
    */
    public function __tostring() {
      return $this->_html;
    } // end method
  } // end class