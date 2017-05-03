/**
 * The Public script.
 *
 * @link              https://github.com/demispatti/cb-parallax
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/public/js
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

(function ( $ ) {
	"use strict";

	/**
	 * requestAnimationFrame polyfill by Erik Möller. fixes from Paul Irish and Tino Zijdel
	 http://paulirish.com/2011/requestanimationframe-for-smart-animating/
	 http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating
	 MIT license
	 */
	(function () {
		var lastTime = 0;
		var vendors  = ['ms', 'moz', 'webkit', 'o'];
		for ( var x = 0 ; x < vendors.length && ! window.requestAnimationFrame ; ++ x ) {
			window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
			window.cancelAnimationFrame  = window[vendors[x] + 'CancelAnimationFrame']
				|| window[vendors[x] + 'CancelRequestAnimationFrame'];
		}

		if ( ! window.requestAnimationFrame ) {
			window.requestAnimationFrame = function ( callback, element ) {
				var currTime   = new Date().getTime();
				var timeToCall = Math.max( 0, 16 - (currTime - lastTime) );
				var id         = window.setTimeout( function () {
						callback( currTime + timeToCall );
					},
					timeToCall );
				lastTime       = currTime + timeToCall;
				return id;
			};
		}

		if ( ! window.cancelAnimationFrame ) {
			window.cancelAnimationFrame = function ( id ) {
				clearTimeout( id );
			};
		}
	}());

	var requestAnimationFrame = window.mozRequestAnimationFrame || window.msRequestAnimationFrame || window.requestAnimationFrame;
	var isMobile              = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent );

	// fix @todo
	var plug = null;

	function Plugin () {

		this.isParallaxMode   = null;
		this.isGlobal         = cbParallax.global;
		this.window           = $( window );
		this.imageAspectRatio = this.getImageAspectRatio();
		this.canvas           = null;

		this.parallaxfactor = 1.2;
	}

	Plugin.prototype = {

		constructor   : Plugin,
		prepare       : function () {

			if ( '1' == cbParallax.canParallax && '1' == cbParallax.parallaxEnabled ) {

				this.isParallaxMode = true;
				var self            = this;

				// Set the canvas as soon as possible, so we can define this.canvas
				self.setCanvas();

				window.onload = function () {

					var canvasDim = self.getComputedCanvasDimensions();
					var canvas    = document.getElementById( 'cb_parallax_canvas' );
					var context   = canvas.getContext( '2d' );
					var img       = new Image();

					img.onload = function () {

						context.drawImage( this, 0, 0, canvasDim.width, canvasDim.height );
					};

					img.src = cbParallax.backgroundImageUrl;
				};
			}
			else if ( '1' == cbParallax.canParallax && '0' == cbParallax.parallaxEnabled || '0' == cbParallax.canParallax || isMobile && '1' == cbParallax.disableOnMobile ) {

				this.isParallaxMode = false;
			}

		},
		setObjects    : function () {
			this.defaultOptions            = {
				backgroundImageUrl   : '',
				backgroundColor      : '',
				positionX            : 'center',
				positionY            : 'center',
				backgroundAttachment : 'fixed',

				canParallax               : false,
				parallaxEnabled           : false,
				direction                 : 'vertical',
				verticalScrollDirection   : 'top',
				horizontalScrollDirection : 'left',
				horizontalAlignment       : 'center',
				verticalAlignment         : 'center',
				overlayImage              : 'none',
				imageWidth                : $( window ).innerWidth(),
				imageHeight               : $( window ).innerHeight(),
				overlayPath               : '',
				overlayOpacity            : '0.3',
				overlayColor              : ''
			};
			this.direction                 = (cbParallax.direction != 'undefined' ? cbParallax.direction : 'vertical');
			this.verticalScrollDirection   = (cbParallax.verticalScrollDirection != 'undefined' ? cbParallax.verticalScrollDirection : this.defaultOptions.verticalScrollDirection);
			this.horizontalScrollDirection = (cbParallax.horizontalScrollDirection != 'undefined' ? cbParallax.horizontalScrollDirection : this.defaultOptions.horizontalScrollDirection);
			this.horizontalAlignment       = (cbParallax.horizontalAlignment != 'undefined' ? cbParallax.horizontalAlignment : this.defaultOptions.horizontalAlignment);
			this.verticalAlignment         = (cbParallax.verticalAlignment != 'undefined' ? cbParallax.verticalAlignment : this.defaultOptions.verticalAlignment);
			this.disableOnMobile           = cbParallax.disableOnMobile == true;
			this.canParallax               = cbParallax.canParallax == true;
			this.parallaxEnabled           = cbParallax.parallaxEnabled == true;
			this.noneString                = cbParallax.noneString;

			this.scrolling = {
				preserved : cbParallax.preserveScrolling == true
			};
			this.image     = {
				src                  : (cbParallax.backgroundImageUrl != 'undefined' ? cbParallax.backgroundImageUrl : this.defaultOptions.backgroundImageUrl),
				//backgroundColor     : (cbParallax.backgroundColor != 'undefined' ? cbParallax.backgroundColor : ''),
				positionX            : (cbParallax.positionX != 'undefined' ? cbParallax.positionX : this.defaultOptions.positionX),
				positionY            : (cbParallax.positionY != 'undefined' ? cbParallax.positionY : this.defaultOptions.positionX),
				backgroundAttachment : (cbParallax.backgroundAttachment != 'undefined' ? cbParallax.backgroundAttachment : this.defaultOptions.backgroundAttachment),
				backgroundRepeat     : (cbParallax.backgroundRepeat != 'undefined' ? cbParallax.backgroundRepeat : this.defaultOptions.backgroundRepeat),

				width  : (cbParallax.imageWidth != 'undefined' ? cbParallax.imageWidth : this.defaultOptions.imageWidth),
				height : (cbParallax.imageHeight != 'undefined' ? cbParallax.imageHeight : this.defaultOptions.imageHeight)
			};
			this.overlay   = {
				path    : (cbParallax.overlayPath != 'undefined' ? cbParallax.overlayPath : this.defaultOptions.overlayPath),
				image   : (cbParallax.overlayImage != 'undefined' ? cbParallax.overlayImage : this.defaultOptions.overlayImage),
				opacity : (cbParallax.overlayOpacity != 'undefined' ? cbParallax.overlayOpacity : this.defaultOptions.overlayOpacity),
				color   : (cbParallax.overlayColor != 'undefined' ? cbParallax.overlayColor : this.defaultOptions.overlayColor)
			};

			//this.overlayContainer        -> defined in setOverlay()
			this.body                  = $( 'body' );
			this.html                  = $( 'html' );
			this.requestAnimationFrame = window.mozRequestAnimationFrame || window.msRequestAnimationFrame || window.requestAnimationFrame;

			this.niceScrollConfig = {
				zindex          : '-9999',
				scrollspeed     : '60',
				mousescrollstep : '180'/*,
				 preservenativescrolling : false,
				 horizrailenabled        : false,
				 cursordragspeed         : '1.2'*/
			};
		},
		bootstrap     : function () {

			if ( this.isParallaxMode ) {

				this.parallaxBootstrap();
			}
			else {

				this.staticBootstrap();
			}
			plug = this;
		},
		setNicescroll : function () {

			var niceRail = $( '#ascrail2000' );

			if ( this.isParallaxMode && this.isGlobal ) {
				// Load nicescroll anyway since we're parallaxing ( if no instance is running )
				if ( typeof niceRail == 'object' ) {

					this.parallaxPreserveScrolling();
				}
			}
			else if ( this.isParallaxMode ) {

				if ( typeof niceRail == 'object' ) {

					this.parallaxPreserveScrolling();
				}
			}
			else {
				// load if defined
				if ( 1 == cbParallax.preserveScrolling ) {

					if ( typeof niceRail == 'object' ) {

						this.parallaxPreserveScrolling();
					}
				}
			}
		},
		bind          : function () {

			if ( '1' == this.canParallax && '1' == this.parallaxEnabled ) {

				if ( false == isMobile || false == isMobile && '1' != this.disableOnMobile ) {

					$( document ).bind( 'scroll', { context : this }, this.parallaxScrollController );
					$( document ).bind( 'mousewheel', { context : this }, this.parallaxScrollController );
					$( document ).bind( 'DOMMouseScroll', { context : this }, this.parallaxScrollController );
				}
			}

			if ( '1' == this.canParallax && '1' == this.parallaxEnabled ) {

				if ( false == isMobile && '0' == this.disableOnMobile ) {

					$( window ).bind( 'resize', { context : this }, this.parallaxResizeController );
				}
			}
			else if ( '1' == this.canParallax && '0' == this.parallaxEnabled || false == isMobile && '1' == this.disableOnMobile ) {

				$( window ).bind( 'resize', { context : this }, this.staticKeepImageAlignedController );
			}
		},
		init          : function () {
			this.prepare();
			this.setObjects();
			this.bootstrap();
			this.setNicescroll();
			this.bind();
		},

		parallaxBootstrap : function () {

			this.setOverlay();
			this.revertBodyStyling();

			this.isUpdateCanvasAlignment = true;
			this.updateCanvasAlignment();
			this.updateParallaxAxis();
			// We call this one once from here so everything is Radiohead, Everything in its right place.
			this.setParallaxTranslate3DTransform( this.getParallaxTransform() );
		},
		setCanvas         : function () {

			var canvasDim = this.getComputedCanvasDimensions();
			$( 'body' ).prepend( '<canvas id="cb_parallax_canvas" class="custom-background" width="' + canvasDim.width + '" height="' + canvasDim.height + '"></canvas>' );
			this.canvas = $( '#cb_parallax_canvas' );
		},
		setOverlay        : function () {

			if ( this.overlay.image != this.noneString ) {

				this.body.prepend( '<div id="cb_parallax_overlay"></div>' );
				this.overlayContainer = $( '#cb_parallax_overlay' );
				this.overlayContainer.css( {
					'background'       : 'url(' + this.overlay.path + this.overlay.image + ')',
					'background-color' : this.hexToRgbA( this.overlay.color ),
					'opacity'          : this.overlay.opacity
				} );
			}
		},

		parallaxScrollController : function ( event ) {
			event.preventDefault();

			var self = event.data.context;

			self.isScrolling = true;
			requestAnimationFrame( self.parallaxScroll );
		},
		parallaxScroll           : function () {
			var self = null;

			if ( typeof this == 'undefined' ) {
				self = plug;
			}
			else {
				self = this;
			}

			if ( self.isScrolling ) {

				self.isSetParallaxTranslate3DTransform = true;
				self.setParallaxTranslate3DTransform( self.getParallaxTransform() );
			}
			self.isScrolling = false;
		},
		parallaxResizeController : function ( event ) {
			var self = event.data.context;

			self.isResizing = true;
			requestAnimationFrame( self.parallaxResize );
		},
		parallaxResize           : function () {
			var self = null;

			if ( typeof this == 'undefined' ) {
				self = plug;
			}
			else {
				self = this;
			}

			if ( self.isResizing ) {

				self.isRefreshCanvas                   = true;
				self.isUpdateCanvasAlignment           = true;
				self.isSetParallaxTranslate3DTransform = true;

				self.refreshCanvas();
				self.updateCanvasAlignment();
				self.setParallaxTranslate3DTransform( self.getParallaxTransform() );
				self.updateParallaxAxis();
			}

			self.isResizing = false;
		},

		getHorizontalAlignment : function () {

			var posX   = null;
			var canvas = this.canvas;

			var landscape = this.getViewportAspectRatio() >= this.imageAspectRatio;
			var portrait  = this.getViewportAspectRatio() < this.imageAspectRatio;

			if ( this.direction == 'vertical' ) {

				if ( this.verticalScrollDirection == 'to top' ) {

					if ( landscape ) {

						switch ( cbParallax.horizontalAlignment ) {

							case 'left':
								posX = '0';
								break;

							case 'center':
								posX = '0%';
								break;

							case 'right':
								posX = parseInt( $( window ).innerWidth() - canvas.width() ) + 'px';
								break;
						}
						return posX;
					}
					else if ( portrait ) {

						switch ( cbParallax.horizontalAlignment ) {

							case 'left':
								posX = '0';
								break;

							case 'center':
								posX = - parseInt( ( (canvas.width() / 2) - $( window ).innerWidth() / 2) ) + 'px';
								break;

							case 'right':
								posX = - parseInt( - $( window ).innerWidth() + canvas.width() ) + 'px';
								break;
						}
						return posX;
					}
				}
				else if ( this.verticalScrollDirection == 'to bottom' ) {

					if ( landscape ) {

						switch ( cbParallax.horizontalAlignment ) {

							case 'left':
								posX = '0';
								break;

							case 'center':
								posX = parseInt( ($( window ).innerWidth() / 2) - (canvas.width() / 2) ) + 'px';
								break;

							case 'right':
								posX = parseInt( $( window ).innerWidth() - canvas.width() ) + 'px';
								break;
						}
						return posX;
					}
					else if ( portrait ) {

						switch ( cbParallax.horizontalAlignment ) {

							case 'left':
								posX = '0';
								break;

							case 'center':
								posX = parseInt( ( - (canvas.width() / 2) + $( window ).innerWidth() / 2) ) + 'px';
								break;

							case 'right':
								posX = parseInt( $( window ).innerWidth() - canvas.width() ) + 'px';
								break;
						}
						return posX;
					}
				}
			}
		},
		getVerticalAlignment   : function () {

			var posY   = null;
			var canvas = this.canvas;

			var landscape = this.getViewportAspectRatio() >= this.imageAspectRatio;
			var portrait  = this.getViewportAspectRatio() < this.imageAspectRatio;

			if ( this.direction == 'horizontal' ) {

				if ( this.horizontalScrollDirection == 'to the left' ) {

					if ( landscape ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posY = '0';
								break;

							case 'center':
								posY = - parseInt( ( (canvas.height() / 2) - $( window ).innerHeight() / 2) ) + 'px';
								break;

							case 'bottom':
								posY = parseInt( $( window ).innerHeight() - canvas.height() ) + 'px';
								break;
						}
						return posY;
					}
					else if ( portrait ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posY = '0';
								break;

							case 'center':
								posY = - parseInt( ( (canvas.height() / 2) - $( window ).innerHeight() / 2) ) / 2 + 'px';
								break;

							case 'bottom':
								posY = parseInt( - $( window ).innerHeight() + canvas.height() ) + 'px';
								break;
						}
						return posY;
					}
				}
				else if ( this.horizontalScrollDirection == 'to the right' ) {

					if ( landscape ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posY = '0';
								break;

							case 'center':
								posY = parseInt( ($( window ).innerHeight() / 2) - (canvas.height() / 2) ) + 'px';
								break;

							case 'bottom':
								posY = parseInt( $( window ).innerHeight() - canvas.height() ) + 'px';
								break;
						}
						return posY;
					}
					else if ( portrait ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posY = '0';
								break;

							case 'center':
								posY = parseInt( ( - (canvas.height() / 2) + $( window ).innerHeight() / 2) ) + 'px';
								break;

							case 'bottom':
								posY = parseInt( $( window ).innerHeight() - canvas.height() ) + 'px';
								break;
						}
						return posY;
					}
				}
			}
		},
		updateCanvasAlignment  : function () {

			var self = null;

			if ( typeof this == 'undefined' ) {
				self = plug;
			}
			else {
				self = this;
			}

			// Keeps either the x or the y axis aligned depending on the alignment settings and the parallax direction ( vertical or horizontal)
			if ( self.isUpdateCanvasAlignment ) {

				if ( self.direction == 'vertical' && self.horizontalAlignment == 'center' || self.direction == 'vertical' && self.horizontalAlignment == 'right' ) {

					self.canvas.css( { 'left' : self.getHorizontalAlignment() } );
				}
				else if ( self.direction == 'horizontal' && self.verticalAlignment == 'center' || self.direction == 'horizontal' && self.verticalAlignment == 'bottom' ) {

					self.canvas.css( { 'top' : self.getVerticalAlignment() } );
				}
			}

			self.isUpdateCanvasAlignment = false;
		},
		updateParallaxAxis     : function () {

			if ( cbParallax.direction == 'vertical' ) {

				if ( this.verticalScrollDirection == 'to bottom' ) {

					this.canvas.css( {
						'position' : 'fixed',
						'top'      : this.getVerticalPositionInPx()
					} );
				}
			}
			else if ( cbParallax.direction == 'horizontal' ) {

				this.canvas.css( {
					'position' : 'fixed',
					'left'     : this.getHorizontalPositionInPx()
				} );
			}
		},

		getParallaxTransform            : function () {

			var self = null;
			if ( typeof this == 'undefined' ) {
				self = plug;
			}
			else {
				self = this;
			}

			var transform         = {
				x : null,
				y : null
			};
			var ratio             = self.parallaxGetScrollRatio();
			var scrollingPosition = $( window ).scrollTop();
			// Determines the values for the transformation.
			if ( self.direction == 'vertical' ) {

				if ( self.verticalScrollDirection == 'to top' ) {

					transform.x = 0;
					transform.y = - scrollingPosition * ratio;
				}
				else if ( self.verticalScrollDirection == 'to bottom' ) {

					transform.x = 0;
					transform.y = scrollingPosition * ratio;
				}
			}
			else if ( self.direction == 'horizontal' ) {

				if ( self.horizontalScrollDirection == 'to the left' ) {
					transform.x = - scrollingPosition * ratio;
					transform.y = 0;

				}
				else if ( self.horizontalScrollDirection == 'to the right' ) {

					transform.x = scrollingPosition * ratio;
					transform.y = 0;
				}
			}
			return transform;
		},
		parallaxGetScrollRatio          : function () {

			var canvas = $( '#cb_parallax_canvas' );

			var documentOffsetX = null;
			var imageOffsetY    = null;
			var imageOffsetX    = null;
			var ratio           = null;
			if ( this.direction == 'vertical' ) {

				documentOffsetX = $( document ).innerHeight() - $( window ).innerHeight();
				imageOffsetY    = canvas.height() - $( window ).height();

				ratio = (imageOffsetY / documentOffsetX);

			}
			else if ( this.direction == 'horizontal' ) {

				documentOffsetX = $( document ).innerHeight() - $( window ).innerHeight();
				imageOffsetX    = canvas.width() - $( window ).innerWidth();

				ratio = (imageOffsetX / documentOffsetX);
			}
			return ratio;
		},
		setParallaxTranslate3DTransform : function ( transform ) {

			var self = null;

			if ( typeof this == 'undefined' ) {
				self = plug;
			}
			else {
				self = this;
			}

			if ( self.isSetParallaxTranslate3DTransform ) {

				$( '#cb_parallax_canvas' ).css( {
					'-webkit-transform' : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
					'-moz-transform'    : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
					'-ms-transform'     : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
					'-o-transform'      : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
					'transform'         : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)'
				} );
			}

			self.isSetParallaxTranslate3DTransform = false;
		},

		parallaxPreserveScrolling : function () {
			var body = $( 'body' );
			var nice = body.niceScroll( this.niceScrollConfig );
			nice.hide();
			body.css( {
				'-ms-overflow-style' : 'scrollbar',
				'overflow-y'         : 'scroll'
			} );
			// Hide the ascrail
			$( '#ascrail2000' ).remove();
		},
		getViewportSize           : function () {

			return {
				height : $( window ).innerHeight(),
				width  : $( window ).innerWidth()
			};
		},

		isLandscapeRatio : function () {

			return this.getViewportAspectRatio() >= this.imageAspectRatio; // @todo
		},
		getComputedCanvasDimensions : function () {

			var self = null;

			if ( typeof this == 'undefined' ) {
				self = plug;
			}
			else {
				self = this;
			}

			var viewportSize = self.getViewportSize();
			var canvasDim    = {};

			if ( cbParallax.direction == 'vertical' ) {

				if ( this.isLandscapeRatio() ) {
					// Landscape
					canvasDim.width  = viewportSize.width;
					canvasDim.height = canvasDim.width / self.imageAspectRatio;
					return canvasDim;
				}
				else {
					// Portrait
					canvasDim.height = viewportSize.height/* * self.parallaxfactor*/; // @todo
					canvasDim.width  = canvasDim.height * self.imageAspectRatio;
					return canvasDim;
				}
			}
			else if ( cbParallax.direction == 'horizontal' ) {


				if ( this.isLandscapeRatio() ) {
					// Landscape
					canvasDim.width  = viewportSize.width * self.parallaxfactor;
					canvasDim.height = canvasDim.width / self.imageAspectRatio;
					return canvasDim;
				}
				else {
					// Portrait
					canvasDim.height = viewportSize.height;
					canvasDim.width  = canvasDim.height * self.imageAspectRatio;
					return canvasDim;
				}
			}
		},
		refreshCanvas               : function () {
			var self = this;

			if ( typeof this == 'undefined' ) {
				self = plug;
			}
			else {
				self = this;
			}

			if ( self.isRefreshCanvas ) {

				var canvasDim = self.getComputedCanvasDimensions();

				self.canvas.width( parseInt(canvasDim.width) );
				self.canvas.height( parseInt(canvasDim.height) );
			}

			self.isRefreshCanvas = false;
		},

		staticBootstrap                            : function () {

			this.setOverlay();
			this.revertBodyStyling();

			this.setupStaticImageContainer();
		},
		setupStaticImageContainer                  : function () {

			var canvasDim = this.staticGetComputedBackgroundImageDimensions();

			this.body.css( {
				'background'            : 'url(' + this.image.src + ')',
				'background-size'       : canvasDim.width + 'px' + ' ' + canvasDim.height + 'px',
				'background-position'   : this.image.positionX + ' ' + this.image.positionY,
				'background-attachment' : this.image.backgroundAttachment,
				'background-repeat'     : this.image.backgroundRepeat
			} );

		},
		staticKeepImageAlignedController           : function ( event ) {
			event.preventDefault();

			var self = event.data.context;

			self.isResizing = true;
			requestAnimationFrame( self.staticKeepImageAligned );

		},
		staticKeepImageAligned                     : function () {

			var self = null;
			if ( typeof this == 'undefined' ) {
				self = plug;
			}
			else {
				self = this;
			}

			if ( self.isResizing ) {
				self.staticUpdateBackgroundImage();
			}
			self.isResizing = false;

		},
		staticUpdateBackgroundImage                : function () {

			var canvasDim = this.staticGetComputedBackgroundImageDimensions();

			this.body.css( {
				'background-size' : canvasDim.width + 'px' + ' ' + canvasDim.height + 'px',
			} );

		},
		staticGetComputedBackgroundImageDimensions : function () {

			var self = null;

			if ( typeof this == 'undefined' ) {
				self = plug;
			}
			else {
				self = this;
			}

			var viewportSize = self.getViewportSize();
			var canvasDim    = {};

			if ( this.getViewportAspectRatio() >= this.imageAspectRatio ) {
				// Landscape
				canvasDim.width  = viewportSize.width;
				canvasDim.height = canvasDim.width / this.imageAspectRatio;
				return (canvasDim);
			}
			else {
				// Portrait
				canvasDim.height = viewportSize.height;
				canvasDim.width  = canvasDim.height * this.imageAspectRatio;
				return canvasDim;
			}

		},

		revertBodyStyling : function () {

			this.body.removeClass( 'custom-background' );
			this.body.removeProp( 'background-image' );
		},
		hexToRgbA         : function ( hex ) {
			var c;
			if ( /^#([A-Fa-f0-9]{3}){1,2}$/.test( hex ) ) {
				c = hex.substring( 1 ).split( '' );
				if ( c.length == 3 ) {
					c = [c[0], c[0], c[1], c[1], c[2], c[2]];
				}
				c = '0x' + c.join( '' );
				return 'rgba(' + [(c >> 16) & 255, (c >> 8) & 255, c & 255].join( ',' ) + ', ' + '0.5'/*cbParallax.overlayOpacity*/ + ')';
			}
			//throw new Error('Bad Hex');
		},

		getHorizontalPositionInPx : function () {

			var posX   = null;
			var canvas = $( '#cb_parallax_canvas' );

			var landscape = this.getViewportAspectRatio() >= this.imageAspectRatio;
			var portrait  = this.getViewportAspectRatio() < this.imageAspectRatio;

			if ( this.direction == 'vertical' ) {

				if ( this.verticalScrollDirection == 'to top' ) {

					if ( landscape ) {

						switch ( cbParallax.horizontalAlignment ) {

							case 'left':
								posX = '0';
								break;

							case 'center':
								posX = parseInt( ($( window ).innerWidth() / 2) - (canvas.width() / 2) ) + 'px';
								break;

							case 'right':
								posX = parseInt( $( window ).innerWidth() - canvas.width() ) + 'px';
								break;
						}
						return posX;
					}
					else if ( portrait ) {

						switch ( cbParallax.horizontalAlignment ) {

							case 'left':
								posX = '0';
								break;

							case 'center':
								posX = parseInt( ( - (canvas.width() / 2) + $( window ).innerWidth() / 2) ) + 'px';
								break;

							case 'right':
								posX = parseInt( $( window ).innerWidth() - canvas.width() ) + 'px';
								break;
						}
						return posX;
					}
				}
				else if ( this.verticalScrollDirection == 'to bottom' ) {

					if ( landscape ) {

						switch ( cbParallax.horizontalAlignment ) {

							case 'left':
								posX = '0';
								break;

							case 'center':
								posX = parseInt( ($( window ).innerWidth() / 2) - (canvas.width() / 2) ) + 'px';
								break;

							case 'right':
								posX = parseInt( $( window ).innerWidth() - canvas.width() ) + 'px';
								break;
						}
						return posX;
					}
					else if ( portrait ) {

						switch ( cbParallax.horizontalAlignment ) {

							case 'left':
								posX = '0';
								break;

							case 'center':
								posX = parseInt( ( - (canvas.width() / 2) + $( window ).innerWidth() / 2) ) + 'px';
								break;

							case 'right':
								posX = parseInt( $( window ).innerWidth() - canvas.width() ) + 'px';
								break;
						}
						return posX;
					}
				}
			}
			else if ( this.direction == 'horizontal' ) {

				if ( this.horizontalScrollDirection == 'to the left' ) {

					if ( landscape ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posX = '0';
								break;

							case 'center':
								posX = 0;
								break;

							case 'bottom':
								posX = 0;
								break;
						}
						return posX;
					}
					else if ( portrait ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posX = '0';
								break;

							case 'center':
								posX = 0;
								break;

							case 'bottom':
								posX = 0;
								break;
						}
						return posX;
					}
				}
				else if ( this.horizontalScrollDirection == 'to the right' ) {

					if ( landscape ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posX = parseInt( ($( window ).innerWidth()) - (canvas.width()) ) + 'px';
								break;

							case 'center':
								posX = parseInt( ($( window ).innerWidth()) - (canvas.width()) ) + 'px';
								break;

							case 'bottom':
								posX = parseInt( ($( window ).innerWidth()) - (canvas.width()) ) + 'px';
								break;
						}
						return posX;
					}
					else if ( portrait ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posX = parseInt( ($( window ).innerWidth()) - (canvas.width()) ) + 'px';
								break;

							case 'center':
								posX = parseInt( ($( window ).innerWidth()) - (canvas.width()) ) + 'px';
								break;

							case 'bottom':
								posX = parseInt( ($( window ).innerWidth()) - (canvas.width()) ) + 'px';
								break;
						}
						return posX;
					}
				}
			}

		},
		getVerticalPositionInPx   : function () {

			var posY   = null;
			var canvas = $( '#cb_parallax_canvas' );

			var landscape = this.getViewportAspectRatio() >= this.imageAspectRatio;
			var portrait  = this.getViewportAspectRatio() < this.imageAspectRatio;

			if ( this.direction == 'vertical' ) {

				if ( this.verticalScrollDirection == 'to top' ) {

					if ( landscape ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posY = '0';
								break;

							case 'center':
								posY = parseInt( ($( window ).innerHeight() / 2) - (canvas.height() / 2) ) + 'px';
								break;

							case 'bottom':
								posY = parseInt( $( window ).innerHeight() - canvas.height() ) + 'px';
								break;
						}
						return posY;

					}
					else if ( portrait ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posY = '0';
								break;

							case 'center':
								posY = - parseInt( ($( window ).innerHeight() / 2) - (canvas.height() / 2) / 2 ) + 'px';
								break;

							case 'bottom':
								posY = - parseInt( $( window ).innerHeight() - canvas.height() ) + 'px';
								break;
						}
						return posY;
					}

				}
				else if ( this.verticalScrollDirection == 'to bottom' ) {

					if ( landscape ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posY = '0';
								break;

							case 'center':
								posY = parseInt( ($( window ).innerHeight() / 2) - (canvas.height() / 2) ) * 2 + 'px';
								break;

							case 'bottom':
								posY = parseInt( $( window ).innerHeight() - canvas.height() ) + 'px';
								break;
						}
						return posY;

					}
					else if ( portrait ) {

						switch ( cbParallax.verticalAlignment ) {

							case 'top':
								posY = '0';
								break;

							case 'center':
								posY = - parseInt( ($( window ).innerHeight() / 2) - (canvas.height() / 2) / 2 ) + 'px';
								break;

							case 'bottom':
								posY = parseInt( $( window ).innerHeight() - canvas.height() ) + 'px';
								break;
						}
						return posY;
					}
				}
			}
		},

		getImageAspectRatio    : function () {
			return cbParallax.imageWidth / cbParallax.imageHeight;
		},
		getViewportAspectRatio : function () {
			return $( window ).innerWidth() / $( window ).innerHeight();
		}

	};

	$( document ).one( 'ready', function () {

		var plugin = new Plugin();
		plugin.init();
	} );

})( jQuery );
