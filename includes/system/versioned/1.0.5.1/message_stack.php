<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License

  Example usage:

  $messageStack = new messageStack();
  $messageStack->add('general', 'Error: Error 1', 'error');
  $messageStack->add('general', 'Error: Error 2', 'warning');
  if ($messageStack->size('general') > 0) echo $messageStack->output('general');
*/
  class messageStack extends alertBlock {

// class constructor
    public function __construct() {
      $this->messages = [];

      if (isset($_SESSION['messageToStack'])) {
        foreach ($_SESSION['messageToStack'] as $message) {
          $this->add($message['class'], $message['text'], $message['type']);
        }
        unset($_SESSION['messageToStack']);
      }
    }

// class methods
    public function add($class, $message, $type = 'error') {
      if ($type == 'error') {
        $this->messages[] = [
          'params' => 'class="alert alert-danger alert-dismissible fade show" role="alert"',
          'class' => $class,
          'text' => $message,
        ];
      } elseif ($type == 'warning') {
        $this->messages[] = [
          'params' => 'class="alert alert-warning alert-dismissible fade show" role="alert"',
          'class' => $class,
          'text' => $message,
        ];
      } elseif ($type == 'success') {
        $this->messages[] = [
          'params' => 'class="alert alert-success alert-dismissible fade show" role="alert"',
          'class' => $class,
          'text' => $message,
        ];
      } else {
        $this->messages[] = [
          'params' => 'class="alert alert-info alert-dismissible fade show" role="alert"',
          'class' => $class,
          'text' => $message,
        ];
      }
    }

    public function add_classed($class, $message, $type = 'error') {
      $this->add($class, $message, $type);
    }

    public function add_session($class, $message, $type = 'error') {
      if (!isset($_SESSION['messageToStack'])) {
        $_SESSION['messageToStack'] = [];
      }

      $_SESSION['messageToStack'][] = ['class' => $class, 'text' => $message, 'type' => $type];
    }

    public function reset() {
      $this->messages = [];
    }

    public function output($class) {
      $output = [];
      foreach ($this->messages as $message) {
        if ($message['class'] == $class) {
          $output[] = $message;
        }
      }

      return $this->alertBlock($output);
    }

    public function size($class) {
      $count = 0;

      foreach ($this->messages as $message) {
        if ($message['class'] == $class) {
          $count++;
        }
      }

      return $count;
    }

  }
