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
                                                                   'links' => array('edit' => array('title' => MY_ACCOUNT_INFORMATION,
                                                                                                    'link' => tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'),
                                                                                                    'icon' => 'fa fa-user fa-3x'),
                                                                                    'address_book' => array('title' => MY_ACCOUNT_ADDRESS_BOOK,
                                                                                                            'link' => tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'),
                                                                                                            'icon' => 'fa fa-home fa-3x'),
                                                                                    'password' => array('title' => MY_ACCOUNT_PASSWORD,
                                                                                                        'link' => tep_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL'),
                                                                                                        'icon' => 'fa fa-cog fa-3x'))),
                                                'orders' => array('title' => MY_ORDERS_TITLE,
                                                                  'links' => array('history' => array('title' => MY_ORDERS_VIEW,
                                                                                                      'link' => tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'),
                                                                                                      'icon' => 'fa fa-shopping-cart fa-3x'))),
                                                'notifications' => array('title' => EMAIL_NOTIFICATIONS_TITLE,
                                                                         'links' => array('newsletters' => array('title' => EMAIL_NOTIFICATIONS_NEWSLETTERS,
                                                                                                                 'link' => tep_href_link(FILENAME_ACCOUNT_NEWSLETTERS, '', 'SSL'),
                                                                                                                 'icon' => 'fa fa-envelope fa-3x'),
                                                                                          'products' => array('title' => EMAIL_NOTIFICATIONS_PRODUCTS,
                                                                                                              'link' => tep_href_link(FILENAME_ACCOUNT_NOTIFICATIONS, '', 'SSL'),
                                                                                                              'icon' => 'fa fa-send fa-3x'))));
    }

    function build() {
      global $oscTemplate;
      
      $count              = 0;
      $output             = '<div role="tabpanel">';
      $output_tab_list    = '<ul class="nav nav-tabs">';
      $output_tab_content = '<div class="tab-content">';

      foreach ( $oscTemplate->_data[$this->group] as $group ) {
        
        $output_tab_list .= '<li role="presentation"' . (( $count == 0 ) ? ' class="active"':'') . '><a href="#' . str_replace(' ', '', strtolower($group['title'])) . '" aria-controls="home" role="tab" data-toggle="tab">' . $group['title'] . '</a></li>';
        
        $output_tab_content .= '<div role="tabpanel" class="tab-pane fade' . (( $count == 0 ) ? ' active in':'') . '" id="' . str_replace(' ', '', strtolower($group['title'])) . '"><ul class="accountLinkList">';

        foreach ( $group['links'] as $entry ) {
          $output_tab_content .= '  <li>';

          if ( isset($entry['icon']) ) {
            $output_tab_content .= '<i class="' . $entry['icon'] . '"></i> ';
          }

          $output_tab_content .= '<a href="' . $entry['link'] . '">' . $entry['title'] . '</a></li>';
        }

        $output_tab_content .= '</ul></div>';
        
        $count++;
      }
      
      $output_tab_content .= '</div>';
      $output_tab_list .= '</ul>';
      
      $output .= $output_tab_list;
      $output .= $output_tab_content;
      $output .= '</div>';

      $oscTemplate->addContent($output, $this->group);
    }
  }
?>
