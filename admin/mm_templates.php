<?php
/*
  mm_templates.php 
  mail manager, css-oscommerce.com, 2014
  adapted from admin/newsletters.php,  oscommerce v2.3.4
  $Id$
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2010 osCommerce
  Released under the GNU General Public License
*/

  require('includes/application_top.php');

 $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  	if (tep_not_null($action)) {
    	switch ($action) {
      		case 'insert':
      		case 'update':
        		if (isset($HTTP_POST_VARS['template_id'])) $template_id = tep_db_prepare_input($HTTP_POST_VARS['template_id']);
        		$title = tep_db_prepare_input($HTTP_POST_VARS['title']);
        		$htmlheader = tep_db_prepare_input($HTTP_POST_VARS['htmlheader']);
        		$htmlfooter = tep_db_prepare_input($HTTP_POST_VARS['htmlfooter']);
        		$txtheader = tep_db_prepare_input($HTTP_POST_VARS['txtheader']);
        		$txtfooter = tep_db_prepare_input($HTTP_POST_VARS['txtfooter']);

        		
          			$sql_data_array = array('title' => $title,
          									'htmlheader' => $htmlheader,
                                  			'htmlfooter' => $htmlfooter,
                                  			'txtheader' => $txtheader,
                                 			'txtfooter' => $txtfooter);

          		if ($action == 'insert') {
            		
            
            		tep_db_perform(TABLE_MM_TEMPLATES, $sql_data_array);
            		$template_id = tep_db_insert_id();
          	   } elseif ($action == 'update') {
            		tep_db_perform(TABLE_MM_TEMPLATES, $sql_data_array, 'update', "template_id = '" . (int)$template_id . "'");
          		
          		
          		tep_redirect(tep_href_link(FILENAME_MM_TEMPLATES, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'nID=' . $template_id));
        		} else {
          		$action = 'new';
        		}
        		break;
      		case 'deleteconfirm':
        		$template_id = tep_db_prepare_input($HTTP_GET_VARS['nID']);
        		tep_db_query("delete from " . TABLE_MM_TEMPLATES . " where template_id = '" . (int)$template_id . "'");
        		tep_redirect(tep_href_link(FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page']));
       		 break;
      		case 'delete':
      		case 'new': if (!isset($HTTP_GET_VARS['nID'])) break;
        		$template_id = tep_db_prepare_input($HTTP_GET_VARS['nID']);

        	break;
    		}
  		}

  require(DIR_WS_INCLUDES . 'template_top.php');
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  if ($action == 'new') {
    $form_action = 'insert';

    $parameters = array('title' => '',
                        'content' => '',
                        'txtcontent' => '',
                        'module' => '');

    $nInfo = new objectInfo($parameters);

    if (isset($HTTP_GET_VARS['nID'])) {
      $form_action = 'update';

      $nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);

      $template_query = tep_db_query("select title, htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where template_id = '" . (int)$nID . "'");
      $template = tep_db_fetch_array($template_query);

      $nInfo->objectInfo($template);
    } elseif ($HTTP_POST_VARS) {
      $nInfo->objectInfo($HTTP_POST_VARS);
    }

   
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr><?php echo tep_draw_form('newsletter', FILENAME_MM_TEMPLATES, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'action=' . $form_action); if ($form_action == 'update') echo tep_draw_hidden_field('template_id', $nID); ?>
        <td><table border="0" cellspacing="0" cellpadding="2">
          
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_TEMPLATE_TITLE; ?></td>
            <td class="main"><?php echo tep_draw_input_field('title', $nInfo->title, '', true); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_TEMPLATE_HTMLHEADER; ?></td>
            <td class="main"><?php echo tep_draw_textarea_field('htmlheader', 'soft', '100%', '20', $nInfo->htmlheader); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_TEMPLATE_HTMLFOOTER; ?></td>
            <td class="main"><?php echo tep_draw_textarea_field('htmlfooter', 'soft', '100%', '20', $nInfo->htmlfooter); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_TEMPLATE_TXTHEADER; ?></td>
            <td class="main"><?php echo tep_draw_textarea_field('txtheader', 'soft', '100%', '20', $nInfo->txtheader); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '20', '20'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_TEMPLATE_TXTFOOTER; ?></td>
            <td class="main"><?php echo tep_draw_textarea_field('txtfooter', 'soft', '100%', '20', $nInfo->txtfooter); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td>
        	<table border="0" width="100%" cellspacing="0" cellpadding="2">
          		<tr>
            		<td class="main" align="right"><?php echo (($form_action == 'insert') ? tep_image_submit('button_save.gif', IMAGE_SAVE) : tep_image_submit('button_update.gif', IMAGE_UPDATE)). '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_MM_TEMPLATES, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . (isset($HTTP_GET_VARS['nID']) ? 'nID=' . $HTTP_GET_VARS['nID'] : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
          		</tr>
        	</table>
        </td>
      	</form>
       </tr>
<?php
  } elseif ($action == 'preview') {
    $nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);

    $newsletter_query = tep_db_query("select title, htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where template_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);

    // compile preview mailpiece
	$output_content_html = $newsletter['htmlheader'].'
			<table cellpadding="10" cellspacing="10"><tr><td class="main"><br /><h1>Preview Content</h1> Go to admin/mm_templates.php line about 183 to edit this text.<br />
			This is just demonstation text for the template preview, and serves to show how this template will incorporate your real content. It appears only in the admin 
			preview, and does not appear in the email sent.<br /><br /></td></tr></table>'
			.$newsletter['htmlfooter'];
    
?>
      <tr><td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td></tr>
      <tr><td><?php echo $output_content_html; ?></td></tr>
      <tr><td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td></tr>
<?php
  }elseif ($action == 'confirm') {
    $nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);

    $newsletter_query = tep_db_query("select title, htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where template_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);

    $nInfo = new objectInfo($newsletter);

  }else {
?>
       <tr><td colspan="2" align="right"></td></tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TEMPLATE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_SIZE; ?></td>
                 
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $templates_query_raw = "select template_id, title, htmlfooter, txtheader, txtfooter, length(htmlheader) as content_length from " . TABLE_MM_TEMPLATES . " order by template_id desc";
    $templates_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $templates_query_raw, $templates_query_numrows);
    $templates_query = tep_db_query($templates_query_raw);
    while ($template = tep_db_fetch_array($templates_query)) {
    if ((!isset($HTTP_GET_VARS['nID']) || (isset($HTTP_GET_VARS['nID']) && ($HTTP_GET_VARS['nID'] == $template['template_id']))) && !isset($nInfo) && (substr($action, 0, 3) != 'new')) {
        $nInfo = new objectInfo($template);
      }

      if (isset($nInfo) && is_object($nInfo) && ($template['template_id'] == $nInfo->template_id) ) {
        echo '<tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->template_id . '&action=preview') . '\'">' . "\n";
      } else {
        echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $template['template_id']) . '\'">' . "\n";
      }
?>
                
                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $template['template_id'] . '&action=preview') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $template['title']; ?></td>
                <td class="dataTableContent" align="right"><?php echo number_format($template['content_length']) . ' bytes'; ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($nInfo) && is_object($nInfo) && ($template['template_id'] == $nInfo->template_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $template['template_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="3">
                	<table border="0" width="100%" cellspacing="0" cellpadding="2">
                  	<tr>
                    	<td class="smallText" valign="top"><?php echo $templates_split->display_count($templates_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS); ?></td>
                    	<td class="smallText" align="right"><?php echo $templates_split->display_links($templates_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                  	</tr>
                  	<tr>
                    	<td>
						<?php echo '<a href="' . tep_href_link(FILENAME_MM_MAIL_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?>
						</td>
                        <td align="right">
						<?php echo '<a href="' . tep_href_link(FILENAME_MM_TEMPLATES, 'action=new') . '">' . tep_image_button('button_new_newsletter.gif', IMAGE_NEW_NEWSLETTER) . '</a>'; ?></td>
                  	</tr>
                	</table>
                </td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'delete':
      $heading[] = array('text' => '<b>' . $nInfo->title . '</b>');

      $contents = array('form' => tep_draw_form('newsletters', FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->template_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTROO);
      $contents[] = array('text' => '<br><b>' . $nInfo->title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($nInfo)) {
        $heading[] = array('text' => '<b>' . $nInfo->title . '</b>');

          $contents[] = array('align' => 'center', 'text' => '
          				<a href="' . tep_href_link(FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->template_id . '&action=new') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> 
          				<a href="' . tep_href_link(FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->template_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> 
          				<a href="' . tep_href_link(FILENAME_MM_TEMPLATES, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->template_id . '&action=preview') . '">' . tep_image_button('button_preview.gif', IMAGE_PREVIEW) . '</a>');       
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
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
