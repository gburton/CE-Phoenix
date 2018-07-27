<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'setflag':
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if (isset($_GET['tID'])) {
            tep_db_query("update testimonials set testimonials_status = '" . (int)$_GET['flag'] . "' where testimonials_id = '" . (int)$_GET['tID'] . "'");
          }
        }

        tep_redirect(tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $_GET['tID']));
        break;
      case 'update':
        $customers_id = (int)$_POST['customers_id'];
        $customers_name = tep_db_prepare_input($_POST['customer_name']);
        $testimonials_id = tep_db_prepare_input($_GET['tID']);
        $testimonials_text = tep_db_prepare_input($_POST['testimonials_text']);
        $testimonials_status = tep_db_prepare_input($_POST['testimonials_status']);

        tep_db_query("update testimonials set customers_id = '" . (int)$customers_id . "', customers_name  = '" . tep_db_input($customers_name) . "', testimonials_status = '" . tep_db_input($testimonials_status) . "', last_modified = now() where testimonials_id = '" . (int)$testimonials_id . "'");
        tep_db_query("update testimonials_description set testimonials_text = '" . tep_db_input($testimonials_text) . "' where testimonials_id = '" . (int)$testimonials_id . "'");

        tep_redirect(tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $testimonials_id));
        break;
      case 'deleteconfirm':
        $testimonials_id = tep_db_prepare_input($_GET['tID']);

        tep_db_query("delete from testimonials where testimonials_id = '" . (int)$testimonials_id . "'");
        tep_db_query("delete from testimonials_description where testimonials_id = '" . (int)$testimonials_id . "'");

        tep_redirect(tep_href_link('testimonials.php', 'page=' . $_GET['page']));
        break;
        
      case 'addnew':
        $customers_id = (int)$_POST['customers_id'];
        $customers_name = tep_db_prepare_input($_POST['customer_name']);
        $testimonial = tep_db_prepare_input($_POST['testimonials_text']);

        tep_db_query("insert into testimonials (customers_id, customers_name, date_added, testimonials_status) values ('" . $customers_id . "', '" . tep_db_input($customers_name) . "', now(), 1)");
        $insert_id = tep_db_insert_id();
        tep_db_query("insert into testimonials_description (testimonials_id, languages_id, testimonials_text) values ('" . (int)$insert_id . "', '" . (int)$languages_id . "', '" . tep_db_input($testimonial) . "')");

        tep_redirect(tep_href_link('testimonials.php', tep_get_all_get_params(array('action'))));
        break;
    }
  }

  require('includes/template_top.php');
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  if ($action == 'edit') {
    $tID = tep_db_prepare_input($_GET['tID']);

    $testimonials_query = tep_db_query("select t.testimonials_id, t.customers_id, t.customers_name, t.date_added, t.last_modified, td.testimonials_text, t.testimonials_status from testimonials t, testimonials_description td where t.testimonials_id = '" . (int)$tID . "' and t.testimonials_id = td.testimonials_id");
    $testimonials = tep_db_fetch_array($testimonials_query);

    $tInfo = new objectInfo($testimonials);

    if (!isset($tInfo->testimonials_status)) $tInfo->testimonials_status = '1';
    switch ($tInfo->testimonials_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }
?>
      <tr><?php echo tep_draw_form('testimonial', 'testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $_GET['tID'] . '&action=update'); ?>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main"><strong><?php echo TEXT_INFO_TESTIMONIAL_STATUS; ?></strong></td>
            <td><?php echo tep_draw_radio_field('testimonials_status', '1', $in_status) . '&nbsp;' . TEXT_TESTIMONIAL_PUBLISHED . '&nbsp;' . tep_draw_radio_field('testimonials_status', '0', $out_status) . '&nbsp;' . TEXT_TESTIMONIAL_NOT_PUBLISHED; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top" width="140"><strong><?php echo ENTRY_FROM; ?></strong></td>
            <td><?php echo tep_draw_customers('customers_id', '', $tInfo->customers_id); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top" width="140"><strong><?php echo ENTRY_FROM_NICKNAME; ?></strong></td>
            <td><?php echo tep_draw_input_field('customer_name', $tInfo->customers_name, 'required aria-required="true" style="font-size:10px; width: 240px;"'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" colspan="2"><strong><?php echo ENTRY_TESTIMONIAL; ?></strong></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_textarea_field('testimonials_text', 'soft', '60', '15', $tInfo->testimonials_text); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>      
      <tr>
        <td align="right" class="smallText"><?php echo tep_draw_hidden_field('testimonials_id', $tInfo->testimonials_id) . tep_draw_hidden_field('customers_name', $tInfo->customers_name) . tep_draw_hidden_field('date_added', $tInfo->date_added) . tep_draw_button(IMAGE_SAVE, 'document') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $_GET['tID'])); ?></td>
      </form></tr>
<?php
  } elseif ($action == 'new') {
    ?>
      <tr><?php echo tep_draw_form('review', 'testimonials.php', 'action=addnew'); ?>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" valign="top" width="140"><strong><?php echo ENTRY_FROM; ?></strong></td>
            <td><?php echo tep_draw_customers('customers_id'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top" width="140"><strong><?php echo ENTRY_FROM_NICKNAME; ?></strong></td>
            <td><?php echo tep_draw_input_field('customer_name', '', 'required aria-required="true" style="font-size:10px; width: 240px;"'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" colspan="2" valign="top"><strong><?php echo ENTRY_TESTIMONIAL; ?></strong><br /><br /><?php echo tep_draw_textarea_field('testimonials_text', 'soft', '60', '15', '', 'required aria-required="true"'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary'); ?></td>
          </tr>      
        </table></td>
      </tr>  
      </form>
       <?php
     } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMER_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMER_NAME; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $testimonials_query_raw = "select testimonials_id, customers_id, customers_name, date_added, last_modified, testimonials_status from testimonials order by testimonials_id DESC";
    $testimonials_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $testimonials_query_raw, $testimonials_query_numrows);
    $testimonials_query = tep_db_query($testimonials_query_raw);
    while ($testimonials = tep_db_fetch_array($testimonials_query)) {
      if ((!isset($_GET['tID']) || (isset($_GET['tID']) && ($_GET['tID'] == $testimonials['testimonials_id']))) && !isset($tInfo)) {
        $testimonials_text_query = tep_db_query("select t.customers_name, length(td.testimonials_text) as testimonials_text_size from testimonials t, testimonials_description td where t.testimonials_id = '" . (int)$testimonials['testimonials_id'] . "' and t.testimonials_id = td.testimonials_id");
        $testimonials_text = tep_db_fetch_array($testimonials_text_query);

        $tInfo_array = array_merge($testimonials, $testimonials_text);
        $tInfo = new objectInfo($tInfo_array);
      }

      if (isset($tInfo) && is_object($tInfo) && ($testimonials['testimonials_id'] == $tInfo->testimonials_id) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $tInfo->testimonials_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $testimonials['testimonials_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo (int)$testimonials['customers_id']; ?></td>
                <td class="dataTableContent"><?php echo $testimonials['customers_name']; ?></td>
                <td class="dataTableContent" align="right"><?php echo tep_date_short($testimonials['date_added']); ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($testimonials['testimonials_status'] == '1') {
        echo tep_image('images/icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link('testimonials.php', 'action=setflag&flag=0&tID=' . $testimonials['testimonials_id'] . '&page=' . $_GET['page']) . '">' . tep_image('images/icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link('testimonials.php', 'action=setflag&flag=1&tID=' . $testimonials['testimonials_id'] . '&page=' . $_GET['page']) . '">' . tep_image('images/icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image('images/icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($tInfo)) && ($testimonials['testimonials_id'] == $tInfo->testimonials_id) ) { echo tep_image('images/icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $testimonials['testimonials_id']) . '">' . tep_image('images/icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $testimonials_split->display_count($testimonials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TESTIMONIALS); ?></td>
                    <td class="smallText" align="right"><?php echo $testimonials_split->display_links($testimonials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
                <tr>
                  <td colspan="5"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>                
                <td colspan="5" class="smallText" align="right"><?php echo tep_draw_button(IMAGE_BUTTON_ADD_TESTIMONIAL, 'triangle-1-e', tep_href_link('testimonials.php', 'action=new')); ?></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();

    switch ($action) {
      case 'delete':
        $heading[] = array('text' => '<strong>' . TEXT_INFO_HEADING_DELETE_TESTIMONIAL . '</strong>');

        $contents = array('form' => tep_draw_form('testimonials', 'testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $tInfo->testimonials_id . '&action=deleteconfirm'));
        $contents[] = array('text' => TEXT_INFO_DELETE_TESTIMONIAL_INTRO);
        $contents[] = array('align' => 'center', 'text' => '<br />' . tep_draw_button(IMAGE_DELETE, 'trash', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $tInfo->testimonials_id)));
        break;
      default:
      if (isset($tInfo) && is_object($tInfo)) {
        $heading[] = array('text' => '<strong>' . $tInfo->customers_name . '</strong>');

        $contents[] = array('align' => 'center', 'text' => tep_draw_button(IMAGE_EDIT, 'document', tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $tInfo->testimonials_id . '&action=edit')) . tep_draw_button(IMAGE_DELETE, 'trash', tep_href_link('testimonials.php', 'page=' . $_GET['page'] . '&tID=' . $tInfo->testimonials_id . '&action=delete')));
        $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($tInfo->date_added));
        if (tep_not_null($tInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($tInfo->last_modified));
        $contents[] = array('text' => '<br />' . TEXT_INFO_TESTIMONIAL_AUTHOR . ' ' . $tInfo->customers_name);
        $contents[] = array('text' => '<br />' . TEXT_INFO_TESTIMONIAL_SIZE . ' ' . $tInfo->testimonials_text_size . ' bytes');
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
<?php
  }
?>
    </table>

<?php
  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
