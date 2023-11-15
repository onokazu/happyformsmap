( function ( $, _, Backbone, api, settings ) {

    happyForms.classes.models.parts.map = happyForms.classes.models.Part.extend( {
        defaults: function () {
            return _.extend(
                {},
                settings.formParts.map.defaults,
                _.result( happyForms.classes.models.Part.prototype, 'defaults' ),
            );
        },
    } );

    happyForms.classes.views.parts.map = happyForms.classes.views.Part.extend( {
        template: '#happyformsmap-customize-template',

        events: _.extend( {}, happyForms.classes.views.Part.prototype.events, {
            'change [data-bind=default_latlng],[data-bind=default_zoom]': 'onDefaultMapSettingChange',
        } ),

        onDefaultMapSettingChange: function ( e ) {
            var $input = $( e.target ),
                attribute = $input.data( 'bind' ),
                model = this.model;

            this.model.set( attribute, attribute === 'default_zoom' ? parseInt( $input.val() ) : $input.val() );

            this.model.fetchHtml( function ( response ) {
                var data = {
                    id: model.get( 'id' ),
                    html: response,
                };

                happyForms.previewSend( 'happyforms-form-part-refresh', data );
            } );
        },

        onPartWidthChange: function ( model, value, options ) {
            this.model.fetchHtml( function ( response ) {
                var data = {
                    id: model.get( 'id' ),
                    html: response,
                };

                happyForms.previewSend( 'happyforms-form-part-refresh', data );
            } );
        },

    } );

} )( jQuery, _, Backbone, wp.customize, _happyFormsSettings );
