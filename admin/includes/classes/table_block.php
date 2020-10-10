<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class tableBlock {

    public $table_row_parameters = '';
    public $table_data_parameters = '';
    
    function __construct() {}

    function tableBlock($contents) {
      $tableBox_string = '';

      for ($i=0, $n=count($contents); $i<$n; $i++) {
        $tableBox_string .= '<tr';
        if (tep_not_null($this->table_row_parameters)) $tableBox_string .= ' ' . $this->table_row_parameters;
        if (isset($contents[$i]['params']) && tep_not_null($contents[$i]['params'])) $tableBox_string .= ' ' . $contents[$i]['params'];
        $tableBox_string .= '>';

        if (isset($contents[$i][0]) && is_array($contents[$i][0])) {
          for ($x=0, $y=count($contents[$i]); $x<$y; $x++) {
            if (isset($contents[$i][$x]['text']) && tep_not_null($contents[$i][$x]['text'])) {
              $tableBox_string .= '<td';
              if (isset($contents[$i][$x]['class']) && tep_not_null($contents[$i][$x]['class'])) $tableBox_string .= ' class="' . $contents[$i][$x]['class'] . '"';
              if (isset($contents[$i][$x]['params']) && tep_not_null($contents[$i][$x]['params'])) {
                $tableBox_string .= ' ' . $contents[$i][$x]['params'];
              } elseif (tep_not_null($this->table_data_parameters)) {
                $tableBox_string .= ' ' . $this->table_data_parameters;
              }
              $tableBox_string .= '>';
              if (isset($contents[$i][$x]['form']) && tep_not_null($contents[$i][$x]['form'])) $tableBox_string .= $contents[$i][$x]['form'];
              $tableBox_string .= $contents[$i][$x]['text'];
              if (isset($contents[$i][$x]['form']) && tep_not_null($contents[$i][$x]['form'])) $tableBox_string .= '</form>';
              $tableBox_string .= '</td>' . PHP_EOL;
            }
          }
        } else {
          $tableBox_string .= '<td';
          if (isset($contents[$i]['class']) && tep_not_null($contents[$i]['class'])) $tableBox_string .= ' class="' . $contents[$i]['class'] . '"';
          if (isset($contents[$i]['params']) && tep_not_null($contents[$i]['params'])) {
            $tableBox_string .= ' ' . $contents[$i]['params'];
          } elseif (tep_not_null($this->table_data_parameters)) {
            $tableBox_string .= ' ' . $this->table_data_parameters;
          }
          $tableBox_string .= '>' . $contents[$i]['text'] . '</td>' . PHP_EOL;
        }

        $tableBox_string .= '</tr>';
      }

      return $tableBox_string;
    }
  }
  