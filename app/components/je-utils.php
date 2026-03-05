<?php

if ( ! function_exists( 'get_max_file_upload' ) ) {
    function get_max_file_upload() {
        $max_upload   = (int) ( ini_get( 'upload_max_filesize' ) );
        $max_post     = (int) ( ini_get( 'post_max_size' ) );
        $memory_limit = (int) ( ini_get( 'memory_limit' ) );
        $upload_mb    = min( $max_upload, $max_post, $memory_limit );

        return $upload_mb;
    }
}

if ( ! function_exists( 'jbp_format_bytes' ) ) {
    function jbp_format_bytes( $bytes, $precision = 2 ) {

        if ( $bytes >= 1073741824 ) {
            $bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
        } elseif ( $bytes >= 1048576 ) {
            $bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
        } elseif ( $bytes >= 1024 ) {
            $bytes = number_format( $bytes / 1024, 2 ) . ' KB';
        } elseif ( $bytes > 1 ) {
            $bytes = $bytes . ' bytes';
        } elseif ( $bytes == 1 ) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}

if ( ! function_exists( 'jbp_filter_text' ) ) {
    function jbp_filter_text( $text ) {
        $allowed_tags = wp_kses_allowed_html( 'post' );

        return wp_kses( $text, $allowed_tags );
    }
}
