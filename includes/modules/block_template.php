<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

ob_start();
include $GLOBALS['oscTemplate']->map_to_template($tpl_data['file'], $tpl_data['type'] ?? 'module');

$GLOBALS['oscTemplate']->addBlock(ob_get_clean(), $tpl_data['group']);
