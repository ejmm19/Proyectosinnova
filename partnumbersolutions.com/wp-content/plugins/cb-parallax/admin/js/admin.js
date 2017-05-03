/**
 * The script for the metabox.

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

		init               : function () {

			this.localizeMetabox();
			this.setObjects();
			this.setImageUrl();
			this.assembleContainers();
			this.initColorpicker();
			this.initFancySelect();
			this.setView();
			this.bind();
		},
		localizeMetabox    : function () {
			if ( cbParallax.locale == 'de_DE' ) {
				$( '<style>.cb-parallax-switch-label.cb_parallax_parallax_enabled:before{content:"' + cbParallax.switchesText.Off + '";}</style>' ).appendTo( 'head' );
				$( '<style>.cb-parallax-switch-label.cb_parallax_parallax_enabled:after{content:"' + cbParallax.switchesText.On + '";}</style>' ).appendTo( 'head' );

				// Localizes the text on the color picker.
				$( '#cb-parallax-meta-box > div:nth-child(3) > p:nth-child(5) > div:nth-child(2) > a:nth-child(1)' ).prop( 'title', cbParallax.backgroundColorText );
				$( '.cb-parallax-parallax-options-container > p:nth-child(8) > div:nth-child(2) > a:nth-child(1)' ).prop( 'title', cbParallax.overlayColorText );
			}
		},
		setObjects         : function () {
			// Background image
			this.backgroundImage            = $( '#cb_parallax_background_image_url' );
			// Attachment id
			this.attachmentId               = $( "#cb_parallax_attachment_id" );
			// On/Off switch
			this.parallaxOnOffCheckBox      = $( '#cb_parallax_parallax_enabled' );
			// Direction switch
			this.parallaxDirectionSelectBox = $( '#cb_parallax_direction' );
			// Overlay options select
			this.overlayImageSelectBox      = $( '#cb_parallax_overlay_image' );
			// Add media button
			this.addMediaButton             = $( '#cb-parallax-meta-box .cb-parallax-media-url' );
			// Remove media button
			this.removeMediaButton          = $( ".cb-parallax-remove-media" );

		},
		setImageUrl        : function () {
			this.backgroundImage.attr( "src", (cbParallax.backgroundImageUrl) != 'undefined' ? cbParallax.backgroundImageUrl : '' );
		},
		assembleContainers : function () {
			// Background image container
			this.backgroundImageContainer      = $( '.cb-parallax-image-container' );
			//
			this.removeMediaButtonContainer    = $( '.cb-parallax-remove-media-button-container' );

			this.backgroundAttachmentContainer = $( '#cb_parallax_background_attachment_container' );
			//this.backgroundColorContainer = $( '#cb_parallax_background_color_container' );

			this.parallaxEnabledContainer = $( '#cb_parallax_parallax_enabled_container' );
			this.directionContainer       = $( '#cb_parallax_direction_container' );

			this.verticalScrollDirectionContainer   = $( '#cb_parallax_vertical_scroll_direction_container' );
			this.horizontalScrollDirectionContainer = $( '#cb_parallax_horizontal_scroll_direction_container' );
			this.verticalAlignmentContainer         = $( '#cb_parallax_vertical_alignment_container' );
			this.horizontalAlignmentContainer       = $( '#cb_parallax_horizontal_alignment_container' );

			//this.overlayImageContainer   = $( '#cb_parallax_overlay_image_container' );
			this.overlayOpacityContainer = $( '#cb_parallax_overlay_opacity_container' );
			this.overlayColorContainer   = $( '#cb_parallax_overlay_color_container' );


			this.imageOptionsContainer = $( '.cb-parallax-image-options-container' );
			this.imageOptionsContainer = $( '#cb_parallax_background_repeat_container, #cb_parallax_background_attachment_container' );

			this.parallaxOptionsContainer = $( '.cb-parallax-parallax-options-container' );
			this.overlayOptionsContainer  = $( '.cb-parallax-overlay-options-container' );

		},
		initColorpicker    : function () {
			$( '#cb_parallax_background_color, #cb_parallax_overlay_color, #background_color, #overlay_color' ).wpColorPicker();
		},
		initFancySelect    : function () {
			$( '.cb-parallax-fancy-select' ).fancySelect();
		},
		setView            : function () {
			// If there is an attachment...
			if ( this.backgroundImage.attr( 'src' ) !== '' ) {

				// If parallax is not possible with this attachment...
				if ( this.attachmentWidth < 1920 || this.attachmentHeight < 1200 ) {
					this.directionContainer.hide();
					this.parallaxEnabledContainer.hide();
					this.parallaxOptionsContainer.hide();
					this.imageOptionsContainer.show();

					this.backgroundImage.show();
					this.removeMediaButton.show();
				}
				// else if parallax is possible AND "off"
				else if ( ( this.attachmentWidth >= 1920 && this.attachmentHeight >= 1200 ) && false == this.parallaxOnOffCheckBox.prop( 'checked' ) ) {
					this.directionContainer.hide();
					this.parallaxEnabledContainer.show();
					this.parallaxOptionsContainer.hide();
					this.imageOptionsContainer.show();

					this.backgroundImage.show();
					this.removeMediaButton.show();
				}
				// else if parallax is possible AND "on"
				else if ( true == this.attachmentWidth >= 1920 && this.attachmentHeight >= 1200 && this.parallaxOnOffCheckBox.prop( 'checked' ) ) {
					this.directionContainer.show();
					this.parallaxEnabledContainer.show();
					this.parallaxOptionsContainer.show();
					this.imageOptionsContainer.hide();

					this.backgroundImage.show();
					this.removeMediaButton.show();
				}
				// Else parallax is possible AND "on"
				else {
					this.directionContainer.hide();
					this.parallaxEnabledContainer.show();
					this.parallaxOptionsContainer.hide();
					this.imageOptionsContainer.show();

					this.backgroundImage.show();
					this.removeMediaButton.show();
				}
			}
			// ...else there is no attachment...
			else {

				this.parallaxEnabledContainer.hide();
				this.imageOptionsContainer.hide();
				this.parallaxOptionsContainer.hide();
				this.directionContainer.hide();

				this.removeMediaButton.hide();
				this.backgroundImage.hide();
			}

			this.toggleOverlayOpacityAndColorOptions();
			this.toggleParallaxDirection();
			this.toggleRemoveMediaButton();
			this.fixView();
			this.toggleCursor();
		},
		bind               : function () {

			this.parallaxOnOffCheckBox.bind( 'click', { context : this }, this.toggleParallaxOnOffSwitch );

			this.parallaxDirectionSelectBox.bind( 'change.fs', { context : this }, this.toggleParallaxDirectionSelectBox );

			this.overlayOptionsContainer.bind( 'change.fs', { context : this }, this.toggleOverlayOpacityAndColorOptions );

			this.removeMediaButton.bind( 'click', { context : this }, this.removeMedia );

			this.addMediaButton.bind( 'click', { context : this }, this.addMedia );

			this.addMediaButton.bind( 'mouseenter', { context : this }, this.removeMediaButtonOnMouseEnter );
			this.addMediaButton.bind( 'mouseleave', { context : this }, this.removeMediaButtonOnMouseLeave );
		},

		toggleParallaxDirectionSelectBox    : function ( event ) {
			var self = event.data.context;

			self.parallaxDirectionSelectBox.trigger( 'change.$' );
			self.toggleParallaxDirection();
		},
		toggleParallaxOnOffSwitch           : function ( event ) {
			var self = event.data.context;

			self.setView();
		},
		toggleParallaxDirection             : function () {
			// If there is an attachment...
			if ( this.backgroundImage.attr( 'src' ) != '' ) {
				// If parallax is on...
				if ( this.parallaxOnOffCheckBox.prop( 'checked' ) ) {
					// If parallax is vertical...
					if ( this.parallaxDirectionSelectBox.val() === cbParallax.verticalString ) {
						this.horizontalScrollDirectionContainer.hide();
						this.verticalAlignmentContainer.hide();

						this.verticalScrollDirectionContainer.show();
						this.horizontalAlignmentContainer.show();
					}
					// ...else it is horizontal
					else {
						this.verticalScrollDirectionContainer.hide();
						this.horizontalAlignmentContainer.hide();

						this.horizontalScrollDirectionContainer.show();
						this.verticalAlignmentContainer.show();
					}
				}
				// ...else parallax is off.
				else {
					this.verticalScrollDirectionContainer.hide();
					this.horizontalAlignmentContainer.show();

					this.horizontalScrollDirectionContainer.hide();
					this.verticalAlignmentContainer.show();
				}

			}
			// ...else there is no attachment.
			else {
				this.verticalScrollDirectionContainer.hide();
				this.horizontalAlignmentContainer.hide();

				this.horizontalScrollDirectionContainer.hide();
				this.verticalAlignmentContainer.hide();
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

				if ( $( "#cb_parallax_overlay_image" ).val() != cbParallax.noneString ) {

					self.overlayOpacityContainer.show();
					self.overlayColorContainer.show();
				}
				else {

					self.overlayOpacityContainer.hide();
					self.overlayColorContainer.hide();
				}
			}
			else {

				self.overlayOpacityContainer.hide();
				self.overlayColorContainer.hide();
				self.overlayOptionsContainer.hide();
			}
		},
		toggleRemoveMediaButton             : function () {

			if ( this.backgroundImage.attr( 'src' ) == '' ) {

				this.removeMediaButtonContainer.hide();
			}
			else {

				this.removeMediaButtonContainer.show();
			}
		},

		fixView : function () {

			if ( false == this.parallaxOnOffCheckBox.prop( 'checked' ) ) {

				this.verticalAlignmentContainer.css( 'height', 'auto' );
				this.horizontalAlignmentContainer.css( 'height', 'auto' );
			}
			else {

				var someContainer = $( '#cb_parallax_overlay_image_container' );
				this.verticalAlignmentContainer.css( 'height', someContainer.height() * 2 + 12 + 'px' );
				this.horizontalAlignmentContainer.css( 'height', someContainer.height() * 2 + 12 + 'px' );
			}
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

			self.removeMediaButtonContainer.css( 'visibility', 'hidden' );

			self.setView();
			return false;
		},

		toggleCursor : function () {

			if ( this.backgroundImage.attr( 'src' ) != '' ) {

				this.addMediaButton.removeClass( 'cb-parallax-image-container-empty' );
				this.addMediaButton.addClass( 'cb-parallax-image-container-in-use' );
			} else {

				this.addMediaButton.removeClass( 'cb-parallax-image-container-in-use' );
				this.addMediaButton.addClass( 'cb-parallax-image-container-empty' );
			}
		},

		removeMediaButtonOnMouseEnter : function ( event ) {
			var self = event.data.context;

			self.removeMediaButtonContainer.css( 'visibility', 'visible' );
		},
		removeMediaButtonOnMouseLeave : function ( event ) {
			var self = event.data.context;

			self.removeMediaButtonContainer.css( 'visibility', 'hidden' );
		}
	};

	$( document ).one( 'ready', function () {

		var plugin = new Plugin();
		plugin.init();
	} );

})( jQuery );
