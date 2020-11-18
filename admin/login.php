<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $login_request = true;

  require 'includes/application_top.php';
  require 'includes/functions/password_funcs.php';

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('login', 'preAction');

// prepare to logout an active administrator if the login page is accessed again
  if (isset($_SESSION['admin'])) {
    $action = 'logoff';
  }

  if (tep_not_null($action)) {
    switch ($action) {
      case 'process':
        if (isset($_SESSION['redirect_origin']) && isset($redirect_origin['auth_user']) && !isset($_POST['username'])) {
          $username = tep_db_prepare_input($redirect_origin['auth_user']);
          $password = tep_db_prepare_input($redirect_origin['auth_pw']);
        } else {
          $username = tep_db_prepare_input($_POST['username']);
          $password = tep_db_prepare_input($_POST['password']);
        }

        $actionRecorder = new actionRecorderAdmin('ar_admin_login', null, $username);

        if ($actionRecorder->canPerform()) {
          $check_query = tep_db_query("SELECT id, user_name, user_password FROM administrators WHERE user_name = '" . tep_db_input($username) . "'");

          if (tep_db_num_rows($check_query) == 1) {
            $check = tep_db_fetch_array($check_query);

            if (tep_validate_password($password, $check['user_password'])) {
// migrate old hashed password to new phpass password
              if (tep_password_type($check['user_password']) != 'phpass') {
                tep_db_query("UPDATE administrators SET user_password = '" . tep_encrypt_password($password) . "' WHERE id = '" . (int)$check['id'] . "'");
              }

              $_SESSION['admin'] = [
                'id' => $check['id'],
                'username' => $check['user_name'],
              ];

              $actionRecorder->_user_id = $_SESSION['admin']['id'];
              $actionRecorder->record();

              if (isset($_SESSION['redirect_origin'])) {
                $page = $redirect_origin['page'];
                $get_string = http_build_query($redirect_origin['get']);

                unset($_SESSION['redirect_origin']);

                tep_redirect(tep_href_link($page, $get_string));
              } else {
                tep_redirect(tep_href_link('index.php'));
              }
            }
          }

          if (isset($_POST['username'])) {
            $messageStack->add(ERROR_INVALID_ADMINISTRATOR, 'error');
          }
        } else {
          $messageStack->add(sprintf(ERROR_ACTION_RECORDER, (defined('MODULE_ACTION_RECORDER_ADMIN_LOGIN_MINUTES') ? (int)MODULE_ACTION_RECORDER_ADMIN_LOGIN_MINUTES : 5)));
        }

        if (isset($_POST['username'])) {
          $actionRecorder->record(false);
        }

        $OSCOM_Hooks->call('login', 'processAction');

        break;

      case 'logoff':
        unset($_SESSION['admin']);

        if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
          $_SESSION['auth_ignore'] = true;
        }

        $OSCOM_Hooks->call('login', 'logoffAction');

        tep_redirect(tep_href_link('index.php'));

        break;

      case 'create':
        $check_query = tep_db_query("SELECT id FROM administrators LIMIT 1");

        if (tep_db_num_rows($check_query) == 0) {
          $username = tep_db_prepare_input($_POST['username']);
          $password = tep_db_prepare_input($_POST['password']);

          if ( !empty($username) ) {
            tep_db_query("INSERT INTO administrators (user_name, user_password) VALUES ('" . tep_db_input($username) . "', '" . tep_db_input(tep_encrypt_password($password)) . "')");
          }
        }

        $OSCOM_Hooks->call('login', 'createAction');

        tep_redirect(tep_href_link('login.php'));

        break;
    }
  }

  $OSCOM_Hooks->call('login', 'postAction');

  $languages = [];
  $language_selected = DEFAULT_LANGUAGE;
  foreach (tep_get_languages() as $l) {
    $languages[] = [
      'id' => $l['code'],
      'text' => $l['name'],
    ];

    if ($l['directory'] == $language) {
      $language_selected = $l['code'];
    }
  }

  $admins_check_query = tep_db_query("SELECT id FROM administrators LIMIT 1");
  if (tep_db_num_rows($admins_check_query) < 1) {
    $messageStack->add(TEXT_CREATE_FIRST_ADMINISTRATOR, 'warning');
    $button_text = BUTTON_CREATE_ADMINISTRATOR;
    $intro_text = TEXT_CREATE_FIRST_ADMINISTRATOR;
    $parameter_string = 'action=create';
  } else {
    $button_text = BUTTON_LOGIN;
    $intro_text = null;
    $parameter_string = 'action=process';
  }

  require 'includes/template_top.php';
?>

  <div class="mx-auto w-75 w-md-25">
    <div class="card text-center shadow mt-5">
      <div class="card-header text-white bg-dark"><?= HEADING_TITLE; ?></div>
      <div class="px-5 py-2">
        <?= tep_image('images/CE-Phoenix.png', 'OSCOM CE Phoenix',  null, null, 'class="card-img-top"'); ?>
      </div>

      <?= tep_draw_form('login', 'login.php', $parameter_string); ?>
        <ul class="list-group list-group-flush">
          <li class="list-group-item border-top"><?= tep_draw_input_field('username', null, 'required autocapitalize="none" aria-required="true" placeholder="' . TEXT_USERNAME . '"', 'text', null, 'class="form-control text-muted border-0 text-muted"'); ?></li>
          <li class="list-group-item"><?= tep_draw_input_field('password', null, 'required autocapitalize="none" aria-required="true" placeholder="' . TEXT_PASSWORD . '"', 'password', null, 'class="form-control text-muted border-0 text-muted"'); ?></li>
          <li class="list-group-item border-bottom-0"><?= tep_draw_bootstrap_button($button_text, 'fas fa-key', null, null, null, 'btn-success btn-block'); ?></li>
        </ul>
      </form>

<?php
  echo $intro_text;
  if (count($languages) > 1) {
?>
      <div class="card-footer">
        <?= tep_draw_form('adminlanguage', 'index.php', '', 'get') . tep_draw_pull_down_menu('language', $languages, $language_selected, 'onchange="this.form.submit();"') . tep_hide_session_id() . '</form>'; ?>
      </div>
<?php
  }
?>
    </div>
  </div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
