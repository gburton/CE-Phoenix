<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License

  Example usage:
  $heading = [];
  $heading[] = ['text'  => BOX_HEADING_TOOLS];
  $contents = [];
  $contents[] = ['text'  => SOME_TEXT];
  $box = new box();
  echo $box->infoBox($heading, $contents);
*/

  class box extends tableBlock {

    //private $this->heading = [];
    //private $this->contents = [];

    function infoBox($heading, $contents) {
      if (is_array($heading)) {
        $heading = $heading[0]['text'];
      }

      //$this->table_row_parameters = '';
      //$this->table_data_parameters = 'class="infoBoxContent"';
      $contents = $this->tableBlock($contents);

      ob_start();
      include dirname(__FILE__) . '/templates/tpl_box.php';

      return ob_get_clean();
    }

  }
