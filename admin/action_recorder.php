<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
  $directory_array = [];
  if ($dir = @dir(DIR_FS_CATALOG_MODULES . 'action_recorder/')) {
    while ($file = $dir->read()) {
      if (!is_dir(DIR_FS_CATALOG_MODULES . 'action_recorder/' . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }

  for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
    $file = $directory_array[$i];

    if (file_exists(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/action_recorder/' . $file)) {
      include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/action_recorder/' . $file);
    }

    include(DIR_FS_CATALOG_MODULES . 'action_recorder/' . $file);

    $class = substr($file, 0, strrpos($file, '.'));
    if (class_exists($class)) {
      ${$class} = new $class;
    }
  }

  $modules_array = [];
  $modules_list_array = [['id' => '', 'text' => TEXT_ALL_MODULES]];

  $modules_query = tep_db_query("select distinct module from action_recorder order by module");
  while ($modules = tep_db_fetch_array($modules_query)) {
    $modules_array[] = $modules['module'];

    $modules_list_array[] = ['id' => $modules['module'],
                             'text' => (is_object(${$modules['module']}) ? ${$modules['module']}->title : $modules['module'])];
  }

  $action = $_GET['action'] ?? '';
  
  $OSCOM_Hooks->call('action_recorder', 'preAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'expire':
        $expired_entries = 0;

        if (isset($_GET['module']) && in_array($_GET['module'], $modules_array)) {
          if (is_object(${$_GET['module']})) {
            $expired_entries += ${$_GET['module']}->expireEntries();
          } else {
            $delete_query = tep_db_query("delete from action_recorder where module = '" . tep_db_input($_GET['module']) . "'");
            $expired_entries += tep_db_affected_rows();
          }
        } else {
          foreach ($modules_array as $module) {
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

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col-12 col-sm-6">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col-8 col-sm-4">
      <?php
      echo tep_draw_form('search', 'action_recorder.php', '', 'get');
        echo tep_draw_input_field('search', null, 'placeholder="' . TEXT_FILTER_SEARCH . '"', null, null, 'class="form-control form-control-sm mb-1"');
        echo tep_draw_hidden_field('module') . tep_hide_session_id();
      echo '</form>';
      echo tep_draw_form('filter', 'action_recorder.php', '', 'get');
        echo tep_draw_pull_down_menu('module', $modules_list_array, null, 'onchange="this.form.submit();"', 'class="form-control form-control-sm"');
        echo tep_draw_hidden_field('search') . tep_hide_session_id();
      echo '</form>';
      ?>
    </div>
    <div class="col-4 col-sm-2">
      <?php
      echo tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('action_recorder.php', 'action=expire' . (isset($_GET['module']) && in_array($_GET['module'], $modules_array) ? '&module=' . $_GET['module'] : '')), 'primary', null, 'btn-danger btn-block btn-sm');
      ?>
    </div>
  </div>

  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_MODULE; ?></th>
              <th><?php echo TABLE_HEADING_CUSTOMER; ?></th>
              <th><?php echo TABLE_HEADING_SUCCESS; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_DATE_ADDED; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $filter = [];

            if (isset($_GET['module']) && in_array($_GET['module'], $modules_array)) {
              $filter[] = " module = '" . tep_db_input($_GET['module']) . "' ";
            }

            if (isset($_GET['search']) && !empty($_GET['search'])) {
              $filter[] = " identifier like '%" . tep_db_input($_GET['search']) . "%' ";
            }

            $actions_query_raw = "select * from action_recorder " . (!empty($filter) ? " where " . implode(" and ", $filter) : "") . " order by date_added desc";
            $actions_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $actions_query_raw, $actions_query_numrows);
            $actions_query = tep_db_query($actions_query_raw);
            while ($actions = tep_db_fetch_array($actions_query)) {
              $module = $actions['module'];

              $module_title = $actions['module'];
              if (is_object(${$module})) {
                $module_title = ${$module}->title;
              }

              if ((!isset($_GET['aID']) || (isset($_GET['aID']) && ($_GET['aID'] == $actions['id']))) && !isset($aInfo)) {
                $actions_extra_query = tep_db_query("select identifier from action_recorder where id = '" . (int)$actions['id'] . "'");
                $actions_extra = tep_db_fetch_array($actions_extra_query);

                $aInfo_array = array_merge($actions, $actions_extra, ['module' => $module_title]);
                $aInfo = new objectInfo($aInfo_array);
              }

              if ( (isset($aInfo) && is_object($aInfo)) && ($actions['id'] == $aInfo->id) ) {
                echo '<tr class="table-active">';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('action_recorder.php', tep_get_all_get_params(['aID']) . 'aID=' . (int)$actions['id']) . '\'">';
              }
              ?>
                <td><?php echo $module_title; ?></td>
                <td><?php echo tep_output_string_protected($actions['user_name']) . ' [' . (int)$actions['user_id'] . ']'; ?></td>
                <td><?php echo ($actions['success'] == '1') ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'; ?></td>
                <td class="text-right"><?php echo tep_datetime_short($actions['date_added']); ?></td>
                <td class="text-right"><?php if ( (isset($aInfo) && is_object($aInfo)) && ($actions['id'] == $aInfo->id) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('action_recorder.php', tep_get_all_get_params(['aID']) . 'aID=' . (int)$actions['id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?></td>
              </tr>
<?php
  }
?>
          </tbody>
        </table>
      </div>

      <div class="row my-1">
        <div class="col"><?php echo $actions_split->display_count($actions_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ENTRIES); ?></div>
        <div class="col text-right mr-2"><?php echo $actions_split->display_links($actions_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], (isset($_GET['module']) && in_array($_GET['module'], $modules_array) && is_object(${$_GET['module']}) ? 'module=' . $_GET['module'] : null) . '&' . (isset($_GET['search']) && !empty($_GET['search']) ? 'search=' . $_GET['search'] : null)); ?></div>
      </div>
    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    default:
      if (isset($aInfo) && is_object($aInfo)) {
        $heading[] = ['text' => $aInfo->module];

        $contents[] =['text' => TEXT_INFO_IDENTIFIER . ' ' . (!empty($aInfo->identifier) ? '<a href="' . tep_href_link('action_recorder.php', 'search=' . $aInfo->identifier) . '"><u>' . tep_output_string_protected($aInfo->identifier) . '</u></a>': '(empty)')];
        $contents[] = ['text' => sprintf(TEXT_INFO_DATE_ADDED, tep_datetime_short($aInfo->date_added))];
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
