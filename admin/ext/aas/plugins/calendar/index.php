<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

*/

defined('AAS') or die;
?>
<div class="overlay" id="calendar">
  <div class="container">
    <div class="top-loriza" style="border-bottom:1px dashed lightGray">
      <span style="font-size:20px;"><?php echo AAS_CALENDAR; ?></span>
      <span class="close-button button applyButton"><?php echo AAS_CALENDAR_CLOSE; ?></span>
    </div>
    <div id="calendar-elem"></div>
  </div>
  <script>
  translate.AAS_CALENDAR_EVENT_NOT_ADDED="<?php echo AAS_CALENDAR_EVENT_NOT_ADDED; ?>";
  translate.AAS_CALENDAR_EVENT_NOT_DELETED="<?php echo AAS_CALENDAR_EVENT_NOT_DELETED; ?>";
  translate.AAS_CALENDAR_EVENT_NOT_UPDATED="<?php echo AAS_CALENDAR_EVENT_NOT_UPDATED; ?>";
  translate.AAS_CALENDAR_SUCCESSFULLY_UPDATED_EVENT="<?php echo AAS_CALENDAR_SUCCESSFULLY_UPDATED_EVENT; ?>";
  translate.AAS_CALENDAR_SUCCESSFULLY_DELETED_EVENT="<?php echo AAS_CALENDAR_SUCCESSFULLY_DELETED_EVENT; ?>";
  translate.AAS_CALENDAR_EDIT_EVENT="<php echo AAS_CALENDAR_EDIT_EVENT; ?>";
  translate.AAS_CALENDAR_NEW_EVENT="<?php echo AAS_CALENDAR_NEW_EVENT; ?>";
  </script>
  <script type="text/javascript" src="ext/aas/plugins/calendar/js/calendar.js"></script>
  <script type="text/javascript" src='ext/aas/plugins/calendar/js/fullcalendar.min.js'></script>
</div>
