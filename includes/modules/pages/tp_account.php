<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

  class tp_account {
    var $group = 'account';

    function prepare() {
      global $oscTemplate;

      $oscTemplate->_data[$this->group] = array('account' => array('title' => MY_ACCOUNT_TITLE,
                                                                   'sort_order' => 10,
                                                                   'links' => array('edit' => array('title' => MY_ACCOUNT_INFORMATION,
                                                                                                    'link' => tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'),
                                                                                                    'icon' => 'glyphicon glyphicon-user'),
                                                                                    'address_book' => array('title' => MY_ACCOUNT_ADDRESS_BOOK,
                                                                                                            'link' => tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'),
                                                                                                            'icon' => 'glyphicon glyphicon-home'),
                                                                                    'password' => array('title' => MY_ACCOUNT_PASSWORD,
                                                                                                        'link' => tep_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL'),
                                                                                                        'icon' => 'glyphicon glyphicon-cog'))),
                                                'orders' => array('title' => MY_ORDERS_TITLE,
                                                                  'sort_order' => 20,
                                                                  'links' => array('history' => array('title' => MY_ORDERS_VIEW,
                                                                                                      'link' => tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'),
                                                                                                      'icon' => 'glyphicon glyphicon-shopping-cart'))),
                                                'notifications' => array('title' => EMAIL_NOTIFICATIONS_TITLE,
                                                                         'sort_order' => 30,
                                                                         'links' => array('newsletters' => array('title' => EMAIL_NOTIFICATIONS_NEWSLETTERS,
                                                                                                                 'link' => tep_href_link(FILENAME_ACCOUNT_NEWSLETTERS, '', 'SSL'),
                                                                                                                 'icon' => 'glyphicon glyphicon-envelope'),
                                                                                          'products' => array('title' => EMAIL_NOTIFICATIONS_PRODUCTS,
                                                                                                              'link' => tep_href_link(FILENAME_ACCOUNT_NOTIFICATIONS, '', 'SSL'),
                                                                                                              'icon' => 'glyphicon glyphicon-send'))));
    }

    function build() {
      global $oscTemplate;
      
      foreach ( $oscTemplate->_data[$this->group] as $key => $row ) {
        $arr[$key] = $row['sort_order'];
      }
      array_multisort($arr, SORT_ASC, $oscTemplate->_data[$this->group]);

      $output = '<div class="col-sm-12">';

      foreach ( $oscTemplate->_data[$this->group] as $group ) {
        $output .= '<h2>' . $group['title'] . '</h2>' .
                   '<div class="contentText">' .
                   '  <ul class="accountLinkList">';

        foreach ( $group['links'] as $entry ) {
          $output .= '    <li>';

          if ( isset($entry['icon']) ) {
            $output .= '<i class="' . $entry['icon'] . '"></i> ';
          }

          $output .= '<a href="' . $entry['link'] . '">' . $entry['title'] . '</a></li>';
        }

        $output .= '  </ul>' .
                   '</div>';
      }

      $output .= '</div>';
      
      $oscTemplate->addContent($output, $this->group);
    }
  }
?>
