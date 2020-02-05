<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class newsletter {

    public $show_choose_audience = false;
    public $title, $content;

    function __construct($title, $content) {
      $this->title = $title;
      $this->content = $content;
    }

    function choose_audience() {
      return false;
    }

    function confirm() {
      $confirm_string = '<div class="alert alert-danger">' . sprintf(TEXT_COUNT_CUSTOMERS, $GLOBALS['customer_data']->count_by_criteria(['newsletter' => 1])) . '</div>' . "\n";

      $confirm_string .= '<table class="table table-striped">' . "\n";
      $confirm_string .= '  <tr>' . "\n";
      $confirm_string .= '    <th scope="row">' . TEXT_TITLE . '</th>' . "\n";
      $confirm_string .= '    <td>' . $this->title . '</td>' . "\n";
      $confirm_string .= '  </tr>' . "\n";
      $confirm_string .= '  <tr>' . "\n";
      $confirm_string .= '    <th scope="row">' . TEXT_CONTENT . '</th>' . "\n";
      $confirm_string .= '    <td>' . $this->content . '</td>' . "\n";
      $confirm_string .= '  </tr>' . "\n";
      $confirm_string .= '</table>' . "\n";

      $confirm_string .= '<div class="buttonSet">';
      $confirm_string .= tep_draw_bootstrap_button(IMAGE_SEND, 'fas fa-paper-plane', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$_GET['nID'] . '&action=confirm_send'), 'primary', null, 'btn-success btn-block btn-lg xxx text-white');
      $confirm_string .= tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-angle-left', tep_href_link('newsletters.php', 'page=' . (int)$_GET['page'] . '&nID=' . (int)$_GET['nID']), null, null, 'btn-light mt-2');
      $confirm_string .= '</div>' . "\n";

      return $confirm_string;
    }

    function send($newsletter_id) {
      if ($GLOBALS['customer_data'] instanceof customer_data) {
        $customer_data = &$GLOBALS['customer_data'];
      } else {
        $customer_data = new customer_data();
      }

      $mail_query = tep_db_query($customer_data->build_read(['name', 'email_address'], 'customers', ['newsletter' => 1]));

      $mimemessage = new email();
      $mimemessage->add_message($this->content);
      $mimemessage->build_message();
      while ($mail = tep_db_fetch_array($mail_query)) {
        $mimemessage->send($customer_data->get('name', $mail), $customer_data->get('email_address', $mail), '', EMAIL_FROM, $this->title);
      }

      $newsletter_id = tep_db_prepare_input($newsletter_id);
      tep_db_query("UPDATE newsletters SET date_sent = NOW(), status = 1 WHERE newsletters_id = '" . tep_db_input($newsletter_id) . "'");
    }

  }

