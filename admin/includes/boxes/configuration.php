<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  $cl_box_groups[] = array('heading' => BOX_HEADING_CONFIGURATION, 'apps' => array());

  $configuration_groups_query = tep_db_query("select configuration_group_id as cgID, configuration_group_title as cgTitle from configuration_group where visible = '1' order by sort_order");
  while ($configuration_groups = tep_db_fetch_array($configuration_groups_query)) {
    $cl_box_groups[sizeof($cl_box_groups)-1]['apps'][] = array(
      'code' => 'configuration.php',
      'title' => $configuration_groups['cgTitle'],
      'link' => tep_href_link('configuration.php', 'gID=' . $configuration_groups['cgID'])
    );
  }
  