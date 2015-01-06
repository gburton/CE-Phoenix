<?php
/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010 - 2014 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License

 */

if (file_exists('mailhive/common/main/inc_mailhive.php')) {
    include_once('mailhive/common/main/inc_mailhive.php');
} else {
    // cloudloader installation

    require_once('includes/application_top.php');

    if (function_exists('xtc_db_query')) {

        function mh_db_check_field_exists($table, $field)
        {
            $query_raw = "SHOW COLUMNS FROM " . $table . "";
            $query = xtc_db_query($query_raw);
            while ($item = xtc_db_fetch_array($query)) {
                if ($item['Field'] == $field) {
                    return $item;
                }
            }
            // not found
            return false;
        }

        function mh_db_add_field($table, $field, $sql)
        {
            // check if exists
            $result = mh_db_check_field_exists($table, $field);
            if ($result === false) {
                if (is_array($sql)) {
                    while (list(, $sql_item) = each($sql)) {
                        xtc_db_query($sql_item);
                    }
                } else {
                    xtc_db_query($sql);
                }
            }
        }


        // gambio has removed TABLE_ADMIN_ACCESS from storefront context...
        if (!defined('TABLE_ADMIN_ACCESS')) {
            define('TABLE_ADMIN_ACCESS', 'admin_access');
        };

        echo "<br />Adding Admin-Right<br>
	<br />
	";

        $sql = array();
        $sql[] = "ALTER TABLE " . TABLE_ADMIN_ACCESS . " ADD mailbeez INT(1) DEFAULT '0' NOT NULL ;";
        mh_db_add_field(TABLE_ADMIN_ACCESS, 'mailbeez', $sql);

        $field_info = mh_db_check_field_exists(TABLE_ADMIN_ACCESS, 'mailbeez');

        if ($field_info != false) {
            echo 'TABLE_ADMIN_ACCESS (' . TABLE_ADMIN_ACCESS . ') updated - added column "mailbeez"<br />';
        }

        xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET mailbeez = '2' WHERE customers_id = 'groups' LIMIT 1");
        xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET mailbeez = '1' WHERE customers_id = '1' LIMIT 1");
        // UPDATE `admin_access` SET mailbeez = 1;

        echo "<br />done<br><br />";
        echo "<b>please go to admin > tools > mailbeez to finish your installation</b>";
    } else {
        ?>

        Please follow the installation manual on
        <a href="http://www.mailbeez.com/documentation/installation/">http://www.mailbeez.com/documentation/installation/</a>

    <?php
    }

    ?>
    Please install MailBeez
<?php
}
?>