/**
 * The script for the admin menu.

 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb-parallax
 * @subpackage        cb-parallax/admin/menu/js
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
(function ( $ ) {
	"use strict";

	function Plugin () {

		this.attachmentWidth  = cbParallax.attachmentWidth != 'undefined' ? cbParallax.attachmentWidth : 0;
		this.attachmentHeight = cbParallax.attachmentHeight != 'undefined' ? cbParallax.attachmentHeight : 0;
	}

	Plugin.prototype = {

		constructor : Plugin,

		init : function () {

			this.localizeScript();
			this.wrapOptionFields();
			this.setObjects();
			this.setImageUrl();
			this.assembleContainers();
			this.insertImageSectionHeading();
			this.wrapImageOptionsHeading();
			this.insertPluginSectionHeading();
			this.initColorpicker();
			this.initFancySelect();
			this.setView();
			this.bind();
		},

		setImageUrl             : function () {

			this.backgroundImage.attr( "src", (cbParallax.backgroundImageUrl) != 'undefined' ? cbParallax.backgroundImageUrl : '' );
		},
		wrapOptionFields        : function () {
			var ids = cbParallax.defaults;

			$( ids ).each( function ( i ) {
				var tr = $( '.form-table tr' ).eq( i );
				var id = $( this ).selector;

				tr.wrap( '<div class="cb-parallax-table-row" id="' + id + '_container"></div>' );
			} );

			$( '.wp-picker-container:nth-of-type(1)' ).wrap( '<div class="cb-parallax-table-row" id="cb_parallax_background_color_container"></div>' );
			$( '.wp-picker-container:nth-of-type(2)' ).wrap( '<div class="cb-parallax-table-row" id="cb_parallax_overlay_color_container"></div>' );

		},
		wrapImageOptionsHeading : function () {

			$( 'h2:nth-of-type(3)' ).wrap( '<a class="cb-parallax-image-options-heading-container"></a>' );
		},

		setObjects         : function () {

			this.backgroundImage            = $( '#cb_parallax_background_image_url' );
			this.attachmentId               = $( "#cb_parallax_attachment_id" );
			this.parallaxOnOffCheckBox      = $( '#cb_parallax_parallax_enabled' );
			this.parallaxDirectionSelectBox = $( '#cb_parallax_direction' );
			this.overlayImageSelectBox      = $( '#cb_parallax_overlay_image' );
			this.addMediaButton             = $( '.cb-parallax-media-url' );
			this.removeMediaButton          = $( ".cb-parallax-remove-media" );
		},
		assembleContainers : function () {
			//
			this.removeMediaButtonContainer    = $( '.cb-parallax-remove-media-button-container' );
			//
			this.backgroundAttachmentContainer = $( '#cb_parallax_background_attachment_container' );
			//
			//backgroundColorContainer = $( '#cb_parallax_background_color_container' );
			//
			//this.backgroundImageContainer = $( '.cb-parallax-image-container' );

			this.parallaxEnabledContainer = $( '#cb_parallax_parallax_enabled_container' );
			this.directionContainer       = $( '#cb_parallax_direction_container' );

			this.verticalScrollDirectionContainer   = $( '#cb_parallax_vertical_scroll_direction_container' );
			this.horizontalScrollDirectionContainer = $( '#cb_parallax_horizontal_scroll_direction_container' );
			this.verticalAlignmentContainer         = $( '#cb_parallax_vertical_alignment_container' );
			this.horizontalAlignmentContainer       = $( '#cb_parallax_horizontal_alignment_container' );

			//this.overlayImageContainer   = $( '#cb_parallax_overlay_image_container' );
			this.overlayOpacityContainer = $( '#cb_parallax_overlay_opacity_container' );
			this.overlayColorContainer   = $( '#cb_parallax_overlay_color_container' );

			this.parallaxGlobalContainer = $( '#cb_parallax_global_container' );

			this.imageOptionsContainer    = $( '#cb_parallax_background_repeat_container, #cb_parallax_position_x_container, #cb_parallax_position_y_container, #cb_parallax_background_attachment_container' ).wrapAll( '<div class="cb-parallax-image-options-container"></div>' );
			this.parallaxOptionsContainer = $( '#cb_parallax_vertical_scroll_direction_container, #cb_parallax_horizontal_scroll_direction_container, #cb_parallax_horizontal_alignment_container, #cb_parallax_vertical_alignment_container, #cb_parallax_overlay_image_container, #cb_parallax_overlay_opacity_container, #cb_parallax_overlay_color_container' ).wrapAll( '<div class="cb-parallax-parallax-options-container"></div>' );
			this.overlayOptionsContainer  = $( '#cb_parallax_overlay_image_container, #cb_parallax_overlay_opacity_container, #cb_parallax_overlay_color_container' ).wrapAll( '<div class="cb-parallax-overlay-options-container"></div>' );

		},

		insertPluginSectionHeading : function () {

			this.parallaxGlobalContainer.before( '<div id="cb_parallax_plugin_options_heading_container"><h2>' + cbParallax.pluginSectionTitleText + '</h2></div>' );
		},
		insertImageSectionHeading  : function () {

			$( '.cb-parallax-image-options-container' ).before( '<div id="cb_parallax_image_options_heading_container"><h2>' + cbParallax.imageSectionTitleText + '</h2></div>' );
		},

		initColorpicker : function () {

			$( '#cb_parallax_background_color, #cb_parallax_overlay_color' ).wpColorPicker();
		},
		initFancySelect : function () {

			$( '.cb-parallax-fancy-select' ).fancySelect();
		},

		localizeScript : function () {
			if ( cbParallax.locale == 'de_DE' ) {

				$( '<style>.cb-parallax-switch-input ~ .cb-parallax-switch-label:before{content:"' + cbParallax.switchesText.Off + '";}</style>' ).appendTo( 'head' );
				$( '<style>.cb-parallax-switch-input:checked ~ .cb-parallax-switch-label:after{content:"' + cbParallax.switchesText.On + '";}</style>' ).appendTo( 'head' );
			}
		},

		setView : function () {
			// If there is an attachment...
			if ( this.backgroundImage.attr( 'src' ) != '' ) {
				// If parallax is not possible with this attachment...
				if ( this.attachmentWidth < 1920 || this.attachmentHeight < 1200 ) {

					this.parallaxOptionsContainer.hide();
					this.parallaxEnabledContainer.hide();
					this.directionContainer.hide();
					this.imageOptionsContainer.show();
				}
				// else if parallax is possible AND "off"
				else if ( ( this.attachmentWidth >= 1920 && this.attachmentHeight >= 1200 ) && false == this.parallaxOnOffCheckBox.prop( 'checked' ) ) {

					this.directionContainer.hide();
					this.parallaxOptionsContainer.hide();
					this.overlayOptionsContainer.hide();
					this.imageOptionsContainer.show();
					this.parallaxEnabledContainer.show();
				}
				// else if parallax is possible AND "on"
				else if ( true == this.attachmentWidth >= 1920 && this.attachmentHeight >= 1200 && this.parallaxOnOffCheckBox.prop( 'checked' ) ) {

					this.parallaxOptionsContainer.show();
					this.parallaxEnabledContainer.show();
					this.directionContainer.show();
					this.imageOptionsContainer.hide();
				}
				// Else parallax is possible AND "on"
				else {

					this.directionContainer.hide();
					this.parallaxOptionsContainer.hide();
					this.overlayOptionsContainer.hide();
					this.imageOptionsContainer.show();
					this.parallaxEnabledContainer.show();
				}
			}
			// ...else there is no attachment...
			else {

				this.parallaxEnabledContainer.hide();
				this.imageOptionsContainer.hide();
				this.parallaxOptionsContainer.hide();
				this.directionContainer.hide();
				$( '#cb_parallax_image_options_heading_container' ).hide();
			}

			this.toggleOverlayOpacityAndColorOptions();
			this.toggleParallaxDirection();

			this.fixView();
			this.toggleCursor();
			this.toggleRemoveMediaButton();
		},
		fixView : function () {

			if ( this.backgroundAttachmentContainer.css( 'display' ) == 'none' ) {

				this.verticalAlignmentContainer.css( 'height', this.directionContainer.height() * 2 );
				this.horizontalAlignmentContainer.css( 'height', this.directionContainer.height() * 2 );
			}
			else {

				this.verticalAlignmentContainer.css( 'height', 'auto' );
				this.horizontalAlignmentContainer.css( 'height', 'auto' );
			}
		},

		toggleParallaxOnOffSwitch           : function ( event ) {

			var self = event.data.context;

			self.setView();
		},
		toggleParallaxDirection             : function ( event ) {

			if ( event ) {
				var self = event.data.context;
			}
			else {
				self = this;
			}

			self.parallaxDirectionSelectBox.trigger( 'change.$' );

			if ( self.parallaxOnOffCheckBox.prop( 'checked' ) ) {
				if ( self.backgroundImage.attr( 'src' ) != '' ) {
					if ( self.parallaxDirectionSelectBox.val() == cbParallax.verticalString ) {
						self.horizontalScrollDirectionContainer.hide();
						self.verticalAlignmentContainer.hide();

						self.verticalScrollDirectionContainer.show();
						self.horizontalAlignmentContainer.show();
					}
					else {
						self.verticalScrollDirectionContainer.hide();
						self.horizontalAlignmentContainer.hide();

						self.horizontalScrollDirectionContainer.show();
						self.verticalAlignmentContainer.show();
					}
				}
				else {
					self.verticalScrollDirectionContainer.hide();
					self.horizontalAlignmentContainer.hide();

					self.horizontalScrollDirectionContainer.hide();
					self.verticalAlignmentContainer.hide();
				}

			}
			else {
				self.verticalScrollDirectionContainer.hide();
				self.horizontalAlignmentContainer.hide();

				self.horizontalScrollDirectionContainer.hide();
				self.verticalAlignmentContainer.hide();
			}

		},
		toggleOverlayOpacityAndColorOptions : function ( event ) {

			if ( event ) {
				var self = event.data.context;
			}
			else {
				self = this;
			}

			self.overlayOptionsContainer.trigger( 'change.$' );

			if ( self.backgroundImage.attr( 'src' ) != '' ) {
				self.overlayOptionsContainer.show();

				if ( self.overlayImageSelectBox.val() != cbParallax.noneString ) {

					self.overlayOpacityContainer.show();
					self.overlayColorContainer.show();
				}
				else {
					self.overlayOpacityContainer.hide();
					self.overlayColorContainer.hide();
				}
			}
			else {
				self.overlayOptionsContainer.hide();
				self.overlayOpacityContainer.hide();
				self.overlayColorContainer.hide();
			}

		},
		toggleRemoveMediaButton             : function () {

			if ( this.backgroundImage.attr( 'src' ) != '' || this.addMediaButton.hasClass( 'cb-parallax-image-container-empty' ) ) {

				this.removeMediaButtonContainer.css( 'visibility', 'hidden' );
			}
			else {

				this.removeMediaButtonContainer.css( 'visibility', 'visible' );
			}
		},

		bind : function () {

			this.parallaxOnOffCheckBox.bind( 'click', { context : this }, this.toggleParallaxOnOffSwitch );

			this.parallaxDirectionSelectBox.bind( 'change.fs', { context : this }, this.toggleParallaxDirection );

			this.overlayOptionsContainer.bind( 'change.fs', { context : this }, this.toggleOverlayOpacityAndColorOptions );

			this.removeMediaButton.bind( 'click', { context : this }, this.removeMedia );

			this.addMediaButton.bind( 'click', { context : this }, this.addMedia );

			this.addMediaButton.bind( 'mouseenter', { context : this }, this.removeMediaButtonOnMouseEnter );
			this.addMediaButton.bind( 'mouseleave', { context : this }, this.removeMediaButtonOnMouseLeave );
		},

		addMedia    : function ( event ) {
			event.preventDefault();
			var self = event.data.context;

			if ( self.backgroundImage.attr( 'src' ) != '' ) {
				return false;
			}

			if ( cb_parallax_frame ) {
				cb_parallax_frame.open();
				return;
			}
			var cb_parallax_frame = wp.media.frames.cb_parallax_frame = wp.media( {

				className : "media-frame cb-parallax-frame",
				frame     : "select",
				multiple  : false,
				title     : cbParallaxMediaFrame.title,
				library   : { type : "image" },
				button    : { text : cbParallaxMediaFrame.button }
			} );

			cb_parallax_frame.on( "select", function () {
				var media_attachment = cb_parallax_frame.state().get( "selection" ).first().toJSON();

				self.attachmentId.val( media_attachment.id );
				self.backgroundImage.attr( 'src', media_attachment.url );

				self.attachmentHeight = media_attachment.height;
				self.attachmentWidth  = media_attachment.width;

				self.setView();
			} );

			// Opens the media frame.
			cb_parallax_frame.open();
		},
		removeMedia : function ( event ) {
			var self = event.data.context;

			self.backgroundImage.attr( 'src', '' );
			self.attachmentId.val( '' );
			self.attachmentHeight = 0;
			self.attachmentWidth  = 0;

			self.setView();
			return false;
		},

		dismissNotice : function () {
			var notice = $( '.cb-parallax-admin-menu .notice' );

			if ( notice.length ) {
				notice.slideUp( 400 );
			}
		},

		toggleCursor : function () {

			if ( this.backgroundImage.attr( 'src' ) != '' ) {

				this.addMediaButton.removeClass( 'cb-parallax-image-container-empty' );
				this.addMediaButton.addClass( 'cb-parallax-image-container-in-use' );
			}
			else {

				this.addMediaButton.removeClass( 'cb-parallax-image-container-in-use' );
				this.addMediaButton.addClass( 'cb-parallax-image-container-empty' );
			}
		},

		removeMediaButtonOnMouseEnter : function ( event ) {
			var self = event.data.context;

			if( ! self.addMediaButton.hasClass( 'cb-parallax-image-container-empty' ) ) {

				self.removeMediaButtonContainer.css( 'visibility', 'visible' );
			}
		},
		removeMediaButtonOnMouseLeave : function ( event ) {
			var self = event.data.context;

			self.removeMediaButtonContainer.css( 'visibility', 'hidden' );
		}
	};

	$( document ).one( 'ready', function () {

		var plugin = new Plugin();
		plugin.init();
		setTimeout( plugin.dismissNotice, 3600 );
	} );

})( jQuery );
