<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

// if gzip_compression is enabled, start to buffer the output
  if ( (GZIP_COMPRESSION == 'true') && extension_loaded('zlib') && !headers_sent() ) {
    if (function_exists('ini_set')) {
      ini_set('zlib.output_compression_level', GZIP_LEVEL);
    }

    if ((int)ini_get('zlib.output_compression') < 1) {
      ob_start('ob_gzhandler');
    }
  }
