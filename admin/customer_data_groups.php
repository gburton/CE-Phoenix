<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $action = $_GET['action'] ?? '';

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
        tep_db_query("INSERT INTO customer_data_groups_sequence VALUES (NULL)");
        $customer_data_groups_id = tep_db_insert_id();
      case 'save':
        if (!isset($customer_data_groups_id)) {
          $customer_data_groups_id = tep_db_prepare_input($_GET['cdgID']);
        }

        $first_language_id = key($_POST['customer_data_groups_name']);
        foreach ($_POST['customer_data_groups_name'] as $language_id => $customer_data_groups_name) {
          // if use_first was checked, get all the values other than the name from the first group
          $index = (1 == $_POST['use_first']) ? $first_language_id : $language_id;

          $sql_data_array = [
            'customer_data_groups_name' => tep_db_prepare_input($customer_data_groups_name),
            'cdg_vertical_sort_order' => tep_db_prepare_input($_POST['cdg_vertical_sort_order'][$index]),
            'cdg_horizontal_sort_order' => tep_db_prepare_input($_POST['cdg_horizontal_sort_order'][$index]),
            'customer_data_groups_width' => tep_db_prepare_input($_POST['customer_data_groups_width'][$index]),
          ];

          if ('insert' == $action) {
            $sql_data_array['customer_data_groups_id'] = $customer_data_groups_id;
            $sql_data_array['language_id'] = $language_id;

            tep_db_perform('customer_data_groups', $sql_data_array);
          } elseif ('save' == $action) {
            tep_db_perform('customer_data_groups', $sql_data_array, 'update', "customer_data_groups_id = " . (int)$customer_data_groups_id . " AND language_id = " . (int)$language_id);
          }
        }

        if ('insert' == $action) {
          tep_redirect(tep_href_link('customer_data_groups.php'));
        } elseif ('save' == $action) {
          tep_redirect(tep_href_link('customer_data_groups.php', 'page=' . $_GET['page'] . '&cdgID=' . $customer_data_groups_id));
        }
        break;
      case 'deleteconfirm':
        $customer_data_groups_id = tep_db_prepare_input($_GET['cdgID']);

        tep_db_query("DELETE FROM customer_data_groups WHERE customer_data_groups_id = " . (int)$customer_data_groups_id);
        tep_db_query("DELETE FROM customer_data_groups_sequence WHERE customer_data_groups_id = " . (int)$customer_data_groups_id);

        tep_redirect(tep_href_link('customer_data_groups.php', 'page=' . $_GET['page']));
        break;
    }
  }

  require 'includes/template_top.php';
?>
    <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMER_DATA_GROUP_NAME; ?></th>
                <th class="dataTableHeadingContent" align="center" colspan="2"><?php echo TABLE_HEADING_SORT_ORDERS; ?></th>
                <th class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_WIDTH; ?>&nbsp;</th>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $customer_data_groups_query_raw = <<<'EOSQL'
select customer_data_groups_id, customer_data_groups_name, cdg_vertical_sort_order, cdg_horizontal_sort_order, customer_data_groups_width
 from customer_data_groups 
 where language_id = 
EOSQL
. (int)$languages_id . " order by cdg_vertical_sort_order, cdg_horizontal_sort_order";
  $customer_data_groups_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customer_data_groups_query_raw, $customer_data_groups_query_numrows);
  $customer_data_groups_query = tep_db_query($customer_data_groups_query_raw);
  while ($customer_data_groups = tep_db_fetch_array($customer_data_groups_query)) {
    if ((!isset($_GET['cdgID']) || (isset($_GET['cdgID']) && ($_GET['cdgID'] == $customer_data_groups['customer_data_groups_id']))) && !isset($cdgInfo) && (substr($action, 0, 3) != 'new')) {
      $cdgInfo = new objectInfo($customer_data_groups);
    }

    if (isset($cdgInfo) && is_object($cdgInfo) && ($customer_data_groups['customer_data_groups_id'] == $cdgInfo->customer_data_groups_id)) {
      echo '                  <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('customer_data_groups.php', 'page=' . $_GET['page'] . '&cdgID=' . $cdgInfo->customer_data_groups_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('customer_data_groups.php', 'page=' . $_GET['page'] . '&cdgID=' . $customer_data_groups['customer_data_groups_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $customer_data_groups['customer_data_groups_name']; ?></td>
                <td class="dataTableContent" align="center" width="40"><?php echo $customer_data_groups['cdg_vertical_sort_order']; ?></td>
                <td class="dataTableContent" align="center" width="40"><?php echo $customer_data_groups['cdg_horizontal_sort_order']; ?></td>
                <td class="dataTableContent" align="center" width="40"><?php echo $customer_data_groups['customer_data_groups_width']; ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($cdgInfo) && is_object($cdgInfo) && ($customer_data_groups['customer_data_groups_id'] == $cdgInfo->customer_data_groups_id) ) { echo tep_image('images/icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link('customer_data_groups.php', 'page=' . $_GET['page'] . '&cdgID=' . $customer_data_groups['customer_data_groups_id']) . '">' . tep_image('images/icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $customer_data_groups_split->display_count($customer_data_groups_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMER_DATA_GROUPS); ?></td>
                    <td class="smallText" align="right"><?php echo $customer_data_groups_split->display_links($customer_data_groups_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (empty($action)) {
?>
                  <tr>
                    <td class="smallText" colspan="3" align="right"><?php echo tep_draw_button(IMAGE_NEW_CUSTOMER_DATA_GROUP, 'plus', tep_href_link('customer_data_groups.php', 'page=' . $_GET['page'] . '&action=new')); ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'new':
      $heading[] = ['text' => '<strong>' . TEXT_INFO_HEADING_NEW_CUSTOMER_DATA_GROUP . '</strong>'];

      $contents = ['form' => tep_draw_form('customer_data_groups', 'customer_data_groups.php', 'page=' . $_GET['page'] . '&action=insert')];
      $contents[] = ['text' => TEXT_INFO_INSERT_INTRO];
      $contents[] = ['text' => '<br />' . tep_draw_checkbox_field('use_first', '1', true) . TEXT_INFO_USE_FIRST_FOR_ALL];

      foreach (tep_get_languages() as $lang) {
        $contents[] = ['text' => '<br />' . TEXT_INFO_CUSTOMER_DATA_GROUP_NAME . '<br />' . tep_image(tep_catalog_href_link('includes/languages/' . $lang['directory'] . '/images/' . $lang['image'], '', 'SSL'), $lang['name']) . '&nbsp;' . tep_draw_input_field('customer_data_groups_name[' . $lang['id'] . ']')];
        $contents[] = ['text' => '<br />' . TEXT_INFO_VERTICAL_SORT_ORDER . '<br />' . tep_draw_input_field('cdg_vertical_sort_order[' . $lang['id'] . ']')];
        $contents[] = ['text' => '<br />' . TEXT_INFO_HORIZONTAL_SORT_ORDER . '<br />' . tep_draw_input_field('cdg_horizontal_sort_order[' . $lang['id'] . ']')];
        $contents[] = ['text' => '<br />' . TEXT_INFO_WIDTH . '<br />' . tep_draw_input_field('customer_data_groups_width[' . $lang['id'] . ']', 12)];
      }
      $contents[] = ['align' => 'center', 'text' => '<br />' . tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('customer_data_groups.php', 'page=' . $_GET['page']))];
      break;
    case 'edit':
      $heading[] = ['text' => '<strong>' . TEXT_INFO_HEADING_EDIT_CUSTOMER_DATA_GROUP . '</strong>'];

      $contents = ['form' => tep_draw_form('customer_data_groups', 'customer_data_groups.php', 'page=' . $_GET['page'] . '&cdgID=' . $cdgInfo->customer_data_groups_id . '&action=save')];
      $contents[] = ['text' => TEXT_INFO_EDIT_INTRO];
      $contents[] = ['text' => '<br />' . TEXT_INFO_USE_FIRST_FOR_ALL . '<br />' . tep_draw_checkbox_field('use_first', '1', true)];

      $cdg_query = tep_db_query(<<<'EOSQL'
SELECT
  cdg.customer_data_groups_name,
  cdg.cdg_vertical_sort_order,
  cdg.cdg_horizontal_sort_order,
  cdg.customer_data_groups_width,
  l.directory,
  l.image,
  l.name,
  l.languages_id AS id
 FROM customer_data_groups cdg INNER JOIN languages l ON cdg.language_id = l.languages_id
 WHERE customer_data_groups_id = 
EOSQL
        . (int)$cdgInfo->customer_data_groups_id);
      while ($cdg = tep_db_fetch_array($cdg_query)) {
        $contents[] = ['text' => '<br />' . TEXT_INFO_CUSTOMER_DATA_GROUP_NAME . '<br />' . tep_image(tep_catalog_href_link('includes/languages/' . $cdg['directory'] . '/images/' . $cdg['image'], '', 'SSL'), $cdg['name']) . '&nbsp;' . tep_draw_input_field('customer_data_groups_name[' . $cdg['id'] . ']', $cdg['customer_data_groups_name'])];
        $contents[] = ['text' => '<br />' . TEXT_INFO_VERTICAL_SORT_ORDER . '<br />' . tep_draw_input_field('cdg_vertical_sort_order[' . $cdg['id'] . ']', $cdg['cdg_vertical_sort_order'])];
        $contents[] = ['text' => '<br />' . TEXT_INFO_HORIZONTAL_SORT_ORDER . '<br />' . tep_draw_input_field('cdg_horizontal_sort_order[' . $cdg['id'] . ']', $cdg['cdg_horizontal_sort_order'])];
        $contents[] = ['text' => '<br />' . TEXT_INFO_WIDTH . '<br />' . tep_draw_input_field('customer_data_groups_width[' . $cdg['id'] . ']', $cdg['customer_data_groups_width'])];
      }
      $contents[] = ['align' => 'center', 'text' => '<br />' . tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('customer_data_groups.php', 'page=' . $_GET['page'] . '&cdgID=' . $cdgInfo->customer_data_groups_id))];
      break;
    case 'delete':
      $heading[] = ['text' => '<strong>' . TEXT_INFO_HEADING_DELETE_CUSTOMER_DATA_GROUP . '</strong>'];

      $contents = ['form' => tep_draw_form('customer_data_groups', 'customer_data_groups.php', 'page=' . $_GET['page'] . '&cdgID=' . $cdgInfo->customer_data_groups_id . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_INFO_DELETE_INTRO];
      
      $cdg_query = tep_db_query(<<<'EOSQL'
SELECT
  cdg.customer_data_groups_name,
  l.directory,
  l.image,
  l.name
 FROM customer_data_groups cdg INNER JOIN languages l ON cdg.language_id = l.languages_id
 WHERE customer_data_groups_id =
EOSQL
        . (int)$cdgInfo->customer_data_groups_id);
      while ($cdg = tep_db_fetch_array($cdg_query)) {
        $contents[] = ['text' => '<br />' . tep_image(tep_catalog_href_link('includes/languages/' . $cdg['directory'] . '/images/' . $cdg['image'], '', 'SSL'), $cdg['name']) . '&nbsp;<strong>' . $cdg['customer_data_groups_name'] . '</strong>'];
      }
      $contents[] = ['align' => 'center', 'text' => '<br />' . tep_draw_button(IMAGE_DELETE, 'trash', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('customer_data_groups.php', 'page=' . $_GET['page'] . '&cdgID=' . $cdgInfo->customer_data_groups_id))];
      break;
    default:
      if (is_object($cdgInfo)) {
        $heading[] = ['text' => '<strong>' . $cdgInfo->customer_data_groups_name . '</strong>'];

        $contents[] = ['align' => 'center', 'text' => tep_draw_button(IMAGE_EDIT, 'document', tep_href_link('customer_data_groups.php', 'page=' . $_GET['page'] . '&cdgID=' . $cdgInfo->customer_data_groups_id . '&action=edit')) . tep_draw_button(IMAGE_DELETE, 'trash', tep_href_link('customer_data_groups.php', 'page=' . $_GET['page'] . '&cdgID=' . $cdgInfo->customer_data_groups_id . '&action=delete'))];

        $cdg_query = tep_db_query(<<<'EOSQL'
SELECT
  cdg.customer_data_groups_name,
  cdg.cdg_vertical_sort_order,
  cdg.cdg_horizontal_sort_order,
  cdg.customer_data_groups_width,
  l.directory,
  l.image,
  l.name
 FROM customer_data_groups cdg INNER JOIN languages l ON cdg.language_id = l.languages_id
 WHERE customer_data_groups_id =
EOSQL
          . (int)$cdgInfo->customer_data_groups_id);
        while ($cdg = tep_db_fetch_array($cdg_query)) {
          $contents[] = ['text' => '<br />' . TEXT_INFO_CUSTOMER_DATA_GROUP_NAME . '<br />' . tep_image(tep_catalog_href_link('includes/languages/' . $cdg['directory'] . '/images/' . $cdg['image'], '', 'SSL'), $cdg['name']) . '&nbsp;' . $cdg['customer_data_groups_name']];
          $contents[] = ['text' => '<br />' . TEXT_INFO_VERTICAL_SORT_ORDER . ' ' . $cdg['cdg_vertical_sort_order']];
          $contents[] = ['text' => '<br />' . TEXT_INFO_HORIZONTAL_SORT_ORDER . ' ' . $cdg['cdg_horizontal_sort_order']];
          $contents[] = ['text' => '<br />' . TEXT_INFO_WIDTH . ' ' . $cdg['customer_data_groups_width']];
        }
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
