<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $files = [];
  if ($dir = @dir(DIR_FS_CATALOG . 'includes/modules/action_recorder/')) {
    while ($file = $dir->read()) {
      if (!is_dir(DIR_FS_CATALOG . 'includes/modules/action_recorder/' . $file)) {
        if ('php' === pathinfo($file, PATHINFO_EXTENSION)) {
          $files[] = $file;
        }
      }
    }
    $dir->close();
    sort($files);
  }

  foreach ($files as $file) {
    $class = pathinfo($file, PATHINFO_FILENAME);
    if (class_exists($class)) {
      ${$class} = new $class;
    }
  }

  $modules = [];
  $modules_list = [['id' => '', 'text' => TEXT_ALL_MODULES]];

  $modules_query = tep_db_query("SELECT DISTINCT module FROM action_recorder ORDER BY module");
  while ($module = $modules_query->fetch_assoc()) {
    $modules[] = $module['module'];

    $modules_list[] = [
      'id' => $module['module'],
      'text' => (${$module['module']}->title ?? $module['module']),
    ];
  }

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('action_recorder', 'preAction');

  if (!Text::is_empty($action)) {
    switch ($action) {
      case 'expire':
        $expired_entries = 0;

        if (isset($_GET['module']) && in_array($_GET['module'], $modules)) {
          if (is_object(${$_GET['module']})) {
            $expired_entries += ${$_GET['module']}->expireEntries();
          } else {
            $delete_query = tep_db_query("DELETE FROM action_recorder WHERE module = '" . tep_db_input($_GET['module']) . "'");
            $expired_entries += tep_db_affected_rows();
          }
        } else {
          foreach ($modules as $module) {
            if (is_object(${$module})) {
              $expired_entries += ${$module}->expireEntries();
            }
          }
        }

        $OSCOM_Hooks->call('action_recorder', 'expireAction');

        $messageStack->add_session(sprintf(SUCCESS_EXPIRED_ENTRIES, $expired_entries), 'success');

        tep_redirect(tep_href_link('action_recorder.php'));

        break;
    }
  }

  $OSCOM_Hooks->call('action_recorder', 'postAction');

  require 'includes/template_top.php';
?>

  <div class="row">
    <div class="col-12 col-sm-6">
      <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>
    </div>
    <div class="col-8 col-sm-4">
      <?=
      tep_draw_form('search', 'action_recorder.php', '', 'get'),
        tep_draw_input_field('search', null, 'placeholder="' . TEXT_FILTER_SEARCH . '"', 'text', null, 'class="form-control form-control-sm mb-1"'),
        tep_draw_hidden_field('module'), tep_hide_session_id(),
      '</form>',
      tep_draw_form('filter', 'action_recorder.php', '', 'get'),
        tep_draw_pull_down_menu('module', $modules_list, null, 'onchange="this.form.submit();"', 'class="form-control form-control-sm"'),
        tep_draw_hidden_field('search'), tep_hide_session_id(),
      '</form>'
      ?>
    </div>
    <div class="col-4 col-sm-2">
      <?= tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('action_recorder.php', 'action=expire' . (isset($_GET['module']) && in_array($_GET['module'], $modules) ? '&module=' . $_GET['module'] : '')), 'primary', null, 'btn-danger btn-block btn-sm') ?>
    </div>
  </div>

  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_MODULE ?></th>
              <th><?= TABLE_HEADING_CUSTOMER ?></th>
              <th><?= TABLE_HEADING_SUCCESS ?></th>
              <th class="text-right"><?= TABLE_HEADING_DATE_ADDED ?></th>
              <th class="text-right"><?= TABLE_HEADING_ACTION ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $filter = [];

            if (isset($_GET['module'])) {
              if (in_array($_GET['module'], $modules)) {
                $filter[] = " module = '" . tep_db_input($_GET['module']) . "' ";
              } else {
                unset($_GET['module']);
              }
            }

            if (!empty($_GET['search'])) {
              $filter[] = " identifier LIKE '%" . tep_db_input($_GET['search']) . "%' ";
            }

            $actions_query_raw = "SELECT * FROM action_recorder " . (empty($filter) ? '' : " WHERE " . implode(" AND ", $filter)) . " ORDER BY date_added DESC";
            $actions_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $actions_query_raw, $actions_query_numrows);
            $actions_query = tep_db_query($actions_query_raw);
            while ($actions = $actions_query->fetch_assoc()) {
              $module = $actions['module'];

              $module_title = ${$module}->title ?? $actions['module'];

              if (!isset($aInfo) && (!isset($_GET['aID']) || ($_GET['aID'] == $actions['id']))) {
                $actions_extra_query = tep_db_query("SELECT identifier FROM action_recorder WHERE id = " . (int)$actions['id']);
                $actions_extra = $actions_extra_query->fetch_assoc();

                $aInfo_array = array_merge($actions, $actions_extra, ['module' => $module_title]);
                $aInfo = new objectInfo($aInfo_array);
              }

              if ( isset($aInfo->id) && ($actions['id'] == $aInfo->id) ) {
                echo '<tr class="table-active">';
                $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('action_recorder.php', tep_get_all_get_params(['aID']) . 'aID=' . (int)$actions['id']) . '\'">';
                $icon = '<a href="' . tep_href_link('action_recorder.php', tep_get_all_get_params(['aID']) . 'aID=' . (int)$actions['id']) . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
              ?>
                <td><?= $module_title ?></td>
                <td><?= htmlspecialchars($actions['user_name']) . ' [' . (int)$actions['user_id'] . ']' ?></td>
                <td><?= ($actions['success'] == '1') ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' ?></td>
                <td class="text-right"><?= tep_datetime_short($actions['date_added']) ?></td>
                <td class="text-right"><?= $icon ?></td>
              </tr>
<?php
  }
?>
          </tbody>
        </table>
      </div>

      <div class="row my-1">
        <div class="col"><?= $actions_split->display_count($actions_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ENTRIES) ?></div>
        <div class="col text-right mr-2"><?= $actions_split->display_links($actions_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], (isset($_GET['module']) && in_array($_GET['module'], $modules) && is_object(${$_GET['module']}) ? 'module=' . $_GET['module'] : null) . (empty($_GET['search']) ? null : '&search=' . $_GET['search'])) ?></div>
      </div>
    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    default:
      if (isset($aInfo) && is_object($aInfo)) {
        $heading[] = ['text' => $aInfo->module];

        $contents[] =['text' => TEXT_INFO_IDENTIFIER . ' ' . (empty($aInfo->identifier) ? '(empty)' : '<a href="' . tep_href_link('action_recorder.php', 'search=' . $aInfo->identifier) . '"><u>' . htmlspecialchars($aInfo->identifier) . '</u></a>')];
        $contents[] = ['text' => sprintf(TEXT_INFO_DATE_ADDED, tep_datetime_short($aInfo->date_added))];
      }
      break;
  }

  if ( ([] !== $heading) && ([] !== $contents) ) {
    echo '<div class="col-12 col-sm-4">';
      $box = new box();
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
