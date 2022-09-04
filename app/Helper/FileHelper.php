<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * File helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class FileHelper extends Base
{
    /**
     * Get file icon
     *
     * @access public
     * @param  string   $filename   Filename
     * @return string               Font-Awesome-Icon-Name
     */
    public function icon($filename)
    {
        switch (get_file_extension($filename)) {
            case 'jpeg':
            case 'jpg':
            case 'png':
            case 'gif':
            case 'svg':
                return 'fa-file-image-o';
            case 'xls':
            case 'xlsx':
            case 'xlsm':
                return 'fa-file-excel-o';
            case 'doc':
            case 'docx':
                return 'fa-file-word-o';
            case 'ppt':
            case 'pptx':
                return 'fa-file-powerpoint-o';
            case 'zip':
            case 'rar':
            case 'tar':
            case 'bz2':
            case 'xz':
            case 'gz':
                return 'fa-file-archive-o';
            case 'mp3':
            case 'amr':
            case 'flac':
            case 'm4a':
            case 'ogg':
            case 'opus':
            case 'wav':
            case 'wma':
            case 'midi':
            case 'mid':
                return 'fa-file-audio-o';
            case 'avi':
            case 'mov':
            case 'mp4':
            case 'mkv':
            case 'webm':
                return 'fa-file-video-o';
            case 'php':
            case 'html':
            case 'css':
            case 'js':
                return 'fa-file-code-o';
            case 'pdf':
                return 'fa-file-pdf-o';
        }

        return 'fa-file-o';
    }

    /**
     * Return the image mimetype based on the file extension
     *
     * @access public
     * @param  $filename
     * @return string
     */
    public function getImageMimeType($filename)
    {
        switch (get_file_extension($filename)) {
            case 'jpeg':
            case 'jpg':
                return 'image/jpeg';
            case 'png':
                return 'image/png';
            case 'gif':
                return 'image/gif';
            default:
                return 'image/jpeg';
        }
    }

    /**
     * Get the preview type
     *
     * @access public
     * @param  string $filename
     * @return string
     */
    public function getPreviewType($filename)
    {
        switch (get_file_extension($filename)) {
            case 'md':
            case 'markdown':
                return 'markdown';
            case 'txt':
                return 'text';
        }

        return null;
    }

    /**
     * Return the browser view mime-type based on the file extension.
     *
     * @access public
     * @param  $filename
     * @return string
     */
    public function getBrowserViewType($filename)
    {
        switch (get_file_extension($filename)) {
            case 'pdf':
                return 'application/pdf';
            case 'mp3':
            case 'ogg':
            case 'flac':
            case 'wav':
                return 'audio/mpeg';
            case 'avi':
                return 'video/x-msvideo';
            case 'webm':
                return 'video/webm';
            case 'mov':
                return 'video/quicktime';
            case 'm4v':
                return 'video/x-m4v';
            case 'mp4':
                return 'video/mp4';
            case 'svg':
                return 'image/svg+xml';
        }

        return null;
    }
}
