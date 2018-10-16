<div class="col-sm-<?php echo $content_width; ?> cm-login-form ">

  <p class="alert alert-success"><?php echo MODULE_CONTENT_LOGIN_TEXT_RETURNING_CUSTOMER; ?></p>

  <?php echo tep_draw_form('login', tep_href_link('login.php', 'action=process', 'SSL'), 'post', '', true); ?>
   
  <div class="form-group row">
    <div class="col-sm-12">
      <?php 
      echo tep_draw_input_field('email_address', NULL, 'required aria-required="true" id="inputEmail" autocomplete="username email" placeholder="' . MODULE_CONTENT_LOGIN_ENTRY_EMAIL_ADDRESS_PLACEHOLDER . '"', 'email');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-sm-12">
      <?php 
      echo tep_draw_input_field('password', NULL, 'required aria-required="true" id="inputPassword" autocomplete="new-password" placeholder="' . MODULE_CONTENT_LOGIN_ENTRY_PASSWORD_PLACEHOLDER . '"', 'password');
      echo FORM_REQUIRED_INPUT;
      ?>
    </div>
  </div>

  <p class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_LOGIN, 'fas fa-sign-in-alt', null, 'primary', NULL, 'btn-success btn-block'); ?></p>

  </form>

  <p><?php echo '<a class="btn btn-link" role="button" href="' . tep_href_link('password_forgotten.php', '', 'SSL') . '">' . MODULE_CONTENT_LOGIN_TEXT_PASSWORD_FORGOTTEN . '</a>'; ?></p>

</div>

<?php
/*
  Copyright (c) 2018, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
?>
