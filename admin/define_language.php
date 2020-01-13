<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  function tep_opendir($path) {
    $path = rtrim($path, '/') . '/';

    $exclude_array = array('.', '..', '.DS_Store', 'Thumbs.db');

    $result = array();

    if ($handle = opendir($path)) {
      while (false !== ($filename = readdir($handle))) {
        if (!in_array($filename, $exclude_array)) {
          $file = array('name' => $path . $filename,
                        'is_dir' => is_dir($path . $filename),
                        'writable' => tep_is_writable($path . $filename),
                        'size' => filesize($path . $filename),
                        'last_modified' => strftime(DATE_TIME_FORMAT, filemtime($path . $filename)));

          $result[] = $file;

          if ($file['is_dir'] == true) {
            $result = array_merge($result, tep_opendir($path . $filename));
          }
        }
      }

      closedir($handle);
    }

    return $result;
  }

  if (!isset($_GET['lngdir'])) $_GET['lngdir'] = $language;

  $languages_array = array();
  $languages = tep_get_languages();
  $lng_exists = false;
  for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
    if ($languages[$i]['directory'] == $_GET['lngdir']) $lng_exists = true;

    $languages_array[] = array('id' => $languages[$i]['directory'],
                               'text' => $languages[$i]['name']);
  }

  if (!$lng_exists) $_GET['lngdir'] = $language;

  if (isset($_GET['filename'])) {
    $file_edit = realpath(DIR_FS_CATALOG_LANGUAGES . $_GET['filename']);

    if (substr($file_edit, 0, strlen(DIR_FS_CATALOG_LANGUAGES)) != DIR_FS_CATALOG_LANGUAGES) {
      tep_redirect(tep_href_link('define_language.php', 'lngdir=' . $_GET['lngdir']));
    }
  }

  $action = $_GET['action'] ?? '';

  if (tep_not_null($action)) {
    switch ($action) {
      case 'save':
        if (isset($_GET['lngdir']) && isset($_GET['filename'])) {
          $file = DIR_FS_CATALOG_LANGUAGES . $_GET['filename'];

          if (file_exists($file) && tep_is_writable($file)) {
            $new_file = fopen($file, 'w');
            $file_contents = stripslashes($_POST['file_contents']);
            fwrite($new_file, $file_contents, strlen($file_contents));
            fclose($new_file);
          }

          tep_redirect(tep_href_link('define_language.php', 'lngdir=' . $_GET['lngdir']));
        }
        break;
    }
  }

  require('includes/template_top.php');
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?php echo HEADING_TITLE; ?></h1>
    </div>
    <div class="col-sm-4 text-right align-self-center">
      <?php 
      echo tep_draw_form('lng', 'define_language.php', '', 'get'); 
      echo tep_draw_pull_down_menu('lngdir', $languages_array, $_GET['lngdir'], 'class="form-control" onchange="this.form.submit();"');
      echo tep_hide_session_id();
      echo '</form>'; 
      ?>
    </div>
  </div>
    
<?php
  if (isset($_GET['lngdir']) && isset($_GET['filename'])) {
    $file = DIR_FS_CATALOG_LANGUAGES . $_GET['filename'];

    if (file_exists($file)) {
      $file_array = file($file);
      $contents = implode('', $file_array);

      $file_writeable = true;
      if (!tep_is_writable($file)) {
        $file_writeable = false;
        $messageStack->reset();
        $messageStack->add(sprintf(ERROR_FILE_NOT_WRITEABLE, $file), 'error');
        echo $messageStack->output();
      }
      
      echo tep_draw_form('language', 'define_language.php', 'lngdir=' . $_GET['lngdir'] . '&filename=' . $_GET['filename'] . '&action=save');
?>
            
        <div class="alert alert-info mb-3">
          <p class="lead mb-0"><?php echo $_GET['filename']; ?></p>
        </div>
      
        <div class="form-group row">
          <div class="col">
            <?php
            echo tep_draw_textarea_field('file_contents', 'soft', '80', '25', $contents, (($file_writeable) ? '' : 'readonly') . ' class="form-control" id="inputFile"');
            ?>
          </div>
        </div>
        
        <?php 
        if ($file_writeable == true) { 
          echo tep_draw_bootstrap_button(IMAGE_SAVE, 'fas fa-pen-alt', null, 'primary', null, 'btn-success btn-lg btn-block');
          echo tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('define_language.php', 'lngdir=' . $_GET['lngdir']), null, null, 'btn-light mt-2'); 
        } 
        else { 
          echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('define_language.php', 'lngdir=' . $_GET['lngdir']), null, null, 'btn-light btn-lg btn-block'); 
        } 
        ?>
        
      </form>
          
      <div class="alert alert-info mt-3">
        <?php echo TEXT_EDIT_NOTE; ?>
      </div>

<?php
    } else {
?>
      <div class="alert alert-warning text-center">
        <?php echo TEXT_FILE_DOES_NOT_EXIST; ?>
      </div>

<?php
      echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('define_language.php', 'lngdir=' . $_GET['lngdir']), null, null, 'btn-warning btn-block btn-lg xxx text-white');
    }
  } else {
    $filename = $_GET['lngdir'] . '.php';
    $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
?>
  
  <div class="table-responsive">  
    <table class="table table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th><?php echo TABLE_HEADING_FILES; ?></th>
          <th class="text-center"><?php echo TABLE_HEADING_WRITABLE; ?></th>
          <th class="text-right"><?php echo TABLE_HEADING_LAST_MODIFIED; ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><a href="<?php echo tep_href_link('define_language.php', 'lngdir=' . $_GET['lngdir'] . '&filename=' . $filename); ?>"><?php echo $filename; ?></a></td>
          <td class="text-center"><?php echo ((tep_is_writable(DIR_FS_CATALOG_LANGUAGES . $filename) == true) ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>'); ?></td>
          <td class="text-right"><?php echo strftime(DATE_TIME_FORMAT, filemtime(DIR_FS_CATALOG_LANGUAGES . $filename)); ?></td>
        </tr>
<?php
    foreach (tep_opendir(DIR_FS_CATALOG_LANGUAGES . $_GET['lngdir']) as $file) {
      if (substr($file['name'], strrpos($file['name'], '.')) == $file_extension) {
        $filename = substr($file['name'], strlen(DIR_FS_CATALOG_LANGUAGES));

        echo '<tr>';
          echo '<td><a href="' . tep_href_link('define_language.php', 'lngdir=' . $_GET['lngdir'] . '&filename=' . $filename) . '">' . substr($filename, strlen($_GET['lngdir'] . '/')) . '</a></td>';
          echo '<td class="text-center">' . (($file['writable'] == true) ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>') . '</td>';
          echo '<td class="text-right">' . $file['last_modified'] . '</td>';
        echo '</tr>';
      }
    }
?>
        </tr>
      </tbody>
    </table>
  </div>
<?php
  }

  require('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
