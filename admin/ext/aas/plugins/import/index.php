<?php
/*

  Alternative Administration System
  Version: 0.3
  Created By John Barounis, johnbarounis.com
  Website: http://www.alternative-administration-system.com

  Information: imports products changes via drag n drop

*/

defined('AAS') or die;
?>
<div id="overlayWindow-import">
  <div class="container">
    <div class="top-lorida"><?php echo AAS_TEXT_IMPORT_TITLE; ?> <span style="font-size:10px;"><?php echo AAS_TEXT_IMPORT_VERSION; ?></span></div>
    <div class="file-import-panel-warning-wrapper">
      <span class="file-import-panel-warning-text"><?php echo AAS_TEXT_IMPORT_WARNING; ?></span>
    </div>
    <div class="file-import-panel-wrapper">
      <div id="dropbox">
        <div class="dropbox-text-wrapper">
          <h2><?php echo AAS_TEXT_IMPORT_DRAG_N_DROP; ?></h2>
          <div class="dropbox-text-1"><?php echo AAS_TEXT_IMPORT_SUPPORTED_FILES; ?></div>
          <div class="dropbox-text-2"><?php echo AAS_TEXT_IMPORT_WARNING_MESSAGE; ?></div>
          <div class="dropbox-text-2"><?php echo AAS_TEXT_IMPORT_WARNING_MESSAGE_1; ?></div>
        </div>
      </div>
      <div id="file-import-data"></div>
    </div>
    <div id="progress"></div>
    <div class="buttons-lorida">
      <button id="file-import-submit" class="file-import-button button-green nodisplay" data-title="<?php echo AAS_TEXT_IMPORT_TOOLTIP_SUBMIT_CHANGES; ?>"><?php echo AAS_TEXT_IMPORT_SUBMIT_CHANGES; ?></button>
      <button id="file-import-new" class="file-import-button button-blue nodisplay" data-title="<?php echo AAS_TEXT_IMPORT_TOOLTIP_UPLOAD_NEW_FILE; ?>"><?php echo AAS_TEXT_IMPORT_UPLOAD_NEW_FILE; ?></button>
      <button id="file-import-close" class="file-import-button button-red" data-title="<?php echo AAS_TEXT_IMPORT_TOOLTIP_CLOSE; ?>"><?php echo AAS_TEXT_IMPORT_CLOSE; ?></button>
    </div>
  </div>
</div>
<script>
$(function(){
  $('#dropbox').filedrop({
    xhrParams:{'X-AAS':config.ajaxToken},
    paramname:'file',
    maxfiles: 1,
    maxfilesize: 6,
    url: 'ext/aas/plugins/import/aas.php',
    allowedfiletypes: [],   // filetypes allowed by Content-Type.  Empty array means no restrictions
        allowedfileextensions: ['.csv','.txt','.xls'], // file extensions allowed. Empty array means no restrictions
        error: function(err, file) {
        aas.dialog.close('dialog-processing');
      switch(err) {
        case 'BrowserNotSupported':
          aas.dialog.open('dialog-error',"<?php echo AAS_TEXT_IMPORT_BROWSER_NOT_SUPPORTED; ?>");
          break;
        case 'TooManyFiles':
          aas.dialog.open('dialog-error',"<?php echo AAS_TEXT_IMPORT_TOO_MANY_FILES; ?>");
          break;
        case 'FileTooLarge':
          aas.dialog.open('dialog-error',file.name+"<?php echo AAS_TEXT_IMPORT_FILE_TOO_LARGE; ?>");
          break;
         case 'FileTypeNotAllowed':
          aas.dialog.open('dialog-error',"<?php echo AAS_TEXT_IMPORT_FILETYPE_NOT_ALLOWED; ?>");
          break;
        case 'FileExtensionNotAllowed':
          aas.dialog.open('dialog-error',"<?php echo AAS_TEXT_IMPORT_FILE_EXTENSION_NOT_ALLOWED; ?>");
          break;
        default:
          break;
      }
    },
     uploadFinished: function(i, file, response, time) {
      aas.dialog.close('dialog-processing');
      if(response=='error') aas.dialog.open('dialog-error',"<?php echo AAS_TEXT_IMPORT_ERROR_PARSING_DATA; ?>");
      else{
         $('#dropbox').hide();
         $('#file-import-data').html(response);
         $('#file-import-submit, #file-import-new').show();
      }
      $('#progress').fadeOut('slow');
    },
    beforeEach: function(file){

    },
    uploadStarted:function(i, file, len){
      aas.dialog.open('dialog-processing');
    },
    progressUpdated: function(i, file, progress) {

    },
    globalProgressUpdated: function(progress) {
       $('#progress').show().css({width:progress+'%'});
    }
  });

  $('#file-import-submit').on('click',function(){
    var fields=[],fields_input=$('table#tbl-file-import thead tr th input'),rows=$('table#tbl-file-import tbody tr'),rows_data=[];
    fields_input.each(function(index){
      fields.push($.trim($(this).val()));
    });
    rows.each(function(index){
      var tds=[];
      $(this).children('td').each(function(){
        tds.push($.trim($(this).text()));
      });
      rows_data.push(tds);
    });

    if(fields.length>0 && rows_data.length>0){
      aas.dialog.open('dialog-processing',"<?php echo AAS_TEXT_IMPORT_PLEASE_WAIT_WHILE_UPDATING; ?>");
      aas.ajax.do({
        data:{fields:JSON.stringify(fields),values:JSON.stringify(rows_data),item:'import'},
        url:config.url.actions,
        dataType:'json'
      },function(msg){
        aas.dialog.close('dialog-processing');
        if(msg=='1') aas.dialog.open('dialog-success','<div class="clear margin-20-auto">'+"<?php echo AAS_TEXT_IMPORT_SUCCESSFULLY_UPDATED; ?>"+'</div><input class="applyButton" type="button" value="'+"<?php echo AAS_TEXT_IMPORT_RELOAD_NOW; ?>"+'" onClick="window.location.reload()" >');
        else aas.dialog.open('dialog-error',"<?php echo AAS_TEXT_IMPORT_SOMETHING_WENT_WRONG; ?>");
      },function(msg){
        aas.dialog.close('dialog-processing');
        aas.dialog.open('dialog-error',"<?php echo AAS_TEXT_IMPORT_COULD_NOT_UPDATE_PRODUCTS_DATA; ?>");
      });
    }else aas.dialog.open('dialog-error',"<?php echo AAS_TEXT_IMPORT_EMPTY_DATA; ?>");
  });

  $('#file-import-close').on('click touchend',function(){
    $('#tooltip').hide();
    $('#overlayWindow-import').hide();
  });
  $('#file-import-new').on('click',function(){
    $('#file-import-data').html('');
    $('#dropbox').fadeIn();
  });
});
</script>
