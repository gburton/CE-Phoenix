<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce
    
  Edited by 2014 Newburns Design and Technology
  *************************************************
  ************ New addon definitions **************
  ************        Below          **************
  *************************************************
  Mail Manager added -- http://addons.oscommerce.com/info/9133/v,23

  Released under the GNU General Public License

  email is a class to assist with PHPmailer
  sendmail, SMTP and gmail compatibility
*/

  require_once 'ext/modules/PHPMailer/class.phpmailer.php';
  $phpMail = new PHPMailer();

  class email {
    var $html;
    var $text;
    var $html_text;
    var $lf;
    var $debug = 0;
    var $debug_output = 'error_log';

    function email($headers = '') {
      global $phpMail;

      $phpMail->XMailer = 'osCommerce ' . tep_get_version();
      $phpMail->SMTPDebug = $this->debug;
      $phpMail->Debugoutput = $this->debug_output;
      $phpMail->CharSet = CHARSET;
      $phpMail->WordWrap = 998;

      if (EMAIL_LINEFEED == 'CRLF') {
        $this->lf = "\r\n";
      } else {
        $this->lf = "\n";
      }
    }

    function add_text($text = '') {
      global $phpMail;

      $phpMail->IsHTML(false);
      $this->text = tep_convert_linefeeds(array("\r\n", "\n", "\r"), $this->lf, $text);
    }

    function add_html($html, $text = NULL, $images_dir = NULL) {
      global $phpMail;

      $phpMail->IsHTML(true);

      $this->html = tep_convert_linefeeds(array("\r\n", "\n", "\r"), '<br />', $html);
      $this->html_text = tep_convert_linefeeds(array("\r\n", "\n", "\r"), $this->lf, $text);

      if (isset($images_dir)) $this->html = $phpMail->msgHTML($this->html, $images_dir);
    }

    function add_attachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment') {
      global $phpMail;

      $phpMail->AddAttachment($path, $name, $encoding, $type, $disposition);
    }

    function build_message() {
      //out of work function
    }

    function send($to_name, $to_addr, $from_name, $from_addr, $subject = '', $reply_to = false) {
      global $phpMail;

      if ((strstr($to_name, "\n") != false) || (strstr($to_name, "\r") != false)) {
        return false;
      }

      if ((strstr($to_addr, "\n") != false) || (strstr($to_addr, "\r") != false)) {
        return false;
      }

      if ((strstr($subject, "\n") != false) || (strstr($subject, "\r") != false)) {
        return false;
      }

      if ((strstr($from_name, "\n") != false) || (strstr($from_name, "\r") != false)) {
        return false;
      }

      if ((strstr($from_addr, "\n") != false) || (strstr($from_addr, "\r") != false)) {
        return false;
      }

      $phpMail->From = $from_addr;
      $phpMail->FromName = $from_name;
      $phpMail->AddAddress($to_addr, $to_name);

      if ($reply_to) {
        $phpMail->AddReplyTo(EMAIL_SMTP_REPLYTO, STORE_NAME);
      } else {
        $phpMail->AddReplyTo($from_addr, $from_name);
      }

      $phpMail->Subject = $subject;

      if (!empty($this->html)) {
        $phpMail->Body = $this->html;
        $phpMail->AltBody = $this->html_text;
      } else {
        $phpMail->Body = $this->text;
      }

      if (EMAIL_TRANSPORT == 'smtp' || EMAIL_TRANSPORT == 'gmail') {
        $phpMail->IsSMTP();

        $phpMail->Host = EMAIL_SMTP_HOSTS;
        $phpMail->SMTPAuth = EMAIL_SMTP_AUTHENTICATION;

        $phpMail->Username = EMAIL_SMTP_USER;
        $phpMail->Password = EMAIL_SMTP_PASSWORD;

        if (EMAIL_TRANSPORT == 'gmail') {
          $phpMail->Port = 587;
          $phpMail->SMTPSecure = 'tls';
        }
      } else {
        $phpMail->isSendmail();
      }

      if (!$phpMail->Send()) {
        return false;
      }

      return true;
    }
  }
/* ** Altered for Mail Manager ** */
// eliminate line feeds as <br>
  class emailMailManager extends email { 
	function add_html($html, $text = NULL, $images_dir = NULL) {
	  $this->html = $html; //tep_convert_linefeeds(array("\r\n", "\n", "\r"), '<br>', $html);
	  $this->html_text = tep_convert_linefeeds(array("\r\n", "\n", "\r"), $this->lf, $text);
	  if (isset($images_dir)) $this->find_html_images($images_dir);
	}
  }
/* ** EOF alterations for Mail Manager ** */  
?>
