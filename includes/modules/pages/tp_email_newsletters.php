<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  class tp_email_newsletters {
    var $group = 'email_newsletters';
    var $template = '';
    var $title = 'Newsletters Template';
    var $description = 'osCommerce Basic Newsletters Template Node with welcome text';
    var $section = 'undefined';
    var $version = '1.01';

    function prepare() {
      global $oscTemplate, $mimemessage;
      $oscTemplate->_email_data['newsletters']['enable_osc_mail'] = 'True';
    }

    function build() {
      global $oscTemplate, $nInfo, $mimemessage;

      if ($oscTemplate->_email_data['newsletters']['enable_osc_mail'] == 'True') {

        $name = sprintf(TEXT_WELCOME, $nInfo->mail['customers_firstname'] . ' ' . $nInfo->mail['customers_lastname']);

        ob_start();
        require(DIR_WS_MODULES . 'pages/templates/email_' . $nInfo->module . '_text.php');
        $text_content = ob_get_clean();

        // Build the text version
        $text_content = tep_convert_linefeeds(array("<br />"), "\n", $text_content);
        if (EMAIL_USE_HTML == 'true') {
          ob_start();
          require(DIR_WS_MODULES . 'pages/templates/email_' . $nInfo->module . '_html.php');
          $html_content = tep_convert_linefeeds(array("\r\n", "\n", "\r"), '', ob_get_clean());

          $mimemessage->add_html($html_content, tep_strip_html_tags($text_content));
        } else {
          $mimemessage->add_text($text_content);
        }

        $mimemessage->build_message();
        $mimemessage->send($nInfo->mail['customers_firstname'] . ' ' . $nInfo->mail['customers_lastname'], $nInfo->mail['customers_email_address'], '', EMAIL_FROM, $nInfo->title);

        // Reset all mime parameters
        $mimemessage->output = null;
        $mimemessage->html = null;
        $mimemessage->text = null;
        $mimemessage->output = null;
        $mimemessage->html_text = null;
        $mimemessage->html_images = array();
        $mimemessage->headers = array('MIME-Version: 1.0', 'X-Mailer: osCommerce');

      }
    }

    function preview() {
      global $mode, $language, $HTTP_GET_VARS;

      include(DIR_WS_LANGUAGES . $language . '/newsletters.php');

      $nID = (isset($HTTP_GET_VARS['nID']) ? $HTTP_GET_VARS['nID'] : 0);

      if (tep_not_null($nID)) {
        $newsletter_query = tep_db_query("select title, content, module from " . TABLE_NEWSLETTERS . " where newsletters_id = '" . (int)$nID . "'");
      } else {
        $newsletter_query = tep_db_query("select title, content, module from " . TABLE_NEWSLETTERS . " order by newsletters_id desc limit 1");
      }

      if (tep_db_num_rows($newsletter_query)) {
        $newsletter = tep_db_fetch_array($newsletter_query);

        $nInfo = new objectInfo($newsletter);

        $mail = array('customers_firstname' => 'Firstname',
                      'customers_lastname' => 'Lastname');

        $name = sprintf(TEXT_WELCOME, $mail['customers_firstname'] . ' ' . $mail['customers_lastname']);

        ob_start();
        if ($mode == 'text') {
          require(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_' . $newsletter['module'] . '_text.php');
        } else {
          require(DIR_FS_CATALOG . DIR_WS_MODULES . 'pages/templates/email_' . $newsletter['module'] . '_html.php');
        }

        $this->template = ob_get_clean();
      } else {
        $mode = 'text';
        $this->template = ERROR_NO_PREVIEW_CONTENT;
      }
    }

    function info() {
      return array('group' => $this->group,
                   'title' => $this->title,
                   'description' => $this->description,
                   'section' => $this->section,
                   'version' => $this->version);
    }
  }

  function tep_strip_html_tags($text) {
    $text = preg_replace(
      array(
        // Remove invisible content
          '@<head[^>]*?>.*?</head>@siu',
          '@<style[^>]*?>.*?</style>@siu',
          '@<script[^>]*?.*?</script>@siu',
          '@<object[^>]*?.*?</object>@siu',
          '@<embed[^>]*?.*?</embed>@siu',
          '@<applet[^>]*?.*?</applet>@siu',
          '@<noframes[^>]*?.*?</noframes>@siu',
          '@<noscript[^>]*?.*?</noscript>@siu',
          '@<noembed[^>]*?.*?</noembed>@siu',
        // Add line breaks before and after blocks
          '@</?((address)|(blockquote)|(center)|(del))@iu',
          '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
          '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
          '@</?((table)|(th)|(td)|(caption))@iu',
          '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
          '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
          '@</?((frameset)|(frame)|(iframe))@iu',
      ),
      array(
          ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
          "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
          "\n\$0", "\n\$0",
      ),
      $text);
    return strip_tags($text);
  }
?>
