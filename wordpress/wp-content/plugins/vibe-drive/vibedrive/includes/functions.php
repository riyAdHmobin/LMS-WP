<?php
/**
 * VibeDrive Functions
 *
 * @author 		VibeThemes
 * @category 	Init
 * @package 	vibedrive/Includes
 * @version     1.0
 */

if (! defined('ABSPATH')) {
    exit;
}

function vibe_drive_get_security()
{
    $init = VibeDrive_Init::init();
    if (!empty($init->security)) {
        return $init->security;
    }

    $init->security = get_transient('vibedrive_security');
    if (empty($init->security)) {
        $init->security =wp_create_nonce('security');
        set_transient('vibedrive_security', $init->security, 12 * HOUR_IN_SECONDS);
    }

    return $init->security;
}

function vibe_drive_upload_scopes()
{
    return apply_filters('vibedrive_upload_file_scope', array(
        'personal'=>__('Personal', 'vibedrive'),
        'shared'=>__('Shared with me', 'vibedrive'),
        'group'=>__('Shared in Group', 'vibedrive'),
    ));
}

function vibe_drive_sharing_scopes()
{
    return apply_filters('vibedrive_share_file_scope', array(
        'personal'=>__('Personal', 'vibedrive'),
        'shared'=>__('Shared', 'vibedrive'),
        'group'=>__('Group', 'vibedrive'),
    ));
}

function vibedrive_getFilesMimeTypes()
{
    return apply_filters(
        'vibedrive_uploaded_file_mime_types',
        array(
            'JPG' => array(
                'image/jpeg',
                'image/jpg',
                'image/jp_',
                'application/jpg',
                'application/x-jpg',
                'image/pjpeg',
                'image/pipeg',
                'image/vnd.swiftview-jpeg',
                'image/x-xbitmap'
            ),
            'GIF' => array(
                'image/gif',
                'image/x-xbitmap',
                'image/gi_'
            ),
            'PNG' => array(
                'image/png',
                'application/png',
                'application/x-png'
            ),
            'DOCX'=> array(
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ),
            'RAR'=> array(
                'application/x-rar'
            ),
            'ZIP' => array(
                'application/zip',
                'application/x-zip',
                'application/x-zip-compressed',
                'application/x-compress',
                'application/x-compressed',
                'multipart/x-zip'
            ),
            'DOC' => array(
                'application/msword',
                'application/doc',
                'application/text',
                'application/vnd.msword',
                'application/vnd.ms-word',
                'application/winword',
                'application/word',
                'application/x-msw6',
                'application/x-msword'
            ),
            'PDF' => array(
                'application/pdf',
                'application/x-pdf',
                'application/acrobat',
                'applications/vnd.pdf',
                'text/pdf',
                'text/x-pdf'
            ),
            'PPT' => array(
                'application/vnd.ms-powerpoint',
                'application/mspowerpoint',
                'application/ms-powerpoint',
                'application/mspowerpnt',
                'application/vnd-mspowerpoint',
                'application/powerpoint',
                'application/x-powerpoint',
                'application/x-m'
            ),
            'PPTX'=> array(
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ),
            'PPS' => array(
                'application/vnd.ms-powerpoint'
            ),
            'PPSX'=> array(
                'application/vnd.openxmlformats-officedocument.presentationml.slideshow'
            ),
            'PSD' => array(
                'application/octet-stream',
                'image/vnd.adobe.photoshop'
            ),
            'ODT' => array(
                'application/vnd.oasis.opendocument.text',
                'application/x-vnd.oasis.opendocument.text'
            ),
            'XLS' => array(
                'application/vnd.ms-excel',
                'application/msexcel',
                'application/x-msexcel',
                'application/x-ms-excel',
                'application/vnd.ms-excel',
                'application/x-excel',
                'application/x-dos_ms_excel',
                'application/xls'
            ),
            'XLSX'=> array(
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            ),
            'MP3' => array(
                'audio/mpeg',
                'audio/x-mpeg',
                'audio/mp3',
                'audio/x-mp3',
                'audio/mpeg3',
                'audio/x-mpeg3',
                'audio/mpg',
                'audio/x-mpg',
                'audio/x-mpegaudio'
            ),
            'M4A' => array(
                'audio/mp4a-latm',
                'audio/m4a',
                'audio/mp4'
            ),
            'OGG' => array(
                'audio/ogg',
                'application/ogg'
            ),
            'WAV' => array(
                'audio/wav',
                'audio/x-wav',
                'audio/wave',
                'audio/x-pn-wav'
            ),
            'WMA' => array(
                'audio/x-ms-wma'
            ),
            'MP4' => array(
                'video/mp4v-es',
                'audio/mp4',
                'video/mp4'
            ),
            'M4V' => array(
                'video/mp4',
                'video/x-m4v'
            ),
            'MOV' => array(
                'video/quicktime',
                'video/x-quicktime',
                'image/mov',
                'audio/aiff',
                'audio/x-midi',
                'audio/x-wav',
                'video/avi'
            ),
            'WMV' => array(
                'video/x-ms-wmv'
            ),
            'AVI' => array(
                'video/avi',
                'video/msvideo',
                'video/x-msvideo',
                'image/avi',
                'video/xmpg2',
                'application/x-troff-msvideo',
                'audio/aiff',
                'audio/avi'
            ),
            'MPG' => array(
                'video/avi',
                'video/mpeg',
                'video/mpg',
                'video/x-mpg',
                'video/mpeg2',
                'application/x-pn-mpg',
                'video/x-mpeg',
                'video/x-mpeg2a',
                'audio/mpeg',
                'audio/x-mpeg',
                'image/mpg'
            ),
            'OGV' => array(
                'video/ogg'
            ),
            '3GP' => array(
                'audio/3gpp',
                'video/3gpp'
            ),
            '3G2' => array(
                'video/3gpp2',
                'audio/3gpp2'
            ),
            'FLV' => array(
                'video/x-flv'
            ),
            'WEBM'=> array(
                'video/webm'
            ),
            'APK' => array(
                'application/vnd.android.package-archive'
            ),
        )
    );
}



function convertToReadableSize($size)
{
    $base = log($size) / log(1024);
    $suffix = array("", "KB", "MB", "GB", "TB");
    $f_base = floor($base);
    return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
}


function vibedrive_usercan($user_id, $file_id, $action)
{
    return true;
}
