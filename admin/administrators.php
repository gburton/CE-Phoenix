<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $htaccess_array = null;
  $htpasswd_array = null;
  $is_iis = stripos($_SERVER['SERVER_SOFTWARE'], 'iis');

  $authuserfile_array = ['##### OSCOM ADMIN PROTECTION - BEGIN #####',
                         'AuthType Basic',
                         'AuthName "OSCOM CE Phoenix Administration Tool"',
                         'AuthUserFile ' . DIR_FS_ADMIN . '.htpasswd_oscommerce',
                         'Require valid-user',
                         '##### OSCOM ADMIN PROTECTION - END #####'];

  if (!$is_iis && file_exists(DIR_FS_ADMIN . '.htpasswd_oscommerce') && tep_is_writable(DIR_FS_ADMIN . '.htpasswd_oscommerce') && file_exists(DIR_FS_ADMIN . '.htaccess') && tep_is_writable(DIR_FS_ADMIN . '.htaccess')) {
    $htaccess_array = [];
    $htpasswd_array = [];

    if (filesize(DIR_FS_ADMIN . '.htaccess') > 0) {
      $fg = fopen(DIR_FS_ADMIN . '.htaccess', 'rb');
      $data = fread($fg, filesize(DIR_FS_ADMIN . '.htaccess'));
      fclose($fg);

      $htaccess_array = explode("\n", $data);
    }

    if (filesize(DIR_FS_ADMIN . '.htpasswd_oscommerce') > 0) {
      $fg = fopen(DIR_FS_ADMIN . '.htpasswd_oscommerce', 'rb');
      $data = fread($fg, filesize(DIR_FS_ADMIN . '.htpasswd_oscommerce'));
      fclose($fg);

      $htpasswd_array = explode("\n", $data);
    }
  }

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('administrators', 'preAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
        require('includes/functions/password_funcs.php');

        $username = tep_db_prepare_input($_POST['username']);
        $password = tep_db_prepare_input($_POST['password']);

        $check_query = tep_db_query("select id from administrators where user_name = '" . tep_db_input($username) . "' limit 1");

        if (tep_db_num_rows($check_query) < 1) {
          tep_db_query("insert into administrators (user_name, user_password) values ('" . tep_db_input($username) . "', '" . tep_db_input(tep_encrypt_password($password)) . "')");

          if (is_array($htpasswd_array)) {
            for ($i=0, $n=count($htpasswd_array); $i<$n; $i++) {
              list($ht_username, $ht_password) = explode(':', $htpasswd_array[$i], 2);

              if ($ht_username == $username) {
                unset($htpasswd_array[$i]);
              }
            }

            if (isset($_POST['htaccess']) && ($_POST['htaccess'] == 'true')) {
              $htpasswd_array[] = $username . ':' . tep_crypt_apr_md5($password);
            }

            $fp = fopen(DIR_FS_ADMIN . '.htpasswd_oscommerce', 'w');
            fwrite($fp, implode("\n", $htpasswd_array));
            fclose($fp);

            if (empty($htpasswd_array)) {
              for ($i=0, $n=count($htaccess_array); $i<$n; $i++) {
                if (in_array($htaccess_array[$i], $authuserfile_array)) {
                  unset($htaccess_array[$i]);
                }
              }
            } elseif (!in_array('AuthUserFile ' . DIR_FS_ADMIN . '.htpasswd_oscommerce', $htaccess_array)) {
              array_splice($htaccess_array, count($htaccess_array), 0, $authuserfile_array);
            }

            $fp = fopen(DIR_FS_ADMIN . '.htaccess', 'w');
            fwrite($fp, implode("\n", $htaccess_array));
            fclose($fp);
          }
        } else {
          $messageStack->add_session(ERROR_ADMINISTRATOR_EXISTS, 'error');
        }

        $OSCOM_Hooks->call('administrators', 'insertAction');

        tep_redirect(tep_href_link('administrators.php'));
        break;
      case 'save':
        require('includes/functions/password_funcs.php');

        $username = tep_db_prepare_input($_POST['username']);
        $password = tep_db_prepare_input($_POST['password']);

        $check_query = tep_db_query("select id, user_name from administrators where id = '" . (int)$_GET['aID'] . "'");
        $check = tep_db_fetch_array($check_query);

// update username in current session if changed
        if ( ($check['id'] == $admin['id']) && ($check['user_name'] != $admin['username']) ) {
          $admin['username'] = $username;
        }

// update username in htpasswd if changed
        if (is_array($htpasswd_array)) {
          for ($i=0, $n=count($htpasswd_array); $i<$n; $i++) {
            if (false === strpos($htpasswd_array[$i], ':')) {
              continue;
            }

            list($ht_username, $ht_password) = explode(':', $htpasswd_array[$i], 2);

            if ( ($check['user_name'] == $ht_username) && ($check['user_name'] != $username) ) {
              $htpasswd_array[$i] = $username . ':' . $ht_password;
            }
          }
        }

        tep_db_query("update administrators set user_name = '" . tep_db_input($username) . "' where id = '" . (int)$_GET['aID'] . "'");

        if (tep_not_null($password)) {
// update password in htpasswd
          if (is_array($htpasswd_array)) {
            for ($i=0, $n=count($htpasswd_array); $i<$n; $i++) {
              list($ht_username, $ht_password) = explode(':', $htpasswd_array[$i], 2);

              if ($ht_username == $username) {
                unset($htpasswd_array[$i]);
              }
            }

            if (isset($_POST['htaccess']) && ($_POST['htaccess'] == 'true')) {
              $htpasswd_array[] = $username . ':' . tep_crypt_apr_md5($password);
            }
          }

          tep_db_query("update administrators set user_password = '" . tep_db_input(tep_encrypt_password($password)) . "' where id = '" . (int)$_GET['aID'] . "'");
        } elseif (!isset($_POST['htaccess']) || ($_POST['htaccess'] != 'true')) {
          if (is_array($htpasswd_array)) {
            for ($i=0, $n=count($htpasswd_array); $i<$n; $i++) {
              list($ht_username, $ht_password) = explode(':', $htpasswd_array[$i], 2);

              if ($ht_username == $username) {
                unset($htpasswd_array[$i]);
              }
            }
          }
        }

// write new htpasswd file
        if (is_array($htpasswd_array)) {
          $fp = fopen(DIR_FS_ADMIN . '.htpasswd_oscommerce', 'w');
          fwrite($fp, implode("\n", $htpasswd_array));
          fclose($fp);

          if (!in_array('AuthUserFile ' . DIR_FS_ADMIN . '.htpasswd_oscommerce', $htaccess_array) && !empty($htpasswd_array)) {
            array_splice($htaccess_array, count($htaccess_array), 0, $authuserfile_array);
          } elseif (empty($htpasswd_array)) {
            for ($i=0, $n=count($htaccess_array); $i<$n; $i++) {
              if (in_array($htaccess_array[$i], $authuserfile_array)) {
                unset($htaccess_array[$i]);
              }
            }
          }

          $fp = fopen(DIR_FS_ADMIN . '.htaccess', 'w');
          fwrite($fp, implode("\n", $htaccess_array));
          fclose($fp);
        }

        $OSCOM_Hooks->call('administrators', 'saveAction');

        tep_redirect(tep_href_link('administrators.php', 'aID=' . (int)$_GET['aID']));
        break;
      case 'deleteconfirm':
        $id = tep_db_prepare_input($_GET['aID']);

        $check_query = tep_db_query("select id, user_name from administrators where id = '" . (int)$id . "'");
        $check = tep_db_fetch_array($check_query);

        if ($admin['id'] == $check['id']) {
          tep_session_unregister('admin');
        }

        tep_db_query("delete from administrators where id = '" . (int)$id . "'");

        if (is_array($htpasswd_array)) {
          for ($i=0, $n=count($htpasswd_array); $i<$n; $i++) {
            list($ht_username, $ht_password) = explode(':', $htpasswd_array[$i], 2);

            if ($ht_username == $check['user_name']) {
              unset($htpasswd_array[$i]);
            }
          }

          $fp = fopen(DIR_FS_ADMIN . '.htpasswd_oscommerce', 'w');
          fwrite($fp, implode("\n", $htpasswd_array));
          fclose($fp);

          if (empty($htpasswd_array)) {
            for ($i=0, $n=count($htaccess_array); $i<$n; $i++) {
              if (in_array($htaccess_array[$i], $authuserfile_array)) {
                unset($htaccess_array[$i]);
              }
            }

            $fp = fopen(DIR_FS_ADMIN . '.htaccess', 'w');
            fwrite($fp, implode("\n", $htaccess_array));
            fclose($fp);
          }
        }

        $OSCOM_Hooks->call('administrators', 'deleteConfirmAction');

        tep_redirect(tep_href_link('administrators.php'));
        break;
    }
  }

  $OSCOM_Hooks->call('administrators', 'postAction');

  $secMessageStack = new messageStack();

  if (is_array($htpasswd_array)) {
    if (empty($htpasswd_array)) {
      $secMessageStack->add(sprintf(HTPASSWD_INFO, implode('<br>', $authuserfile_array)), 'error');
    } else {
      $secMessageStack->add(HTPASSWD_SECURED, 'success');
    }
  } else if (!$is_iis) {
    $secMessageStack->add(HTPASSWD_PERMISSIONS, 'error');
  }

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(IMAGE_INSERT_NEW_ADMIN, 'fas fa-users', tep_href_link('administrators.php', 'action=new'), null, null, 'btn-danger');
      } else {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('administrators.php'), null, null, 'btn-light');
      }
      ?>
    </div>
  </div>

  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_ADMINISTRATORS ?></th>
              <th class="text-center"><?= TABLE_HEADING_HTPASSWD ?></th>
              <th class="text-right"><?= TABLE_HEADING_ACTION ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $admins_query = tep_db_query("select id, user_name from administrators order by user_name");
            while ($admins = tep_db_fetch_array($admins_query)) {
              if (!isset($aInfo) && (!isset($_GET['aID']) || ($_GET['aID'] == $admins['id'])) && (substr($action, 0, 3) != 'new')) {
                $aInfo = new objectInfo($admins);
              }

              $htpasswd_secured = '<i class="fas fa-times-circle text-danger"></i>';

              if ($is_iis) {
                $htpasswd_secured = 'N/A';
              }

              if (is_array($htpasswd_array)) {
                for ($i=0, $n=count($htpasswd_array); $i<$n; $i++) {
                  if (false === strpos($htpasswd_array[$i], ':')) {
                    continue;
                  }

                  list($ht_username, $ht_password) = explode(':', $htpasswd_array[$i], 2);

                  if ($ht_username == $admins['user_name']) {
                    $htpasswd_secured = '<i class="fas fa-check-circle text-success"></i>';
                    break;
                  }
                }
              }

              if ( isset($aInfo->id) && ($admins['id'] == $aInfo->id) ) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('administrators.php', 'aID=' . $aInfo->id . '&action=edit') . '\'">' . "\n";
                $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('administrators.php', 'aID=' . $admins['id']) . '\'">' . "\n";
                $icon = '<a href="' . tep_href_link('administrators.php', 'aID=' . $admins['id']) . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
              ?>
                <td><?= $admins['user_name'] ?></td>
                <td class="text-center"><?= $htpasswd_secured ?></td>
                <td class="text-right"><?= $icon ?></td>
              </tr>
<?php
  }
?>
          </tbody>
        </table>
      </div>

      <?php
      echo $secMessageStack->output();
      ?>

    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'new':
      $heading[] = ['text' => TEXT_INFO_HEADING_NEW_ADMINISTRATOR];

      $contents = ['form' => tep_draw_form('administrator', 'administrators.php', 'action=insert', 'post', 'autocomplete="off"')];
      $contents[] = ['text' => TEXT_INFO_INSERT_INTRO];
      $contents[] = ['text' => TEXT_INFO_USERNAME . tep_draw_input_field('username', null, 'required autocapitalize="none" aria-required="true"')];
      $contents[] = ['text' => TEXT_INFO_PASSWORD . tep_draw_input_field('password', null, 'required autocapitalize="none" aria-required="true"', 'password')];

      if (is_array($htpasswd_array)) {
        $contents[] = ['text' => '<div class="custom-control custom-switch">' . tep_draw_selection_field('htaccess', 'checkbox', 'true', null, 'class="custom-control-input" id="aHtpasswd"') . '<label for="aHtpasswd" class="custom-control-label text-muted"><small>' . TEXT_INFO_PROTECT_WITH_HTPASSWD . '</small></label></div>'];
      }

      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('administrators.php'), null, null, 'btn-light')];
      break;
    case 'edit':
      $heading[] = ['text' => $aInfo->user_name];

      $contents = ['form' => tep_draw_form('administrator', 'administrators.php', 'aID=' . $aInfo->id . '&action=save', 'post', 'autocomplete="off"')];
      $contents[] = ['text' => TEXT_INFO_EDIT_INTRO];
      $contents[] = ['text' => TEXT_INFO_USERNAME . tep_draw_input_field('username', $aInfo->user_name, 'required autocapitalize="none" aria-required="true"')];
      $contents[] = ['text' => TEXT_INFO_NEW_PASSWORD . tep_draw_input_field('password', null, 'required autocapitalize="none" aria-required="true"', 'password')];

      if (is_array($htpasswd_array)) {
        $default_flag = false;

        for ($i=0, $n=count($htpasswd_array); $i<$n; $i++) {
          list($ht_username, $ht_password) = explode(':', $htpasswd_array[$i], 2);

          if ($ht_username == $aInfo->user_name) {
            $default_flag = true;
            break;
          }
        }

        $contents[] = ['text' => '<div class="custom-control custom-switch">' . tep_draw_selection_field('htaccess', 'checkbox', 'true', $default_flag, 'class="custom-control-input" id="aHtpasswd"') . '<label for="aHtpasswd" class="custom-control-label text-muted"><small>' . TEXT_INFO_PROTECT_WITH_HTPASSWD . '</small></label></div>'];
      }

      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times',  tep_href_link('administrators.php', 'aID=' . $aInfo->id), null, null, 'btn-light')];
      break;
    case 'delete':
      $heading[] = ['text' => $aInfo->user_name];

      $contents = ['form' => tep_draw_form('administrator', 'administrators.php', 'aID=' . $aInfo->id . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO];
      $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $aInfo->user_name];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times',  tep_href_link('administrators.php', 'aID=' . $aInfo->id), null, null, 'btn-light')];
      break;
    default:
      if (isset($aInfo) && is_object($aInfo)) {
        $heading[] = ['text' => $aInfo->user_name];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('administrators.php', 'aID=' . $aInfo->id . '&action=edit'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('administrators.php', 'aID=' . $aInfo->id . '&action=delete'), null, null, 'btn-danger')];
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-4">';
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
