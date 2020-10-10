<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

////
// The HTML image wrapper function
  function tep_image($src, $alt = '', $width = '', $height = '', $parameters = '', $responsive = true, $bootstrap_css = '') {
    if (defined('DEFAULT_IMAGE') && tep_not_null(DEFAULT_IMAGE) && !is_file(DIR_FS_CATALOG . $src)) {
      $src = DEFAULT_IMAGE;
    } elseif ( (empty($src) || ($src == 'images/')) && (IMAGE_REQUIRED == 'false') ) {
      return false;
    }

// alt is added to the img tag even if it is null to prevent browsers from outputting
// the image filename as default
    $image = '<img src="' . tep_output_string($src) . '" alt="' . tep_output_string($alt) . '"';

    if (tep_not_null($alt)) {
      $image .= ' title="' . tep_output_string($alt) . '"';
    }

    if ( (CONFIG_CALCULATE_IMAGE_SIZE == 'true') && (empty($width) || empty($height)) ) {
      if ($image_size = @getimagesize($src)) {
        if (empty($width) && empty($height)) {
          $width = $image_size[0];
          $height = $image_size[1];
        } elseif (empty($width)) {
          $ratio = $height / $image_size[1];
          $width = (int)($image_size[0] * $ratio);
        } else {
          $ratio = $width / $image_size[0];
          $height = (int)($image_size[1] * $ratio);
        }
      } elseif (IMAGE_REQUIRED == 'false') {
        return false;
      }
    }

    if (tep_not_null($width) && tep_not_null($height)) {
      $image .= ' width="' . tep_output_string($width) . '" height="' . tep_output_string($height) . '"';
    }

    $image .= ' class="';

    if ($responsive === true) {
      $image .= 'img-fluid';
    }

    if (tep_not_null($bootstrap_css)) {
      $image .= ' ' . $bootstrap_css;
    }

    $image .= '"';

    if (tep_not_null($parameters)) {
      $image .= ' ' . $parameters;
    }

    $image .= ' />';

    return $image;
  }
