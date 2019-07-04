<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2018 osCommerce

  Released under the GNU General Public License
*/

  class tp_account {
    var $group = 'account';

    function prepare() {
      global $oscTemplate;

      $oscTemplate->_data[$this->group] = array('account' => array('title' => MY_ACCOUNT_TITLE,
                                                                   'sort_order' => 10,
                                                                   'links' => array('edit' => array('title' => MY_ACCOUNT_INFORMATION,
                                                                                                    'link' => tep_href_link('account_edit.php', '', 'SSL'),
                                                                                                    'icon' => 'fas fa-user fa-5x'),
                                                                                    'address_book' => array('title' => MY_ACCOUNT_ADDRESS_BOOK,
                                                                                                            'link' => tep_href_link('address_book.php', '', 'SSL'),
                                                                                                            'icon' => 'fas fa-home fa-5x'),
                                                                                    'password' => array('title' => MY_ACCOUNT_PASSWORD,
                                                                                                        'link' => tep_href_link('account_password.php', '', 'SSL'),
                                                                                                        'icon' => 'fas fa-cog fa-5x'))),
                                                'orders' => array('title' => MY_ORDERS_TITLE,
                                                                  'sort_order' => 20,
                                                                  'links' => array('history' => array('title' => MY_ORDERS_VIEW,
                                                                                                      'link' => tep_href_link('account_history.php', '', 'SSL'),
                                                                                                      'icon' => 'fas fa-shopping-cart fa-5x'))),
                                                'notifications' => array('title' => EMAIL_NOTIFICATIONS_TITLE,
                                                                         'sort_order' => 30,
                                                                         'links' => array('newsletters' => array('title' => EMAIL_NOTIFICATIONS_NEWSLETTERS,
                                                                                                                 'link' => tep_href_link('account_newsletters.php', '', 'SSL'),
                                                                                                                 'icon' => 'fas fa-envelope fa-5x'),
                                                                                          'products' => array('title' => EMAIL_NOTIFICATIONS_PRODUCTS,
                                                                                                              'link' => tep_href_link('account_notifications.php', '', 'SSL'),
                                                                                                              'icon' => 'fas fa-paper-plane fa-5x'))));
    }

    function build() {
      global $oscTemplate;
      
      foreach ( $oscTemplate->_data[$this->group] as $key => $row ) {
        $arr[$key] = $row['sort_order'];
      }
      array_multisort($arr, SORT_ASC, $oscTemplate->_data[$this->group]);

      $output = '<div class="col-sm-12">';

      foreach ( $oscTemplate->_data[$this->group] as $group ) {
        $output .= '<h4>' . $group['title'] . '</h4>';
        $output .= '<div class="list-group list-group-horizontal-sm">';

        foreach ( $group['links'] as $entry ) {
          $output .= '<a class="text-center col-sm-4 col-lg-3 list-group-item list-group-item-action" href="' . $entry['link'] . '">';
            $output .= '<i title="' . $entry['title'] . '" class="d-none d-sm-block ' . $entry['icon'] . '"></i>';
            $output .= $entry['title'];
          $output .= '</a>';
        }

        $output .= '</div>';
      }

      $output .= '</div>';
      
      $oscTemplate->addContent($output, $this->group);
    }
  }
