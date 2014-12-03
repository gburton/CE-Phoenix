<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

  Information: Displays cities local analog & digital time and date.

*/

defined('AAS') or die;
?>
<div class="overlay" id="clocks">
  <div class="container">
  <div class="close"><a href="#"><img style="opacity:0.3" src="ext/aas/images/remove_white_no_round_1.png" alt="Close"></a></div>
<?php if(is_array($clocks) && count($clocks)>0 ){ ?>
    <div class="module_content">
      <div id="clocks_module_content"></div>
      <div class="clear"></div>
    </div>
    <script src="ext/aas/plugins/clocks/js/jQueryRotateCompressed.js" type="text/javascript"></script>
    <script src="ext/aas/plugins/clocks/js/clocks.js" type="text/javascript"></script>
    <script>
    $(function(){

      aas.clocks.renderTo='#clocks_module_content';
      <?php foreach($clocks as $keyClock => $valClock) echo 'aas.clocks.create("'.$keyClock.'%'.($valClock>0?'+':'').$valClock.'",'.$valClock.');'; ?>

      $('#clocks .close a').on('click',function(){
        $('#clocks').fadeOut('fast',function(){
          toggleBodyVerticalScrollbar(0);
        });
        return false;
      });
      $('#clocksbutton').click(function(){
        $('#clocksPreviewBox').hide().html('');
        $('#clocks').fadeIn('fast',function(){
          toggleBodyVerticalScrollbar(0);
        });

      });
      $('#clocksbutton').hover(function(){
         var $_this=$(this);
         var emptyBox= $('#clocksPreviewBox');
         emptyBox.html($('#clocks_module_content').html());
         emptyBox.css({top:$_this.position().top+$_this.outerHeight()+15,left:($(window).width()-emptyBox.width())/2}).stop().fadeIn('fast');
      },function(){
        $('#clocksPreviewBox').hide().html('');
      });

    });
    </script>
<?php } ?>
  </div>
</div>
<div id="clocksPreviewBox"></div>
