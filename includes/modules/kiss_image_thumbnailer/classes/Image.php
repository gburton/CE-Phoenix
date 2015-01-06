<?php
  /**
  * KISS Image Thumbnailer
  * Creates image thumbnails where the image size requested differs from the actual image size.
  * Ensures that the browser does not have to resize images resulting in far greater loading speeds.
  * Once thumbnails have been created the system has been designed to use very minimal resources.
  * 
  * This is based on the code of S. Mohammed Alsharaf, http://www.zfsnippets.com/snippets/view/id/44.
  * The class has been modified but remains in the most part unchanged,. 
  * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU Public License)
  * @package KISS Image Thumbnailer
  * @link http://www.fwrmedia.co.uk
  * @copyright Copyright 2008-2009 FWR Media ( Robert Fisher )
  * @author Robert Fisher, FWR Media, http://www.fwrmedia.co.uk 
  * @lastdev $Author:: FWR Media                                        $:  Author of last commit
  * @lastmod $Date:: 2012-07-08 12:05:35 +0100 (Sun, 08 Jul 2012)       $:  Date of last commit
  * @version $Rev:: 7                                                   $:  Revision of last commit
  * @Id $Id:: Image.php 7 2012-07-08 11:05:35Z FWR Media                $:  Full Details
  */
  class Image {
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $_filename = '';
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $_image = '';
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $_width = '';
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $_height = '';
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $_mime_type = '';
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $_view = null;
    /**
    * put your comment there...
    * 
    * @var integer
    */
    protected $_requested_thumbnail_width = '';
    /**
    * put your comment there...
    * 
    * @var integer
    */
    protected $_requested_thumbnail_height = '';
    /**
    * The resize parameters are given as $max_, setting this to bool true will set them as absolute and not a max.
    * A thumbnail will be created of the exact width and height placing the new resized image within it
    * 
    * @var mixed
    */
    protected $_take_resize_dimensions_as_absolute = true;
    /**
    * put your comment there...
    * 
    * @var mixed
    */
    protected $_thumb_background_rgb = array( 'red' => 255, 'green' => 255, 'blue' => 255 );
    /**
    * 
    */
    const IMAGETYPE_GIF = 'image/gif';
    /**
    * 
    */
    const IMAGETYPE_JPEG = 'image/jpeg';
    /**
    * 
    */
    const IMAGETYPE_PNG = 'image/png';
    /**
    * 
    */
    const IMAGETYPE_JPG = 'image/jpg';
    /**
    * put your comment there...
    * 
    * @param mixed $filename
    */
    public function open( $filename, array $_thumb_background_rgb = array() ) {
      if ( count( array_intersect_key( $this->_thumb_background_rgb, $_thumb_background_rgb ) ) == 3  ) {
        $this->_thumb_background_rgb = $_thumb_background_rgb;  
      }
      $this->_filename = $filename;
      $this->_setInfo(); 
      switch( $this->_mime_type ) {
        case self::IMAGETYPE_GIF :
          $this->_image = @imagecreatefromgif ( $this->_filename );
          break;
        case self::IMAGETYPE_JPEG :
        case self::IMAGETYPE_JPG :
          $this->_image = @imagecreatefromjpeg ( $this->_filename );
          break;
        case self::IMAGETYPE_PNG :
          $this->_image = @imagecreatefrompng ( $this->_filename );
          break;
        default :
          trigger_error ( 'Image extension is invalid or not supported.', E_USER_NOTICE );
          break;
      } // end switch
      return $this;
    } // end method
    /**
    * put your comment there...
    * 
    * @param mixed $save_in
    * @param mixed $quality
    * @param mixed $filters
    */
    protected function _output( $save_in = null, $quality, $filters = null ) {
      switch ($this->_mime_type) {
        case self::IMAGETYPE_GIF :
          return imagegif ( $this->_image, $save_in );
          break;
        case self::IMAGETYPE_JPEG :
        case self::IMAGETYPE_JPG :
          $quality = is_null ( $quality ) ? 75 : $quality;
          return imagejpeg ( $this->_image, $save_in, $quality );
          break;
        case self::IMAGETYPE_PNG :
          $quality = is_null ( $quality ) ? 0 : $quality;
          $filters = is_null ( $filters ) ? null : $filters;
          return imagepng ( $this->_image, $save_in, $quality, $filters );
          break;
        default :
          trigger_error ( 'Image cannot be created.', E_USER_NOTICE );
          break;
      } // end switch
    } // end method
    /**
    * put your comment there...
    * 
    * @param mixed $save_in
    * @param mixed $quality
    * @param mixed $filters
    */
    public function save( $save_in = null, $quality = null, $filters = null ) {
      return $this->_output ( $save_in, $quality, $filters );
    } // end method
    /**
    * put your comment there...
    * 
    */
    public function __destruct() {
      @imagedestroy ( $this->_image );
    } // end method
    /**
    * put your comment there...
    * 
    */
    protected function _setInfo() {
      $img_size = @getimagesize ( $this->_filename );
      if (!$img_size) {
        trigger_error ( 'Could not extract image size.', E_USER_NOTICE );
      } elseif ($img_size[0] == 0 || $img_size[1] == 0) {
        trigger_error ( 'Image has dimension of zero.', E_USER_NOTICE );
      }
      $this->_width = $img_size[0];
      $this->_height = $img_size[1];
      $this->_mime_type = $img_size['mime'];
    } // end method
    /**
    * put your comment there...
    * 
    */
    public function getWidth() {
      return $this->_width;
    } // end method
    /**
    * put your comment there...
    * 
    */
    public function getHeight() {
      return $this->_height;
    } // end method
    /**
    * put your comment there...
    * 
    */
    protected function _refreshDimensions() {
      $this->_height = imagesy ( $this->_image );
      $this->_width = imagesx ( $this->_image );
    } // end method
    /**
     * If image is GIF or PNG keep transparent colors
     * 
     * @credit http://github.com/maxim/smart_resize_image/tree/master
     * @param $image src of the image
     * @return the modified image
     */
    protected function _handleTransparentColor( $image = null ) {
      $image = is_null ( $image ) ? $this->_image : $image;
      if (($this->_mime_type == self::IMAGETYPE_GIF) || ($this->_mime_type == self::IMAGETYPE_PNG)) {
        $trnprt_indx = @imagecolortransparent ( $this->_image );
        // If we have a specific transparent color
        if ($trnprt_indx >= 0) {
          // Get the original image's transparent color's RGB values
          $trnprt_color = @imagecolorsforindex ( $this->_image, $trnprt_indx );
          // Allocate the same color in the new image resource
          $trnprt_indx = @imagecolorallocate ( $image, $trnprt_color ['red'], $trnprt_color ['green'], $trnprt_color ['blue'] );
          // Completely fill the background of the new image with allocated color.
          imagefill ( $image, 0, 0, $trnprt_indx );
          // Set the background color for new image to transparent
          imagecolortransparent ( $image, $trnprt_indx );
        } elseif ($this->_mime_type == self::IMAGETYPE_PNG) {
          // Always make a transparent background color for PNGs that don't have one allocated already
          // Turn off transparency blending (temporarily)
          imagealphablending ( $image, false );
          // Create a new transparent color for image
          $color = imagecolorallocatealpha ( $image, 0, 0, 0, 127 );
          // Completely fill the background of the new image with allocated color.
          imagefill ( $image, 0, 0, $color );
          // Restore transparency blending
          imagesavealpha ( $image, true );
        }
        return $image;
      }
    } // end method
    /**
     * Resize image based on max width and height
     * 
     * @param integer $maxWidth
     * @param integer $maxHeight
     * @return resized image
     */
    public function resize( $max_width, $max_height ) {
      $this->_requested_thumbnail_width = $max_width;
      $this->_requested_thumbnail_height = $max_height;
      if ( !$this->_take_resize_dimensions_as_absolute ) {
        if ($this->_width < $max_width && $this->_height < $max_height) {
          $this->_handleTransparentColor ();
          return $this;
        }
      }
      //maintain the aspect ratio of the image. 
      $ratio_orig = $this->_width/$this->_height;
      if ($max_width/$max_height > $ratio_orig) {
          $max_width = $max_height*$ratio_orig;
      } else {
          $max_height = $max_width/$ratio_orig;
      }
      //$this->debugIndividualImage( 'logo_goodridgeSuz.gif', $max_width . ' :: ' .  $max_height );
      $new_image = imagecreatetruecolor ( $max_width, $max_height );
      $this->_handleTransparentColor ( $new_image );
      imagecopyresampled ( $new_image, $this->_image, 0, 0, 0, 0, $max_width, $max_height, $this->_width, $this->_height );
      $this->_image = $new_image;
      if ( $this->_take_resize_dimensions_as_absolute ) {
        // the image has scaled badly we need to add a background
        $info = pathinfo($this->_filename);
        $image_name = $info['basename'];
        if ( $max_width < $this->_requested_thumbnail_width || $max_height < $this->_requested_thumbnail_height ) {
          $thumb_background = imagecreatetruecolor ( $this->_requested_thumbnail_width, $this->_requested_thumbnail_height );
          $background_color = imagecolorallocate ( $thumb_background, $this->_thumb_background_rgb['red'], $this->_thumb_background_rgb['green'], $this->_thumb_background_rgb['blue'] );
          imagefill ( $thumb_background, 0, 0, $background_color );
          $dst_x = 0;
          $dst_y = 0;
          $new_image_width = imagesx ( $new_image );
          $new_image_height = imagesy ( $new_image );
          if ( $this->_requested_thumbnail_width > $new_image_width ) {
            $dst_x = floor( ( $this->_requested_thumbnail_width - $new_image_width ) /2 );
          } elseif( $this->_requested_thumbnail_height > $new_image_height ) {
            $dst_y = floor( ( $this->_requested_thumbnail_height - $new_image_height ) /2 );
          }
          imagecopyresampled ( $thumb_background, $new_image, $dst_x, $dst_y, 0, 0, $new_image_width, $new_image_height, $new_image_width, $new_image_height );
          $this->_image = $thumb_background;
        }
      } // end need a background
      $this->_refreshDimensions();
      return $this;
    } // end method
    /**
    * put your comment there...
    *  // end method
    */
    protected function debugIndividualImage( $image_target, $message ) {
      $info = pathinfo ( $this->_filename );
      $image_name = $info['basename'];
      if ( $image_name == $image_target ) {
        die ( 'Image target was: ' . $image_target . '<br />Message: ' . $message );
      }  
    } // end method
  } // end class