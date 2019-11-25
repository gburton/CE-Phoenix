<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

  $login_request = true;

  require('includes/application_top.php');
  require('includes/functions/password_funcs.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

// prepare to logout an active administrator if the login page is accessed again
  if (tep_session_is_registered('admin')) {
    $action = 'logoff';
  }

  if (tep_not_null($action)) {
    switch ($action) {
      case 'process':
        if (tep_session_is_registered('redirect_origin') && isset($redirect_origin['auth_user']) && !isset($_POST['username'])) {
          $username = tep_db_prepare_input($redirect_origin['auth_user']);
          $password = tep_db_prepare_input($redirect_origin['auth_pw']);
        } else {
          $username = tep_db_prepare_input($_POST['username']);
          $password = tep_db_prepare_input($_POST['password']);
        }

        $actionRecorder = new actionRecorderAdmin('ar_admin_login', null, $username);

        if ($actionRecorder->canPerform()) {
          $check_query = tep_db_query("select id, user_name, user_password from administrators where user_name = '" . tep_db_input($username) . "'");

          if (tep_db_num_rows($check_query) == 1) {
            $check = tep_db_fetch_array($check_query);

            if (tep_validate_password($password, $check['user_password'])) {
// migrate old hashed password to new phpass password
              if (tep_password_type($check['user_password']) != 'phpass') {
                tep_db_query("update administrators set user_password = '" . tep_encrypt_password($password) . "' where id = '" . (int)$check['id'] . "'");
              }

              tep_session_register('admin');

              $admin = array('id' => $check['id'],
                             'username' => $check['user_name']);

              $actionRecorder->_user_id = $admin['id'];
              $actionRecorder->record();

              if (tep_session_is_registered('redirect_origin')) {
                $page = $redirect_origin['page'];

                $get_string = http_build_query($redirect_origin['get']);

                tep_session_unregister('redirect_origin');

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

        break;

      case 'logoff':
        tep_session_unregister('admin');

        if (isset($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && !empty($_SERVER['PHP_AUTH_PW'])) {
          tep_session_register('auth_ignore');
          $auth_ignore = true;
        }

        tep_redirect(tep_href_link('index.php'));

        break;

      case 'create':
        $check_query = tep_db_query("select id from administrators limit 1");

        if (tep_db_num_rows($check_query) == 0) {
          $username = tep_db_prepare_input($_POST['username']);
          $password = tep_db_prepare_input($_POST['password']);

          if ( !empty($username) ) {
            tep_db_query("insert into administrators (user_name, user_password) values ('" . tep_db_input($username) . "', '" . tep_db_input(tep_encrypt_password($password)) . "')");
          }
        }

        tep_redirect(tep_href_link('login.php'));

        break;
    }
  }

  $languages = tep_get_languages();
  $languages_array = array();
  $languages_selected = DEFAULT_LANGUAGE;
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $languages_array[] = array('id' => $languages[$i]['code'],
                               'text' => $languages[$i]['name']);
    if ($languages[$i]['directory'] == $language) {
      $languages_selected = $languages[$i]['code'];
    }
  }

  $admins_check_query = tep_db_query("select id from administrators limit 1");
  if (tep_db_num_rows($admins_check_query) < 1) {
    $messageStack->add(TEXT_CREATE_FIRST_ADMINISTRATOR, 'warning');
  }

  require('includes/template_top.php');
  
  if ($messageStack->size > 0) echo $messageStack->output();
  
?>

  <div class="col d-flex justify-content-center">
    <div class="card text-center shadow mt-5">
      <div class="card-header text-white bg-dark"><?php echo HEADING_TITLE; ?></div>
      <div class="px-5 py-2">
        <?php echo tep_image('images/CE-Phoenix.png', 'OSCOM CE Phoenix',  null, null, 'class="card-img-top"'); ?>
      </div>
      <?php
      if (tep_db_num_rows($admins_check_query) > 0) {
        echo tep_draw_form('login', 'login.php', 'action=process');
        $button_text = BUTTON_LOGIN;
        $intro_text = null;
      }
      else {
        echo tep_draw_form('login', 'login.php', 'action=create');
        $button_text = BUTTON_CREATE_ADMINISTRATOR;
        $intro_text = TEXT_CREATE_FIRST_ADMINISTRATOR;
      }
      ?>
        
      <ul class="list-group list-group-flush">
        <li class="list-group-item border-top"><?php echo tep_draw_input_field('username', null, 'required aria-required="true" class="form-control text-muted border-0 text-muted" placeholder="' . TEXT_USERNAME . '"'); ?></li>
        <li class="list-group-item"><?php echo tep_draw_input_field('password', null, 'required aria-required="true" class="form-control text-muted border-0 text-muted" placeholder="' . TEXT_PASSWORD . '"', null, 'password'); ?></li>
        <li class="list-group-item border-bottom-0"><?php echo tep_draw_bootstrap_button($button_text, 'fas fa-key', null, null, null, 'btn-success btn-block'); ?></li>
      </ul>
      <?php 
      echo $intro_text;
      if (sizeof($languages_array) > 1) {
        ?>
        <div class="card-footer">
          <?php echo tep_draw_form('adminlanguage', 'index.php', '', 'get') . tep_draw_pull_down_menu('language', $languages_array, $languages_selected, 'class="form-control" onchange="this.form.submit();"') . tep_hide_session_id() . '</form>'; ?>
        </div>
        <?php
        }
      ?>             
    </div>
  </div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
