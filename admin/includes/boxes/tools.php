<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  $cl_box_groups[] = array(
    'heading' => BOX_HEADING_TOOLS,
    'apps' => array(
      array(
        'code' => 'action_recorder.php',
        'title' => BOX_TOOLS_ACTION_RECORDER,
        'link' => tep_href_link('action_recorder.php')
      ),
      array(
        'code' => 'backup.php',
        'title' => BOX_TOOLS_BACKUP,
        'link' => tep_href_link('backup.php')
      ),
      array(
        'code' => 'banner_manager.php',
        'title' => BOX_TOOLS_BANNER_MANAGER,
        'link' => tep_href_link('banner_manager.php')
      ),
      array(
        'code' => 'cache.php',
        'title' => BOX_TOOLS_CACHE,
        'link' => tep_href_link('cache.php')
      ),
      array(
        'code' => FILENAME_DEFINE_LANGUAGE,
        'title' => BOX_TOOLS_DEFINE_LANGUAGE,
        'link' => tep_href_link(FILENAME_DEFINE_LANGUAGE)
      ),
      array(
        'code' => FILENAME_MAIL,
        'title' => BOX_TOOLS_MAIL,
        'link' => tep_href_link(FILENAME_MAIL)
      ),
      array(
        'code' => FILENAME_NEWSLETTERS,
        'title' => BOX_TOOLS_NEWSLETTER_MANAGER,
        'link' => tep_href_link(FILENAME_NEWSLETTERS)
      ),
      array(
        'code' => FILENAME_SEC_DIR_PERMISSIONS,
        'title' => BOX_TOOLS_SEC_DIR_PERMISSIONS,
        'link' => tep_href_link(FILENAME_SEC_DIR_PERMISSIONS)
      ),
      array(
        'code' => FILENAME_SERVER_INFO,
        'title' => BOX_TOOLS_SERVER_INFO,
        'link' => tep_href_link(FILENAME_SERVER_INFO)
      ),
      array(
        'code' => FILENAME_VERSION_CHECK,
        'title' => BOX_TOOLS_VERSION_CHECK,
        'link' => tep_href_link(FILENAME_VERSION_CHECK)
      ),
      array(
        'code' => FILENAME_WHOS_ONLINE,
        'title' => BOX_TOOLS_WHOS_ONLINE,
        'link' => tep_href_link(FILENAME_WHOS_ONLINE)
      )
    )
  );
?>
