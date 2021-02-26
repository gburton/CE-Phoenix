<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class payment {

    public $modules, $selected_module;

// class constructor
    function __construct($module = '') {
      if (defined('MODULE_PAYMENT_INSTALLED') && tep_not_null(MODULE_PAYMENT_INSTALLED)) {
        $this->modules = explode(';', MODULE_PAYMENT_INSTALLED);

        $include_modules = [];

        if ( (tep_not_null($module)) && (in_array($module . '.php', $this->modules)) ) {
          $this->selected_module = $module;

          $include_modules[] = ['class' => $module, 'file' => "$module.php"];
        } else {
          foreach ($this->modules as $value) {
            $include_modules[] = [
              'class' => pathinfo($value, PATHINFO_FILENAME),
              'file' => $value,
            ];
          }
        }

        foreach ($include_modules as $include_module) {
          $GLOBALS[$include_module['class']] = new $include_module['class']();
        }

// if there is only one payment method, select it as default because in
// checkout_confirmation.php the $payment variable is being assigned the
// $_POST['payment'] value which will be empty (no radio button selection possible)
        if ( (tep_count_payment_modules() == 1) && (!isset($_SESSION['payment']) || !is_object($GLOBALS[$_SESSION['payment']] ?? null)) ) {
          $_SESSION['payment'] = $include_modules[0]['class'];
        }

        if ( (tep_not_null($module)) && (in_array($module, $this->modules)) && (isset($GLOBALS[$module]->form_action_url)) ) {
          $this->form_action_url = $GLOBALS[$module]->form_action_url;
        }
      }
    }

// class methods
/* The following method is needed in the checkout_confirmation.php page
   due to a chicken and egg problem with the payment class and order class.
   The payment modules needs the order destination data for the dynamic status
   feature, and the order class needs the payment module title.
   The following method is a work-around to implementing the method in all
   payment modules available which would break the modules in the contributions
   section. This should be looked into again post 2.2.
*/
    function update_status() {
      if (is_array($this->modules)
        && is_object($GLOBALS[$this->selected_module])
        && method_exists($GLOBALS[$this->selected_module], 'update_status'))
      {
        $GLOBALS[$this->selected_module]->update_status();
      }
    }

    function javascript_validation() {
      $js = '';
      if (is_array($this->modules)) {
        $js = '<script><!-- ' . "\n" .
              'function check_form() {' . "\n" .
              '  var error = 0;' . "\n" .
              '  var error_message = "' . JS_ERROR . '";' . "\n" .
              '  var payment_value = null;' . "\n" .
              '  if (document.checkout_payment.payment.length) {' . "\n" .
              '    for (var i=0; i<document.checkout_payment.payment.length; i++) {' . "\n" .
              '      if (document.checkout_payment.payment[i].checked) {' . "\n" .
              '        payment_value = document.checkout_payment.payment[i].value;' . "\n" .
              '      }' . "\n" .
              '    }' . "\n" .
              '  } else if (document.checkout_payment.payment.checked) {' . "\n" .
              '    payment_value = document.checkout_payment.payment.value;' . "\n" .
              '  } else if (document.checkout_payment.payment.value) {' . "\n" .
              '    payment_value = document.checkout_payment.payment.value;' . "\n" .
              '  }' . "\n\n";

        foreach($this->modules as $value) {
          $class = pathinfo($value, PATHINFO_FILENAME);
          if ($GLOBALS[$class]->enabled) {
            $js .= $GLOBALS[$class]->javascript_validation();
          }
        }

        $js .= "\n" . '  if (payment_value == null) {' . "\n" .
               '    error_message = error_message + "' . JS_ERROR_NO_PAYMENT_MODULE_SELECTED . '";' . "\n" .
               '    error = 1;' . "\n" .
               '  }' . "\n\n" .
               '  if (error == 1) {' . "\n" .
               '    alert(error_message);' . "\n" .
               '    return false;' . "\n" .
               '  } else {' . "\n" .
               '    return true;' . "\n" .
               '  }' . "\n" .
               '}' . "\n" .
               '//--></script>' . "\n";
      }

      return $js;
    }

    function checkout_initialization_method() {
      $initialize_array = [];

      if (is_array($this->modules)) {
        foreach($this->modules as $value) {
          $class = pathinfo($value, PATHINFO_FILENAME);

          if ($GLOBALS[$class]->enabled && method_exists($GLOBALS[$class], 'checkout_initialization_method')) {
            $initialize_array[] = $GLOBALS[$class]->checkout_initialization_method();
          }
        }
      }

      return $initialize_array;
    }

    function selection() {
      $selection_array = [];

      if (is_array($this->modules)) {
        foreach ($this->modules as $value) {
          $class = pathinfo($value, PATHINFO_FILENAME);

          if ($GLOBALS[$class]->enabled) {
            $selection = $GLOBALS[$class]->selection();

            if (is_array($selection)) {
              $selection_array[] = $selection;
            }
          }
        }
      }

      return $selection_array;
    }

    protected function is_selected_enabled() {
      return (is_array($this->modules)
        && is_object($GLOBALS[$this->selected_module])
        && ($GLOBALS[$this->selected_module]->enabled) );
    }

    function pre_confirmation_check() {
      if ($this->is_selected_enabled()) {
        $GLOBALS[$this->selected_module]->pre_confirmation_check();
      }
    }

    function confirmation() {
      if ($this->is_selected_enabled()) {
        return $GLOBALS[$this->selected_module]->confirmation();
      }
    }

    function process_button() {
      if ($this->is_selected_enabled()) {
        return $GLOBALS[$this->selected_module]->process_button();
      }
    }

    function before_process() {
      if ($this->is_selected_enabled()) {
        return $GLOBALS[$this->selected_module]->before_process();
      }
    }

    function after_process() {
      if ($this->is_selected_enabled()) {
        return $GLOBALS[$this->selected_module]->after_process();
      }
    }

    function get_error() {
      if ($this->is_selected_enabled()) {
        return $GLOBALS[$this->selected_module]->get_error();
      }
    }

  }
