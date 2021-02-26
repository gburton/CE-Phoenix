<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License

  Example usage:
  $heading = BOX_HEADING_TOOLS;
  $contents = [];
  $contents[] = ['text'  => SOME_TEXT];
  $box = new box();
  echo $box->infoBox($heading, $contents);
*/

  class box extends tableBlock {

    function infoBox($heading, $contents) {
      if (is_array($heading)) {
        $heading = $heading[0]['text'];
      }
      $parameters = ['heading' => &$heading, 'contents' => &$contents];
      $GLOBALS['OSCOM_Hooks']->call(pathinfo($GLOBALS['PHP_SELF'], PATHINFO_FILENAME), 'infoBox', $parameters);

      if (isset($contents['form'])) {
        $form_start = $contents['form'] . PHP_EOL;
        $form_close = '</form>' . PHP_EOL;
        unset($contents['form']);
      } else {
        $form_start = '';
        $form_close = '';
      }
      $contents = $this->tableBlock($contents);

      ob_start();
      include __DIR__ . '/templates/tpl_box.php';

      return ob_get_clean();
    }

  }
