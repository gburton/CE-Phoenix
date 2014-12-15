<?php
/*
  mm_bulkmail.php 
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
      case 'lock':
      case 'unlock':
        $newsletter_id = tep_db_prepare_input($HTTP_GET_VARS['nID']);
        $status = (($action == 'lock') ? '1' : '0');

        tep_db_query("update " . TABLE_MM_NEWSLETTERS . " set locked = '" . $status . "' where newsletters_id = '" . (int)$newsletter_id . "'");

        tep_redirect(tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']));
        break;
      case 'insert':
      case 'update':
        if (isset($HTTP_POST_VARS['newsletter_id'])) $newsletter_id = tep_db_prepare_input($HTTP_POST_VARS['newsletter_id']);
        $newsletter_module = tep_db_prepare_input($HTTP_POST_VARS['module']);
        $mailrate = tep_db_prepare_input($HTTP_POST_VARS['mailrate']);
        $txtcontent = tep_db_prepare_input($HTTP_POST_VARS['txtcontent']);
        $title = tep_db_prepare_input($HTTP_POST_VARS['title']);
        $subject = tep_db_prepare_input($HTTP_POST_VARS['subject']);
        $content = tep_db_prepare_input($HTTP_POST_VARS['content']);
        $template = tep_db_prepare_input($HTTP_POST_VARS['template']);
        
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
          $sql_data_array = array('title' => $title,
          						  'subject' => $subject,
                                  'content' => $content,
                                  'txtcontent' => $txtcontent,
                                  'template' => $template,
                                  'module' => $newsletter_module,
                                  'mailrate' => $mailrate );

          if ($action == 'insert') {
            $sql_data_array['date_added'] = 'now()';
            $sql_data_array['status'] = '0';
            $sql_data_array['locked'] = '0';

            tep_db_perform(TABLE_MM_NEWSLETTERS, $sql_data_array);
            $newsletter_id = tep_db_insert_id();
          } elseif ($action == 'update') {
            tep_db_perform(TABLE_MM_NEWSLETTERS, $sql_data_array, 'update', "newsletters_id = '" . (int)$newsletter_id . "'");
          }

          tep_redirect(tep_href_link(FILENAME_MM_BULKMAIL, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'nID=' . $newsletter_id));
        } else {
          $action = 'new';
        }
        break;
      case 'deleteconfirm':
        $newsletter_id = tep_db_prepare_input($HTTP_GET_VARS['nID']);

        tep_db_query("delete from " . TABLE_MM_NEWSLETTERS . " where newsletters_id = '" . (int)$newsletter_id . "'");

        tep_redirect(tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page']));
        break;
      case 'delete':
      case 'new': if (!isset($HTTP_GET_VARS['nID'])) break;
      case 'send':
      case 'test':
	  //Added new in 2013-07-13 version by toniroger
	  case 'confirm':
      case 'confirm_send':
        $newsletter_id = tep_db_prepare_input($HTTP_GET_VARS['nID']);

        $check_query = tep_db_query("select locked from " . TABLE_MM_NEWSLETTERS . " where newsletters_id = '" . (int)$newsletter_id . "'");
        $check = tep_db_fetch_array($check_query);

        if ($check['locked'] < 1) {
          switch ($action) {
            case 'delete': $error = ERROR_REMOVE_UNLOCKED_NEWSLETTER; break;
            case 'new': $error = ERROR_EDIT_UNLOCKED_NEWSLETTER; break;
            case 'send': $error = ERROR_SEND_UNLOCKED_NEWSLETTER; break;
            case 'confirm_send': $error = ERROR_SEND_UNLOCKED_NEWSLETTER; break;
          }

          $messageStack->add_session($error, 'error');

          tep_redirect(tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']));
        }
        break;
    }
  }

  require(DIR_WS_INCLUDES . 'template_top.php');
?>

   <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
        	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          		<tr>
            		<td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            		<td class="pageHeading" align="right">			
						<?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          		</tr>
        	</table>
          </td>
      </tr>
<?php
  if ($action == 'new') {
    $form_action = 'insert';

	$parameters = array('title' => $title,
                        'content' => '',
                        'txtcontent' => '',
                        'template' =>'',
                        'module' => '',
                        'mailrate' => '' );

	$nInfo = new objectInfo($parameters);

    if (isset($HTTP_GET_VARS['nID'])) {
      $form_action = 'update';
      $nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);
      $newsletter_query = tep_db_query("select title, subject, content, txtcontent, template, module, mailrate from " . TABLE_MM_NEWSLETTERS . " where newsletters_id = '" . (int)$nID . "'");
      $newsletter = tep_db_fetch_array($newsletter_query);
      $nInfo->objectInfo($newsletter);
    } elseif ($HTTP_POST_VARS) {
      $nInfo->objectInfo($HTTP_POST_VARS);
    }

    $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
    $directory_array = array();
    if ($dir = dir(DIR_WS_MODULES . 'mail_manager/')) {
      while ($file = $dir->read()) {
        if (!is_dir(DIR_WS_MODULES . 'mail_manager/' . $file)) {
          if (substr($file, strrpos($file, '.')) == $file_extension) {
            $directory_array[] = $file;
          }
        }
      }
      sort($directory_array);
      $dir->close();
    }

    for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
      $modules_array[] = array('id' => substr($directory_array[$i], 0, strrpos($directory_array[$i], '.')), 'text' => substr($directory_array[$i], 0, strrpos($directory_array[$i], '.')));
    }
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr><?php echo tep_draw_form('newsletter', FILENAME_MM_BULKMAIL, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . 'action=' . $form_action); if ($form_action == 'update') echo tep_draw_hidden_field('newsletter_id', $nID); ?>
        <td>
        	<table border="0" cellspacing="0" cellpadding="2">
          		<tr>
            		<td class="main"><?php echo TEXT_NEWSLETTER_MODULE; ?></td>
            		<td class="main"><?php echo tep_draw_pull_down_menu('module', $modules_array, $nInfo->module). NOTES_TARGET; ?></td>
          		</tr>
          
          		<tr>
                	<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
          		<tr>
            <td class="main"><?php echo TEXT_NEWSLETTER_TITLE; ?></td>
            <td class="main"><?php echo tep_draw_input_field('title', $nInfo->title, '', ''). NOTES_TITLE; ?></td> 
          </tr>
          <tr><td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
           <tr>
            <td class="main"><?php echo TEXT_NEWSLETTER_SUBJECT; ?></td>
            <td class="main"><?php echo tep_draw_input_field('subject', $nInfo->subject, '', '').NOTES_SUBJECT; ?></td> 
          </tr>
          <tr><td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
          <tr>
            <td class="main"><?php echo TEXT_MAILRATE; ?></td>
            <td class="main"><?php echo tep_draw_input_field('mailrate', $nInfo->mailrate, 'size="2" maxlength="2"', '').NOTES_MAILRATE; ?></td> 
          </tr>
          
          <tr><td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
          <tr><td class="main"><?php echo TEXT_TEMPLATE_TITLE; ?></td>	
              
          		
          				<?php
          			//attach template 
          					
          				// template if exists
          				$template_title = $newsletter['template'];
          				        	 			
  						$template_array = array();
  						$template_array[0]= array('id'=>$template_title, 'text'=>$template_title);
  						$template_query = tep_db_query("select template_id, title from " . TABLE_MM_TEMPLATES . " ");
  						while ($template_values = tep_db_fetch_array($template_query)) {
    					$template_array[] = array('id' => $template_values['title'], 
    											  'text' => $template_values['title']);
    								}
    				   ?>    	
				
					<td class="main" valign="top"><?php echo tep_draw_pull_down_menu('template', $template_array, '', true); ?></td>						
           </tr>
           <tr><td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_NEWSLETTER_CONTENT; ?></td>
            <td class="main"><?php echo tep_draw_textarea_field('content', 'soft', '100%', '20', $nInfo->content); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '20', '20'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_NEWSLETTER_TXTCONTENT; ?></td>
            <td class="main"><?php echo tep_draw_textarea_field('txtcontent', 'soft', '100%', '20', $nInfo->txtcontent); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" align="right"><?php echo (($form_action == 'insert') ? tep_image_submit('button_save.gif', IMAGE_SAVE) : tep_image_submit('button_update.gif', IMAGE_UPDATE)). '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, (isset($HTTP_GET_VARS['page']) ? 'page=' . $HTTP_GET_VARS['page'] . '&' : '') . (isset($HTTP_GET_VARS['nID']) ? 'nID=' . $HTTP_GET_VARS['nID'] : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
          </tr>
        </table></td>
      </form></tr>
<?php
  } elseif ($action == 'preview') {
    //assemble mailpiece
    $nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);
    
    $newsletter_query = tep_db_query("select title, subject, content, txtcontent, template, module, mailrate from " . TABLE_MM_NEWSLETTERS . " where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);
    $template_name = $newsletter['template'];   

    // retrieve template   	
	$template_query = tep_db_query("select title, htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '" . $template_name . "'");
    $template = tep_db_fetch_array($template_query);
    
	// compile view mailpiece
	$output_html = $template['htmlheader'].$newsletter['content'].$template['htmlfooter'];
          
?>
      <tr>
        <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>      
      <tr><td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
       <tr><td class="main"><?php echo TEXT_PREVIEW; ?></td></tr>
       <tr>
        <td><tt><?php echo $output_html;?></tt></td>
      </tr>
      <tr><td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
      <tr> 
      	<td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
  } elseif ($action == 'test') {  
  	//assemble mailpiece
    $nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);    
    //retrieve content and template name
	$newsletter_query = tep_db_query("select title, subject, content, txtcontent, template, module, mailrate from " . TABLE_MM_NEWSLETTERS . " where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);
    $template_name = $newsletter['template'];
    
    // retrieve template   	
	$template_query = tep_db_query("select title, htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '" . $template_name . "'");
    $template = tep_db_fetch_array($template_query);
	
	// compile mailpiece
	//additional_htmlcontent (and additional_txtcontent) adds optional content from module files located in admin/includes/mail_manager, if that module adds content. Otherwise this variable is ignored.  
	$output_content_html= $template['htmlheader'].$newsletter['content'].$template['htmlfooter'];	
	$output_content_txt= $template['txtheader'].$newsletter['txtcontent'].$template['txtfooter'];	
    
    // placeholders 
    $placeholders=array('storeurl', 'storeowner','storeemail');
    $values=array(HTTP_CATALOG_SERVER,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS);
    $output_content_html=str_replace($placeholders, $values, $output_content_html);
    $output_content_txt=str_replace($placeholders, $values, $output_content_txt);
        
	tep_mm_sendmail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $newsletter['subject'], $output_content_html, $output_content_txt);
		
	echo '<tr><td><em>'.$newsletter['title'].'</em> sent to '.STORE_OWNER_EMAIL_ADDRESS.'</td></tr>';
    echo '<tr><td><a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a></td></tr>';
////////////////////////////////////////////////SEND      SEND            SEND  
  } elseif ($action == 'send') {  
  		
  		//assemble mailpiece  
    $nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);
    
    $newsletter_query = tep_db_query("select  module from " . TABLE_MM_NEWSLETTERS . " where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);
    include(DIR_WS_MODULES . 'mail_manager/' . $newsletter['module'] . substr($PHP_SELF, strrpos($PHP_SELF, '.')));   
        
    //retrieve content and template name
	$newsletter_query = tep_db_query("select title, subject, content, txtcontent, template, module, mailrate from " . TABLE_MM_NEWSLETTERS . " where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);
    $template_name = $newsletter['template'];   
    //additional_htmlcontent (and additional_txtcontent) adds optional content from module files located in admin/includes/mail_manager, if that module adds content. Otherwise this variable is ignored.
    $htmlcontent = $newsletter['content'].$additional_htmlcontent;
    $txtcontent = $newsletter['txtcontent'].$additional_txtcontent;
    $output_subject = $newsletter['subject'];

    // retrieve template   	
	$template_query = tep_db_query("select title, htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '" . $template_name . "'");
    $template = tep_db_fetch_array($template_query);    
	
	// compile mailpiece
	    //additional_htmlcontent (and additional_txtcontent) adds optional content from module files located in admin/includes/mail_manager, if that module adds content. Otherwise this variable is ignored.
	$output_content_html= $template['htmlheader'].$newsletter['content'].$template['htmlfooter'];	
	$output_content_txt= $template['txtheader'].$newsletter['txtcontent'].$template['txtfooter'];	
	
	//Added the if clause in 2013-07-13 version by toniroger
	if ($newsletter['module'] != 'product_notification') {	
	  echo '<tr><td><b>' . TEXT_NEWSLETTER_TITLE.' '.$newsletter['module'].'<br /> '.sprintf(TEXT_COUNT_CUSTOMERS, $count['count']) . '</b></td></tr>
 	      <tr><td><a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID'] . '&action=confirm_send') . '">' . tep_image_button('button_send.gif', IMAGE_SEND) . '</a> <a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a></td></tr>
 	      <tr><td class="main">'.TEXT_PREVIEW.'</td></tr><tr><td class="main"><br />Subject: '.$output_subject.'</td></tr><tr><td>' .$output_content_html . '<br /></td></tr>';
	}
 	   
  } elseif ($action == 'confirm') {
	  
    $nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);

    $newsletter_query = tep_db_query("select title, content, txtcontent, module, mailrate from " . TABLE_MM_NEWSLETTERS . " where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);

    $nInfo = new objectInfo($newsletter);
	
	//Changhed in 2013-07-13 version by toniroger
	//retrieve content and template name
	$newsletter_query = tep_db_query("select title, subject, content, txtcontent, template, module, mailrate from " . TABLE_MM_NEWSLETTERS . " where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);
    $template_name = $newsletter['template'];   
    //additional_htmlcontent (and additional_txtcontent) adds optional content from module files located in admin/includes/mail_manager, if that module adds content. Otherwise this variable is ignored.
    $htmlcontent = $newsletter['content'].$additional_htmlcontent;
    $txtcontent = $newsletter['txtcontent'].$additional_txtcontent;
    $output_subject = $newsletter['subject'];

    // retrieve template   	
	$template_query = tep_db_query("select title, htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '" . $template_name . "'");
    $template = tep_db_fetch_array($template_query);    
	
	// compile mailpiece
	    //additional_htmlcontent (and additional_txtcontent) adds optional content from module files located in admin/includes/mail_manager, if that module adds content. Otherwise this variable is ignored.
	$output_content_html= $template['htmlheader'].$newsletter['content'].$template['htmlfooter'];	
	$output_content_txt= $template['txtheader'].$newsletter['txtcontent'].$template['txtfooter'];	
	
	//   include(DIR_WS_LANGUAGES . $language . '/modules/mail_manager/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    include(DIR_WS_MODULES . 'mail_manager/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
	
	if (($newsletter['module'] == 'product_notification') && isset($HTTP_GET_VARS['global']) && ($HTTP_GET_VARS['global'] == 'true')) {
		echo '<tr><td><b>' . TEXT_NEWSLETTER_TITLE.' '.$newsletter['module'].'<br /> '.sprintf(TEXT_COUNT_CUSTOMERS, $count['count']) . '</b></td></tr>
		<tr><td><a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID'] . '&action=confirm_send&global=true') . '">' . tep_image_button('button_send.gif', IMAGE_SEND) . '</a> <a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a></td></tr>
		  <tr><td class="main">'.TEXT_PREVIEW.'</td></tr><tr><td class="main"><br />Subject: '.$output_subject.'</td></tr><tr><td>' .$output_content_html . '<br /></td></tr>';
		} 
	elseif ($newsletter['module'] == 'product_notification') {
		$chosen = $HTTP_POST_VARS['chosen'];
		$ids = implode('-', $chosen);
		echo $ids;
		echo '<tr><td><b>' . TEXT_NEWSLETTER_TITLE.' '.$newsletter['module'].'<br /> '.sprintf(TEXT_COUNT_CUSTOMERS, $count['count']) . '</b></td></tr>
		<tr><td><a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID'] . '&action=confirm_send&chosen='.$ids) . '">' . tep_image_button('button_send.gif', IMAGE_SEND) . '</a> <a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a></td></tr>
		<tr><td class="main">'.TEXT_PREVIEW.'</td></tr><tr><td class="main"><br />Subject: '.$output_subject.'</td></tr><tr><td>' .$output_content_html . '<br /></td></tr>';
	}
	
	//removed new in 2013-07-13 version by toniroger
	/*<tr><td><?php echo $module->confirm(); ?></td></tr>*/
	
  } elseif ($action == 'confirm_send') {   
    //assemble mailpiece
    $nID = tep_db_prepare_input($HTTP_GET_VARS['nID']);
    $newsletter_query = tep_db_query("select  module, mailrate from " . TABLE_MM_NEWSLETTERS . " where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);
    $mailrate = $newsletter['mailrate'];
    
    //retrieve content and template name
	$newsletter_query = tep_db_query("select title, subject, content, txtcontent, template, module, mailrate from " . TABLE_MM_NEWSLETTERS . " where newsletters_id = '" . (int)$nID . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);
    $template_name = $newsletter['template'];  

    // retrieve template   	
	$template_query = tep_db_query("select title, htmlheader, htmlfooter, txtheader, txtfooter from " . TABLE_MM_TEMPLATES . " where title = '" . $template_name . "'");
    $template = tep_db_fetch_array($template_query);
    	
	// compile mailpiece
	$output_content_html= $template['htmlheader'].$newsletter['content'].$template['htmlfooter'];	
	$output_content_txt= $template['txtheader'].$newsletter['txtcontent'].$template['txtfooter'];	
    
    // placeholders 
    $placeholders=array('storeurl', 'storeowner','storeemail');
    $values=array(HTTP_SERVER,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS);
    $output_content_html=str_replace($placeholders, $values, $output_content_html);
 
 // get customer selection criteria and additional content from module     
 require(DIR_WS_MODULES . 'mail_manager/' . $newsletter['module'] . substr($PHP_SELF, strrpos($PHP_SELF, '.')));   
    
    if ($additional_htmlcontent!= NULL){
    //add additional_htmlcontent and additional_txtcontent, if it exists.  from selected module. Module files located in admin/includes/mail_manager.
	$output_content_html= $template['htmlheader'].$newsletter['content'].$additional_htmlcontent.$template['htmlfooter'];	
	$output_content_txt= $template['txtheader'].$newsletter['txtcontent'].strip_tags($additional_txtcontent).$template['txtfooter'];	    
   }
       
    //start mailing loop
       if($mail['mmstatus']=='0'){
    	tep_mm_sendmail($mail['customers_firstname'].' '.$mail['customers_lastname'], $mail['customers_email_address'], STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $newsletter['subject'], $output_content_html, $output_content_txt);
    	$email=$mail['customers_email_address'];
    	
    	tep_db_query("UPDATE ".TABLE_CUSTOMERS." SET mmstatus =  '9'  WHERE   customers_email_address ='" . $email . "'  Limit 1");
		//Create/update log file of sent emails.
		  $log_file_id = tep_session_id (); //Using tep_session_id to make the file name unique to this mailing.  It's unlikely that someone would send the same newsletter while still logged in with the same session_id.
		  $email_name = $mail['first_name'] . ' ' . $mail['last_name'];
		  $date_time = date("D, d M Y g:i a");
		  //Does the log file already exist?
		  $log_file_check = tep_db_input($newsletter_id) . '_' . $log_file_id . '.html';
		  if (!file_exists($log_file_check)) {
		   $log_data = '
				<table>
				 <tr>
				  <td colspan="3"><h1>Log File of Sent Emails for Newsletter ID #' . tep_db_input($newsletter_id) . '</h1></td>
				 </tr>
				 <tr>
				  <td colspan="3"><h3>Sending began on ' . $date_time . ' (server time)</h3></td>
				 </tr>
				 <tr>
				  <td><h3>Name</h3></td>
				  <td><h3>Email</h3></td>
				  <td><h3>Date/Time</h3></td>
				 </tr>
				 <tr>
				  <td>' . $email_name . '</td>
				  <td>' . $email . '</td>
				  <td>' . $date_time . '</td>
				 </tr>
		   ';
		  } else {
		   $log_data = '
				 <tr>
				  <td>' . $email_name . '</td>
				  <td>' . $email . '</td>
				  <td>' . $date_time . '</td>
				 </tr>
		   ';
		  }
		 
		 
		  $log_file_id = tep_session_id ();
		  $log_file = fopen(tep_db_input($newsletter_id) . '_' . $log_file_id . '.html', 'a+');
		  fwrite($log_file, $log_data);
		  fclose($log_file);
		  
        echo "<meta http-equiv='refresh' content=$mailrate>";
        ?>
    	<table border="0" cellspacing="0" cellpadding="2">
    	    
    	    <tr><td class="main"><strong><?php echo TEXT_NEWSLETTER_TITLE.' '.$newsletter['title'];?></strong></td></tr>        
 			<tr><td class="main"><?php echo sprintf(TEXT_COUNT_SENDING, $queue['count']).' ';?></td></tr>
 			<tr><td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
 			<tr><td class="main">sending now:<?php echo $mail['customers_email_address'];?></td></tr>
 			<tr><td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>		
 			<tr><td class="main"><?php echo sprintf(TEXT_COUNT_SENT, $mailed['count']); ?></td></tr>    
        	<tr><td class="main" valign="middle"><?php echo tep_image(DIR_WS_IMAGES . 'ani_send_email.gif', IMAGE_ANI_SEND_EMAIL); ?></td></tr>
        	<tr><td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
        	<tr><td class="main" valign="middle"><b><?php echo TEXT_PLEASE_WAIT; ?></b></td></tr>
        </table>
     <?php
	}else{
    // end loop
      
      $newsletter_id = tep_db_prepare_input($newsletter_id);
      tep_db_query("update " . TABLE_MM_NEWSLETTERS . " set date_sent = now(), status = '1' where newsletters_id = '" . tep_db_input($newsletter_id) . "'");
      tep_db_query("UPDATE ".TABLE_CUSTOMERS." SET  mmstatus =  '0' ");
	  
	  //Finish updating log file of sent emails.
	  $date_time = date("D, d M Y g:i a");
	  $log_data = '</table><br><br> END OF LOG DATA FOR THIS BULK EMAIL SEND.<br><i>Ended sending on ' . $date_time . ' (server time)</i>';
	  $log_file_id = tep_session_id ();
	  $log_file = fopen(tep_db_input($newsletter_id) . '_' . $log_file_id . '.html', 'a+');
	  fwrite($log_file, $log_data);
	  fclose($log_file);
	 
	  //Email the store admin about the mailing being complete
	  $log_file_location = tep_db_input($newsletter_id) . '_' . $log_file_id . '.html';
	  $log_email_subject = 'Bulkmail sending of ' . $newsletter['title'] . ' complete';
	  $log_email_body = 'The bulkmail sending of ' . $newsletter['title'] . ' complete.  You can view the log file containing information on who was emailed and more information <a href="' . HTTP_CATALOG_SERVER . DIR_WS_ADMIN . $log_file_location . '">here</a>.';
	  tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $log_email_subject, $log_email_body, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      
?>
     <tr><td><?php echo tep_draw_separator('pixel_trans.gif', '10', '20'); ?></td></tr>     
     <tr><td class="main"><font color="#ff0000"><b>
     			<?php 
     			
     			echo TEXT_FINISHED_SENDING_EMAILS; 
     			?></b></font>
				<br><br>
				You can view the log file containing information on who was emailed and more information <a href="<?php echo HTTP_CATALOG_SERVER . DIR_WS_ADMIN . $log_file_location?>">here</a>.
                </td></tr>
     <tr><td><?php echo tep_draw_separator('pixel_trans.gif', '10', '20'); ?></td></tr>
     <tr><td><?php echo '<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td></tr>
      <?php
      		$mail_query = tep_db_query("select customers_newsletter from " . TABLE_CUSTOMERS . " where customers_newsletter = 'mm'");
            $mail = tep_db_fetch_array($mail_query);
            
      			if($mail['customers_newsletter']=='9'){
      			echo '<tr><td class="main">Warning: Customers Newsletter status <strong>not</strong> reset.</td></tr>';
      			}elseif ($mail['customers_newsletter']!='9'){
      		   	echo '<tr><td class="main">Success: Customers Newsletter status reset <br /></td></tr>';
      	  		}      		
     }      
  } else {
?>
      <tr>
        <td>
        	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          		
            		<td valign="top">
            			<table border="0" width="100%" cellspacing="0" cellpadding="2">
              				<tr class="dataTableHeadingRow">
                				<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NEWSLETTERS; ?></td>
                				<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_SIZE; ?></td>
                				<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TEMPLATE; ?></td>
                				<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_AUDIENCE; ?></td>
                				<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_MAILRATE; ?></td>
                				<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_SENT; ?></td>
                				<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                				<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              				</tr>
<?php
    $newsletters_query_raw = "select newsletters_id, title, template, length(content) as content_length, module, mailrate, date_added, date_sent, status, locked from " . TABLE_MM_NEWSLETTERS . " order by date_added desc";
    $newsletters_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $newsletters_query_raw, $newsletters_query_numrows);
    $newsletters_query = tep_db_query($newsletters_query_raw);
    while ($newsletters = tep_db_fetch_array($newsletters_query)) {
    if ((!isset($HTTP_GET_VARS['nID']) || (isset($HTTP_GET_VARS['nID']) && ($HTTP_GET_VARS['nID'] == $newsletters['newsletters_id']))) && !isset($nInfo) && (substr($action, 0, 3) != 'new')) {
        $nInfo = new objectInfo($newsletters);
      }

      if (isset($nInfo) && is_object($nInfo) && ($newsletters['newsletters_id'] == $nInfo->newsletters_id) ) {
        echo '               <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->newsletters_id . '&action=preview') . '\'">' . "\n";
      } else {
        echo '               <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $newsletters['newsletters_id']) . '\'">' . "\n";
      }
?>              
                				<td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $newsletters['newsletters_id'] . '&action=preview') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $newsletters['title']; ?></td>
                				<td class="dataTableContent" align="right"><?php echo number_format($newsletters['content_length']) . ' bytes'; ?></td>
                				<td class="dataTableContent" align="right"><?php echo $newsletters['template']; ?></td>
                				<td class="dataTableContent" align="right"><?php echo $newsletters['module']; ?></td>
                				<td class="dataTableContent" align="right"><?php echo $newsletters['mailrate']; ?></td>
                				<td class="dataTableContent" align="center"><?php if ($newsletters['status'] == '1') { echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK); } else { echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS); } ?></td>
                				<td class="dataTableContent" align="center"><?php if ($newsletters['locked'] > 0) { echo tep_image(DIR_WS_ICONS . 'locked.gif', ICON_LOCKED); } else { echo tep_image(DIR_WS_ICONS . 'unlocked.gif', ICON_UNLOCKED); } ?></td>
                				<td class="dataTableContent" align="right"><?php if (isset($nInfo) && is_object($nInfo) && ($newsletters['newsletters_id'] == $nInfo->newsletters_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $newsletters['newsletters_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              				</tr>
<?php
    }
?>
              				<tr>
                				<td colspan="6">
                                	<table border="0" width="100%" cellspacing="0" cellpadding="2">
                  						<tr>
                    						<td class="smallText" valign="top"><?php echo $newsletters_split->display_count($newsletters_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS); ?></td>
                    						<td class="smallText" align="right"><?php echo $newsletters_split->display_links($newsletters_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                  						</tr>
                  						<tr>
                    						<td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_MM_MAIL_MANAGER, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?>
          									                  	
                    						<td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'action=new') . '">' . tep_image_button('button_new_newsletter.gif', IMAGE_NEW_NEWSLETTER) . '</a>'; ?></td>
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
    case 'delete':
      $heading[] = array('text' => '<b>' . $nInfo->title . '</b>');

      $contents = array('form' => tep_draw_form('newsletters', FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->newsletters_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTROO);
      $contents[] = array('text' => '<br><b>' . $nInfo->title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $HTTP_GET_VARS['nID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($nInfo)) {
        $heading[] = array('text' => '<b>' . $nInfo->title . '</b>');

        if ($nInfo->locked > 0) {
          //buttons if newsletter locked
          $contents[] = array('align' => 'center', 'text' => '
          				<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->newsletters_id . '&action=new') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> 
          				<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->newsletters_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> 
          				<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->newsletters_id . '&action=preview') . '">' . tep_image_button('button_preview.gif', IMAGE_PREVIEW) . '</a> 
          				<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->newsletters_id . '&action=send') . '">' . tep_image_button('button_send.gif', IMAGE_SEND) . '</a> 
          				<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->newsletters_id . '&action=test') . '">' . tep_image_button('button_test.gif', 'test') . '</a>          				
          				<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->newsletters_id . '&action=unlock') . '">' . tep_image_button('button_unlock.gif', IMAGE_UNLOCK) . '</a>
          				');
                         
          //buttons if newsletter unlocked
        } else {
          $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->newsletters_id . '&action=preview') . '">' . tep_image_button('button_preview.gif', IMAGE_PREVIEW) . '</a> <a href="' . tep_href_link(FILENAME_MM_BULKMAIL, 'page=' . $HTTP_GET_VARS['page'] . '&nID=' . $nInfo->newsletters_id . '&action=lock') . '">' . tep_image_button('button_lock.gif', IMAGE_LOCK) . '</a>');
        }
        $contents[] = array('text' => '<br>' . TEXT_NEWSLETTER_DATE_ADDED . ' ' . tep_date_short($nInfo->date_added));
        if ($nInfo->status == '1') $contents[] = array('text' => TEXT_NEWSLETTER_DATE_SENT . ' ' . tep_date_short($nInfo->date_sent));
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
        </table>
       </td>
      </tr>
<?php
  }
?>
    </table>
<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
