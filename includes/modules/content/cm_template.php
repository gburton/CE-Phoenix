<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  ob_start();
  include($GLOBALS['oscTemplate']->map_to_template($tpl_data['file']));

  $GLOBALS['oscTemplate']->addContent(ob_get_clean(), $tpl_data['group']);
