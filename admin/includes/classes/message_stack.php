<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License

  Example usage:

  $messageStack = new messageStack();
  $messageStack->add('Error: Error 1', 'error');
  $messageStack->add('Error: Error 2', 'warning');
  if ($messageStack->size > 0) echo $messageStack->output();
*/

  class messageStack extends tableBlock {

    public $size = 0;

	public function __construct() {
      $this->errors = [];

      foreach (($_SESSION['messageToStack'] ?? []) as $message) {
        $this->add($message['text'], $message['type']);
      }

      unset($_SESSION['messageToStack']);
    }

    public function add($message, $type = 'error') {
      if ($type == 'error') {
        $this->errors[] = [
          'params' => 'class="messageStackError"',
          'text' => tep_image('images/icons/error.gif', ICON_ERROR) . '&nbsp;' . $message,
        ];
      } elseif ($type == 'warning') {
        $this->errors[] = [
          'params' => 'class="messageStackWarning"',
          'text' => tep_image('images/icons/warning.gif', ICON_WARNING) . '&nbsp;' . $message,
        ];
      } elseif ($type == 'success') {
        $this->errors[] = [
          'params' => 'class="messageStackSuccess"',
          'text' => tep_image('images/icons/success.gif', ICON_SUCCESS) . '&nbsp;' . $message,
        ];
      } else {
        $this->errors[] = [
          'params' => 'class="messageStackError"',
          'text' => $message,
        ];
      }

      $this->size++;
    }

    public function add_classed($class, $message, $type = 'error') {
      $this->add($message, $type);
    }

    public function add_session($message, $type = 'error') {
      if (!isset($_SESSION['messageToStack'])) {
        $_SESSION['messageToStack'] = [];
      }

      $_SESSION['messageToStack'][] = ['text' => $message, 'type' => $type];
    }

    public function reset() {
      $this->errors = [];
      $this->size = 0;
    }

    public function output() {
      $this->table_data_parameters = 'class="messageBox"';
      return $this->tableBlock($this->errors);
    }

  }
