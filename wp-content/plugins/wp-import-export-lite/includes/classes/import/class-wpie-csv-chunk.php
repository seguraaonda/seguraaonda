<?php

namespace wpie\import\chunk\csv;

use WP_Error;
use wpie\lib\xml\array2xml;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'wp-import-export-lite'));
}

if (file_exists(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-chunk.php')) {
    require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-chunk.php');
}

class WPIE_CSV_Chunk extends \wpie\import\chunk\WPIE_Chunk {

    public function __construct() {
        
    }

    public function process_csv($fileDir = "", $file_name = "", $baseDir = "", $wpie_csv_delimiter = ",", $wpie_xml_fileName) {

        $file = WPIE_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . $file_name;

        $newFileDir = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse";

        if (!file_exists($file)) {
            return new \WP_Error('wpie_import_error', __('File not found', 'wp-import-export-lite'));
        }

        if (file_exists(WPIE_LIBRARIES_DIR . '/xml/class-wpie-array2xml.php')) {
            require_once(WPIE_LIBRARIES_DIR . '/xml/class-wpie-array2xml.php');
        }

        $converter = new \wpie\lib\xml\array2xml\ArrayToXml();

        $converter->create_root("wpiedata");

        $headers = array();

        $wfp = fopen($file, "rb");

        while (($keys = fgetcsv($wfp, 0, $wpie_csv_delimiter)) !== false) {

            if (empty($headers)) {

                foreach ($keys as $key => $value) {

                    $value = trim(strtolower(preg_replace('/[^a-z0-9_]/i', '', $value)));

                    if (preg_match('/^[0-9]{1}/', $value)) {
                        $value = 'el_' . trim(strtolower($value));
                    }

                    $value = (!empty($value)) ? $value : 'undefined' . $key;

                    if (isset($headers[$key])) {
                        $key = $this->unique_array_key_name($key, $headers);
                    }

                    $headers[$key] = $value;
                }

                continue;
            }

            $fileData = array();

            foreach ($keys as $key => $value) {

                $header = isset($headers[$key]) ? $headers[$key] : "";

                if (!empty($header)) {

                    if (isset($fileData[$header])) {
                        $header = $this->unique_array_key_name($header, $fileData);
                    }

                    $fileData[$header] = $value;
                }
            }

            $converter->addNode($converter->root, "item", $fileData, 0);

            unset($fileData);
        }

        $converter->saveFile($newFileDir . '/' . $wpie_xml_fileName . '1.xml');

        unset($file, $newFileDir, $converter, $headers);

        return true;
    }

    private function unique_array_key_name($key = "", $array = array()) {

        $count = 1;

        $new_key = $key;

        while (isset($array[$key])) {

            $key = $new_key . "_" . $count;
            $count++;
        }

        unset($count, $new_key);

        return $key;
    }

}
