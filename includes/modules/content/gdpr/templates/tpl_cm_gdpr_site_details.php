<div class="col-sm-<?php echo $content_width; ?> cm-gdpr-site-details">
  <table class="table table-striped table-hover">
    <thead class="thead-dark">
      <tr>
        <th colspan="2"><?php echo MODULE_CONTENT_GDPR_SITE_DETAILS_PUBLIC_TITLE; ?></th>
      </tr>
    </thead>
    <tr>
      <th class="w-50"><?php echo MODULE_CONTENT_GDPR_SITE_DETAILS_NEWSLETTER_SUB; ?></th>
      <td><?php echo $port_my_data['YOU']['SITE']['NEWSLETTER']; ?></td>
    </tr>
    <tr>
      <th><?php echo MODULE_CONTENT_GDPR_SITE_DETAILS_ACCOUNT_CREATED; ?></th>
      <td><?php echo $port_my_data['YOU']['SITE']['ACCOUNTCREATED']; ?></td>
    </tr>
    <tr>
      <th><?php echo MODULE_CONTENT_GDPR_SITE_DETAILS_NUMBER_LOGON; ?></th>
      <td><?php echo $port_my_data['YOU']['SITE']['LOGONS']['COUNT']; ?></td>
    </tr> 
    <tr>
      <th><?php echo MODULE_CONTENT_GDPR_SITE_DETAILS_RECENT_LOGON; ?></th>
      <td><?php echo $port_my_data['YOU']['SITE']['LOGONS']['MOSTRECENT']; ?></td>
    </tr> 
    <tr>
      <th><?php echo MODULE_CONTENT_GDPR_SITE_DETAILS_NUMBER_REVIEWS; ?></th>
      <td><?php echo $port_my_data['YOU']['REVIEW']['COUNT']; ?></td>
    </tr> 
    <tr>
      <th><?php echo MODULE_CONTENT_GDPR_SITE_DETAILS_NUMBER_NOTIFICATIONS; ?></th>
      <td><?php echo $port_my_data['YOU']['NOTIFICATION']['COUNT']; ?></td>
    </tr>    
  </table>
</div>

<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
?>
