<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  trigger_error('The checkout/reset segment has been deprecated.', E_USER_DEPRECATED);
  // use the reset pipeline instead:
  $GLOBALS['hooks']->register_pipeline('reset');
