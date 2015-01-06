<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/

defined('AAS') or die;
?>
<div class="overlay" id="contactme" style="display:none">
  <div class="container">
    <div class="top-loriza" style="border-bottom:1px dashed lightGray">
      <span class="close-button button applyButton"><?php echo AAS_CONTACTME_CLOSE; ?></span>
    </div>
    <div class="clear margin-20-auto"></div>
    <p><?php echo AAS_CONTACTME_HELLO_MY_NAME_IS; ?><a target="_blank" href="http://www.linkedin.com/pub/john-barounis/31/430/617">John Barounis</a>,<?php echo AAS_CONTACTME_I_AM_THE_CREATOR_OF_AAS; ?></p>

    <div class="content" style="text-align:center;">
      <div class="clear margin-10-auto"></div>
      <p class="para"><?php echo AAS_CONTACTME_TEXT_1; ?></p>
      <p class="para"><?php echo AAS_CONTACTME_TEXT_2; ?><a href="mailto:jbqwerty@gmail.com">jbqwerty@gmail.com</a></p>
      <div class="clear margin-30-auto"></div>
      <input type="text" class="lfor" style="width:400px;" id="contactme-name" placeholder="Your Name" value="<?php echo STORE_OWNER; ?>">
      <div class="clear margin-10-auto"></div>
      <input type="email" class="lfor" style="width:400px;" id="contactme-email" placeholder="Your Email" value="<?php echo STORE_OWNER_EMAIL_ADDRESS; ?>">
      <div class="clear margin-10-auto"></div>
      <input type="text" class="lfor" style="width:400px;" id="contactme-subject" placeholder="Subject" value="">
      <div class="clear margin-10-auto"></div>
      <textarea class="lfor contactme-textarea" id="contactme-message" placeholder="Message"></textarea>
      <div class="clear margin-20-auto"></div>
      <button id="contactme-send-message-button" class="applyButton"><?php echo AAS_CONTACTME_BUTTON_SEND_MESSAGE; ?></button>
      <div class="clear margin-20-auto"></div>
      <p class="note"><?php echo AAS_CONTACTME_TEXT_NOTE; ?></p>
    </div>
  </div>
  <script>
  $(function(){
    $('#contactmebutton').on('click',function(){
      $('#contactme').fadeIn('fast',function(){
        toggleBodyVerticalScrollbar(0);
      });
    });
    $('#contactme close-button').on('click',function(){
      $('#contactme').fadeOut('fast',function(){
        toggleBodyVerticalScrollbar(1);
      });
    });
    $('#contactme-send-message-button').click(function(){
      var cdata={
        name:$('#contactme-name').val(),
        email:$('#contactme-email').val(),
        subject:$('#contactme-subject').val(),
        message:$('#contactme-message').val()
      }
      if(cdata.name=='' || cdata.email=='' || cdata.subject=='' || cdata.message==''){
        aas.dialog.open('dialog-error','<?php echo AAS_CONTACTME_EMPTY_FIELDS_FOUND; ?>');
      }else{
        aas.dialog.open('dialog-processing');
        aas.ajax.do({data:cdata,url:config.url.plugins+'contactme/aas.php'},function(msg){
          aas.dialog.close('dialog-processing');
          if(msg=='1'){
            aas.dialog.open('dialog-success','<?php echo AAS_CONTACTME_MESSAGE_SUCCESSFULLY_SENT; ?>');
            $('#contactme-subject').val('');
            $('#contactme-message').val('');
          }else aas.dialog.open('dialog-error','<?php echo AAS_CONTACTME_MESSAGE_COULD_NOT_BE_SENT; ?>');
        });
      }
    });
  });
  </script>
</div>
