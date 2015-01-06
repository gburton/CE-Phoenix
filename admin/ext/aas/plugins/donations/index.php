<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/

defined('AAS') or die;
?>
<div class="overlay" id="donations">
  <div class="container">
    <div class="top-loriza" style="border-bottom:1px dashed lightGray">
        <span style="font-size:20px;"><?php echo AAS_DONATIONS_TITLE; ?></span>
        <span class="close-button button applyButton"><?php echo AAS_DONATIONS_CLOSE; ?></span>
    </div>
    <div class="clear margin-20-auto"></div>
    <div class="content">
      <p><?php echo AAS_DONATIONS_TEXT_1; ?></p>
      <p><?php echo AAS_DONATIONS_TEXT_2; ?></p>
      <p><?php echo AAS_DONATIONS_TEXT_3; ?></p>
      <p><?php echo AAS_DONATIONS_TEXT_4; ?></p>
      <p><?php echo AAS_DONATIONS_TEXT_5; ?></p>
    </div>
    <div class="clear margin-20-auto"></div>
    <div class="donation_box">
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="hosted_button_id" value="4PNZBV3YS6YTJ">
      <input type="hidden" name="on0" value="amount">
      <select name="os0">
        <option value="29">$29.00</option>
        <option value="59">$59.00</option>
        <option value="99">$99.00</option>
        <option value="129">$129.00</option>
        <option value="169">$169.00</option>
        <option value="229">$229.00</option>
        <option value="269">$269.00</option>
        <option value="329">$329.00</option>
        <option value="369">$369.00</option>
        <option value="429">$429.00</option>
      </select>
      <input type="hidden" name="currency_code" value="USD">
      <input type="submit" value="Make a Donation" class="applyButton" style="font-size:18px" />
      </form>
    </div>
    <div class="donation_box">
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="hosted_button_id" value="6EZ89Q6ZMTUPJ">
      <input type="hidden" name="on0" value="prices">
      <select name="os0">
        <option value="29">€29.00</option>
        <option value="59">€59.00</option>
        <option value="99">€99.00</option>
        <option value="129">€129.00</option>
        <option value="169">€169.00</option>
        <option value="229">€229.00</option>
        <option value="269">€269.00</option>
        <option value="329">€329.00</option>
        <option value="369">€369.00</option>
        <option value="429">€429.00</option>
      </select>
      <input type="hidden" name="currency_code" value="EUR">
      <input type="submit" value="Make a Donation" class="applyButton" style="font-size:18px" />
      </form>
    </div>
    <div class="donation_box">
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="57JBVNSZQPVQ6">
        <table>
        <tr><td><input type="hidden" name="on0" value="AAS monthly donation"></td></tr>
        <tr><td>
        <select name="os0" class="form-control" required="required" onchange="this.form.submit()">
          <option value="">Select AAS monthly donation!</option>
          <option value="1 Dollar">1 Dollar : $1.00 USD - monthly</option>
          <option value="10 Dollars">10 Dollars : $10.00 USD - monthly</option>
          <option value="20 Dollars">20 Dollars : $20.00 USD - monthly</option>
          <option value="30 Dollars">30 Dollars : $30.00 USD - monthly</option>
          <option value="40 Dollars">40 Dollars : $40.00 USD - monthly</option>
          <option value="50 Dollars">50 Dollars : $50.00 USD - monthly</option>
          <option value="60 Dollars">60 Dollars : $60.00 USD - monthly</option>
        </select>
        </td></tr>
        </table>
        <input type="hidden" name="currency_code" value="USD">
        <!--<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">-->
      </form>
    </div>
    <div class="clear margin-20-auto"></div>
    
    <?php echo AAS_DONATIONS_OR; ?> <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=HNWC57MT3GBAC"><?php echo AAS_DONATIONS_DONATE_CUSTOM_AMOUNT; ?></a>
  </div>
  <script>
  $(function(){
    $('#donations .close-button').on('click touchend',function(){
      $('#donations').fadeOut('fast',function(){
        toggleBodyVerticalScrollbar(1);
      });
    });
  });
  </script>
</div>
