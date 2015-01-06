<?php


class Cloudloader extends CloudloaderBase
{
    /**
     * @var InstallerRewrite Configuration rewriter object.
     */
    protected $rewriter;

    /**
     * @var string Application base path.
     */

    protected $baseDirectory;

    /**
     * @var string A temporary working directory.
     */
    protected $tempDirectory;

    /**
     * @var string A temporary working directory.
     */
    protected $unzipDirectory;

//    protected $deployDirectory = '../mailhive/';

    var $deployDirectory;


    /**
     * Constructor/Router
     */
    public function __construct()
    {

        /*
         * Establish directory paths
         */
        $this->baseDirectory = PATH_INSTALL;
        $this->deployDirectory = PATH_INSTALL . '/../';
        $this->tempDirectory = PATH_INSTALL . '/cloudloader/temp'; // @todo Use sys_get_temp_dir()
        $this->unzipDirectory = PATH_INSTALL . '/cloudloader/work';
        $this->backupDirectory = PATH_INSTALL . '/backup';
        $this->glob_pattern = '*/catalog/mailhive';
        $this->exclude_overwrite = array('');
        $this->exclude_overwrite_package = array('');


        $this->logFile = PATH_INSTALL . '/cloudloader/install.log';

        // some servers do not allow to create a zip-file on root level
        $this->backup_file = (isset($_SESSION['mailbeez_installer_backup_location'])) ? $_SESSION['mailbeez_installer_backup_location'] : '/mailhive' . date("Ymd-His") . '.zip';


        $this->apikey = CLOUDLOADER_API_KEY;

    }

    public function run()
    {

        $_SESSION['mailbeez_installer_backup_location'] = $this->backup_file;
        $_SESSION['mailbeez_installer_backup_location_dir'] = $this->backupDirectory . $this->backup_file;


        if (!is_null($handler = $this->post('handler'))) {
            if (!strlen($handler)) exit;

            try {
                $this->log('Execute AJAX handler: %s', $handler);

                if (!preg_match('/^on[A-Z]{1}[\w+]*$/', $handler))
                    throw new Exception(sprintf('Invalid handler: %s', $handler));

                if (method_exists($this, $handler) && ($result = $this->$handler()) !== null) {
                    header('Content-Type: application/json');
                    die(json_encode($result));
                }
            } catch (Exception $ex) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                $this->log('Handler error (%s): %s', $handler, $ex->getMessage());
                $this->log(array('Trace log:', '%s'), $ex->getTraceAsString());
                die($ex->getMessage());
            }

            exit;
        }

    }

    protected function onCheckRequirement()
    {
        $checkCode = $this->post('code');
        $this->log('System check: %s', $checkCode);

        $result = false;
        switch ($checkCode) {
            case 'liveConnection':
                $check = $this->requestServerData('ping', '', 'public');
                $result = (is_array($check) && $check['result']);
                break;
            case 'liveConnectionPrivate':
                $check = $this->requestServerData('ping', '', 'private');
                $result = (is_array($check) && $check['result']);
                break;
            case 'writePermission':
                $result = (is_writable(PATH_INSTALL)
                    && is_writable($this->tempDirectory)
                    && is_writable($this->unzipDirectory)
                    && is_writable($this->backupDirectory));
                break;
            case 'phpVersion':
                $result = version_compare(PHP_VERSION, "5.2", ">=");
                break;
            case 'safeMode':
                $result = !ini_get('safe_mode');
                break;
            case 'pdoLibrary':
                $result = defined('PDO::ATTR_DRIVER_NAME');
                break;
            case 'mcryptLibrary':
                $result = extension_loaded('mcrypt');
                break;
            case 'gdLibrary':
                $result = extension_loaded('gd');
                break;
            case 'curlLibrary':
                $result = function_exists('curl_init');
                break;
            case 'zipLibrary':
                $result = class_exists('ZipArchive');
                break;
        }

        $this->log('Requirement %s %s', $checkCode, ($result ? '+OK' : '=FAIL'));
        return array('result' => $result);
    }

    protected function onValidateAdvancedConfig()
    {
//        if (!strlen($this->post('encryption_code')))
//            throw new CloudloaderException('Please specify encryption key', 'encryption_code');

//        if (strlen($this->post('encryption_code')) < 6)
//            throw new CloudloaderException('The encryption key should be at least 6 characters in length.', 'encryption_code');

        if (!strlen($this->post('folder_mask')))
            throw new CloudloaderException('Please specify folder permission mask', 'folder_mask');

        if (!strlen($this->post('file_mask')))
            throw new CloudloaderException('Please specify file permission mask', 'file_mask');

        if (!preg_match("/^[0-9]{3}$/", $this->post('folder_mask')) || $this->post('folder_mask') > 777)
            throw new CloudloaderException('Please specify a valid folder permission mask', 'folder_mask');

        if (!preg_match("/^[0-9]{3}$/", $this->post('file_mask')) || $this->post('file_mask') > 777)
            throw new CloudloaderException('Please specify a valid file permission mask', 'file_mask');
    }

    protected function onGetPopularPlugins()
    {
        return $this->requestServerData('plugin/popular');
    }

    protected function onGetPopularThemes()
    {
        return $this->requestServerData('theme/popular');
    }

    protected function onSearchPlugins()
    {
        return $this->requestServerData('plugin/search', array('query' => $this->post('query')));
    }

    protected function onSearchThemes()
    {
        return $this->requestServerData('theme/search', array('query' => $this->post('query')));
    }

    protected function onPluginDetails()
    {
        return $this->requestServerData('plugin/detail', array('name' => $this->post('name')));
    }

    protected function onThemeDetails()
    {
        return $this->requestServerData('theme/detail', array('name' => $this->post('name')));
    }

    protected function onProjectDetails()
    {
        return $this->requestServerData('project/detail', array('id' => $this->post('code')));
    }

    protected function onInstallStep()
    {
        $installStep = $this->post('step');
        $this->log('Install step: %s', $installStep);
        $result = false;

        switch ($installStep) {
            case 'getMetaDataCore':
                $result = $this->requestServerData('core/install', '', 'public');
                $this->log('getMetaDataCore:' . print_r($result, true));
                if (!$result) {
                    throw new Exception('Unable to get core information');
                }
                break;

            case 'downloadCore':
                $hash = $this->getHashFromMeta('core');
                $result = $this->requestServerFile('core', $hash, 'core/get', array('type' => 'install'), 'public');

                if (!$result) {
                    throw new Exception('Unable to open plugin archive file');
                }
                break;

            // todo package installation

            case 'getMetaDataPackage':

                // todo

                $result = $this->requestServerData('package/install', '', 'private');
                $this->log('getMetaDataPackage:' . print_r($result, true));
                if (!$result) {
                    throw new Exception('Unable to get package information');
                }

                break;

            case 'downloadPackage':
                // todo

                $hash = $this->getHashFromMeta('package');
                $this->requestServerFile('package', $hash, 'package/get', array('name' => 'todo: packagetype'), 'private');
                $this->log('downloadPackage:' . print_r($result, true));
                break;

            // todo

            /*
            $name = $this->post('name');
            if (!$name)
                throw new Exception('Plugin download failed, missing name');

            $params = array('name' => $name);
            if ($project = $this->post('project', false))
                $params['project'] = $project;

            $hash = $this->getHashFromMeta($name, 'plugin');
            $this->requestServerFile($name, $hash, 'plugin/get', $params);
            break;
            */

            case 'backupZip':

                $exclude_dirs = array('common/templates_c', 'cloudbeez', 'cloudbeez/cloudloader/work', 'cloudbeez/cloudloader/temp', 'cloudbeez/backup');

                $backup_result = $this->backup('../mailhive/', $this->backupDirectory, $this->backup_file, $exclude_dirs);
                unset($_SESSION['mailbeez_installer_backup_location']);

                if (!$backup_result) {
                    throw new Exception('Unable to backup application files');
                }

                break;

            case 'checkFilePermission':
                $workpath = $this->extract_zip($this->getFilePath('core'), $this->unzipDirectory);
                if (!$workpath) {
                    throw new Exception('Unable to extract application files');
                }

                $write_test = $this->test_deploy_files($workpath, $this->deployDirectory, $this->exclude_overwrite, $this->glob_pattern);
                if (!$write_test) {
                    throw new Exception('Could not extract application files (not writeable) ' . $write_test);
                }

                $_SESSION['mailbeez_installer_workpath'] = $workpath;

                return true;
                break;

            case 'checkFilePermissionPackage':

                $this->debug_output("getFilePath('package') " . $this->getFilePath('package'));
                $workpath = $this->extract_zip($this->getFilePath('package'), $this->unzipDirectory);

                $this->debug_output("starting checkFilePermissionPackage: workpath $workpath\n");


                if (!$workpath) {
                    throw new Exception('Unable to extract package files');
                }

                $this->debug_output("starting checkFilePermissionPackage: workpath $workpath\n");


                $write_test = $this->test_deploy_files($workpath, $this->deployDirectory, $this->exclude_overwrite_package, $this->glob_pattern);
                if (!$write_test) {
                    throw new Exception('Could not extract package files (not writeable) ' . $write_test);
                }

                $_SESSION['mailbeez_package_installer_workpath'] = $workpath;

                return true;
                break;

            case 'extractCore':
                $workpath = $_SESSION['mailbeez_installer_workpath'];

                $result = $this->deploy_files($workpath, $this->deployDirectory, $this->exclude_overwrite, $this->glob_pattern);

                $this->delete_folder($workpath);

                unset($_SESSION['mailbeez_installer_workpath']);

                if (!$result) {
                    throw new Exception('Unable to deploy application files');
                }

                // todo
                // update signature


                break;

            case 'extractPackage':

                $workpath = $_SESSION['mailbeez_package_installer_workpath'];

                $this->debug_output("starting extractPackage: workpath $workpath\n");


                $result = $this->deploy_files($workpath, $this->deployDirectory, $this->exclude_overwrite_package, $this->glob_pattern);

                $this->delete_folder($workpath);

                unset($_SESSION['mailbeez_package_installer_workpath']);

                if (!$result) {
                    throw new Exception('Unable to deploy package files');
                }

                // update signature
                $hashFile = $this->deployDirectory . 'package.hash';
                if (file_exists($hashFile)) {
                    $package_hash = file_get_contents($hashFile);
                    mh_insert_config_value(array('configuration_title' => 'Package hash',
                        'configuration_key' => 'MAILBEEZ_CLOUDBEEZ_PACKAGE_HASH',
                        'configuration_value' => $package_hash,
                        'configuration_description' => 'set automatically',
                        'set_function' => ''
                    ), true);
                }

                $this->debug_output("set package hash: $package_hash\n");


                break;

            case 'finishInstall':
                break;
        }

        $this->log('Step %s +OK', $installStep);

        return array('result' => $result);
    }

    //
    // Installation Steps
    //


    public function setCoreBuild()
    {
        /*
        $this->bootFramework();

        call_user_func('System\Models\Parameters::set', array(
            'system::core.hash'  => post('hash'),
            'system::core.build' => post('build'),
        ));
        */
    }

    //
    // File Management
    //

    private function moveHtaccess($old = null, $new = null)
    {
        $oldFile = $this->baseDirectory . '/.htaccess';
        if ($old) $oldFile .= '.' . $old;

        $newFile = $this->baseDirectory . '/.htaccess';
        if ($new) $newFile .= '.' . $new;

        if (file_exists($oldFile))
            rename($oldFile, $newFile);
    }

    private function unzipFile($fileCode, $directory = null)
    {
        $source = $this->getFilePath($fileCode);
        $destination = $this->unzipDirectory;

        $this->log('Extracting file (%s): %s', $fileCode, basename($source));

        if ($directory)
            $destination .= '/' . $directory;

        if (!file_exists($destination))
            mkdir($destination, $this->dir_permissions, true);

        $zip = new ZipArchive;
        if ($zip->open($source) === true) {
            $zip->extractTo($destination);
            $zip->close();
            return true;
        }

        return false;
    }

    private function getFilePath($fileCode)
    {
        $name = md5($fileCode) . '.arc';
        return $this->tempDirectory . '/' . $name;
    }

    public function cleanWorkDirectory()
    {
        // delete all folders underneath $this->unzipDirectory;
        $dir = $this->unzipDirectory;
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            if ($file == 'index.html') {
                continue;
            }
            (is_dir("$dir/$file")) ? $this->delete_folder("$dir/$file") : unlink("$dir/$file");
        }
    }


    //
    // Logging
    //

    public function cleanLog()
    {
        $message = array(
            "====================================================================.",
            "========================== INSTALLATION LOG ========================'",
            "",
        );

        file_put_contents($this->logFile, implode(PHP_EOL, $message) . PHP_EOL);
    }

    public function log()
    {
        $args = func_get_args();
        $message = array_shift($args);

        if (is_array($message))
            $message = implode(PHP_EOL, $message);

        $filename = $this->logFile;
        $stream = fopen($filename, 'a');
        $string = "[" . date("Y/m/d h:i:s", time()) . "] " . vsprintf($message, $args);
        fwrite($stream, $string . PHP_EOL);
        fclose($stream);
    }

    //
    // Helpers
    //

    private function bootFramework()
    {
        require $this->baseDirectory . '/bootstrap/autoload.php';
        $this->app = $app = require_once $this->baseDirectory . '/bootstrap/start.php';
        $app->boot();
    }

    private function requestServerData($uri = null, $params = array(), $auth_mode = 'private')
    {
        $result = null;
        $error = null;
        try {
            $curl = $this->prepareServerRequest($uri, $params, $auth_mode);
            $result = curl_exec($curl);

            $this->log('Server request: %s', $uri);

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($httpCode == 500) {
                $error = $result;
                $result = '';
            }

            $this->log('Request information: %s', print_r(curl_getinfo($curl), true));

            curl_close($curl);
        } catch (Exception $ex) {
            $this->log('Failed to get server data (ignored): ' . $ex->getMessage());
        }
        if ($error !== null)
            throw new Exception('Server responded with error: ' . $error);

        if (!$result || !strlen($result))
            throw new Exception('Server responded had no response.');

        try {
            $_result = @json_decode($result, true);
        } catch (Exception $ex) {
        }

        if (!is_array($_result)) {
            $this->log('Server response: ' . $result);
            throw new Exception('Server returned an invalid response.');
        }

        return $_result;
    }

    private function requestServerFile($fileCode, $expectedHash, $uri = null, $params = array(), $auth_mode = 'private')
    {
        $result = null;
        $error = null;


        try {

            if (!file_exists($this->tempDirectory))
                mkdir($this->tempDirectory, $this->dir_permissions, true);

            $filePath = $this->getFilePath($fileCode);
            $stream = fopen($filePath, 'w');


            $curl = $this->prepareServerRequest($uri, $params, $auth_mode);
            curl_setopt($curl, CURLOPT_FILE, $stream);
            curl_exec($curl);


            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpCode == 500) {
                $error = file_get_contents($filePath);
            }

            curl_close($curl);
            fclose($stream);
        } catch (Exception $ex) {
            $this->log('Server request: %s', $uri);
            $this->log('Failed to get server delivery: ' . $ex->getMessage());
            throw new Exception('Server failed to deliver the package');
        }

        if ($error !== null)
            throw new Exception('Server responded with error: ' . $error);

        $fileHash = filesize($filePath);


        if ($expectedHash != $fileHash) {
            $this->log('File hash mismatch: %s (expected) vs %s (actual)', $expectedHash, $fileHash);
            $this->log('Local file size: %s', filesize($filePath));


            $filePath_failed = $filePath . '_failed';
            @unlink($filePath_failed);
            rename($filePath, $filePath_failed);

            throw new Exception("Transfer of Package files $fileCode from server $uri failed, check the content of $filePath_failed");
        }

        $this->log('Saving to file (%s): %s', $fileCode, $filePath);

        return true;
    }

    private function prepareServerRequest($uri, $params = array(), $auth_mode = 'private')
    {

        $params['p'] = urlencode(base64_encode(serialize(array('domain' => $_SERVER['SERVER_NAME'],
            'p' => MH_PLATFORM,
            'a' => MH_ID,
            'url' => base64_encode($this->getBaseUrl())))));

        // set api

        if ($auth_mode == 'private') {
            $params['apikey'] = $this->apikey;
        }

        $uri .= (stristr($uri, '?')) ? '&' : '?';
        $uri .= http_build_query($params, '', '&');

        $url = (($auth_mode == 'public') ? CLOUDBEEZ_GATEWAY_PUBLIC : CLOUDBEEZ_GATEWAY_PRIVATE) . '/' . $uri;

        $this->log('Server request: %s', $uri);


        // workaround for CURLOPT_FOLLOWLOCATION -> open_basedir conflict
        $real_url = $this->curlProcessRedirects($url);

        $curl = curl_init($real_url);

        // Issue a HEAD request and follow any redirects.

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
//        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false); // open_basedir conflict

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        if (defined('CLOUDBEEZ_GATEWAY_AUTH')) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, CLOUDBEEZ_GATEWAY_AUTH);
        }


        return $curl;
    }

    private function post($var, $default = null)
    {
        if (array_key_exists($var, $_REQUEST)) {
            $result = $_REQUEST[$var];
            if (is_string($result)) $result = trim($result);
            return $result;
        }

        return $default;
    }

    private function getHashFromMeta($targetCode, $packageType = 'plugin')
    {
        $meta = $this->post('meta');
        $packageType .= 's';

        if ($targetCode == 'core')
            return (isset($meta['core']['hash'])) ? $meta['core']['hash'] : null;

        if ($targetCode == 'package')
            return (isset($meta['package']['hash'])) ? $meta['package']['hash'] : null;

        if (!isset($meta[$packageType]))
            return null;

        $collection = $meta[$packageType];
        if (!is_array($collection))
            return null;

        foreach ($collection as $code => $hash) {
            if ($code == $targetCode)
                return $hash;
        }

        return null;
    }

    public function getBaseUrl()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $baseUrl = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $baseUrl .= '://' . $_SERVER['HTTP_HOST'];
            $baseUrl .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        } else {
            $baseUrl = 'http://localhost/';
        }

        return $baseUrl;
    }

    public function curlProcessRedirects($url)
    {
        $curl_location = curl_init($url);

        // Issue a HEAD request and follow any redirects.

        curl_setopt($curl_location, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl_location, CURLOPT_NOBODY, true);
        curl_setopt($curl_location, CURLOPT_HEADER, true);
        curl_setopt($curl_location, CURLOPT_FOLLOWLOCATION, false); // open_basedir conflict
        curl_setopt($curl_location, CURLOPT_RETURNTRANSFER, true);

        $rawResult = curl_exec($curl_location);

        // check if redirect
        if (preg_match('#Location: (.*)#', $rawResult, $r)) {
            $real_url = trim($r[1]);
            //$this->log('real url: %s', $real_url);
            return $this->curlProcessRedirects($real_url);
        } else {
            return $url;
        }

    }


    public function cleanUp()
    {
        $path = $this->tempDirectory;
        if (!file_exists($path))
            return;

        $d = dir($path);
        while (($entry = $d->read()) !== false) {
            $filePath = $path . '/' . $entry;

            if ($entry == '.' || $entry == '..' || $entry == '.htaccess' || is_dir($filePath))
                continue;

            $this->log('Cleaning up file: %s', $entry);
            @unlink($filePath);
        }

        $d->close();
    }

    public function getContent($route, $params)
    {

        $result = null;
        $error = null;
        try {
            $curl = $this->prepareServerRequest('content/' . $route, $params, 'public');
            $content = curl_exec($curl);

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($httpCode == 500) {
                $error = $result;
                $result = '';
            }

            curl_close($curl);
        } catch (Exception $ex) {
            $this->log('Failed to get server data (ignored): ' . $ex->getMessage());
        }

        $a = 'a=' . MH_ID;
        $content = preg_replace("#href=\"(([a-zA-Z]+://)([a-zA-Z0-9%.;:/=+_-]*[?]+[a-zA-Z0-9&%.;:/=+_-]*))\"#", "href=\"$1" . "&" . $a . "\"", $content);
        $content = preg_replace("#href=\"(([a-zA-Z]+://)([a-zA-Z0-9%.;:/=+_-]*))\"#", "href=\"$1" . "?" . $a . "\"", $content);
        return $content;
    }
}


?>