<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License

  Example usage:

  $heading = [];
  $heading[] = [
    'params' => 'class="menuBoxHeading"',
    'text'  => BOX_HEADING_TOOLS,
    'link'  => tep_href_link(basename($PHP_SELF),
  ];

  $contents = [];
  $contents[] = ['text'  => SOME_TEXT];

  $box = new box();
  echo $box->infoBox($heading, $contents);
*/

  class box extends tableBlock {

    function __construct() {
      $this->heading = [];
      $this->contents = [];
    }

    function infoBox($heading, $contents) {
      $parameters = ['heading' => &$heading, 'contents' => &$contents];
      echo $GLOBALS['OSCOM_Hooks']->call(pathinfo($GLOBALS['PHP_SELF'], PATHINFO_FILENAME), 'infoBox', $parameters);

      $this->table_row_parameters = 'class="infoBoxHeading"';
      $this->table_data_parameters = 'class="infoBoxHeading"';
      $this->heading = $this->tableBlock($heading);

      $this->table_row_parameters = '';
      $this->table_data_parameters = 'class="infoBoxContent"';
      $this->contents = $this->tableBlock($contents);

      return $this->heading . $this->contents;
    }

  }
