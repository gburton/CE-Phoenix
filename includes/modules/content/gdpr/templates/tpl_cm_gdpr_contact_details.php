<div class="col-sm-<?php echo $content_width; ?> cm-gdpr-contact-details">
  <table class="table table-striped table-hover">
    <thead class="thead-dark">
      <tr>
        <th colspan="2"><?php echo MODULE_CONTENT_GDPR_CONTACT_DETAILS_PUBLIC_TITLE; ?></th>
      </tr>
    </thead>
    <tr>
      <th class="w-50"><?php echo MODULE_CONTENT_GDPR_CONTACT_DETAILS_EMAIL; ?></th>
      <td><?php echo $port_my_data['YOU']['CONTACT']['EMAIL']; ?></td>
    </tr>
    <tr>
      <th><?php echo MODULE_CONTENT_GDPR_CONTACT_DETAILS_PHONE; ?></th>
      <td><?php echo $port_my_data['YOU']['CONTACT']['PHONE']; ?></td>
    </tr> 
    <tr>
      <th><?php echo MODULE_CONTENT_GDPR_CONTACT_DETAILS_FAX; ?></th>
      <td><?php echo $port_my_data['YOU']['CONTACT']['FAX']; ?></td>
    </tr> 
    <tr>
      <th><?php echo MODULE_CONTENT_GDPR_CONTACT_DETAILS_MAIN_ADDRESS; ?></th> 
      <td><?php echo $customer->make_address_label($customer->get_default_address_id(), true, ' ', '<br>'); ?></td>
    </tr>      
  </table>
</div>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>

