<?php
/*
 $Id$

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2020 osCommerce

 Released under the GNU General Public License
 */

  trigger_error('The checkout/after segment has been deprecated.', E_USER_DEPRECATED);
  // use the after pipeline instead:
  $GLOBALS['hooks']->register_pipeline('after');
