<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $section_language_array = array('undefined' => SECTION_UNDEFINED_TITLE,
                                  'shop' => SECTION_STORE_TITLE,
                                  'admin' => SECTION_ADMIN_TITLE,
                                  'payment' => SECTION_PAYMENT_MODULES_TITLE);

  function is_email_templated_file($file, $search = 'tep_mail(') {
    $fp = file($file);
 
    for ($idx = 0; $idx < count($fp); ++$idx) {
      if (!(strpos($fp[$idx], $search) === false))
        return true;
    }  
    return false;
  }

  function get_php_files($dirname = '.', $exeptions = array()) {

    $dirname = realpath($dirname);

    if (substr($dirname, -1) != '/') {
      $dirname.= '/';
    }

    $dirstack = array($dirname);
    $cutFrom = strrpos(substr($dirname, 0, -1), '/')+1;
    $filestoadd = array();

    while (!empty($dirstack)) {
      $currentdir = array_pop($dirstack);
      $dir = dir($currentdir);

      while (false !== ($node = $dir->read())) {
        if (($node == '..') || ($node == '.')) {
            continue;
        }
        if (is_dir($currentdir . $node)) {
          array_push($dirstack, $currentdir . $node . '/');
        }
        if (is_file($currentdir . $node) && strpos($node, '.php') !== false && !isset($exeptions[$node])) {
          $filestoadd[] = $currentdir . $node;
        }
      }
    }
    return $filestoadd;
  }

  $filestoadd = get_php_files(DIR_FS_DOCUMENT_ROOT, array_flip(array('general.php', 'email_previews.php')));
  $mailfiles = array();

  foreach ($filestoadd as $file) {
    if (is_email_templated_file($file)) {
      $mailfiles[] = $file;
    }
  }

  // email templates array
  $et = array();
  $sections['undefined'] = '';

  // search both admin and shop modules
  $result = glob(DIR_FS_DOCUMENT_ROOT . 'includes/modules/pages/tp_email_*.php');
  foreach ($result as $key => $value) {
    include($value);
    $template_page_class = str_replace('.php', '', basename($value));

    if ( class_exists($template_page_class) ) {
      $template_page = new $template_page_class();

      foreach ($mailfiles as $mkey => $mvalue) {
        if (is_email_templated_file($mvalue, 'oscTemplate->getContent(\'' . $template_page->group)) {

          //unregist templated info
          unset($mailfiles[$mkey]);
          break;
        }
      }

      if (method_exists($template_page, 'info') && is_array($template_page->info())) {
        $ti = $template_page->info();

        $et[$ti['section']][] = array('class' => $template_page_class,
                                      'title' => $ti['title'],
                                      'description' => $ti['description'],
                                      'version' => $ti['version']);
        //regist a new section
        if (!isset($sections[$ti['section']])) {
          $sections[$ti['section']] = '';
        }

      } else {
        $et['undefined'][] = array('class' => $template_page_class,
                                   'title' => $template_page->group,
                                   'description' => $value,
                                   'version' => '');
      }
    }
  }

  ksort($sections);
  $sections = array_keys($sections);

  require(DIR_WS_INCLUDES . 'template_top.php');
?>
<!-- temporary non standard stylesheet for hovers and links -->
<style>
.hover {background: #fff888}
.table a:link {color: #0000FF;}
.table a:visited, .nontemplated {color: #C9C9C9;}
.table a:hover {color: #55AAFF;}
</style>

<table width="100%">
  <tr>
    <td><table cellspacing="0" cellpadding="4" border="0" width="100%">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><img border="0" width="57" height="40" alt="" src="images/pixel_trans.gif"></td>
          </tr>
    </table></td>
  </tr>

  <tr>
    <td>
      <table class="table" cellspacing="2" cellpadding="2" border="0" width="100%">
        <thead>
        <tr class="dataTableHeadingRow">
          <th class="dataTableHeadingContent" width="10%"><?php echo TABLE_HEADING_SECTION; ?></th>
          <th class="dataTableHeadingContent" width="20%"><?php echo TABLE_HEADING_HTML_MODULE; ?></th>
          <th class="dataTableHeadingContent" width="20%"><?php echo TABLE_HEADING_TEXT_MODULE; ?></th>
          <th class="dataTableHeadingContent" align="center" width="10%"><?php echo TABLE_HEADING_VERSION; ?></th>
          <th class="dataTableHeadingContent" width="40%"><?php echo TABLE_HEADING_DESCRIPTION; ?></th>
        </tr>
        </thead>
        <tbody>
<?php

  for ($i=0; $i<count($sections); $i++) {
    $counted = count($et[$sections[$i]]);
?>
        <tr class="dataTableRow">
          <td <?php echo ($counted>1 ? 'rowspan="' . $counted . '" ' : ''); ?>width="10%"><?php echo (isset($section_language_array[$sections[$i]]) ? $section_language_array[$sections[$i]] : $sections[$i]); ?></td>
<?php
     if (isset($et[$sections[$i]]) && $counted>0) {
        for ($n=0; $n<$counted; $n++) {
          if ($n>0) {
?>
        <tr class="dataTableRow">
<?php
          }
?>
          <td><?php echo (method_exists($et[$sections[$i]][$n]['class'], 'preview') ? '<a href="' . tep_href_link('email_viewer.php', 'page=' . $et[$sections[$i]][$n]['class'] . '&html') .'" target="_blank" title="' . TITLE_PREVIEW_HTML . '">' . $et[$sections[$i]][$n]['title'] . '</a>' : $et[$sections[$i]][$n]['class']); ?></td>
          <td><?php echo (method_exists($et[$sections[$i]][$n]['class'], 'preview') ? '<a href="' . tep_href_link('email_viewer.php', 'page=' . $et[$sections[$i]][$n]['class']) .'" target="_blank" title="' . TITLE_PREVIEW_TEXT . '">' . $et[$sections[$i]][$n]['title'] . '</a>' : $et[$sections[$i]][$n]['class']); ?></td>
          <td align="center"><?php echo $et[$sections[$i]][$n]['version']; ?></td>
          <td><?php echo $et[$sections[$i]][$n]['description']; ?></td>
        </tr>
<?php
      }
     } else {
?>
          <td colspan="4"></td>
        </tr>
<?php
     }
  }
      foreach ($mailfiles as $key => $value) {
?>
        <tr class="dataTableRow">
          <td></td>
          <td class="nontemplated" colspan="3" align="center"><?php echo TITLE_NON_TEMPLATED_FILE; ?></td>
          <td><?php echo $value; ?></td>
        </tr>
<?php
      }
?>
        </tbody>
      </table>
    </td>
  </tr>
</table>

<script>
function findBlocks(theTable) {
  if ($(theTable).data('hasblockrows') == null) {
    console.log('findBlocks');
    var rows = $(theTable).find('tr');
    for (var i = 0; i < rows.length;) {
      var firstRow = rows[i];
      var maxRowspan = 1;
      $(firstRow).find('td').each(function () {
          var attr = parseInt($(this).attr('rowspan') || '1', 10)
          if (attr > maxRowspan) maxRowspan = attr;
      });
      maxRowspan += i;
      var blockRows = [];
      for (; i < maxRowspan; i++) {
          $(rows[i]).data('blockrows', blockRows);
          blockRows.push(rows[i]);
      }
    }
    $(theTable).data('hasblockrows', 1);
  }
}

$(".table td").hover(function () {
  $el = $(this);
  $.each($el.parent().data('blockrows'), function () {
      $(this).find('td').addClass('hover');
  });
}, function () {
  $el = $(this);
  $.each($el.parent().data('blockrows'), function () {
      $(this).find('td').removeClass('hover');
  });
});

findBlocks($('.table'));
</script>

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
