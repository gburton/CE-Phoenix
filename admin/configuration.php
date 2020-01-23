<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = $_GET['action'] ?? '';

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        $configuration_value = tep_db_prepare_input($_POST['configuration_value']);
        $cID = tep_db_prepare_input($_GET['cID']);

        tep_db_query("update configuration set configuration_value = '" . tep_db_input($configuration_value) . "', last_modified = now() where configuration_id = '" . (int)$cID . "'");

        tep_redirect(tep_href_link('configuration.php', 'gID=' . $_GET['gID'] . '&cID=' . $cID));
        break;
    }
  }

  $gID = (isset($_GET['gID'])) ? $_GET['gID'] : 1;

  $cfg_group_query = tep_db_query("select configuration_group_title from configuration_group where configuration_group_id = '" . (int)$gID . "'");
  $cfg_group = tep_db_fetch_array($cfg_group_query);

  require('includes/template_top.php');
?>

  <h1 class="display-4 mb-2"><?php echo $cfg_group['configuration_group_title']; ?></h1>
  
  <div class="row no-gutters">
    <div class="col">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?php echo TABLE_HEADING_CONFIGURATION_TITLE; ?></th>
              <th><?php echo TABLE_HEADING_CONFIGURATION_VALUE; ?></th>
              <th class="text-right"><?php echo TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $configuration_query = tep_db_query("select configuration_id, configuration_title, configuration_value, use_function from configuration where configuration_group_id = '" . (int)$gID . "' order by sort_order");
            while ($configuration = tep_db_fetch_array($configuration_query)) {
              $cfgValue = 0; 
              if (tep_not_null($configuration['use_function'])) {
                $use_function = $configuration['use_function'];
                if (preg_match('/->/', $use_function)) {
                  $class_method = explode('->', $use_function);
                  if (!is_object(${$class_method[0]})) {
                    include('includes/classes/' . $class_method[0] . '.php');
                    ${$class_method[0]} = new $class_method[0]();
                  }
                  $cfgValue = tep_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
                } else {
                  if (function_exists($use_function)) { 
                    $cfgValue = tep_call_function($use_function, $configuration['configuration_value']);
                  }
                }
              } else {
                $cfgValue = $configuration['configuration_value'];
              }
    
              if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $configuration['configuration_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
                $cfg_extra_query = tep_db_query("select configuration_key, configuration_description, date_added, last_modified, use_function, set_function from configuration where configuration_id = '" . (int)$configuration['configuration_id'] . "'");
                $cfg_extra = tep_db_fetch_array($cfg_extra_query);

                $cInfo_array = array_merge($configuration, $cfg_extra);
                $cInfo = new objectInfo($cInfo_array);
              }

              if ( (isset($cInfo) && is_object($cInfo)) && ($configuration['configuration_id'] == $cInfo->configuration_id) ) {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('configuration.php', 'gID=' . (int)$_GET['gID'] . '&cID=' . (int)$cInfo->configuration_id . '&action=edit') . '\'">' . "\n";
              } else {
                echo '<tr onclick="document.location.href=\'' . tep_href_link('configuration.php', 'gID=' . (int)$_GET['gID'] . '&cID=' . (int)$configuration['configuration_id']) . '\'">' . "\n";
              }
?>
                <td><?php echo $configuration['configuration_title']; ?></td>
                <td><?php echo htmlspecialchars($cfgValue); ?></td>
                <td class="text-right"><?php if ( (isset($cInfo) && is_object($cInfo)) && ($configuration['configuration_id'] == $cInfo->configuration_id) ) { echo '<i class="fas fa-chevron-circle-right text-info"></i>'; } else { echo '<a href="' . tep_href_link('configuration.php', 'gID=' . $_GET['gID'] . '&cID=' . $configuration['configuration_id']) . '"><i class="fas fa-info-circle text-muted"></i></a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
            </table>
          </div>
        </div>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'edit':
      $heading[] = array('text' => $cInfo->configuration_title);

      if ($cInfo->set_function) {
        eval('$value_field = ' . $cInfo->set_function . '"' . htmlspecialchars($cInfo->configuration_value) . '");');
      } else {
        $value_field = tep_draw_input_field('configuration_value', $cInfo->configuration_value);
      }

      $contents = array('form' => tep_draw_form('configuration', 'configuration.php', 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<strong>' . $cInfo->configuration_title . '</strong><br />' . $cInfo->configuration_description . '<br />' . $value_field);
      $contents[] = array('class' => 'text-center', 'text' => tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('configuration.php', 'gID=' . (int)$_GET['gID'] . '&cID=' . (int)$cInfo->configuration_id)));
      break;
    default:
      if (isset($cInfo) && is_object($cInfo)) {
        $heading[] = array('text' => $cInfo->configuration_title);

        $contents[] = array('class' => 'text-center', 'text' => tep_draw_button(IMAGE_EDIT, 'document', tep_href_link('configuration.php', 'gID=' . (int)$_GET['gID'] . '&cID=' . (int)$cInfo->configuration_id . '&action=edit')));
        $contents[] = array('text' => $cInfo->configuration_description);
        $contents[] = array('text' => TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($cInfo->date_added));
        if (tep_not_null($cInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($cInfo->last_modified));
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
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
