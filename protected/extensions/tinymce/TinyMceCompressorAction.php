<?php
/**
 * Some code come from:
 *
 * tiny_mce_gzip.php Version 2.0.4 (2011-03-23)
 *
 * Copyright 2010, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 */

class TinyMceCompressorAction extends CAction
{
    private static $defaultSettings = array(
        "plugins" => "",
        "themes" => "",
        "languages" => "",
        "disk_cache" => true,
        "expires" => "30d",
        "cache_dir" => "",
        "tinymce_dir" => "",
        "compress" => true,
        "files" => "",
        "source" => false,
        'baseUrl' => "",
    );
    /**
     * Add any site-specific defaults here that you may wish to implement. For example:
     * array(
     *  "languages" => "en",
     *  "expires"   => "1m",
     * );
     */
    public $settings = array();

    /**
     * Returns TinyMCE script url
     * @static
     * @param $route string route to compressor action
     * @param $settings array name/value array with settings for the script.
     * @return string url for script
     */
    public static function scripUrl($route, $settings)
    {

        $settings = array_merge(self::$defaultSettings, $settings);

        $urlParams = array();

        $urlParams['js'] = '1';

        // Add plugins
        if (isset($settings["plugins"])) {
            $urlParams['plugins'] = (is_array($settings["plugins"]) ? implode(',', $settings["plugins"]) : $settings["plugins"]);
        }

        // Add themes
        if (isset($settings["themes"])) {
            $urlParams['themes'] = (is_array($settings["themes"]) ? implode(',', $settings["themes"]) : $settings["themes"]);
        }

        // Add languages
        if (isset($settings["languages"])) {
            $urlParams['languages'] = (is_array($settings["languages"]) ? implode(',', $settings["languages"]) : $settings["languages"]);
        }

        // Add disk_cache
        if (isset($settings["disk_cache"])) {
            $urlParams['diskcache'] = ($settings["disk_cache"] === true ? "true" : "false");
        }

        // Add any explicitly specified files if the default settings have been overriden by the tag ones
        /*
         * Specifying tag files will override (rather than merge with) any site-specific ones set in the
         * TinyMCE_Compressor object creation.  Note that since the parameter parser limits content to alphanumeric
         * only base filenames can be specified.  The file extension is assumed to be ".js" and the directory is
         * the TinyMCE root directory.  A typical use of this is to include a script which initiates the TinyMCE object.
         */
        if (isset($settings["files"])) {
            $urlParams['files'] = (is_array($settings["files"]) ? implode(',', $settings["files"]) : $settings["files"]);
        }

        // Add src flag
        if (isset($settings["source"])) {
            $urlParams['src'] = ($settings["source"] === true ? "true" : "false");
        }

        return Yii::app()->createUrl($route, $urlParams);
    }

    public function run()
    {
        $this->init();
        $this->handleRequest();

        if (Yii::app()->log instanceof CLogRouter) {
            foreach (Yii::app()->log->routes as $route) {
                if ($route instanceof CWebLogRoute)
                    $route->enabled = false;
            }
        }
    }

    public function init()
    {
        $this->settings = array_merge(self::$defaultSettings, $this->settings);

        $dir = dirname(__FILE__) . '/vendors/tinymce/js/tinymce';
        if (empty($this->settings["cache_dir"]))
            $this->settings["cache_dir"] = Yii::getPathOfAlias('application.runtime');
        if (empty($this->settings["tinymce_dir"]))
            $this->settings["tinymce_dir"] = dirname(__FILE__) . '/vendors/tinymce/js/tinymce';
        if (empty($this->settings["baseUrl"]))
            $this->settings["baseUrl"] = Yii::app()->assetManager->publish($dir);
    }

    /**
     * Handles the incoming HTTP request and sends back a compressed script depending on settings and client support.
     */
    public function handleRequest()
    {
        $files = array();

        $expiresOffset = $this->parseTime($this->settings["expires"]);

        $tinymceDir = $this->settings["tinymce_dir"];

        // Override settings with querystring params
        $plugins = self::getParam("plugins");
        if ($plugins)
            $this->settings["plugins"] = $plugins;
        $plugins = explode(',', $this->settings["plugins"]);

        $themes = self::getParam("themes");
        if ($themes) // Plugins
            $plugins = self::getParam("plugins");
        if ($plugins) {
            $this->settings["plugins"] = $plugins;
        }

        //$plugins = preg_split('/,/', $this->settings["plugins"], -1, PREG_SPLIT_NO_EMPTY);

        // Themes
        $themes = self::getParam("themes");
        if ($themes) {
            $this->settings["themes"] = $themes;
        }

        $themes = preg_split('/,/', $this->settings["themes"], -1, PREG_SPLIT_NO_EMPTY);

        // Languages
        $languages = self::getParam("languages");
        if ($languages) {
            $this->settings["languages"] = $languages;
        }

        $languages = preg_split('/,/', $this->settings["languages"], -1, PREG_SPLIT_NO_EMPTY);

        // Files
        $tagFiles = self::getParam("files");
        if ($tagFiles) {
            $this->settings["files"] = $tagFiles;
        }

        // Diskcache option
        $diskCache = self::getParam("diskcache");
        if ($diskCache) {
            $this->settings["disk_cache"] = ($diskCache === "true");
        }

        // Source or minified version
        $src = self::getParam("src");
        if ($src) {
            $this->settings["source"] = ($src === "true");
        }

        // Add core js
        if (self::getParam("core", "true") === "true") {
            $files[] = "tinymce";
        }

        // Add core languages
        foreach ($languages as $language) {
            $files[] = "langs/" . $language;
        }

        // Add plugins
        foreach ($plugins as $plugin) {
            $files[] = "plugins/" . $plugin . "/plugin";

            foreach ($languages as $language) {
                $files[] = "plugins/" . $plugin . "/langs/" . $language;
            }
        }

        // Add themes
        foreach ($themes as $theme) {
            $files[] = "themes/" . $theme . "/theme";

            foreach ($languages as $language) {
                $files[] = "themes/" . $theme . "/langs/" . $language;
            }
        }

        // Add any specified files.
        $allFiles = array_merge($files, preg_split('/,/', $this->settings['files'], -1, PREG_SPLIT_NO_EMPTY));

        // Process source files
        for ($i = 0; $i < count($allFiles); $i++) {
            $file = $tinymceDir . "/" . $allFiles[$i];

            if ($this->settings["source"] && file_exists($file . ".js")) {
                $file .= ".js";
            } else if (file_exists($file . ".min.js")) {
                $file .= ".min.js";
            } else {
                $file = "";
            }

            $allFiles[$i] = $file;
        }

        // Generate hash for all files
        $hash = md5(implode('', $allFiles) . $this->settings['baseUrl']);

        // Check if it supports gzip
        $zlibOn = ini_get('zlib.output_compression') || (ini_set('zlib.output_compression', 0) === false);
        $encodings = (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) ? strtolower($_SERVER['HTTP_ACCEPT_ENCODING']) : "";
        $encoding = preg_match('/\b(x-gzip|gzip)\b/', $encodings, $match) ? $match[1] : "";

        // Is northon antivirus header
        if (isset($_SERVER['---------------']))
            $encoding = "x-gzip";

        $supportsGzip = $this->settings['compress'] && !empty($encoding) && !$zlibOn && function_exists('gzencode');

        // Set cache file name
        $cacheFile = $this->settings["cache_dir"] . "/tinymce.gzip-" . $hash . ($supportsGzip ? ".gz" : ".js");

        // Set headers
        header("Content-type: text/javascript");
        header("Vary: Accept-Encoding"); // Handle proxies
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");
        header("Cache-Control: public, max-age=" . $expiresOffset);

        if ($supportsGzip)
            header("Content-Encoding: " . $encoding);


        // Use cached file
        if ($this->settings['disk_cache'] && file_exists($cacheFile)) {
            readfile($cacheFile);
            return;
        }

        // Set base URL for where tinymce is loaded from
        $buffer = "var tinyMCEPreInit={base:'" . $this->settings['baseUrl'] . "',suffix:'.min'};";

        // Load all tinymce script files into buffer
        foreach ($allFiles as $file) {
            if ($file) {
                $fileContents = $this->getFileContents($file);
                //$buffer .= "\n//-FILE-$file (" . strlen($fileContents) . " bytes)\n";
                $buffer .= $fileContents;
            }
        }

        // Mark all themes, plugins and languages as done
        $buffer .= 'tinymce.each("' . implode(',', $files) . '".split(","),function(f){tinymce.ScriptLoader.markDone(tinyMCE.baseURL+"/"+f+".js");});';

        // Compress data
        if ($supportsGzip)
            $buffer = gzencode($buffer, 9, FORCE_GZIP);

        // Write cached file
        if ($this->settings["disk_cache"])
            @file_put_contents($cacheFile, $buffer);

        // Stream contents to client
        echo $buffer;

    }

    /**
     * Parses the specified time format into seconds. Supports formats like 10h, 10d, 10m.
     *
     * @param String $time Time format to convert into seconds.
     * @return Int Number of seconds for the specified format.
     */
    private function parseTime($time)
    {
        $multipel = 1;

        // Hours
        if (strpos($time, "h") > 0)
            $multipel = 3600;

        // Days
        if (strpos($time, "d") > 0)
            $multipel = 86400;

        // Months
        if (strpos($time, "m") > 0)
            $multipel = 2592000;

        // Trim string
        return intval($time) * $multipel;
    }

    /**
     * Returns a sanitized query string parameter.
     *
     * @param String $name Name of the query string param to get.
     * @param String $default Default value if the query string item shouldn't exist.
     * @return String Sanitized query string parameter value.
     */
    public static function getParam($name, $default = "")
    {
        if (!isset($_GET[$name]))
            return $default;

        return preg_replace("/[^0-9a-z\-_,.]+/i", "", $_GET[$name]); // Sanatize for security, remove anything but 0-9,a-z,-_,
    }

    /**
     * Returns the contents of the script file if it exists and removes the UTF-8 BOM header if it exists.
     *
     * @param String $file File to load.
     * @return String File contents or empty string if it doesn't exist.
     */
    private function getFileContents($file)
    {
        $content = file_get_contents($file);

        // Remove UTF-8 BOM
        if (substr($content, 0, 3) === pack("CCC", 0xef, 0xbb, 0xbf))
            $content = substr($content, 3);

        return $content;
    }
}
