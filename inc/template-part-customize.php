<script type="text/template" id="happyformsmap-customize-template">
	<?php include happyforms_get_core_folder() . '/templates/customize-form-part-header.php'; ?>
	<div class="label-field-group">
		<label for="<%= instance.id %>_title"><?php echo esc_html__( 'Label', 'happyformsmap' ); ?></label>
		<div class="label-group">
			<input type="text" id="<%= instance.id %>_title" class="widefat title" value="<%- instance.label %>" data-bind="label" />
			<div class="happyforms-buttongroup">
				<label for="<%= instance.id %>-label_placement-show">
					<input type="radio" id="<%= instance.id %>-label_placement-show" value="show" name="<%= instance.id %>-label_placement" data-bind="label_placement" <%= ( instance.label_placement == 'show' ) ? 'checked' : '' %> />
					<span><?php echo esc_html__( 'Show', 'happyformsmap' ); ?></span>
				</label>
				<label for="<%= instance.id %>-label_placement-hidden">
					<input type="radio" id="<%= instance.id %>-label_placement-hidden" value="hidden" name="<%= instance.id %>-label_placement" data-bind="label_placement" <%= ( instance.label_placement == 'hidden' ) ? 'checked' : '' %> />
					<span><?php echo esc_html__( 'Hide', 'happyformsmap' ); ?></span>
				</label>
 			</div>
		</div>
	</div>
	<p>
		<label for="<%= instance.id %>_description"><?php echo esc_html__( 'Hint', 'happyformsmap' ); ?></label>
		<textarea id="<%= instance.id %>_description" data-bind="description"><%= instance.description %></textarea>
	</p>
	<p>
		<label for="<%= instance.id %>_default_latlng"><?php echo esc_html__( 'Default lat/lng', 'happyformsmap' ); ?></label>
		<input type="text" id="<%= instance.id %>_default_latlng" class="widefat title" value="<%- instance.default_latlng %>" data-bind="default_latlng" />
	</p>
	<p>
		<label for="<%= instance.id %>_default_zoom"><?php echo esc_html__( 'Default zoom level', 'happyformsmap' ); ?></label>
		<input type="number" id="<%= instance.id %>_default_zoom" class="widefat title" value="<%- instance.default_zoom %>" data-bind="default_zoom" min="0" max="18" />
	</p>

	<?php do_action( 'happyformsmap_part_customize_before_options' ); ?>

	<?php do_action( 'happyformsmap_part_customize_after_options' ); ?>

	<?php do_action( 'happyformsmap_part_customize_before_advanced_options' ); ?>

	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php echo esc_html__( 'Require an answer', 'happyformsmap' ); ?>
		</label>
	</p>

	<?php happyforms_customize_part_width_control(); ?>

	<p>
		<label for="<%= instance.id %>_css_class"><?php echo esc_html__( 'Additional CSS class(es)', 'happyformsmap' ); ?></label>
		<input type="text" id="<%= instance.id %>_css_class" class="widefat title" value="<%- instance.css_class %>" data-bind="css_class" />
	</p>

	<?php do_action( 'happyformsmap_part_customize_after_advanced_options' ); ?>

	<div class="happyforms-part-logic-wrap">
		<div class="happyforms-logic-view">
			<?php happyforms_customize_part_logic(); ?>
		</div>
	</div>

	<?php happyforms_customize_part_footer(); ?>
</script>
