<?php

require_once 'pclzip.lib.php';

// issues
// -> Package files core from server core/get are corrupt
// check if /mailhive/cloudbeez/cloudloader/temp & work is writeable
// domains might not be open for CURL
// test for correct result of ping and download

class CloudloaderBase
{

    var $debug = true;
    var $dir_permissions = 0755;
    var $file_permissions = 0755;
    var $write_test_failed_file;

    function makeDir($dir, $mode = 0755, $recursive = true)
    {
        if (is_null($dir) || $dir === "") {
            return FALSE;
        }

        if (is_dir($dir) || $dir === "/") {
//            @chmod($dir, $mode);
            return TRUE;
        }
        if ($this->makeDir(dirname($dir), $mode, $recursive)) {
            //http://stackoverflow.com/questions/4570796/php-copy-function-and-apache-group
            $oldmask = umask(0);
            $result = mkdir($dir, $mode);
            umask($oldmask);
            return $result;
        }
        return FALSE;
    }


    /**
     * Copies file or folder from source to destination, it can also do
     * recursive copy by recursively creating the dest file or directory path if it wasn't exist
     * Use cases:
     * - Src:/home/test/file.txt ,Dst:/home/test/b ,Result:/home/test/b -> If source was file copy file.txt name with b as name to destination
     * - Src:/home/test/file.txt ,Dst:/home/test/b/ ,Result:/home/test/b/file.txt -> If source was file Creates b directory if does not exsits and copy file.txt into it
     * - Src:/home/test ,Dst:/home/ ,Result:/home/test/** -> If source was directory copy test directory and all of its content into dest
     * - Src:/home/test/ ,Dst:/home/ ,Result:/home/**-> if source was direcotry copy its content to dest
     * - Src:/home/test ,Dst:/home/test2 ,Result:/home/test2/** -> if source was directoy copy it and its content to dest with test2 as name
     * - Src:/home/test/ ,Dst:/home/test2 ,Result:->/home/test2/** if source was directoy copy it and its content to dest with test2 as name
     * @author Sina Salek (<a href="http://sina.salek.ws/node/1289" title="http://sina.salek.ws/node/1289">http://sina.salek.ws/node/1289</a>)
     * @todo
     *  - Should have rollback so it can undo the copy when it wasn't completely successful
     *  - It should be possible to turn off auto path creation feature f
     *  - Supporting callback function
     *  - May prevent some issues on shared enviroments : <a href="http://us3.php.net/umask" title="http://us3.php.net/umask">http://us3.php.net/umask</a>
     * @param $source //file or folder
     * @param $dest ///file or folder
     * @param $options //folderPermission,filePermission
     * @return boolean
     */
    function smartCopy($source, $dest, $options = array('folderPermission' => 0766, 'filePermission' => 0766, 'exclude_overwrite' => array(), 'test_writable' => false))
    {
        $result = false;
        // todo
        // http://stackoverflow.com/questions/9614835/changing-owner-group-id-in-php

        //For Cross Platform Compatibility
        if (!isset($options['noTheFirstRun'])) {
            $source = str_replace('\\', '/', $source);
            $dest = str_replace('\\', '/', $dest);
            $options['noTheFirstRun'] = true;
        }

        if (is_file($source)) {
            if ($dest[strlen($dest) - 1] == '/') {
                if (!file_exists($dest)) {
                    $makeDir_result = $this->makeDir($dest, $options['folderPermission'], true);

                    if (!$makeDir_result) {
                        $this->debug_output("could not create dir $dest<br>");
                        throw new Exception(sprintf(MAILBEEZ_INSTALL_ERROR_DIR_NOT_CREATE, $dest));
                        return false;
                    }
                }
                $__dest = $dest . "/" . basename($source);
            } else {
                $__dest = $dest;
            }

            /*
                    if (check_in_array($source, $options['exclude_overwrite'])) {
                        $this->debug_output( "###$source###";
                        print_r($options['exclude_overwrite']);
                    }
            */

//            if (!file_exists($__dest) || !($this->check_in_array($source, $options['exclude_overwrite']))) {
            if (!($this->check_in_array($source, $options['exclude_overwrite']))) {
                if ($options['test_writable']) {
                    $this->debug_output("test $__dest<br>");
                    $result = (file_exists($__dest)) ? is_writeable($__dest) : true;
                    if (!$result) {
                        $this->write_test_failed_file[] = str_replace(realpath(PATH_INSTALL . "/../../"), '', $__dest);
                        return true;
                    }

                } else {
                    $result = copy($source, $__dest);
                    //chmod($__dest, $options['filePermission']);
                    @chmod($__dest, fileperms($source));
                }
            } else {
                $result = true;
            }
        } elseif (is_dir($source)) {
            if ($dest[strlen($dest) - 1] == '/') {
                if ($source[strlen($source) - 1] == '/') {
                    //Copy only contents
                } else {
                    //Change parent itself and its contents
                    $dest = $dest . basename($source);
                    //http://stackoverflow.com/questions/4570796/php-copy-function-and-apache-group
                    $oldmask = umask(0);
                    $makeDir_result = $this->makeDir($dest);
                    if (!$makeDir_result) {
                        $this->debug_output("could not create dir $dest<br>");
                        throw new Exception(sprintf(MAILBEEZ_INSTALL_ERROR_DIR_NOT_CREATE, $dest));
                        return false;
                    }
                    @umask($oldmask);
                    @chmod($dest, $options['filePermission']);
                }
            } else {
                if ($source[strlen($source) - 1] == '/') {
                    //Copy parent directory with new name and all its content
                    //http://stackoverflow.com/questions/4570796/php-copy-function-and-apache-group
                    $oldmask = umask(0);
                    $makeDir_result = $this->makeDir($dest, $options['folderPermission']);
                    if (!$makeDir_result) {
                        $this->debug_output("could not create dir $dest<br>");
                        throw new Exception(sprintf(MAILBEEZ_INSTALL_ERROR_DIR_NOT_CREATE, $dest));
                        return false;
                    }
                    @umask($oldmask);
                    @chmod($dest, $options['filePermission']);
                } else {
                    //Copy parent directory with new name and all its content
                    //http://stackoverflow.com/questions/4570796/php-copy-function-and-apache-group
                    $oldmask = umask(0);
                    $makeDir_result = $this->makeDir($dest, $options['folderPermission']);
                    if (!$makeDir_result) {
                        $this->debug_output("could not create dir $dest<br>");
                        throw new Exception(sprintf(MAILBEEZ_INSTALL_ERROR_DIR_NOT_CREATE, $dest));
                        return false;
                    }
                    @umask($oldmask);
                    @chmod($dest, $options['filePermission']);
                }
            }

            $dirHandle = opendir($source);
            $result = true; // in case of empty directories
            while ($file = readdir($dirHandle)) {
                if ($file != "." && $file != "..") {
                    $__dest = $dest . "/" . $file;
                    $__source = $source . "/" . $file;
                    //$this->debug_output( "$__source ||| $__dest<br />";
                    if ($__source != $dest) {
                        if ($options['test_writable'] && is_dir($__source)) {
                            $this->debug_output("test write permissions $__dest<br>");
                        } else if (!$options['test_writable']) {
                            $this->debug_output("copy $__source -> $__dest<br>");
                        }
                        $result = $this->smartCopy($__source, $__dest, $options);

                        if (!$result) {
                            closedir($dirHandle);
                            $this->debug_output("Error: not writable $__dest<br>");
                            return false;
                        }
                    }
                }
            }
            closedir($dirHandle);

        } else {
            $result = false;
        }
        return $result;
    }


    function check_in_array($input, $check_array)
    {
        foreach ($check_array as $check_element) {
            if (stripos($input, $check_element) !== false) {
                return true;
            }
        }
        return false;
    }


    function delete_folder($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delete_folder("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }


    function extract_zip($zipfile, $work_dir)
    {
        $this->debug_output("starting extract_zip\n");


        $makeDir_result = $this->makeDir($work_dir, $this->dir_permissions);
        if (!$makeDir_result) {
            $this->debug_output("could not create dir $work_dir<br>");
            throw new Exception(sprintf(MAILBEEZ_INSTALL_ERROR_DIR_NOT_CREATE, $work_dir));

            return false;
        }

        /// arrgggg!
        // $this->makeDir($target_dir);


        // Generate random unzip directory to prevent overwriting
        // This will generate something like "./unzip<RANDOM SEQUENCE>"
        $workpath = $work_dir . '/unzip' . date("Ymd-His") . '/';

        if ($this->makeDir($workpath) === TRUE) {

            $archive = new PclZip($zipfile);

            if (($v_result_list = $archive->extract(PCLZIP_OPT_PATH, $workpath)) == 0) {
                die("Error : " . $archive->errorInfo(true));
            } else {
                //            var_dump($v_result_list);
//                $this->debug_output("</pre>");

                //            $zip = new ZipArchive;
                //        if ($zip->open($zipfile) === TRUE) {
                //            $zip->extractTo($workpath);
            }


            return $workpath;
        } else {
            $this->debug_output("could not create dir $workpath<br>");
            throw new Exception(sprintf(MAILBEEZ_INSTALL_ERROR_DIR_NOT_CREATE, $workpath));
            return false;
        }
    }


    function test_deploy_files($workpath, $target_dir, $exclude_overwrite, $glob_path = '*/catalog/mailhive')
    {
        $this->debug_output("starting test_deploy_files\n");

        return $this->deploy_files($workpath, $target_dir, $exclude_overwrite, $glob_path, true);
    }


    function deploy_files($workpath, $target_dir, $exclude_overwrite, $glob_path = '*/catalog/mailhive', $test_writable = false)
    {
        if (!$test_writable) {
            $this->debug_output("starting deploy_files: workpath $workpath\n");
        }

        // $directories = glob($workpath . $glob_path, GLOB_ONLYDIR);

        $glob_dir = $workpath;
        $directories = array();
        while ($dirs = glob($glob_dir . '/*', GLOB_ONLYDIR)) {
            $glob_dir .= '/*';
            foreach ($dirs as $current_dir) {
                if (stristr($current_dir, '__MACOSX')) {
                    continue;
                }
                if ($found_dirs = glob($current_dir . $glob_path, GLOB_ONLYDIR)) {
                    $directories = array_merge($directories, $found_dirs);

                }
            }

        }
//        print_r($directories);


        $target_path = realpath($target_dir) . '/';

        if (defined('CLOUDLOADER_DISABLE_DEPLOY') && CLOUDLOADER_DISABLE_DEPLOY && !$test_writable) {
            return true;
        }


        if ($directories !== FALSE) {
            foreach ($directories as $source_directory) {
                $dir_handle = opendir($source_directory);
                while (($filename = readdir($dir_handle)) !== FALSE) {
                    // Ignore "." and ".." folders
                    if (in_array($filename, array('.', '..'))) {
                        continue;
                    }
                    $source_path = $source_directory . '/' . $filename;

                    if ($test_writable) {
                        $this->debug_output("*** check permissions $target_path$filename");
                        if ($this->smartCopy($source_path, $target_path . $filename,
                                array('folderPermission' => $this->dir_permissions,
                                    'filePermission' => $this->file_permissions,
                                    'exclude_overwrite' => $exclude_overwrite,
                                    'test_writable' => true)) === FALSE
                        ) {
                            $this->debug_output("can not write $target_path$filename\n");
                            // return false;
                        }
                    } else {

                        $this->debug_output("copy $source_path -> $target_path $filename\n");

                        if ($this->smartCopy($source_path, $target_path . $filename,
                                array('folderPermission' => $this->dir_permissions,
                                    'filePermission' => $this->file_permissions,
                                    'exclude_overwrite' => $exclude_overwrite)) === FALSE
                        ) {

                            $this->debug_output("Error copying file ($workpath $filename) \n");
                            return false;
                        }
                    }
                }
            }

            if (sizeof($this->write_test_failed_file) > 0) {
                throw new Exception(sprintf(MAILBEEZ_INSTALL_ERROR_FILE_NOT_WRITEABLE, sizeof($this->write_test_failed_file), implode('<li>', array_slice($this->write_test_failed_file, 0, 10)) ));
                return false;
            }

            return true;
        } else {
            return false;
        }
    }


    function backup($backup_source_folder, $backup_directory, $backup_file, $exclude_dirs)
    {

        $this->debug_output("Backing up: $backup_source_folder\n");

        $makeDir_result = $this->makeDir($backup_directory, $this->dir_permissions);
        if (!$makeDir_result) {
            $this->debug_output("could not create dir $backup_directory<br>");
            throw new Exception(sprintf(MAILBEEZ_INSTALL_ERROR_DIR_NOT_CREATE, $backup_directory));

        }

        $backup_filename = $backup_directory . $backup_file;

//        throw new Exception("Backup parameters $backup_source_folder, $backup_filename, $exclude_files, $exclude_dirs\n");
//print_r($exclude_dirs);
        $this->debug_output("Backup parameters $backup_source_folder, $backup_filename, $exclude_dirs\n");
        $res = $this->Zip($backup_source_folder, $backup_filename, '', $exclude_dirs);
//exit();

        if ($res === TRUE) {
            $this->debug_output("Backup stored in $backup_filename \n");
            return true;
        } else {
            $this->debug_output("Backup failed\n");
            throw new Exception(sprintf(MAILBEEZ_INSTALL_ERROR_BACKUP));
        }

    }


    // for backup

    function Zip($source, $destination, $exclude_files_array = array(), $exclude_dir_array = array())
    {
        global $GLOBALS;
        //    $this->debug_output( "$source, $destination";
        $GLOBALS['exclude_files_array'] = $exclude_files_array;
        $GLOBALS['exclude_dir_array'] = $exclude_dir_array;

        // http://www.phpconcept.net/pclzip/faq#faq05
        $archive = new PclZip($destination);
        //      $v_dir = getcwd(); // or dirname(__FILE__);

        $v_dir = $source;
        $v_remove = $v_dir;
        // To support windows and the C: root you need to add the
        // following 3 lines, should be ignored on linux
        if (substr($v_dir, 1, 1) == ':') {
            $v_remove = substr($v_dir, 2);
        }


        $v_list = $archive->create($v_dir, PCLZIP_OPT_REMOVE_PATH, $v_remove, PCLZIP_CB_PRE_ADD, 'ZipPreAddCallBack');
//        $v_list = $archive->create($v_dir, PCLZIP_OPT_REMOVE_PATH, $v_remove);
        if ($v_list == 0) {
            die("Error : " . $archive->errorInfo(true));
        }

        return true;

    }


    function debug_output($output)
    {
        if ($this->debug) {
            if (method_exists($this, 'log')) {
                $this->log($output);
            } else {
                echo $output;
            }
        }
    }

}

function ZipPreAddCallBack($p_event, &$p_header)
{
    global $GLOBALS;

//    print_r($p_header);
//    print_r($GLOBALS['exclude_dir_array'] );
    $GLOBALS['exclude_files_array'];
    $GLOBALS['exclude_dir_array'];

    $path = pathinfo($p_header['stored_filename'], PATHINFO_DIRNAME);
    if (in_array($path, $GLOBALS['exclude_dir_array'])) {
//        debug_output('<br>exclude directory:' . $path . "<hr>");
//        echo '<br>exclude directory:' . $path . "<hr>";
        return 0;
    }

    if (in_array($p_header['stored_filename'], $GLOBALS['exclude_files_array'])) {
//        debug_output('<br>exclude file:' . $p_header['stored_filename'] . "<hr>");
        return 0;
    } else {
        return 1;
    }
}

?>