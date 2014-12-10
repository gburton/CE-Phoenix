<?php

/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010, 2011 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
 */

// make path work from admin
require_once(MH_DIR_FS_CATALOG . 'mailhive/common/classes/mailbeez.php');

class trustpilot extends mailbeez
{

    // class constructor
    function trustpilot()
    {
        // call constructor
        mailbeez::mailbeez();

        // set some stuff:
        $this->code = 'trustpilot';
        $this->module = 'trustpilot'; // same as folder name
        $this->version = '2.4'; // float value
        $this->required_mb_version = 2.6; // required mailbeez version
        $this->iteration = 1;
        $this->title = MAILBEEZ_TRUSTPILOT_TEXT_TITLE;
        $this->description = MAILBEEZ_TRUSTPILOT_TEXT_DESCRIPTION;
        $this->description_image = 'top_64.png';
        $this->icon = 'icon_tp.png';
        $this->sort_order = MAILBEEZ_TRUSTPILOT_SORT_ORDER;
        $this->enabled = ((MAILBEEZ_TRUSTPILOT_STATUS == 'True') ? true : false);
        $this->has_submodules = false;
        $this->googleanalytics_enabled = 'False';
        $this->googleanalytics_rewrite_mode = 'False';
        $this->sender = MAILBEEZ_TRUSTPILOT_SENDER;
        $this->sender_name = MAILBEEZ_TRUSTPILOT_SENDER_NAME;
        $this->status_key = 'MAILBEEZ_TRUSTPILOT_STATUS';

        //$this->icon = 'icon.png';

        $this->documentation_key = $this->module; // leave empty if no documentation available
        // $this->documentation_root = 'http:://yoursite.com/' // modify documentation root if necessary

        $this->htmlBodyTemplateResource = 'body_html.tpl'; // located in folder of this module
        $this->txtBodyTemplateResource = 'body_txt.tpl'; // located in folder of this module
        $this->subjectTemplateResource = 'subject.tpl'; // located in folder of this module

        $this->audience = array();
        $this->additionalFields = array('customers_id' => '007', 'customers_email_address' => 'customer@mail.com', 'order_id' => '123456', 'order_date' => '12.12.2000', 'status_date' => '31.12.2000', 'language' => 'en-GB');
        // list of additional fields to show in listing with testvalues used for Test-Mail
    }

// class methods

    function getAudience()
    {
        $status = MAILBEEZ_TRUSTPILOT_ORDER_STATUS_ID;

        // early check to avoid processing when email was already sent
        $mb_chk = new mailbeez_mailer($this);

        // MAILBEEZ_TRUSTPILOT_PASSED_DAYS
        // MAILBEEZ_TRUSTPILOT_PASSED_DAYS_SKIP

        $date_skip = $this->dbdate(MAILBEEZ_TRUSTPILOT_PASSED_DAYS_SKIP);
        $date_passed = $this->dbdate(MAILBEEZ_TRUSTPILOT_PASSED_DAYS);

        $query_raw = "select c.customers_firstname, c.customers_lastname, o.orders_id, o.customers_name,
                        o.customers_id, o.customers_email_address, o.date_purchased, s.date_added as status_date
                      from " . TABLE_ORDERS . " o
                        left join " . TABLE_CUSTOMERS . " c
                          on (o.customers_id = c.customers_id)
                        left join " . TABLE_ORDERS_STATUS_HISTORY . " s
                          on (o.orders_id = s.orders_id)
                      where o.orders_status = s.orders_status_id
                        and s.orders_status_id = '" . (int)$status . "'
                        and s.date_added <= '" . $date_passed . "'
                        and s.date_added > '" . $date_skip . "'
                      order by o.orders_id DESC";

        $query = mh_db_query($query_raw);
        while ($item = mh_db_fetch_array($query)) {
            // mandatory fields:
            // - firstname
            // - lastname
            // - email_address
            // - customers-id -> block
            // other keys are replaced while sending: $<key>
            // early check to avoid processing when email was already sent
            $chk_result = $mb_chk->check($this->module, $this->iteration, $item['customers_id'], $item['orders_id']);
            if ($chk_result != false) {
                // this iteration was already sent -> skip
                continue;
            }

            $tp_block_token = base64_encode($mail['customers_id'] . '|' . $item['customers_email_address']);
            $tp_block_url = HTTP_SERVER . DIR_WS_HTTP_CATALOG . FILENAME_HIVE . '?ma=block&m=' . $this->module . '&mp=' . $tp_block_token;


            $this->audience[$item['customers_id']] = array('firstname' => $item['customers_firstname'],
                'lastname' => $item['customers_lastname'],
                'customers_email_address' => $item['customers_email_address'],
                'email_address' => MAILBEEZ_TRUSTPILOT_TRIGGER_EMAIL,
                'customers_id' => $item['customers_id'],
                'order_id' => $item['orders_id'],
                'order_date' => mh_date_short($item['date_purchased']),
                'status_date' => mh_date_short($item['status_date']),
                'language' => MAILBEEZ_TRUSTPILOT_LANGUAGE,
                'tp_block_url' => $tp_block_url
            );
        }
        return $this->audience;
    }

    // installation methods

    function keys()
    {
        return array('MAILBEEZ_TRUSTPILOT_STATUS', 'MAILBEEZ_TRUSTPILOT_ORDER_STATUS_ID', 'MAILBEEZ_TRUSTPILOT_PASSED_DAYS', 'MAILBEEZ_TRUSTPILOT_PASSED_DAYS_SKIP', 'MAILBEEZ_TRUSTPILOT_SENDER', 'MAILBEEZ_TRUSTPILOT_SENDER_NAME', 'MAILBEEZ_TRUSTPILOT_TRIGGER_EMAIL', 'MAILBEEZ_TRUSTPILOT_SORT_ORDER', 'MAILBEEZ_TRUSTPILOT_LANGUAGE');
    }

    function install()
    {
        mh_insert_config_value(array('configuration_title' => 'Send trustpilot trigger email',
            'configuration_key' => 'MAILBEEZ_TRUSTPILOT_STATUS',
            'configuration_value' => 'False',
            'configuration_description' => 'Do you want to send trustpilot trigger email to ask your customer for a review?',
            'set_function' => 'mh_cfg_select_option(array(\'True\', \'False\'), '
        ));

        mh_insert_config_value(array('configuration_title' => 'Set Order Status',
            'configuration_key' => 'MAILBEEZ_TRUSTPILOT_ORDER_STATUS_ID',
            'configuration_value' => '3',
            'configuration_description' => 'Set the status of orders to send trigger',
            'set_function' => 'mh_cfg_pull_down_order_statuses(',
            'use_function' => 'mh_get_order_status_name'
        ));

        mh_insert_config_value(array('configuration_title' => 'your trustpilot email address',
            'configuration_key' => 'MAILBEEZ_TRUSTPILOT_TRIGGER_EMAIL',
            'configuration_value' => 'yourid@trustpilotservice.com',
            'configuration_description' => 'your unique trustpilot email address',
            'set_function' => ''
        ));

        mh_insert_config_value(array('configuration_title' => 'Set days passed',
            'configuration_key' => 'MAILBEEZ_TRUSTPILOT_PASSED_DAYS',
            'configuration_value' => '5',
            'configuration_description' => 'number of days to wait before sending the emails',
            'set_function' => ''
        ));

        mh_insert_config_value(array('configuration_title' => 'Set days to skip after',
            'configuration_key' => 'MAILBEEZ_TRUSTPILOT_PASSED_DAYS_SKIP',
            'configuration_value' => '10',
            'configuration_description' => 'number of days after which do skip the reminder',
            'set_function' => ''
        ));

        mh_insert_config_value(array('configuration_title' => 'sender email',
            'configuration_key' => 'MAILBEEZ_TRUSTPILOT_SENDER',
            'configuration_value' => STORE_OWNER_EMAIL_ADDRESS,
            'configuration_description' => 'sender email',
            'set_function' => ''
        ));

        mh_insert_config_value(array('configuration_title' => 'sender name',
            'configuration_key' => 'MAILBEEZ_TRUSTPILOT_SENDER_NAME',
            'configuration_value' => STORE_NAME,
            'configuration_description' => 'sender email',
            'set_function' => ''
        ));

        mh_insert_config_value(array('configuration_title' => 'Default Customer Language',
            'configuration_key' => 'MAILBEEZ_TRUSTPILOT_LANGUAGE',
            'configuration_value' => 'automatic',
            'configuration_description' => 'Please choose the language Trustpilot will ask your Customers.',
            'set_function' => 'mh_cfg_select_option(array(\'da-DK\', \'de-DE\', \'en-GB\', \'es-ES\', \'fr-FR\', \'it-IT\', \'nb-NO\', \'nl-NL\', \'ro-RO\', \'ru-RU\', \'sv-SE\', \'automatic\'), '
        ));

        mh_insert_config_value(array('configuration_title' => 'Sort order of display.',
            'configuration_key' => 'MAILBEEZ_TRUSTPILOT_SORT_ORDER',
            'configuration_value' => '10',
            'configuration_description' => 'Sort order of display. Lowest is displayed first.',
            'set_function' => ''
        ));
    }

}

?>
