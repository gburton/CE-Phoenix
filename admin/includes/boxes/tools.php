<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  $cl_box_groups[] = array(
    'heading' => BOX_HEADING_TOOLS,
    'apps' => array(
      array(
        'code' => 'sec_dir_permissions.php',
        'title' => BOX_TOOLS_SEC_DIR_PERMISSIONS,
        'link' => tep_href_link('sec_dir_permissions.php')
      ),
      array(
        'code' => 'server_info.php',
        'title' => BOX_TOOLS_SERVER_INFO,
        'link' => tep_href_link('server_info.php')
      ),
      array(
        'code' => 'version_check.php',
        'title' => BOX_TOOLS_VERSION_CHECK,
        'link' => tep_href_link('version_check.php')
      ),
      array(
        'code' => 'whos_online.php',
        'title' => BOX_TOOLS_WHOS_ONLINE,
        'link' => tep_href_link('whos_online.php')
      )
    )
  );
?>
