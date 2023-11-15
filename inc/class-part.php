<?php

class HappyFormsMap_Part extends HappyForms_Form_Part {

	public $type = HAPPYFORMSMAP_PART_TYPE;

	const LAT_REGEX = '/^-?([1-8]?[1-9]|[1-9]?0)\.{1}\d{4,}/',
        LNG_REGEX = '/^-?((([1]?[0-7][0-9]|[1-9]?[0-9])\.{1}\d{5,}$)|[1]?[1-8][0]\.{1}0{4,}$)/';

	public function __construct() {
		$this->label = __( 'Map', 'happyformsmap' );
		$this->description = __( 'For collecting map locations.', 'happyformsmap' );

		add_filter( 'happyforms_stringify_part_value', [ $this, 'stringify_value' ], 10, 3 );
		add_filter( 'happyforms_message_part_value', [ $this, 'message_part_value' ], 10, 4 );
		add_filter( 'happyforms_frontend_dependencies', [ $this, 'script_dependencies' ], 10, 2 );
		add_filter( 'happyforms_style_dependencies', [ $this, 'style_dependencies' ], 10, 2 );
		// Frontend styles and scripts
		add_action( 'happyforms_print_frontend_styles', [ $this, 'print_frontend_styles' ] );
		add_action( 'happyforms_print_scripts', [ $this, 'print_frontend_scripts' ] );
		// Add icon
		add_filter( 'customize_controls_print_scripts', [ $this, 'customize_controls_print_styles' ] );
	}

	public function get_customize_fields() {
		$fields = [
			'type' => [
				'default' => $this->type,
				'sanitize' => 'sanitize_text_field',
			],
			'label' => [
				'default' => __( '', 'happyformsmap' ),
				'sanitize' => 'sanitize_text_field',
			],
			'label_placement' => [
				'default' => 'show',
				'sanitize' => 'sanitize_text_field'
			],
			'description' => [
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			],
			'description_mode' => [
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			],
			'default_latlng' => [
				'default' => '40.69847,-73.95144',
				'sanitize' => 'sanitize_text_field'
			],
			'default_zoom' => [
				'default' => 13,
				'sanitize' => 'intval'
			],
			'width' => [
				'default' => 'full',
				'sanitize' => 'sanitize_key'
			],
			'css_class' => [
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			],
			'required' => [
				'default' => 1,
				'sanitize' => 'happyforms_sanitize_checkbox',
			],
		];

		return happyforms_get_part_customize_fields( $fields, $this->type );
	}

	public function customize_templates() {
		$template_path = HAPPYFORMSMAP_PATH . '/inc/template-part-customize.php';
		$template_path = happyforms_get_part_customize_template_path( $template_path, $this->type );

		require_once $template_path;
	}

	public function frontend_template( $part_data = [], $form_data = [] ) {
		$part = wp_parse_args( $part_data, $this->get_customize_defaults() );
		$form = $form_data;

		include HAPPYFORMSMAP_PATH . '/inc/template-part-frontend.php';
	}

	public function sanitize_value( $part_data = [], $form_data = [], $request = [] ) {
		$sanitized_value = $this->get_default_value( $part_data );
		$part_name = happyforms_get_part_name( $part_data, $form_data );

		if ( isset( $request[$part_name] ) ) {
			$sanitized_value = wp_parse_args( $request[$part_name], $sanitized_value );
			$sanitized_value = array_map( 'sanitize_text_field', $sanitized_value );
		}

		return $sanitized_value;
	}

	public function validate_value( $value, $part = [], $form = [] ) {
		if ( 1 === $part['required'] && empty( $value['latlng'] ) ) {
			return new WP_Error( 'error', happyforms_get_validation_message( 'field_empty' ) );
		}

		$latlng_string = $value['latlng'];
		if ( ! empty( $latlng_string ) ) {
			if ( ! strpos( $latlng_string, ',' ) 
				|| ( ! $latlng = explode( ',', $latlng_string ))
				|| count( $latlng ) !== 2
				|| ! preg_match( self::LAT_REGEX, $latlng[0] )
				|| ! preg_match( self::LNG_REGEX, $latlng[1] )
			) {
				return new WP_Error( 'error', happyforms_get_validation_message( 'field_invalid' ) );
			}
		}

		$zoom = $value['zoom'];
		if ( $zoom < 0 || $zoom > 18 ) {
			return new WP_Error( 'error', happyforms_get_validation_message( 'field_invalid' ) );
		}

		return $value;
	}

	public function stringify_value( $value, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			$value = $value['latlng'];
		}

		return $value;
	}

	public function message_part_value( $value, $original_value, $part, $destination ) {
		if ( $this->type === $part['type'] ) {
			switch( $destination ) {
				case 'email':
				case 'admin-column':
					if ( strlen( $value ) ) {
						$value = '<a href="https://www.google.com/maps/search/?api=1&query=' . $value . '" title="' . $value . '" target="_blank" rel="noopener noreferrer">'
							. esc_html__( 'View on Google Maps', 'happyformsmap' ) . '</a>';
					}
					break;
				default:
					break;
			}
		}

		return $value;
	}

	public function customize_enqueue_scripts( $deps = [] ) {
		wp_enqueue_script(
			'part-map',
			HAPPYFORMSMAP_URL . '/assets/js/part-customize.js',
			$deps, HAPPYFORMSMAP_VERSION, true
		);
	}

	public function script_dependencies( $deps, $forms ) {
		$contains_map = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_map = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_map ) {
			return $deps;
		}

		$this->register_scripts();

		$deps[] = 'happyformsmap-part';

		return $deps;
	}

	public function style_dependencies( $deps, $forms ) {
		$contains_map = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_map = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_map ) {
			return $deps;
		}

		$this->register_styles();

		$deps[] = 'happyformsmap-part';

		return $deps;
	}

	public function register_styles( $enqueue = false )
	{
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
	}

	public function register_scripts( $enqueue = false )
	{
		$func = $enqueue ? 'wp_enqueue_script' : 'wp_register_script';
		$func(
			'leaflet',
			'https://unpkg.com/leaflet@' . HAPPYFORMSMAP_LEAFLET_VERSION . '/dist/leaflet.js',
			[], HAPPYFORMSMAP_LEAFLET_VERSION, true
		);
		$func(
			'happyformsmap-part',
			HAPPYFORMSMAP_URL . '/assets/js/part-frontend.js',
			[ 'leaflet' ], HAPPYFORMSMAP_VERSION, true
		);
	}

	public function print_frontend_styles() {
		$this->register_styles( true );
	}

	public function print_frontend_scripts() {
		
		$this->register_scripts( true );
	}

	public function customize_controls_print_styles() {
		?>
		<style>
		ul.happyforms-parts-list li[data-part-type="<?php echo $this->type; ?>"] .happyforms-parts-list-item-title:before {
            background-image: url(<?php echo HAPPYFORMSMAP_URL; ?>/assets/svg/icons/map.svg);
        }
		</style>
		<?php
	}

}