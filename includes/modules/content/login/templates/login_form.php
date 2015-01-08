<div class="login-form <?php echo (MODULE_CONTENT_LOGIN_FORM_CONTENT_WIDTH == 'Half') ? 'col-sm-6' : 'col-sm-12'; ?>">
  <div class="panel panel-success">
    <div class="panel-body">
      <h2><?php echo MODULE_CONTENT_LOGIN_HEADING_RETURNING_CUSTOMER; ?></h2>

      <p class="alert alert-success"><?php echo MODULE_CONTENT_LOGIN_TEXT_RETURNING_CUSTOMER; ?></p>

      <?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL'), 'post', '', true); ?>

        <div class="form-group">
          <?php echo tep_draw_input_field('email_address', NULL, 'autofocus="autofocus" required id="inputEmail" placeholder="' . ENTRY_EMAIL_ADDRESS . '"', 'email'); ?>
        </div>

        <div class="form-group">
          <?php echo tep_draw_password_field('password', NULL, 'required aria-required="true" id="inputPassword" placeholder="' . ENTRY_PASSWORD . '"'); ?>
        </div>

        <p class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_LOGIN, 'glyphicon glyphicon-log-in', null, 'primary', NULL, 'btn-success btn-block'); ?></p>

      </form>
    </div>
  </div>

  <p><?php echo '<a class="btn btn-default" role="button" href="' . tep_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL') . '">' . MODULE_CONTENT_LOGIN_TEXT_PASSWORD_FORGOTTEN . '</a>'; ?></p>

</div>
