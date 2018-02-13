<div class="login-form <?php echo (MODULE_CONTENT_LOGIN_FORM_CONTENT_WIDTH == 'Half') ? 'col-sm-6' : 'col-sm-12'; ?>">
  <div class="panel panel-success">
    <div class="panel-body">
      <h2><?php echo MODULE_CONTENT_LOGIN_HEADING_RETURNING_CUSTOMER; ?></h2>

      <p class="alert alert-success"><?php echo MODULE_CONTENT_LOGIN_TEXT_RETURNING_CUSTOMER; ?></p>

      <?php echo tep_draw_form('login', tep_href_link('login.php', 'action=process', 'SSL'), 'post', '', true); ?>

        <div class="form-group has-feedback">
          <?php 
          echo tep_draw_input_field('email_address', NULL, 'autofocus="autofocus" required aria-required="true" id="inputEmail" placeholder="' . MODULE_CONTENT_LOGIN_ENTRY_EMAIL_ADDRESS_PLACEHOLDER . '"', 'email');
          echo FORM_REQUIRED_INPUT;
          ?>
        </div>

        <div class="form-group has-feedback">
          <?php 
          echo tep_draw_input_field('password', NULL, 'required aria-required="true" id="inputPassword" autocomplete="new-password" placeholder="' . MODULE_CONTENT_LOGIN_ENTRY_PASSWORD_PLACEHOLDER . '"', 'password');
          echo FORM_REQUIRED_INPUT;
          ?>
        </div>

        <p class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_LOGIN, 'fas fa-sign-in-alt', null, 'primary', NULL, 'btn-success btn-block'); ?></p>

      </form>
    </div>
  </div>

  <p><?php echo '<a class="btn btn-default" role="button" href="' . tep_href_link('password_forgotten.php', '', 'SSL') . '">' . MODULE_CONTENT_LOGIN_TEXT_PASSWORD_FORGOTTEN . '</a>'; ?></p>

</div>
