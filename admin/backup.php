<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  // Used in the "Backup Manager" to compress backups
  define('LOCAL_EXE_GZIP', '/usr/bin/gzip');
  define('LOCAL_EXE_GUNZIP', '/usr/bin/gunzip');
  define('LOCAL_EXE_ZIP', '/usr/bin/zip');
  define('LOCAL_EXE_UNZIP', '/usr/bin/unzip');

  $action = $_GET['action'] ?? '';

  if (tep_not_null($action)) {
    switch ($action) {
      case 'forget':
        tep_db_query("DELETE FROM configuration WHERE configuration_key = 'DB_LAST_RESTORE'");

        $messageStack->add_session(SUCCESS_LAST_RESTORE_CLEARED, 'success');

        tep_redirect(tep_href_link('backup.php'));
        break;
      case 'backupnow':
        tep_set_time_limit(0);
        $backup_file = 'db_' . DB_DATABASE . '-' . date('YmdHis') . '.sql';
        $fp = fopen(DIR_FS_BACKUP . $backup_file, 'w');

        $schema = sprintf(<<<'EOSQL'
# osCommerce, Open Source E-Commerce Solutions
# http://www.oscommerce.com
#
# Database Backup For %s
# Copyright (c) %d %s
#
# Database: %s
# Database Server: %s
#
# Backup Date: %s

EOSQL
, STORE_NAME, date('Y'), STORE_OWNER, DB_DATABASE, DB_SERVER, date(PHP_DATE_TIME_FORMAT));
        fputs($fp, $schema);

        $tables_query = tep_db_query('SHOW TABLES');
        while ($tables = tep_db_fetch_array($tables_query)) {
          $table = reset($tables);

          $schema = "\n" . 'DROP TABLE IF EXISTS ' . $table . ';' . "\n" .
                    'CREATE TABLE ' . $table . ' (' . "\n";

          $table_list = [];
          $fields_query = tep_db_query("SHOW FIELDS FROM " . $table);
          while ($fields = tep_db_fetch_array($fields_query)) {
            $table_list[] = $fields['Field'];

            $schema .= '  ' . $fields['Field'] . ' ' . $fields['Type'];

            if (strlen($fields['Default']) > 0) {
              $schema .= ' default \'' . $fields['Default'] . '\'';
            }

            if ($fields['Null'] != 'YES') {
              $schema .= ' NOT NULL';
            }

            if (!empty($fields['Extra'])) {
              $schema .= ' ' . strtoupper($fields['Extra']);
            }

            $schema .= ',' . "\n";
          }

          $schema = preg_replace("/,\n$/", '', $schema);

// add the keys
          $index = [];
          $keys_query = tep_db_query("SHOW KEYS FROM " . $table);
          while ($keys = tep_db_fetch_array($keys_query)) {
            $kname = $keys['Key_name'];

            if (!isset($index[$kname])) {
              $index[$kname] = ['unique' => !$keys['Non_unique'],
                                'fulltext' => ($keys['Index_type'] == 'FULLTEXT' ? '1' : '0'),
                                'columns' => []];
            }

            $index[$kname]['columns'][] = $keys['Column_name'];
          }

          foreach ($index as $kname => $info) {
            $schema .= ',' . "\n";

            $columns = implode(', ', $info['columns']);

            if ($kname == 'PRIMARY') {
              $schema .= '  PRIMARY KEY (' . $columns . ')';
            } elseif ( $info['fulltext'] == '1' ) {
              $schema .= '  FULLTEXT ' . $kname . ' (' . $columns . ')';
            } elseif ($info['unique']) {
              $schema .= '  UNIQUE ' . $kname . ' (' . $columns . ')';
            } else {
              $schema .= '  KEY ' . $kname . ' (' . $columns . ')';
            }
          }

          $schema .= "\n" . ');' . "\n\n";
          fputs($fp, $schema);

// dump the data
          if ( ($table != 'sessions' ) && ($table != 'whos_online') ) {
            $rows_query = tep_db_query("SELECT " . implode(',', $table_list) . " FROM " . $table);
            while ($rows = tep_db_fetch_array($rows_query)) {
              $schema = 'INSERT INTO ' . $table . ' (' . implode(', ', $table_list) . ') VALUES (';

              foreach ($table_list as $i) {
                if (!isset($rows[$i])) {
                  $schema .= 'NULL, ';
                } elseif (tep_not_null($rows[$i])) {
                  $row = addslashes($rows[$i]);
                  $row = preg_replace("/\n#/", "\n".'\#', $row);

                  $schema .= '\'' . $row . '\', ';
                } else {
                  $schema .= '\'\', ';
                }
              }

              $schema = preg_replace('/, $/', '', $schema) . ');' . "\n";
              fputs($fp, $schema);
            }
          }
        }

        fclose($fp);

        if (isset($_POST['download']) && ($_POST['download'] == 'yes')) {
          switch ($_POST['compress']) {
            case 'gzip':
              exec(LOCAL_EXE_GZIP . ' ' . DIR_FS_BACKUP . $backup_file);
              $backup_file .= '.gz';
              break;
            case 'zip':
              exec(LOCAL_EXE_ZIP . ' -j ' . DIR_FS_BACKUP . $backup_file . '.zip ' . DIR_FS_BACKUP . $backup_file);
              unlink(DIR_FS_BACKUP . $backup_file);
              $backup_file .= '.zip';
          }
          header('Content-type: application/x-octet-stream');
          header('Content-disposition: attachment; filename=' . $backup_file);

          readfile(DIR_FS_BACKUP . $backup_file);
          unlink(DIR_FS_BACKUP . $backup_file);

          exit;
        } else {
          switch ($_POST['compress']) {
            case 'gzip':
              exec(LOCAL_EXE_GZIP . ' ' . DIR_FS_BACKUP . $backup_file);
              break;
            case 'zip':
              exec(LOCAL_EXE_ZIP . ' -j ' . DIR_FS_BACKUP . $backup_file . '.zip ' . DIR_FS_BACKUP . $backup_file);
              unlink(DIR_FS_BACKUP . $backup_file);
          }

          $messageStack->add_session(SUCCESS_DATABASE_SAVED, 'success');
        }

        tep_redirect(tep_href_link('backup.php'));
        break;
      case 'restorenow':
      case 'restorelocalnow':
        tep_set_time_limit(0);

        if ($action == 'restorenow') {
          $read_from = $_GET['file'];

          if (file_exists(DIR_FS_BACKUP . $_GET['file'])) {
            $restore_file = DIR_FS_BACKUP . $_GET['file'];
            $extension = substr($_GET['file'], -3);

            if ( ($extension == 'sql') || ($extension == '.gz') || ($extension == 'zip') ) {
              switch ($extension) {
                case 'sql':
                  $restore_from = $restore_file;
                  $remove_raw = false;
                  break;
                case '.gz':
                  $restore_from = substr($restore_file, 0, -3);
                  exec(LOCAL_EXE_GUNZIP . ' ' . $restore_file . ' -c > ' . $restore_from);
                  $remove_raw = true;
                  break;
                case 'zip':
                  $restore_from = substr($restore_file, 0, -4);
                  exec(LOCAL_EXE_UNZIP . ' ' . $restore_file . ' -d ' . DIR_FS_BACKUP);
                  $remove_raw = true;
              }

              if (isset($restore_from) && file_exists($restore_from) && (filesize($restore_from) > 15000)) {
                $fd = fopen($restore_from, 'rb');
                $restore_query = fread($fd, filesize($restore_from));
                fclose($fd);
              }
            }
          }
        } elseif ($action == 'restorelocalnow') {
          $sql_file = new upload('sql_file');

          if ($sql_file->parse() == true) {
            $restore_query = fread(fopen($sql_file->tmp_filename, 'r'), filesize($sql_file->tmp_filename));
            $read_from = $sql_file->filename;
          }
        }

        if (isset($restore_query)) {
          $sql_statements = [];
          $drop_table_names = [];
          $sql_length = strlen($restore_query);
          for ($i = strpos($restore_query, ';'); $i<$sql_length; $i++) {
            if ($restore_query[0] == '#') {
              $restore_query = ltrim(substr($restore_query, strpos($restore_query, "\n")));
              $sql_length = strlen($restore_query);
              $i = strpos($restore_query, ';')-1;
              continue;
            }
            if ($restore_query[($i+1)] == "\n") {
              for ($j=($i+2); $j<$sql_length; $j++) {
                if (trim($restore_query[$j]) != '') {
                  $next = substr($restore_query, $j, 6);
                  if ($next[0] == '#') {
// find out where the break position is so we can remove this line (#comment line)
                    for ($k=$j; $k<$sql_length; $k++) {
                      if ($restore_query[$k] == "\n") break;
                    }
                    $query = substr($restore_query, 0, $i+1);
                    $restore_query = substr($restore_query, $k);
// join the query before the comment appeared, with the rest of the dump
                    $restore_query = $query . $restore_query;
                    $sql_length = strlen($restore_query);
                    $i = strpos($restore_query, ';')-1;
                    continue 2;
                  }
                  break;
                }
              }
              if ($next == '') { // get the last insert query
                $next = 'insert';
              }
              if ( (preg_match('/create/i', $next)) || (preg_match('/insert/i', $next)) || (preg_match('/drop t/i', $next)) ) {
                $query = substr($restore_query, 0, $i);

                $next = '';
                $sql_statements[] = $query;
                $restore_query = ltrim(substr($restore_query, $i+1));
                $sql_length = strlen($restore_query);
                $i = strpos($restore_query, ';')-1;

                if (preg_match('/^create*/i', $query)) {
                  $table_name = trim(substr($query, stripos($query, 'table ')+6));
                  $table_name = substr($table_name, 0, strpos($table_name, ' '));

                  $drop_table_names[] = $table_name;
                }
              }
            }
          }

          tep_db_query('DROP TABLE IF EXISTS ' . implode(', ', $drop_table_names));

          foreach ($sql_statements as $sql_statement) {
            tep_db_query($sql_statement);
          }

          tep_session_close();

          tep_db_query("DELETE FROM whos_online");
          tep_db_query("DELETE FROM sessions");

          tep_db_query("DELETE FROM configuration WHERE configuration_key = 'DB_LAST_RESTORE'");
          tep_db_query("INSERT INTO configuration VALUES (null, 'Last Database Restore', 'DB_LAST_RESTORE', '" . $read_from . "', 'Last database restore file', '6', '0', null, NOW(), '', '')");

          if (!empty($remove_raw)) {
            unlink($restore_from);
          }

          $messageStack->add_session(SUCCESS_DATABASE_RESTORED, 'success');
        }

        tep_redirect(tep_href_link('backup.php'));
        break;
      case 'download':
        $extension = substr($_GET['file'], -3);

        if ( ($extension == 'zip') || ($extension == '.gz') || ($extension == 'sql') ) {
          if ($fp = fopen(DIR_FS_BACKUP . $_GET['file'], 'rb')) {
            $buffer = fread($fp, filesize(DIR_FS_BACKUP . $_GET['file']));
            fclose($fp);

            header('Content-type: application/x-octet-stream');
            header('Content-disposition: attachment; filename=' . $_GET['file']);

            echo $buffer;

            exit;
          }
        } else {
          $messageStack->add(ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE, 'error');
        }
        break;
      case 'deleteconfirm':
        if (strstr($_GET['file'], '..')) tep_redirect(tep_href_link('backup.php'));

        tep_remove(DIR_FS_BACKUP . '/' . $_GET['file']);

        if (!$tep_remove_error) {
          $messageStack->add_session(SUCCESS_BACKUP_DELETED, 'success');

          tep_redirect(tep_href_link('backup.php'));
        }
        break;
    }
  }

// check if the backup directory exists
  $dir_ok = false;
  if (is_dir(DIR_FS_BACKUP)) {
    if (tep_is_writable(DIR_FS_BACKUP)) {
      $dir_ok = true;
    } else {
      $messageStack->add(ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE, 'error');
    }
  } else {
    $messageStack->add(ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST, 'error');
  }

  require 'includes/template_top.php';
?>

  <h1 class="display-4 mb-2"><?= HEADING_TITLE; ?></h1>

  <div class="row no-gutters">
    <div class="col-12 col-sm-8">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th><?= TABLE_HEADING_TITLE; ?></th>
              <th><?= TABLE_HEADING_FILE_DATE; ?></th>
              <th class="text-right"><?= TABLE_HEADING_FILE_SIZE; ?></th>
              <th class="text-right"><?= TABLE_HEADING_ACTION; ?></th>
            </tr>
          </thead>
          <tbody>

<?php
  $dir = dir(DIR_FS_BACKUP);
  $contents = [];
  while ($file = $dir->read()) {
    if (!is_dir(DIR_FS_BACKUP . $file) && in_array(substr($file, -3), ['zip', 'sql', '.gz'])) {
      $contents[] = $file;
    }
  }
  sort($contents);

  foreach ($contents as $entry) {
    if (!isset($buInfo) && (!isset($_GET['file']) || ($_GET['file'] == $entry)) && ($action != 'backup') && ($action != 'restorelocal')) {
      $file_array['file'] = $entry;
      $file_array['date'] = date(PHP_DATE_TIME_FORMAT, filemtime(DIR_FS_BACKUP . $entry));
      $file_array['size'] = number_format(filesize(DIR_FS_BACKUP . $entry)) . ' bytes';
      switch (substr($entry, -3)) {
        case 'zip': $file_array['compression'] = 'ZIP'; break;
        case '.gz': $file_array['compression'] = 'GZIP'; break;
        default: $file_array['compression'] = TEXT_NO_EXTENSION; break;
      }

      $buInfo = new objectInfo($file_array);
    }

    if (isset($buInfo->file) && ($entry == $buInfo->file)) {
      $onclick_link = 'file=' . $buInfo->file . '&action=restore';
      $icon = '<i class="fas fa-chevron-circle-right text-info"></i>';
    } else {
      $onclick_link = 'file=' . $entry;
      $icon = '<a href="' . tep_href_link('backup.php', 'file=' . $entry) . '"><i class="fas fa-info-circle text-muted"></i></a>';
    }
?>
            <tr>
                <td onclick="document.location.href='<?= tep_href_link('backup.php', $onclick_link); ?>'"><?= '<a href="' . tep_href_link('backup.php', 'action=download&file=' . $entry) . '"><i title="' . ICON_FILE_DOWNLOAD . '" class="fas fa-file-download text-muted"></i></a>&nbsp;' . $entry; ?></td>
                <td onclick="document.location.href='<?= tep_href_link('backup.php', $onclick_link); ?>'"><?= date(PHP_DATE_TIME_FORMAT, filemtime(DIR_FS_BACKUP . $entry)); ?></td>
                <td class="text-right" onclick="document.location.href='<?= tep_href_link('backup.php', $onclick_link); ?>'"><?= sprintf(TEXT_INFO_BACKUP_SIZE, number_format(filesize(DIR_FS_BACKUP . $entry)/1024000, 2)) ; ?></td>
                <td class="text-right"><?= $icon; ?></td>
              </tr>
<?php
  }
  $dir->close();
?>
          </tbody>
        </table>
      </div>

      <div class="row my-1">
        <div class="col"><?= sprintf(TEXT_BACKUP_DIRECTORY, DIR_FS_BACKUP); ?></div>
        <div class="col text-right mr-2"><?php if ( ($action != 'backup') && $dir_ok && isset($dir) ) echo tep_draw_bootstrap_button(IMAGE_BACKUP, 'fas fa-download', tep_href_link('backup.php', 'action=backup'), null, null, 'btn-light mr-2'); if ( ($action != 'restorelocal') && isset($dir) ) echo tep_draw_bootstrap_button(IMAGE_RESTORE, 'fas fa-upload', tep_href_link('backup.php', 'action=restorelocal'), null, null, 'btn-light'); ?></div>
      </div>

<?php
  if (defined('DB_LAST_RESTORE')) {
?>
        <hr>
        <div class="row my-1">
          <div class="col"><?= sprintf(TEXT_LAST_RESTORATION, DB_LAST_RESTORE); ?></div>
          <div class="col text-right mr-2">
          <?= tep_draw_bootstrap_button(TEXT_FORGET, 'fas fa-bell-slash', tep_href_link('backup.php', 'action=forget'), null, null, 'btn-light'); ?></div>
        </div>
<?php
  }
?>

    </div>

<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'backup':
      $heading[] = ['text' => TEXT_INFO_HEADING_NEW_BACKUP];

      $contents = ['form' => tep_draw_form('backup', 'backup.php', 'action=backupnow')];
      $contents[] = ['text' => TEXT_INFO_NEW_BACKUP];

      $contents[] = ['text' => '<div class="custom-control custom-radio custom-control-inline">' . tep_draw_selection_field('compress', 'radio', 'no', true, 'id="cNo" class="custom-control-input"') . '<label class="custom-control-label" for="cNo"><small>' . TEXT_INFO_USE_NO_COMPRESSION . '</small></label></div>'];
      if (file_exists(LOCAL_EXE_GZIP)) $contents[] = ['text' => '<div class="custom-control custom-radio custom-control-inline">' . tep_draw_selection_field('compress', 'radio', 'gzip', null, 'id="cGzip" class="custom-control-input"') . '<label class="custom-control-label" for="cGzip"><small>' . TEXT_INFO_USE_GZIP . '</small></label></div>'];
      if (file_exists(LOCAL_EXE_ZIP)) $contents[] = ['text' => '<div class="custom-control custom-radio custom-control-inline">' . tep_draw_selection_field('compress', 'radio', 'zip', null, 'id="czip" class="custom-control-input"') . '<label class="custom-control-label" for="czip"><small>' . TEXT_INFO_USE_ZIP . '</small></label></div>'];

      if ($dir_ok) {
        $contents[] = ['text' => '<div class="custom-control custom-switch">' . tep_draw_selection_field('download', 'checkbox', 'yes', null, 'class="custom-control-input" id="d"') . '<label for="d" class="custom-control-label text-muted"><small>' . TEXT_INFO_DOWNLOAD_ONLY . '<br>' . TEXT_INFO_BEST_THROUGH_HTTPS . '</small></label></div>'];
      } else {
        $contents[] = ['text' => '<div class="custom-control custom-radio custom-control-inline">' . tep_draw_selection_field('download', 'radio', 'yes', true, 'id="d" class="custom-control-input"') . '<label class="custom-control-label" for="d"><small>' . TEXT_INFO_DOWNLOAD_ONLY . '<br>' . TEXT_INFO_BEST_THROUGH_HTTPS . '</small></label></div>'];
      }

      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_BACKUP, 'fas fa-download', null, null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('backup.php'), null, null, 'btn-light')];
      break;
    case 'restore':
      $heading[] = ['text' => $buInfo->date];

      $contents[] = ['text' => tep_break_string(sprintf(TEXT_INFO_RESTORE, DIR_FS_BACKUP . (($buInfo->compression != TEXT_NO_EXTENSION) ? pathinfo($buInfo->file, PATHINFO_FILENAME) : $buInfo->file), ($buInfo->compression != TEXT_NO_EXTENSION) ? TEXT_INFO_UNPACK : ''), 35, ' ')];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_RESTORE, 'fas fa-file-upload', tep_href_link('backup.php', 'file=' . $buInfo->file . '&action=restorenow'), null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('backup.php', 'file=' . $buInfo->file), null, null, 'btn-light')];
      break;
    case 'restorelocal':
      $heading[] = ['text' => TEXT_INFO_HEADING_RESTORE_LOCAL];

      $contents = ['form' => tep_draw_form('restore', 'backup.php', 'action=restorelocalnow', 'post', 'enctype="multipart/form-data"')];
      $contents[] = ['text' => TEXT_INFO_RESTORE_LOCAL . '<br><br>' . TEXT_INFO_BEST_THROUGH_HTTPS];
      $contents[] = ['text' => '<div class="custom-file mb-2">' . tep_draw_input_field('sql_file', '', 'required aria-required="true" id="upload"', 'file', null, 'class="custom-file-input"') . '<label class="custom-file-label" for="upload">&nbsp;</label></div>'];
      $contents[] = ['text' => TEXT_INFO_RESTORE_LOCAL_RAW_FILE];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_RESTORE, 'fas fa-file-upload', null, null, null, 'btn-warning mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('backup.php'), null, null, 'btn-light')];
      break;
    case 'delete':
      $heading[] = ['text' => $buInfo->date];

      $contents = ['form' => tep_draw_form('delete', 'backup.php', 'file=' . $buInfo->file . '&action=deleteconfirm')];
      $contents[] = ['text' => TEXT_DELETE_INTRO];
      $contents[] = ['class' => 'text-center text-uppercase font-weight-bold', 'text' => $buInfo->file];
      $contents[] = ['class' => 'text-center', 'text' => tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', null, 'primary', null, 'btn-danger mr-2') . tep_draw_bootstrap_button(IMAGE_CANCEL, 'fas fa-times', tep_href_link('backup.php', 'file=' . $buInfo->file), null, null, 'btn-light')];
      break;
    default:
      if (isset($buInfo->file)) {
        $heading[] = ['text' => $buInfo->date];

        $buttons = tep_draw_bootstrap_button(IMAGE_RESTORE, 'fas fa-file-upload', tep_href_link('backup.php', 'file=' . $buInfo->file . '&action=restore'), null, null, 'btn-warning mr-2');
        if ($dir_ok) {
          $buttons .= tep_draw_bootstrap_button(IMAGE_DELETE, 'fas fa-trash', tep_href_link('backup.php', 'file=' . $buInfo->file . '&action=delete'), null, null, 'btn-danger');
        }

        $contents[] = ['class' => 'text-center', 'text' => $buttons];
        $contents[] = ['text' => sprintf(TEXT_INFO_DATE, $buInfo->date)];
        $contents[] = ['text' => sprintf(TEXT_INFO_SIZE, $buInfo->size)];
        $contents[] = ['text' => sprintf(TEXT_INFO_COMPRESSION, $buInfo->compression)];
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '<div class="col-12 col-sm-4">';
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '</div>';
  }
?>

  </div>

  <script>$(document).on('change', '#upload', function (event) { $(this).next('.custom-file-label').html(event.target.files[0].name); });</script>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
