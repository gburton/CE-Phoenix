<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  $cl_box_groups[] = array('heading' => BOX_HEADING_CONFIGURATION, 'apps' => array());

  $configuration_groups_query = tep_db_query("select configuration_group_id as cgID, configuration_group_title as cgTitle from " . TABLE_CONFIGURATION_GROUP . " where visible = '1' order by sort_order");
  while ($configuration_groups = tep_db_fetch_array($configuration_groups_query)) {
    $cl_box_groups[sizeof($cl_box_groups)-1]['apps'][] = array(
      'code' => 'configuration.php',
      'title' => $configuration_groups['cgTitle'],
      'link' => tep_href_link('configuration.php', 'gID=' . $configuration_groups['cgID'])
    );
  }
  