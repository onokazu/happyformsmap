<?php

class HappyFormsMap_Admin {

    private static $instance;

    static public function init() {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self();
            self::$instance->hook();
        }

        return self::$instance;
    }

    public function hook() {
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
        add_action( 'manage_posts_extra_tablenav', [ $this, 'manage_posts_extra_tablenav' ] );
        add_action( 'manage_happyforms-message_posts_custom_column', [ $this, 'manage_posts_custom_column' ], 10, 2 );
    }

    public function admin_enqueue_scripts() {
        wp_enqueue_script(
            'leaflet',
            'https://unpkg.com/leaflet@' . HAPPYFORMSMAP_LEAFLET_VERSION . '/dist/leaflet.js',
            [], HAPPYFORMSMAP_LEAFLET_VERSION, true
        );
        wp_enqueue_script(
            'happyformsmap-map',
            HAPPYFORMSMAP_URL . '/assets/js/map.js',
            [ 'leaflet' ], HAPPYFORMSMAP_VERSION, true
        );

        wp_enqueue_style(
            'leaflet',
            'https://unpkg.com/leaflet@' . HAPPYFORMSMAP_LEAFLET_VERSION . '/dist/leaflet.css',
            [], HAPPYFORMSMAP_LEAFLET_VERSION
        );
        wp_enqueue_style(
            'happyformsmap-map',
            HAPPYFORMSMAP_URL . '/assets/css/map.css',
            [ 'leaflet' ], HAPPYFORMSMAP_VERSION
        );
    }

    public function manage_posts_extra_tablenav( $which ) {
        global $typenow;

        if ( $which !== 'top' ) return;

        if ( $typenow === 'happyforms-message' 
            && isset( $_GET['form_id'] )
            && ( $form_id = intval( $_GET['form_id'] ) )
            && ( $form = happyforms_get_form_controller()->get($form_id) )
        ) {
            $map_parts = [];
            foreach ( $form['parts'] as $part ) {
                if ( HAPPYFORMSMAP_PART_TYPE === $part['type'] ) {
                    $map_parts[] = $part;
                }
            }
            if ( ! empty( $map_parts ) ) {
                echo '<div class="alignleft actions">';
                if ( count( $map_parts ) > 1 ) {
                    foreach ( $map_parts as $part ) {
                        echo $this->_getMapButtonHtml( $form_id, $part, sprintf( __( 'Map - %s', 'happyformsmap' ), $part['label'] ) );
                    }
                } else {
                    echo $this->_getMapButtonHtml( $form_id, $map_parts[0], __( 'Show on Map', 'happyformsmap' ), __( 'Close Map', 'happyformsmap' ) );
                }
                echo '</div>';
            }
        }
    }

    protected function _getMapButtonHtml( $form_id, $part, $label = null, $close_label = null ) {
        if ( ! isset( $label ) ) $label = __( 'Show on Map', 'happyformsmap' );
        $attributes = [
            'data-container' => '#posts-filter',
            'data-target' => '.wp-list-table.posts',
            'data-item-container' => 'tr',
            'data-item-data' => '.submission-data',
            'data-form-id' => $form_id,
            'data-part' => $part['id'],
            'data-part-label' => esc_attr( $part['label'] ),
            'data-map-class' => 'happyformsmap-admin-map',
            'data-map-height' => 400,
            'data-default-latlng' => esc_attr( $part['default_latlng'] ),
            'data-default-zoom' => intval( $part['default_zoom'] ),
        ];
        if ( isset( $close_label ) ) {
            $attributes['data-label-close'] = esc_attr( $close_label );
        }
        $attr_string = '';
        foreach ( $attributes as $attr_key => $attr_value ) {
            $attr_string .= ' ' . $attr_key . '="' . $attr_value . '"';
        }
        return '<button type="button" class="button happyformsmap-map-trigger"' . $attr_string .'>'
            . esc_html( $label )
            . '</button>';
    }

    public function manage_posts_custom_column( $column, $id ) {
        if ( $column !== 'submission' ) return;

        $message = happyforms_get_message_controller()->get( $id );
        $form_controller = happyforms_get_form_controller();
        $form = $form_controller->get( $message['form_id'] );

        foreach ( $form['parts'] as $part ) {
            if ( HAPPYFORMSMAP_PART_TYPE === $part['type'] ) {
                $value = happyforms_get_message_part_value( $message['parts'][$part['id']], $part );
                echo '<span class="happyformsmap-map-latlng" data-part="' . $part['id'] . '" data-latlng="' . $value . '"></span>';
            }
        }
    }

}