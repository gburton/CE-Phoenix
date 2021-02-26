<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $cl_box_groups[] = array('heading' => BOX_HEADING_MODULES, 'apps' => array());

  foreach ($cfgModules->getAll() as $m) {
    $cl_box_groups[sizeof($cl_box_groups)-1]['apps'][] = array('code' => 'modules.php',
                                                               'title' => $m['title'],
                                                               'link' => tep_href_link('modules.php', 'set=' . $m['code']));
  }
  