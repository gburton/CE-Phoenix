<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  $cl_box_groups[] = array(
    'heading' => BOX_HEADING_AAS,
    'apps' => array(
      array(
        'code' => FILENAME_AAS,
        'title' => '<b>'.BOX_AAS_ACCESS_AAS.'</b>',
        'link' => tep_href_link(FILENAME_AAS)
      ),
      array(
        'code' => FILENAME_AAS,
        'title' => BOX_AAS_SUPPORT,
        'link' => 'http://www.alternative-administration-system.com/support'
      ),
      array(
        'code' => FILENAME_AAS,
        'title' => BOX_AAS_DISCUSSION_BOARD,
        'link' => 'http://www.alternative-administration-system.com/discussion-board'
      ),
      array(
        'code' => FILENAME_AAS,
        'title' => BOX_AAS_DONATIONS,
        'link' => 'http://www.alternative-administration-system.com/donations'
      )
      
    )
  );
?>
