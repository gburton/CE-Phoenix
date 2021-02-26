<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License

  Example usage:

  $messageStack = new messageStack();
  $messageStack->add('<strong>Error:</strong> Error 1', 'error');
  $messageStack->add('<strong>Error:</strong> Error 2', 'warning');
  if ($messageStack->size > 0) echo $messageStack->output();
*/

  class messageStack {

    public $size = 0;

	  public function __construct() {
      $this->errors = [];

      foreach (($_SESSION['messageToStack'] ?? []) as $message) {
        $this->add($message['text'], $message['type']);
      }

      unset($_SESSION['messageToStack']);
    }

    public function add($message, $type = 'error') {
      switch ($type) {
        case 'primary':
          $this->errors[] = ['params' => 'alert alert-primary', 'text' => $message];
        break;
        case 'secondary':
          $this->errors[] = ['params' => 'alert alert-secondary', 'text' => $message];
        break;
        case 'light':
          $this->errors[] = ['params' => 'alert alert-light', 'text' => $message];
        break;
        case 'dark':
          $this->errors[] = ['params' => 'alert alert-dark', 'text' => $message];
        break;
        case 'warning':
          $this->errors[] = ['params' => 'alert alert-warning', 'text' => $message];
          break;
        case 'success':
          $this->errors[] = ['params' => 'alert alert-success', 'text' => $message];
          break;
        default:
          // error & danger
          $this->errors[] = ['params' => 'alert alert-danger', 'text' => $message];
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
      $alert = null;
      foreach ($this->errors as $e) {
        $alert .= '<div class="' . $e['params'] . ' mb-1 alert-dismissible fade show" role="alert">';
          $alert .= $e['text'];
          $alert .= '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>';
        $alert .= '</div>';
      }
      
      return $alert;
    }

  }
