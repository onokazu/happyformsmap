<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
    <div class="happyforms-part-wrap">
        <?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
            <?php happyforms_the_part_label( $part, $form ); ?>
        <?php endif; ?>

        <?php happyforms_print_part_description( $part ); ?>

        <div class="happyforms-part__el">

            <?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

            <div class="happyformsmap-map" style="height:300px;" data-initial-latlng="<?php echo esc_attr( $part['default_latlng'] ); ?>" data-initial-zoom="<?php echo esc_attr( $part['default_zoom'] ); ?>"></div>
            <input class="happyformsmap-latlng" type="hidden" value="<?php happyforms_the_part_value( $part, $form, 'latlng' ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>[latlng]" <?php happyforms_the_part_attributes( $part, $form ); ?> />
            <input class="happyformsmap-zoom" type="hidden" value="<?php happyforms_the_part_value( $part, $form, 'zoom' ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>[zoom]" <?php happyforms_the_part_attributes( $part, $form ); ?> />

            <?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

            <?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
        </div>
    </div>
</div>
