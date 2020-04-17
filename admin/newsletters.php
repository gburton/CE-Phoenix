<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = $_GET['action'] ?? '';

  if (tep_not_null($action)) {
    switch ($action) {
      case 'lock':
      case 'unlock':
        $newsletter_id = tep_db_prepare_input($_GET['nID']);
        $status = (($action == 'lock') ? '1' : '0');

        tep_db_query("update newsletters set locked = '" . $status . "' where newsletters_id = '" . (int)$newsletter_id . "'");

        tep_redirect(tep_href_link('newsletters.php', 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']));
        break;
      case 'insert':
      case 'update':
        if (isset($_POST['newsletter_id'])) $newsletter_id = tep_db_prepare_input($_POST['newsletter_id']);
        $newsletter_module = tep_db_prepare_input($_POST['module']);
        
        $allowed = array_map(function($v) {return basename($v, '.php');}, glob('includes/modules/newsletters/*.php'));
        if (!in_array($newsletter_module, $allowed)) {
          $messageStack->add(ERROR_NEWSLETTER_MODULE_NOT_EXISTS, 'error');
          $newsletter_error = true;
        }
        
        $title = tep_db_prepare_input($_POST['title']);
        $content = tep_db_prepare_input($_POST['content']);

        $newsletter_error = false;
        if (empty($title)) {
          $messageStack->add(ERROR_NEWSLETTER_TITLE, 'error');
          $newsletter_error = true;
        }

        if (empty($newsletter_module)) {
          $messageStack->add(ERROR_NEWSLETTER_MODULE, 'error');
          $newsletter_error = true;
        }

        if ($newsletter_error == false) {
          $sql_data_array = ['title' => $title,
                             'content' => $content,
                             'module' => $newsletter_module];

          if ($action == 'insert') {
            $sql_data_array['date_added'] = 'now()';
            $sql_data_array['status'] = '0';
            $sql_data_array['locked'] = '0';

            tep_db_perform('newsletters', $sql_data_array);
            $newsletter_id = tep_db_insert_id();
          } elseif ($action == 'update') {
            tep_db_perform('newsletters', $sql_data_array, 'update', "newsletters_id = '" . (int)$newsletter_id . "'");
          }

          tep_redirect(tep_href_link('newsletters.php', (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'nID=' . $newsletter_id));
        } else {
          $action = 'new';
        }
        break;
      case 'deleteconfirm':
        $newsletter_id = tep_db_prepare_input($_GET['nID']);

        tep_db_query("delete from newsletters where newsletters_id = '" . (int)$newsletter_id . "'");

        tep_redirect(tep_href_link('newsletters.php', 'page=' . $_GET['page']));
        break;
      case 'delete':
      case 'new': if (!isset($_GET['nID'])) break;
      case 'send':
      case 'confirm_send':
        $newsletter_id = tep_db_prepare_input($_GET['nID']);

        $check_query = tep_db_query("select locked from newsletters where newsletters_id = '" . (int)$newsletter_id . "'");
        $check = tep_db_fetch_array($check_query);

        if ($check['locked'] < 1) {
          switch ($action) {
            case 'delete': $error = ERROR_REMOVE_UNLOCKED_NEWSLETTER; break;
            case 'new': $error = ERROR_EDIT_UNLOCKED_NEWSLETTER; break;
            case 'send': $error = ERROR_SEND_UNLOCKED_NEWSLETTER; break;
            case 'confirm_send': $error = ERROR_SEND_UNLOCKED_NEWSLETTER; break;
          }

          $messageStack->add_session($error, 'error');

          tep_redirect(tep_href_link('newsletters.php', 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']));
        }
        break;
    }
  }

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col text-right align-self-center">
      <?php
      if (empty($action)) {
        echo tep_draw_bootstrap_button(IMAGE_NEW_NEWSLETTER, 'fas fa-newspaper', tep_href_link('newsletters.php', 'action=new'), null, null, 'btn-danger xxx text-white');
      } else {
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('newsletters.php'), null, null, 'btn-light');
      }
      ?>
    </div>
  </div>

  <?php
  if ($action == 'new') {
    $form_action = 'insert';

    $parameters = ['title' => '', 'content' => '', 'module' => ''];

    $nInfo = new objectInfo($parameters);

    if (isset($_GET['nID'])) {
      $form_action = 'update';

      $nID = tep_db_prepare_input($_GET['nID']);

      $newsletter_query = tep_db_query("select title, content, module from newsletters where newsletters_id = '" . (int)$nID . "'");
      $newsletter = tep_db_fetch_array($newsletter_query);

      $nInfo->objectInfo($newsletter);
    } elseif ($_POST) {
      $nInfo->objectInfo($_POST);
    }

    $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
    $directory_array = [];
    if ($dir = dir('includes/modules/newsletters/')) {
      while ($file = $dir->read()) {
        if (!is_dir('includes/modules/newsletters/' . $file)) {
          if (substr($file, strrpos($file, '.')) == $file_extension) {
            $directory_array[] = $file;
          }
        }
      }
      sort($directory_array);
      $dir->close();
    }

    for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
      $modules_array[] = ['id' => substr($directory_array[$i], 0, strrpos($directory_array[$i], '.')), 'text' => substr($directory_array[$i], 0, strrpos($directory_array[$i], '.'))];
    }

    echo tep_draw_form('newsletter', 'newsletters.php', (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'action=' . $form_action); if ($form_action == 'update') echo tep_draw_hidden_field('newsletter_id', $nID); ?>

    <div class="form-group row">
      <label for="Module" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_NEWSLETTER_MODULE; ?></label>
      <div class="col-sm-9">
        <?php echo tep_draw_pull_down_menu('module', $modules_array, $nInfo->module, 'id="Module" required aria-required="true"'); ?>
      </div>
    </div>

    <div class="form-group row">
      <label for="Title" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_NEWSLETTER_TITLE; ?></label>
      <div class="col-sm-9">
        <?php echo tep_draw_input_field('title', $nInfo->title, 'id="Title" required aria-required="true"'); ?>
      </div>
    </div>

    <div class="form-group row">
      <label for="Content" class="col-form-label col-sm-3 text-left text-sm-right"><?php echo TEXT_NEWSLETTER_CONTENT; ?></label>
      <div class="col-sm-9">
        <?php echo tep_draw_textarea_field('content', 'soft', '60', '15', $nInfo->content, 'id="Content" required aria-required="true"'); ?>
      </div>
    </div>

    <div class="buttonSet">
      <?php
      echo tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success btn-block btn-lg');
      echo tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('newsletters.php', (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . (isset($_GET['nID']) ? 'nID=' . (int)$_GET['nID'] : '')), null, null, 'btn-light mt-2');
      ?>
    </div>

  </form>

  <?php
  } elseif ($action == 'preview') {
    $nID = tep_db_prepare_input($_GET['nID']);

    $newsletter_query = tep_db_query("select title, content, module from newsletters where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);

    $nInfo = new objectInfo($newsletter);
    ?>

    <table class="table table-striped">
      <tr>
        <th class="w-25"><?php echo TEXT_TITLE; ?></th>
        <td><?php echo $nInfo->title; ?></td>
      </tr>
      <tr>
        <th><?php echo TEXT_CONTENT; ?></th>
        <td><?php echo nl2br($nInfo->content); ?></td>
      </tr>
    </table>

    <div class="buttonSet">
      <?php
      echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left',  tep_href_link('newsletters.php', 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']), 'primary', null, 'btn-light');
      ?>
    </div>

<?php
  } elseif ($action == 'send') {
    $nID = tep_db_prepare_input($_GET['nID']);

    $newsletter_query = tep_db_query("select title, content, module from newsletters where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);

    $nInfo = new objectInfo($newsletter);

    include('includes/languages/' . $language . '/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    include('includes/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    $module_name = $nInfo->module;
    $module = new $module_name($nInfo->title, $nInfo->content);

    if ($module->show_choose_audience) {
      echo $module->choose_audience();
    }
    else {
      echo $module->confirm();
    }
  } elseif ($action == 'confirm') {
    $nID = tep_db_prepare_input($_GET['nID']);

    $newsletter_query = tep_db_query("select title, content, module from newsletters where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);

    $nInfo = new objectInfo($newsletter);

    include('includes/languages/' . $language . '/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    include('includes/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    $module_name = $nInfo->module;
    $module = new $module_name($nInfo->title, $nInfo->content);

    echo $module->confirm();
  } elseif ($action == 'confirm_send') {
    $nID = tep_db_prepare_input($_GET['nID']);

    $newsletter_query = tep_db_query("select newsletters_id, title, content, module from newsletters where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);

    $nInfo = new objectInfo($newsletter);

    include('includes/languages/' . $language . '/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    include('includes/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    $module_name = $nInfo->module;
    $module = new $module_name($nInfo->title, $nInfo->content);
?>

  <div class="alert alert-info">
    <i class="fas fa-spinner fa-5x fa-spin float-left mr-4"></i>
    <?php echo TEXT_PLEASE_WAIT; ?>
    <div class="clearfix"></div>
  </div>
  
  <?php
  tep_set_time_limit(0);
  flush();
  $module->send($nInfo->newsletters_id);
  ?>
  
  <div class="alert alert-success">
    <i class="fas fa-thumbs-up fa-5x float-left mr-4"></i>
    <?php echo TEXT_FINISHED_SENDING_EMAILS; ?>
    <div class="clearfix"></div>
  </div>
  
  <div class="buttonSet">
    <?php
    echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('newsletters.php', 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']), 'primary', null, 'btn-light mt-2');
    ?>
  </div>

<?php
  } else {
?>

   <div class="row no-gutters">
    <div class="col">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_NEWSLETTERS; ?></th>
              <th><?php echo TABLE_HEADING_MODULE; ?></th>
              <th><?php echo TABLE_HEADING_DATE_ADDED; ?></th>
              <th><?php echo TABLE_HEADING_SIZE; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_SENT; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_STATUS; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
          <?php
          $newsletters_query_raw = "select *, length(content) as content_length from newsletters order by date_added desc";
          $newsletters_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $newsletters_query_raw, $newsletters_query_numrows);
          $newsletters_query = tep_db_query($newsletters_query_raw);
          while ($newsletters = tep_db_fetch_array($newsletters_query)) {
          if ((!isset($_GET['nID']) || (isset($_GET['nID']) && ($_GET['nID'] == $newsletters['newsletters_id']))) && !isset($nInfo) && (substr($action, 0, 3) != 'new')) {
              $nInfo = new objectInfo($newsletters);
            }

            if (isset($nInfo) && is_object($nInfo) && ($newsletters['newsletters_id'] == $nInfo->newsletters_id) ) {
              echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$nInfo->newsletters_id . '&action=preview') . '\'">';
            } else {
              echo '<tr onclick="document.location.href=\'' . tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$newsletters['newsletters_id']) . '\'">';
            }
            ?>
              <th><?php echo $newsletters['title']; ?></th>
              <td><?php echo $newsletters['module']; ?></td>
              <td><?php echo tep_date_short($newsletters['date_added']); ?></td>
              <td><?php echo number_format($newsletters['content_length']) . ' bytes'; ?></td>
              <td class="text-center"><?php if ($newsletters['status'] == '1') { echo '<i class="fas fa-check-circle text-success"></i>'; } else { echo '<i class="fas fa-times-circle text-danger"></i>'; } ?></td>
              <td class="text-center"><?php if ($newsletters['locked'] > 0) { echo '<i class="fas fa-lock text-success"></i>'; } else { echo '<i class="fas fa-lock-open text-danger"></i>'; } ?></td>
              <td class="text-right"><?php if (isset($nInfo) && is_object($nInfo) && ($newsletters['newsletters_id'] == $nInfo->newsletters_id) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('newsletters.php', 'page=' . $_GET['page'] . '&nID=' . $newsletters['newsletters_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
            </tr>
            <?php
          }
          ?>
          </tbody>
        </table>
      </div>

      <div class="row my-1">
        <div class="col"><?php echo $newsletters_split->display_count($newsletters_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS); ?></div>
        <div class="col text-right mr-2"><?php echo $newsletters_split->display_links($newsletters_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div>
      </div>

    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'delete':
      $heading[] = ['text' => $nInfo->title];

      $contents = ['form' => tep_draw_form('newsletters', 'newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$nInfo->newsletters_id . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO];
      $contents[] = ['text' => '<strong>' . $nInfo->title . '</strong>'];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$_GET['nID']), null, null, 'btn-light')];
      break;
    default:
      if (isset($nInfo) && is_object($nInfo)) {
        $heading[] = ['text' => $nInfo->title];

        if ($nInfo->locked > 0) {
          $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$nInfo->newsletters_id . '&action=new'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$nInfo->newsletters_id . '&action=delete'), null, null, 'btn-danger xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_PREVIEW, 'fas fa-eye', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$nInfo->newsletters_id . '&action=preview'), null, null, 'btn-info xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_SEND, 'fas fa-paper-plane', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$nInfo->newsletters_id . '&action=send'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_UNLOCK, 'fas fa-lock-open', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$nInfo->newsletters_id . '&action=unlock'), null, null, 'btn-warning')];
        } else {
          $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_PREVIEW, 'fas fa-eye', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$nInfo->newsletters_id . '&action=preview'), null, null, 'bt-info xxx text-white mr-2') . tep_draw_bootstrap_button(IMAGE_LOCK, 'fas fa-lock', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$nInfo->newsletters_id . '&action=lock'), null, null, 'btn-warning')];
        }
        $contents[] = ['text' => sprintf(TEXT_NEWSLETTER_DATE_ADDED, tep_date_short($nInfo->date_added))];
        if ($nInfo->status == '1') $contents[] = ['text' => sprintf(TEXT_NEWSLETTER_DATE_SENT, tep_date_short($nInfo->date_sent))];
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-3">';
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>

<?php
  }

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
