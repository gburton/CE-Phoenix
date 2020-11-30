<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $mysql_charsets = [['id' => 'auto', 'text' => ACTION_UTF8_CONVERSION_FROM_AUTODETECT]];

  $charsets_query = tep_db_query("SHOW CHARACTER SET");
  while ( $charsets = tep_db_fetch_array($charsets_query) ) {
    $mysql_charsets[] = ['id' => $charsets['Charset'], 'text' => sprintf(ACTION_UTF8_CONVERSION_FROM, $charsets['Charset'])];
  }

  $action = null;
  $actions = [
    [
      'id' => 'check',
      'text' => ACTION_CHECK_TABLES,
    ],
    [
      'id' => 'analyze',
      'text' => ACTION_ANALYZE_TABLES,
    ],
    [
      'id' => 'optimize',
      'text' => ACTION_OPTIMIZE_TABLES,
    ],
    [
      'id' => 'repair',
      'text' => ACTION_REPAIR_TABLES,
    ],
    [
      'id' => 'utf8',
      'text' => ACTION_UTF8_CONVERSION,
    ],
  ];

  if ( isset($_POST['action']) && !empty($_POST['id']) && is_array($_POST['id'])
    && in_array($_POST['action'], ['check', 'analyze', 'optimize', 'repair', 'utf8']) )
  {
    $tables = [];

    $tables_query = tep_db_query('show table status');
    while ( $table = tep_db_fetch_array($tables_query) ) {
      $tables[] = $table['Name'];
    }

    foreach ( $_POST['id'] as $key => $value ) {
      if ( !in_array($value, $tables) ) {
        unset($_POST['id'][$key]);
      }
    }

    if ( !empty($_POST['id']) ) {
      $action = $_POST['action'];
    }
  }

  switch ( $action ) {
    case 'check':
    case 'analyze':
    case 'optimize':
    case 'repair':
      tep_set_time_limit(0);

      $table_headers = [
        TABLE_HEADING_TABLE,
        TABLE_HEADING_MSG_TYPE,
        TABLE_HEADING_MSG,
        tep_draw_checkbox_field('masterblaster'),
      ];

      $table_data = [];

      foreach ( $_POST['id'] as $table ) {
        $current_table = null;
        $table = tep_db_input(tep_db_prepare_input($table));

        $sql_query = tep_db_query("$action table $table");
        while ( $sql = tep_db_fetch_array($sql_query) ) {
          $table_data[] = [
            ($table === $current_table) ? '' : htmlspecialchars($table),
            htmlspecialchars($sql['Msg_type']),
            htmlspecialchars($sql['Msg_text']),
            ($table === $current_table) ? '' : tep_draw_checkbox_field('id[]', $table, isset($_POST['id']) && in_array($table, $_POST['id'])),
          ];

          $current_table = $table;
        }
      }

      break;

    case 'utf8':
      $charset_pass = false;

      if ( isset($_POST['from_charset']) ) {
        if ( $_POST['from_charset'] == 'auto' ) {
          $charset_pass = true;
        } else {
          foreach ( $mysql_charsets as $c ) {
            if ( $_POST['from_charset'] == $c['id'] ) {
              $charset_pass = true;
              break;
            }
          }
        }
      }

      if ( $charset_pass === false ) {
        tep_redirect(tep_href_link('database_tables.php'));
      }

      tep_set_time_limit(0);

      if ( isset($_POST['dryrun']) ) {
        $table_headers = [TABLE_HEADING_QUERIES];
      } else {
        $table_headers = [TABLE_HEADING_TABLE, TABLE_HEADING_MSG, tep_draw_checkbox_field('masterblaster')];
      }

      $table_data = [];

      foreach ( $_POST['id'] as $table ) {
        $result = 'OK';

        $queries = [];

        $cols_query = tep_db_query("SHOW FULL COLUMNS FROM " . tep_db_input(tep_db_prepare_input($table)));
        while ( $cols = tep_db_fetch_array($cols_query) ) {
          if ( !empty($cols['Collation']) ) {
            if ( $_POST['from_charset'] == 'auto' ) {
              $old_charset = tep_db_prepare_input(substr($cols['Collation'], 0, strpos($cols['Collation'], '_')));
            } else {
              $old_charset = tep_db_prepare_input($_POST['from_charset']);
            }

            $queries[] = sprintf(<<<'EOSQL'
UPDATE %1$s
 SET %2$s = CONVERT(BINARY CONVERT(%2$s USING %3$s) USING utf8)
 WHERE CHAR_LENGTH(%2$s) = LENGTH(CONVERT(BINARY CONVERT(%2$s USING %3$s) USING utf8))
EOSQL
              , tep_db_input(tep_db_prepare_input($table)), $cols['Field'], $old_charset);
          }
        }

        $query = sprintf("ALTER TABLE %s CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci",
          tep_db_input(tep_db_prepare_input($table)));

        if ( isset($_POST['dryrun']) ) {
          $table_data[] = [$query];

          foreach ( $queries as $q ) {
            $table_data[] = [$q];
          }
        } else {
// mysqli_query() is directly called as tep_db_query() dies when an error occurs
          if ( mysqli_query($db_link, $query) ) {
            foreach ( $queries as $q ) {
              if ( !mysqli_query($db_link, $q) ) {
                $result = mysqli_error($db_link);
                break;
              }
            }
          } else {
            $result = mysqli_error($db_link);
          }
        }

        if ( !isset($_POST['dryrun']) ) {
          $table_data[] = [
            htmlspecialchars($table),
            htmlspecialchars($result),
            tep_draw_checkbox_field('id[]', $table, true),
          ];
        }
      }

      break;

    default:
      $table_headers = [
        TABLE_HEADING_TABLE,
        TABLE_HEADING_ROWS,
        TABLE_HEADING_SIZE,
        TABLE_HEADING_ENGINE,
        TABLE_HEADING_COLLATION,
        tep_draw_checkbox_field('masterblaster'),
      ];

      $table_data = [];

      $sql_query = tep_db_query('SHOW TABLE STATUS');
      while ( $sql = tep_db_fetch_array($sql_query) ) {
        $table_data[] = [
          htmlspecialchars($sql['Name']),
          htmlspecialchars($sql['Rows']),
          round(($sql['Data_length'] + $sql['Index_length']) / 1024 / 1024, 2) . 'M',
          htmlspecialchars($sql['Engine']),
          htmlspecialchars($sql['Collation']),
          tep_draw_checkbox_field('id[]', $sql['Name']),
        ];
      }
  }

  require 'includes/template_top.php';
?>

  <div class="row">
    <div class="col">
      <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>
    </div>
    <?php
    if ( isset($action) ) {
      echo '<div class="col-sm-4 text-right align-self-center">';
        echo tep_draw_bootstrap_button(IMAGE_BACK, 'fas fa-angle-left', tep_href_link('database_tables.php'), null, null, 'btn-light');
      echo '</div>';
    }
    ?>
  </div>

  <?= tep_draw_form('sql', 'database_tables.php') ?>
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <?php
          foreach ( $table_headers as $th ) {
            echo '<th>' . $th . '</th>';
          }
          ?>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ( $table_data as $td ) {
          echo '<tr>';

          foreach ( $td as $data ) {
            echo '<td>' . $data . '</td>';
          }

          echo '</tr>';
        }
        ?>
      </tbody>
    </table>
  </div>

<?php
if ( !isset($_POST['dryrun']) ) {
 ?>

  <div class="row">
    <div class="col">
      <?php
      echo tep_draw_pull_down_menu('action', $actions, '', 'id="sqlActionsMenu"');
      echo tep_draw_bootstrap_button(BUTTON_ACTION_GO, 'fas fa-cogs', null, null, null, 'btn-success btn-block mt-2');
      ?>
    </div>
    <div class="col">
      <?php
      echo '<span class="runUtf8" style="display: none;">' . tep_draw_pull_down_menu('from_charset', $mysql_charsets) . '<br>' . sprintf(ACTION_UTF8_DRY_RUN, tep_draw_checkbox_field('dryrun')) . '</span>';
      ?>
    </div>
  </div>

  <?php
}
?>

</form>

<script>
$(function() {
  if ( $('form[name="sql"] input[type="checkbox"][name="masterblaster"]').length > 0 ) {
    $('form[name="sql"] input[type="checkbox"][name="masterblaster"]').click(function() {
      $('form[name="sql"] input[type="checkbox"][name="id[]"]').prop('checked', $('form[name="sql"] input[type="checkbox"][name="masterblaster"]').prop('checked'));
    });
  }

  if ( $('#sqlActionsMenu').val() == 'utf8' ) {
    $('.runUtf8').show();
  }

  $('#sqlActionsMenu').change(function() {
    var selected = $(this).val();

    if ( selected == 'utf8' ) {
      $('.runUtf8').show();
    } else {
      $('.runUtf8').hide();
    }
  });
});
</script>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
