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
        happyformsmap_register_map_scripts( true );
        happyformsmap_register_map_styles( true );
    }

    public function manage_posts_extra_tablenav( $which ) {
        global $typenow, $wp_list_table;

        if ( $which !== 'top'
            ||  $typenow !== 'happyforms-message'
            || ! $wp_list_table->has_items()
            || empty( $_GET['form_id'] )
            || ( ! $form_id = intval( $_GET['form_id'] ) )
            || ( ! $form = happyforms_get_form_controller()->get($form_id) )
        ) return;

        $map_parts = [];
        foreach ( $form['parts'] as $part ) {
            if ( 'map' === $part['type'] ) {
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

    protected function _getMapButtonHtml( $form_id, $part, $label = null, $close_label = null ) {
        $attributes = [
            'data-container' => '#posts-filter',
            'data-target' => '.wp-list-table.posts',
            'data-item-container' => 'tr',
            'data-item-content' => '.submission-data',
            'data-item-url' => '.row-actions .edit a',
            'data-form-id' => $form_id,
            'data-part' => $part['id'],
            'data-part-label' => esc_attr( $part['label'] ),
            'data-map-class' => 'happyformsmap-admin-map widefat',
            'data-map-height' => 400,
            'data-map-control-position' => 'bottomright',
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
            . esc_html( isset( $label ) ? $label : __( 'Show on Map', 'happyformsmap' ) )
            . '</button>';
    }

    public function manage_posts_custom_column( $column, $id ) {
        if ( $column !== 'submission' ) return;

        $message = happyforms_get_message_controller()->get( $id );
        $form_controller = happyforms_get_form_controller();
        $form = $form_controller->get( $message['form_id'] );

        foreach ( $form['parts'] as $part ) {
            if ( 'map' === $part['type'] ) {
                $value = happyforms_get_message_part_value( $message['parts'][$part['id']], $part );
                echo '<span class="happyformsmap-map-latlng" data-part="' . $part['id'] . '" data-latlng="' . $value . '"></span>';
            }
        }
    }

}