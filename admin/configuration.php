<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $action = $_GET['action'] ?? '';

  $OSCOM_Hooks->call('configuration', 'preAction');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        $configuration_value = tep_db_prepare_input($_POST['configuration_value']);
        $cID = tep_db_prepare_input($_GET['cID']);

        tep_db_query("UPDATE configuration SET configuration_value = '" . tep_db_input($configuration_value) . "', last_modified = NOW() WHERE configuration_id = " . (int)$cID);

        $OSCOM_Hooks->call('configuration', 'saveAction');

        tep_redirect(tep_href_link('configuration.php', 'gID=' . $_GET['gID'] . '&cID=' . $cID));
        break;
    }
  }

  $OSCOM_Hooks->call('configuration', 'postAction');

  $gID = $_GET['gID'] ?? 1;

  $cfg_group_query = tep_db_query("SELECT configuration_group_title FROM configuration_group WHERE configuration_group_id = " . (int)$gID);
  $cfg_group = tep_db_fetch_array($cfg_group_query);

  require 'includes/template_top.php';
?>

  <h1 class="display-4 mb-2"><?= $cfg_group['configuration_group_title']; ?></h1>

  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_CONFIGURATION_TITLE; ?></th>
              <th><?= TABLE_HEADING_CONFIGURATION_VALUE; ?></th>
              <th class="text-right"><?= TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $configuration_query = tep_db_query("SELECT configuration_id, configuration_title, configuration_value, use_function FROM configuration WHERE configuration_group_id = " . (int)$gID . " ORDER BY sort_order");
            while ($configuration = tep_db_fetch_array($configuration_query)) {
              if (tep_not_null($configuration['use_function'])) {
                if (strpos($configuration['use_function'], '->')) {
                  // if there is a -> with something before it
                  // make sure that the something is instantiated
                  $class_method = explode('->', $configuration['use_function'], 2);
                  $use_function = [Guarantor::ensure_global($class_method[0]), $class_method[1]];
                } else {
                  $use_function = $configuration['use_function'];
                }

                if (is_callable($use_function)) {
                  $cfgValue = call_user_func($use_function, $configuration['configuration_value']);
                } else {
                  $cfgValue = 0;
                  $messageStack->add(
                    sprintf(
                      WARNING_INVALID_USE_FUNCTION,
                      $configuration['use_function'],
                      $configuration['configuration_title']),
                    'warning');
                }
              } else {
                $cfgValue = $configuration['configuration_value'];
              }

              if (!isset($cInfo) && (!isset($_GET['cID']) || ($_GET['cID'] == $configuration['configuration_id'])) && (substr($action, 0, strlen('new')) !== 'new')) {
                $cfg_extra_query = tep_db_query("SELECT configuration_key, configuration_description, date_added, last_modified, use_function, set_function FROM configuration WHERE configuration_id = " . (int)$configuration['configuration_id']);
                $cfg_extra = tep_db_fetch_array($cfg_extra_query);

                $cInfo_array = array_merge($configuration, $cfg_extra);
                $cInfo = new objectInfo($cInfo_array);
              }

              if ( isset($cInfo->configuration_id) && ($configuration['configuration_id'] == $cInfo->configuration_id) ) {
                echo '<tr class="table-active" onclick="document.location.href=\'' . tep_href_link('configuration.php', 'gID=' . (int)$_GET['gID'] . '&cID=' . (int)$cInfo->configuration_id . '&action=edit') . '\'">' . "\n";
                $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('configuration.php', 'gID=' . (int)$_GET['gID'] . '&cID=' . (int)$configuration['configuration_id']) . '\'">' . "\n";
                $icon = '<a href="' . tep_href_link('configuration.php', 'gID=' . $_GET['gID'] . '&cID=' . $configuration['configuration_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>';
              }
?>
                <td><?= $configuration['configuration_title']; ?></td>
                <td><?= htmlspecialchars($cfgValue); ?></td>
                <td class="text-right"><?= $icon ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              </tbody>
            </table>
          </div>
        </div>
<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'edit':
      $heading[] = ['text' => $cInfo->configuration_title];

      if ($cInfo->set_function) {
        eval('$value_field = ' . $cInfo->set_function . '"' . htmlspecialchars($cInfo->configuration_value) . '");');
      } else {
        $value_field = tep_draw_input_field('configuration_value', $cInfo->configuration_value);
      }

      $contents = ['form' => tep_draw_form('configuration', 'configuration.php', 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save')];
      $contents[] = ['text' => TEXT_INFO_EDIT_INTRO];
      $contents[] = ['text' => '<strong>' . $cInfo->configuration_title . '</strong><br>' . $cInfo->configuration_description . '<br>' . $value_field];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-save', null, 'primary', null, 'btn-success mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('configuration.php', 'gID=' . (int)$_GET['gID'] . '&cID=' . (int)$cInfo->configuration_id), null, null, 'btn-light')];
      break;
    default:
      if (isset($cInfo) && is_object($cInfo)) {
        $heading[] = ['text' => $cInfo->configuration_title];

        $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_EDIT, 'fas fa-cogs', tep_href_link('configuration.php', 'gID=' . (int)$_GET['gID'] . '&cID=' . (int)$cInfo->configuration_id . '&action=edit'), null, null, 'btn-warning mr-2')];
        $contents[] = ['text' => $cInfo->configuration_description];
        $contents[] = ['text' => TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($cInfo->date_added)];
        if (tep_not_null($cInfo->last_modified)) {
          $contents[] = ['text' => TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($cInfo->last_modified)];
        }
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
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
