<div class="create-account-link <?php echo (MODULE_CONTENT_CREATE_ACCOUNT_LINK_CONTENT_WIDTH == 'Half') ? 'col-sm-6' : 'col-sm-12'; ?>">
  <div class="panel panel-info">
    <div class="panel-body">
      <h2><?php echo MODULE_CONTENT_LOGIN_HEADING_NEW_CUSTOMER; ?></h2>

      <p class="alert alert-info"><?php echo MODULE_CONTENT_LOGIN_TEXT_NEW_CUSTOMER; ?></p>
      <p><?php echo MODULE_CONTENT_LOGIN_TEXT_NEW_CUSTOMER_INTRODUCTION; ?></p>

      <p class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'glyphicon glyphicon-chevron-right', tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'), null, null, 'btn-primary btn-block'); ?></p>
    </div>
  </div>
</div>
