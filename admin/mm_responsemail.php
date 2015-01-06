<?php
/*
  mm_responsemail.php 
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
      case 'resetconfirm';
      case 'restoreconfirm';
      			switch($action){
      				case 'resetconfirm';
      					$table_mail = TABLE_MM_RESPONSEMAIL_RESET;
      				break;
      			
      				case 'restoreconfirm';
      					$table_mail = TABLE_MM_RESPONSEMAIL_RESTORE;	
      				break;
      				}
 		  		
      		$mail_id = tep_db_prepare_input($HTTP_GET_VARS['nID']);      		
      		$mail_manager_query = tep_db_query("select title, htmlcontent, txtcontent, template, status from " .$table_mail . " where mail_id = '" . $mail_id . "'");          
            $mail_manager = tep_db_fetch_array($mail_manager_query);          
            $title = $mail_manager['title'];
            $content = $mail_manager['htmlcontent'];
            $txtcontent = $mail_manager['txtcontent'];
            $template = $mail_manager['template'];
            $status = $mail_manager['status'];
            
      		tep_db_query("delete from " . TABLE_MM_RESPONSEMAIL . " where mail_id = '".$HTTP_GET_VARS['nID']."'"); 
            		$sql_data_array = array('mail_id' => $mail_id,
           								'title' => $title,
                                    		'htmlcontent' => $content,
                                    		'txtcontent' => $txtcontent);
            tep_db_perform(TABLE_MM_RESPONSEMAIL, $sql_data_array);
      	                         	    
         break;
              
      case 'insert':
      case 'update':
        	if (isset($HTTP_POST_VARS['mail_id'])) $mail_id = tep_db_prepare_input($HTTP_POST_VARS['mail_id']);
        		$title = tep_db_prepare_input($HTTP_POST_VARS['title']);
        		$content = tep_db_prepare_input($HTTP_POST_VARS['htmlcontent']);
        		$txtcontent = tep_db_prepare_input($HTTP_POST_VARS['txtcontent']);
        		$template = tep_db_prepare_input($HTTP_POST_VARS['template']);
       	
        	if ($mail_error == false) {
          		$sql_data_array = array('title' => $title,
                                  		'htmlcontent' => $content,
                                  		'txtcontent' => $txtcontent,                                 
                                  		'template' =>$template);
                               

          	if ($action == 'insert') {          		                   		
            	$sql_data_array = array('mail_id' => $mail_id,
        								'title' => $title,
                                    	'htmlcontent' => $content,
                                    	'txtcontent' => $txtcontent,
                                    	'template' =>$template);
                                    
            	tep_db_perform(TABLE_MM_RESPONSEMAIL, $sql_data_array);
            	tep_db_perform(TABLE_MM_RESPONSEMAIL_RESET, $sql_data_array);
            	tep_db_perform(TABLE_MM_RESPONSEMAIL_RESTORE, $sql_data_array);
            
          		} elseif ($action == 'update') {           
              		tep_db_perform(TABLE_MM_RESPONSEMAIL, $sql_data_array, 'update', "mail_id = '" . (int)$mail_id . "'");
              		tep_db_perform(TABLE_MM_RESPONSEMAIL_RESTORE, $sql_data_array, 'update', "mail_id = '" . (int)$mail_id . "'");
          			}

          		tep_redirect(tep_href_link(FILENAME_MM_RESPONSEMAIL, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'nID=' . $mail_id));
          		} else {
           			$action = 'new';       
         			}
      break;           
    }
  }
  
  require(DIR_WS_INCLUDES . 'template_top.php');
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            		<td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          		</tr>
        	</table>
		</td>
	</tr>
<?php
	//uncommment 'new newsletter' button below to activate
  if ($action == 'new') {
        	
    $form_action = 'insert';
    $parameters = array('title' => $title,
                        'htmlcontent' => $htmlcontent,
                        'txtcontent' => $txtcontent,
                        'template' =>$template);
                 
    $nInfo = new objectInfo($parameters);

    	if (isset($HTTP_GET_VARS['nID'])) {
      		$form_action = 'update';
      		$nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);
      		$mail_query = tep_db_query("select title, htmlcontent, txtcontent, placeholders, template, status from " . TABLE_MM_RESPONSEMAIL . " where mail_id = '" . (int)$nID . "'");
      		$mail = tep_db_fetch_array($mail_query);
      		$placeholders = $mail['placeholders'];
      		$template_title=$mail['template'];
      		$nInfo->objectInfo($mail);

    		}elseif ($HTTP_POST_VARS) {
      			$nInfo->objectInfo($HTTP_POST_VARS);
    			}

?>

	<tr>
		<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
     <tr>
     	<td><?php echo tep_draw_form('mail', FILENAME_MM_RESPONSEMAIL, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'action=' . $form_action); if ($form_action == 'update') echo tep_draw_hidden_field('mail_id', $nID); ?>       
        	<table border="0" cellspacing="0" cellpadding="2">
        	<?php       	
        	 	$newmailid = '<tr><td class="main">'.TEXT_NEWMAIL_WARNING.'</td></tr>
        	 				  <tr><td class="main"><p>'.TEXT_MAIL_MAIL_ID.'</p>'.tep_draw_input_field('mail_id', $nInfo->mail_id, '', true).'</td></tr>';
   					if($nID==null){   				
        	 			echo $newmailid;
        	 		}else{
   						//displays list of placeholders in admin edit screen
   						echo $placeholders;  
        	 			}       	 		          	         	         	 
        		?>
          			<tr><td ><?php echo tep_draw_separator('pixel_trans.gif', '20', '20'); ?></td></tr>
          			<tr>
          			  <td class="main" >
          					<p><?php echo TEXT_MAIL_TITLE; ?></p><?php echo tep_draw_input_field('title', $nInfo->title, '', true); ?>
          			  </td>
          		
          		   
          		
          				<?php
          				//attach template          	 			
  						$template_array = array();
  						$template_array[0]= array('id'=>$template_title, 'text'=>$template_title);
  						$template_query = tep_db_query("select template_id, title from " . TABLE_MM_TEMPLATES . " ");
  						while ($template_values = tep_db_fetch_array($template_query)) {
    					$template_array[] = array('id' => $template_values['title'], 
    											  'text' => $template_values['title']);
    								}
    				   ?>    	
				
					  <td class="main" valign="top" align="left">
						<p><?php echo TEXT_TEMPLATE_TITLE; ?></p>
						<?php echo tep_draw_pull_down_menu('template', $template_array, '', true); ?>
					  </td>						
          		</tr>
          		<tr><td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '20', '20'); ?></td></tr>
          		<tr>
          			<td class="main" valign="top" colspan="2"><p><?php echo TEXT_MAIL_CONTENT; ?></p><?php echo tep_draw_textarea_field('htmlcontent', 'soft', '100%', '20', $nInfo->htmlcontent); ?>
          			</td>
          		</tr>
          		
          		<tr><td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '20', '20'); ?></td></tr>
          		<tr>
          			<td class="main" valign="top" colspan="2"><p><?php echo TEXT_MAIL_TXTCONTENT; ?></p><?php echo tep_draw_textarea_field('txtcontent', 'soft', '100%', '20', $nInfo->txtcontent); ?>
          			</td>
          		</tr>
			</table>
         </td>
      </tr>
      <tr><td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
      
      <tr>
        <td>
        	<table border="0" width="100%" cellspacing="0" cellpadding="2">
          		<tr>
            		<td class="main" align="right">
            		<?php echo (($form_action == 'insert') ? tep_image_submit('button_save.gif', IMAGE_SAVE) : tep_image_submit('button_update.gif', IMAGE_UPDATE)). '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . (isset($HTTP_GET_VARS['nID']) ? 'nID=' . $HTTP_GET_VARS['nID'] : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?>
            		</td>
          		</tr>
        	</table>
        </td>
      	</form>
      </tr>
<?php
  }elseif ($action == 'setflag') {
  if ( ($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1') ) {
          if (isset($HTTP_GET_VARS['nID'])) {
            tep_mm_set_mailstatus($HTTP_GET_VARS['nID'], $HTTP_GET_VARS['flag']);
          }

          if (USE_CACHE == 'true') {
            tep_reset_cache_block('topics');
          }
        }
          
            	if($HTTP_GET_VARS['flag']=='1'){
            	$newstatus='Active';
            	}else{
            	$newstatus='Inactive';
            	}
            	            
            echo '<tr><td class="main">Status changed to: <strong>'.$newstatus.'</strong><a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' .$HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a></td></tr>';    	
          
        
  }elseif ($action == 'preview') {
  	$nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);
    $mail_query = tep_db_query("select title, htmlcontent, txtcontent, template from " . TABLE_MM_RESPONSEMAIL . " where mail_id = '" . (int)$nID . "'");
    $mail = tep_db_fetch_array($mail_query);
    $template_name = $mail['template'];
    
    $template_query = tep_db_query("select title, htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '" . $template_name . "'");
    $template = tep_db_fetch_array($template_query);
    
    $output_subject = $mail[title];
    $output_content_html = $template['htmlheader'].$mail['htmlcontent'].$template['htmlfooter'];
    $output_content_txt = $template['txtheader'].$mail['txtcontent'].$template['txtfooter'];  
?>
      <tr><td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td></tr>
      <tr><td><tt><?php echo $output_content_html; ?></tt></td></tr>
      <tr><td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td></tr>

<?php

  }elseif ($action == 'test') { 
    $nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);
    
    $mail_query = tep_db_query("select title, htmlcontent, txtcontent, template from " . TABLE_MM_RESPONSEMAIL . " where mail_id = '" . (int)$nID . "'");
    $mail = tep_db_fetch_array($mail_query);
    $template_name = $mail['template'];
    
    $template_query = tep_db_query("select title, htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '" . $template_name . "'");
    $template = tep_db_fetch_array($template_query);
    
    $output_subject = $mail[title];
    $output_content_html = $template['htmlheader'].$mail['htmlcontent'].$template['htmlfooter'];
    $output_content_txt = $template['txtheader'].$mail['txtcontent'].$template['txtfooter'];    
    
   	$placeholders=array('$storeurl', '$storeowner','$storeemail');
   	$values=array(HTTP_CATALOG_SERVER,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS);
   	$output_content_html=str_replace($placeholders, $values, $output_content_html);
       
	tep_mm_sendmail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $output_subject, $output_content_html, $output_content_txt);
	?>
		<tr>
			<td><em><?php echo $mail['title'].'</em> sent to '.STORE_OWNER_EMAIL_ADDRESS; ?></td></tr>
    	<tr>
    		<td><?php echo '<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK); ?></a></td></tr>

	<?php 
   }else{
	?>
      	<tr>
        	<td>
        		<table border="0" width="100%" cellspacing="0" cellpadding="0">
          			    				
                    <tr>
            			<td valign="top">
            				<table border="0" width="100%" cellspacing="0" cellpadding="2">
             					<tr class="dataTableHeadingRow">
                					<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MAIL; ?></td>
                					<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ID; ?></td>
                					<!--<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_SIZE; ?></td>-->
                					<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TEMPLATE; ?></td>
                					<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                					<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              					</tr>

<?php
    $mail_query_raw = "select mail_id, title, status, template, length(htmlcontent) as content_length from " . TABLE_MM_RESPONSEMAIL . " order by mail_id desc";
    $mail_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $mail_query_raw, $mail_query_numrows);
    $mail_query = tep_db_query($mail_query_raw);
    while ($mail = tep_db_fetch_array($mail_query)) {
    if ((!isset($HTTP_GET_VARS['nID']) || (isset($HTTP_GET_VARS['nID']) && ($HTTP_GET_VARS['nID'] == $mail['mail_id']))) && !isset($nInfo) && (substr($action, 0, 3) != 'new')) {
        $nInfo = new objectInfo($mail);
      }

      if (isset($nInfo) && is_object($nInfo) && ($mail['mail_id'] == $nInfo->mail_id) ) {
       echo '<tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->mail_id . '&action=preview') . '\'">' . "\n";      
      } else {
        echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $mail['mail_id']) . '\'">' . "\n";
      }
							?>             
                					<td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $mail['mail_id'] . '&action=preview') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $mail['title']; ?></td>
                					<td class="dataTableContent" align="right"><?php echo $mail['mail_id']; ?></td>
                					<!--<td class="dataTableContent" align="right"><?php echo number_format($mail['htmlcontent_length']) . ' bytes'; ?></td>  -->             			
                					<td class="dataTableContent" align="right"><?php echo $mail['template']; ?></td>
                					<td class="dataTableContent" align="right">
                						<?php 
                							if ($mail['status'] == '1') {
        									echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'action=setflag&flag=0&nID=' . $mail['mail_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      										} else {
       								 		echo '<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'action=setflag&flag=1&nID=' . $mail['mail_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
     									 		}               					               			
       						?>
       								 		</td>                			
                					<td class="dataTableContent" align="right"><?php if (isset($nInfo) && is_object($nInfo) && ($mail['mail_id'] == $nInfo->mail_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $mail['mail_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              					</tr>
	<?php
    	}
	?>
              					<tr>
                					<td colspan="6">
                  						<table border="0" width="100%" cellspacing="0" cellpadding="2">
                  							<tr>
                    	  						<td class="smallText" valign="top"><?php echo $mail_split->display_count($mail_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS); ?></td>
                    	  						<td class="smallText" align="right"><?php echo $mail_split->display_links($mail_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                  							</tr>
                  							<tr>
                    							<td>
                    							<?php echo '<a href="' . tep_href_link(FILENAME_MM_MAIL_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?> 
                    							</td>
                                                <td>
												
                    							</td>
                  							</tr>
                						</table>
                					</td>
              					</tr>
            				</table>
            			</td>
<?php
  $heading = array();
  $contents = array();
switch ($action) {
    case 'restore':
    case 'reset':
    		switch($action){
    			case 'restore':
    				$confirm_action = 'restoreconfirm';
    				$text_confirm = TEXT_CONFIRM_RESTORE;
    			break;
    			case 'reset';
    	     		$confirm_action = 'resetconfirm';
    	     		$text_confirm = TEXT_CONFIRM_RESET;
    			break;
    			}   	
      $heading[] = array('text' => '<b>' . $nInfo->title . '</b>');
      $contents[] = array('text' => $text_confirm);
      $contents[] = array('text' => '<br><b>' . $nInfo->title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->mail_id . '&action='.$confirm_action) . '">' . tep_image_button('button_confirm.gif', 'confirm') . '</a>
      								<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
    break;
    default:
      if (is_object($nInfo)) {
        $heading[] = array('text' => '<b>' . $nInfo->title . '</b>');  
          $contents[] = array('align' => 'center', 'text' => '
          				<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->mail_id . '&action=new') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> 
          				<a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->mail_id . '&action=preview') . '">' . tep_image_button('button_preview.gif', IMAGE_PREVIEW) . '</a>           							       				
          				<table><tr><td class="main"><a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->mail_id . '&action=restore') . '">' . tep_image_button('button_restore.gif', 'restore') . '</a>           				          				         				
          				</td></tr><tr><td class="main">'.TEXT_BACKUP_MESSAGE.'
          				</td></tr></table><table><tr><td class="main"><a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->mail_id . '&action=reset') . '">' . tep_image_button('button_reset.gif', 'reset') . '</a>           				          				
          				</td></tr><tr><td class="main">'.TEXT_RESET_MESSAGE.'
          				</td></tr></table><table><tr><td class="main"><a href="' . tep_href_link(FILENAME_MM_RESPONSEMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->mail_id . '&action=test') . '">' . tep_image_button('button_send.gif', 'test') . '</a>
          				</td></tr><tr><td class="main">'.TEXT_TEST_MESSAGE.'</td></tr></table>
          				');
   break;       
 }
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
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
