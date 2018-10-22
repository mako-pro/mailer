<?php

namespace placer\mailer\helpers;

use Exception;

class MIMEtypes
{

    /**
     * Mime types
     *
     * @var array
     */
    private static $mimeTypes =
    [
        'aac'        => 'audio/aac',
        'atom'       => 'application/atom+xml',
        'avi'        => 'video/avi',
        'bmp'        => 'image/x-ms-bmp',
        'c'          => 'text/x-c',
        'class'      => 'application/octet-stream',
        'css'        => 'text/css',
        'csv'        => 'text/csv',
        'deb'        => 'application/x-deb',
        'dll'        => 'application/x-msdownload',
        'dmg'        => 'application/x-apple-diskimage',
        'doc'        => 'application/msword',
        'docx'       => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'exe'        => 'application/octet-stream',
        'flv'        => 'video/x-flv',
        'gif'        => 'image/gif',
        'gz'         => 'application/x-gzip',
        'h'          => 'text/x-c',
        'htm'        => 'text/html',
        'html'       => 'text/html',
        'ics'        => 'text/calendar',
        'ical'       => 'text/calendar',
        'ini'        => 'text/plain',
        'jar'        => 'application/java-archive',
        'java'       => 'text/x-java',
        'jpeg'       => 'image/jpeg',
        'jpg'        => 'image/jpeg',
        'js'         => 'text/javascript',
        'json'       => 'application/json',
        'jp2'        => 'image/jp2',
        'mid'        => 'audio/midi',
        'midi'       => 'audio/midi',
        'mka'        => 'audio/x-matroska',
        'mkv'        => 'video/x-matroska',
        'mp3'        => 'audio/mpeg',
        'mp4'        => 'video/mp4',
        'mpeg'       => 'video/mpeg',
        'mpg'        => 'video/mpeg',
        'm4a'        => 'video/mp4',
        'm4v'        => 'video/mp4',
        'odt'        => 'application/vnd.oasis.opendocument.text',
        'ogg'        => 'audio/ogg',
        'pdf'        => 'application/pdf',
        'php'        => 'text/x-php',
        'png'        => 'image/png',
        'psd'        => 'image/vnd.adobe.photoshop',
        'py'         => 'application/x-python',
        'ra'         => 'audio/vnd.rn-realaudio',
        'ram'        => 'audio/vnd.rn-realaudio',
        'rar'        => 'application/x-rar-compressed',
        'rss'        => 'application/rss+xml',
        'safariextz' => 'application/x-safari-extension',
        'sh'         => 'text/x-shellscript',
        'shtml'      => 'text/html',
        'swf'        => 'application/x-shockwave-flash',
        'tar'        => 'application/x-tar',
        'tif'        => 'image/tiff',
        'tiff'       => 'image/tiff',
        'torrent'    => 'application/x-bittorrent',
        'txt'        => 'text/plain',
        'wav'        => 'audio/wav',
        'webp'       => 'image/webp',
        'wma'        => 'audio/x-ms-wma',
        'xls'        => 'application/vnd.ms-excel',
        'xml'        => 'text/xml',
        'zip'        => 'application/zip',
        '3gp'        => 'video/3gpp',
        '3g2'        => 'video/3gpp2',
    ];

    /**
     * Returns the MIME type file
     *
     * @param  string  $ext  File extension
     * @return string|Exception
     */
    public static function get(string $ext)
    {
        if (array_key_exists($ext, static::$mimeTypes))
        {
            return static::$mimeTypes[$ext];
        }

        throw new Exception('Unknown MIME type for given extension: '.$ext);
    }
    
}
