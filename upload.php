<?php
error_reporting(0);
date_default_timezone_set('Asia/Shanghai');
define('BASE_PATH', '');
define('BASE_URL', '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (!isset($_FILES['file'])) {
    $res = [
        'result' => 'failed',
    ];
} elseif ($_FILES['file']['error']) {
    $res = [
        'result' => 'failed',
        'error' => $_FILES['file']['error'],
    ];
} else {
    $name = date('Ymd_His_') . sanitize_file_name($_FILES['file']['name']);
    $path = BASE_PATH . $name;
    if (file_exists($path)) {
        $res = [
            'result' => 'failed',
        ];
    } else {
        move_uploaded_file($_FILES['file']['tmp_name'], $path);
        $res = [
            'result' => 'succeed',
            'url' => BASE_URL . $name,
        ];
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

/**
 * Sanitizes a filename, replacing whitespace with dashes.
 *
 * Removes special characters that are illegal in filenames on certain
 * operating systems and special characters requiring special escaping
 * to manipulate at the command line. Replaces spaces and consecutive
 * dashes with a single dash. Trims period, dash and underscore from beginning
 * and end of filename. It is not guaranteed that this function will return a
 * filename that is allowed to be uploaded.
 *
 * @since 2.1.0
 *
 * @param string $filename The filename to be sanitized
 *
 * @return string The sanitized filename
 */
function sanitize_file_name($filename)
{
    $filename_raw = $filename;
    $special_chars = ["?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", "%", "+", chr(0)];
    $filename = preg_replace("#\x{00a0}#siu", ' ', $filename);
    $filename = str_replace($special_chars, '', $filename);
    $filename = str_replace(['%20', '+'], '-', $filename);
    $filename = preg_replace('/[\r\n\t -]+/', '-', $filename);
    $filename = trim($filename, '.-_');
    // Split the filename into a base and extension[s]
    $parts = explode('.', $filename);
    // Return if only one extension
    if (count($parts) <= 2) {
        return $filename;
    }
    // Process multiple extensions
    $filename = array_shift($parts);
    $extension = array_pop($parts);
    $mimes = [
        // Image formats
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'bmp' => 'image/bmp',
        'tif|tiff' => 'image/tiff',
        'ico' => 'image/x-icon',

        // Video formats
        'asf|asx' => 'video/x-ms-asf',
        'wmv' => 'video/x-ms-wmv',
        'wmx' => 'video/x-ms-wmx',
        'wm' => 'video/x-ms-wm',
        'avi' => 'video/avi',
        'divx' => 'video/divx',
        'flv' => 'video/x-flv',
        'mov|qt' => 'video/quicktime',
        'mpeg|mpg|mpe' => 'video/mpeg',
        'mp4|m4v' => 'video/mp4',
        'ogv' => 'video/ogg',
        'webm' => 'video/webm',
        'mkv' => 'video/x-matroska',

        // Text formats
        'txt|asc|c|cc|h' => 'text/plain',
        'csv' => 'text/csv',
        'tsv' => 'text/tab-separated-values',
        'ics' => 'text/calendar',
        'rtx' => 'text/richtext',
        'css' => 'text/css',
        'htm|html' => 'text/html',

        // Audio formats
        'mp3|m4a|m4b' => 'audio/mpeg',
        'ra|ram' => 'audio/x-realaudio',
        'wav' => 'audio/wav',
        'ogg|oga' => 'audio/ogg',
        'mid|midi' => 'audio/midi',
        'wma' => 'audio/x-ms-wma',
        'wax' => 'audio/x-ms-wax',
        'mka' => 'audio/x-matroska',

        // Misc application formats
        'rtf' => 'application/rtf',
        'js' => 'application/javascript',
        'pdf' => 'application/pdf',
        'swf' => 'application/x-shockwave-flash',
        'class' => 'application/java',
        'tar' => 'application/x-tar',
        'zip' => 'application/zip',
        'gz|gzip' => 'application/x-gzip',
        'rar' => 'application/rar',
        '7z' => 'application/x-7z-compressed',
        'exe' => 'application/x-msdownload',

        // MS Office formats
        'doc' => 'application/msword',
        'pot|pps|ppt' => 'application/vnd.ms-powerpoint',
        'wri' => 'application/vnd.ms-write',
        'xla|xls|xlt|xlw' => 'application/vnd.ms-excel',
        'mdb' => 'application/vnd.ms-access',
        'mpp' => 'application/vnd.ms-project',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
        'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',

        // OpenOffice formats
        'odt' => 'application/vnd.oasis.opendocument.text',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odf' => 'application/vnd.oasis.opendocument.formula',

        // WordPerfect formats
        'wp|wpd' => 'application/wordperfect',

        // iWork formats
        'key' => 'application/vnd.apple.keynote',
        'numbers' => 'application/vnd.apple.numbers',
        'pages' => 'application/vnd.apple.pages',
    ];
    /*
     * Loop over any intermediate extensions. Postfix them with a trailing underscore
     * if they are a 2 - 5 character long alpha string not in the extension whitelist.
     */
    foreach ((array)$parts as $part) {
        $filename .= '.' . $part;
        if (preg_match("/^[a-zA-Z]{2,5}\d?$/", $part)) {
            $allowed = false;
            foreach ($mimes as $ext_preg => $mime_match) {
                $ext_preg = '!^(' . $ext_preg . ')$!i';
                if (preg_match($ext_preg, $part)) {
                    $allowed = true;
                    break;
                }
            }
            if (!$allowed) {
                $filename .= '_';
            }
        }
    }
    $filename .= '.' . $extension;
    return $filename;
}