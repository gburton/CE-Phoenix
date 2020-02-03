<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class newsletter {
    var $show_choose_audience, $title, $content;

    function __construct($title, $content) {
      $this->show_choose_audience = false;
      $this->title = $title;
      $this->content = $content;
    }

    function choose_audience() {
      return false;
    }

    function confirm() {
      $mail_query = tep_db_query("select count(*) as count from customers where customers_newsletter = '1'");
      $mail = tep_db_fetch_array($mail_query);
      
      $confirm_string = '<div class="alert alert-danger">' . sprintf(TEXT_COUNT_CUSTOMERS, $mail['count']) . '</div>';
      
      $confirm_string .= '<table class="table table-striped">';
        $confirm_string .= '<tr>';
          $confirm_string .= '<th>' . TEXT_TITLE . '</th>';
          $confirm_string .= '<td>' . $this->title . '</td>';
        $confirm_string .= '</tr>';
        $confirm_string .= '<tr>';
          $confirm_string .= '<th>' . TEXT_CONTENT . '</th>';
          $confirm_string .= '<td>' . $this->content . '</td>';
        $confirm_string .= '</tr>';
      $confirm_string .= '</table>';
      
      $confirm_string .= '<div class="buttonSet">';
        $confirm_string .= tep_draw_bootstrap_button(IMAGE_SEND, 'fas fa-paper-plane', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$_GET['nID'] . '&action=confirm_send'), 'primary', null, 'btn-success btn-block btn-lg xxx text-white');
        $confirm_string .= tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$_GET['nID']), null, null, 'btn-light mt-2');
      $confirm_string .= '</div>';

      return $confirm_string;
    }

    function send($newsletter_id) {
      $mail_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from customers where customers_newsletter = '1'");

      $mimemessage = new email();
      $mimemessage->add_message($this->content);
      $mimemessage->build_message();
      while ($mail = tep_db_fetch_array($mail_query)) {
        $mimemessage->send($mail['customers_firstname'] . ' ' . $mail['customers_lastname'], $mail['customers_email_address'], '', EMAIL_FROM, $this->title);
      }

      $newsletter_id = tep_db_prepare_input($newsletter_id);
      tep_db_query("update newsletters set date_sent = now(), status = '1' where newsletters_id = '" . tep_db_input($newsletter_id) . "'");
    }
  }
  
