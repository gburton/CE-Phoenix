<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com
  
*/

chdir('../../../../');
require('includes/application_top.php');

if(!isset($_SERVER['HTTP_X_AAS'])) die;
if(isset($sessionTimeout)){

	$dataType = (isset($_POST['dataType']) ? $_POST['dataType'] : 'html');
	if($dataType=='json') echo json_encode(array('response'=>'aasSessionTimeout'));
	else echo'aasSessionTimeout';
	die;
	
}
if($_SERVER['HTTP_X_AAS']!==$_SESSION['admin']['AAS']['ajaxToken']) die;
defined('AAS') or define('AAS', 1);

if(file_exists('ext/aas/languages/'.$language.'.php')) include 'ext/aas/languages/'.$language.'.php'; else include 'ext/aas/languages/english.php';
if(isset($sessionTimeout)){echo'aasSessionTimeout'; die;}

require('ext/aas/application_top.php');

$name = (isset($_POST['name']) ? tep_db_prepare_input($_POST['name']) : '');
$email = (isset($_POST['email']) ? tep_db_prepare_input($_POST['email']) : '');
$subject = (isset($_POST['subject']) ? tep_db_prepare_input($_POST['subject']) : '');
$message = (isset($_POST['message']) ? tep_db_prepare_input($_POST['message']) : '');

	
if(tep_not_null($name) && tep_not_null($email) && tep_not_null($subject) && tep_not_null($message) ){

	//Let's build a message object using the email class
	$mimemessage = new email(array('X-Mailer: osCommerce'));
	
	// Build the text version
    $text = strip_tags($message);
    if (EMAIL_USE_HTML == 'true') {
      $mimemessage->add_html($message, $text);
    } else {
      $mimemessage->add_text($text);
    }

    $mimemessage->build_message();
    if($mimemessage->send($name, 'jbqwerty@gmail.com', '', $email, $subject))echo '1';
    else echo '0';
    
}else echo '0';

?>
