<?php

if ( ! function_exists( 'happyformsmap_register_map_scripts' ) ):

    function happyformsmap_register_map_scripts( $enqueue = false, $includeAll = true ) {
        $func = $enqueue ? 'wp_enqueue_script' : 'wp_register_script';
        $func(
            'leaflet',
            'https://unpkg.com/leaflet@' . HAPPYFORMSMAP_LEAFLET_VERSION . '/dist/leaflet.js',
            [], HAPPYFORMSMAP_LEAFLET_VERSION, true
        );
        if ( $includeAll ) {
            $func(
                'happyformsmap-map',
                HAPPYFORMSMAP_URL . '/assets/js/map.js',
                ['leaflet'], HAPPYFORMSMAP_VERSION, true
            );
            $func(
                'leaflet-control-fullscreen',
                HAPPYFORMSMAP_URL . '/assets/lib/Control.FullScreen/Control.FullScreen.js',
                ['leaflet'], '3.0.0', true
            );
        }
    }

endif;

if ( ! function_exists( 'happyformsmap_register_map_styles' ) ):

    function happyformsmap_register_map_styles( $enqueue = false, $includeAll = true ) {
        $func = $enqueue ? 'wp_enqueue_style' : 'wp_register_style';
        $func(
            'leaflet',
            'https://unpkg.com/leaflet@' . HAPPYFORMSMAP_LEAFLET_VERSION . '/dist/leaflet.css',
            [], HAPPYFORMSMAP_LEAFLET_VERSION
        );
        $func(
            'happyformsmap-map',
            HAPPYFORMSMAP_URL . '/assets/css/map.css',
            [ 'leaflet' ], HAPPYFORMSMAP_VERSION
        );
        if ($includeAll) {
            $func(
                'leaflet-control-fullscreen',
                HAPPYFORMSMAP_URL . '/assets/lib/Control.FullScreen/Control.FullScreen.css',
                [ 'leaflet' ], '3.0.0'
            );
        }
    }

endif;