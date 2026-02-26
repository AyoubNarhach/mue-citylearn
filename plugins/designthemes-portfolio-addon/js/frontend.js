(function ($) {

	var scriptLoaded = false;

	var dtPortfolioPostsWidgetHandler = function($scope, $){

		if(scriptLoaded && dtportfoliofrontendobject.elementor_preview_mode != 'true') {
			return;
		}

		scriptLoaded = true;


		var isMobile = (navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i)) || (navigator.userAgent.match(/Android/i)) || (navigator.userAgent.match(/Blackberry/i)) || (navigator.userAgent.match(/Windows Phone/i) || navigator.platform.match(/(Mac|iPhone|iPod|iPad)/i) ) ? true : false;
		
		var currentWidth = window.innerWidth || document.documentElement.clientWidth;


		// On Window Resize
			$(window).bind("resize", function() {

				// Portfolio template fullwidth
				calculatePortfolioTemplateWidth();
				calculatePortfolioMultirowHeight();
				calculatePortfolioSingleItemHeight();


				// Portfolio Isotope
			    jQuery('.dtportfolio-container:not(.swiper-wrapper, .portfolio-container-carousel, .dtportfolio-container-gallery-list)').each(function() {

			    	if(jQuery(this).hasClass('dtportfolio-infinite-portfolio-container')) {
			    		var $postperpage = 2;
			    	} else {
			    		var $postperpage = parseInt(jQuery(this).attr('data-postperpage'), 10);
			    	}
			  		if( jQuery(this).length && $postperpage != 1) {
				        jQuery('.dtportfolio-container:not(.swiper-wrapper, .portfolio-container-carousel, .dtportfolio-container-gallery-list)').isotope({
							itemSelector: '.dtportfolio-item',
							percentPosition: true,
							masonry: {
								columnWidth: '.dtportfolio-grid-sizer'
							}        	
				        });
				    }

			    });

			});

		// On Load Functions

		$(window).on('load', function(){

			// Portfolio Isotope
		    jQuery('.dtportfolio-container:not(.swiper-wrapper, .portfolio-container-carousel, .dtportfolio-container-gallery-list)').each(function() {

		    	if(jQuery(this).hasClass('dtportfolio-infinite-portfolio-container')) {
		    		var $postperpage = 2;
		    	} else {
		    		var $postperpage = parseInt(jQuery(this).attr('data-postperpage'), 10);
		    	}
		  		if( jQuery(this).length && $postperpage != 1) {
			        jQuery('.dtportfolio-container:not(.swiper-wrapper, .portfolio-container-carousel, .dtportfolio-container-gallery-list)').isotope({
						itemSelector: '.dtportfolio-item',
						percentPosition: true,
						masonry: {
							columnWidth: '.dtportfolio-grid-sizer'
						}        	
			        });
			    }

		    });

		    // Portfolio Isotope Sorting
			if($('div.dtportfolio-sorting').length){
				$('div.dtportfolio-sorting a').on('click', function() {

					$('div.dtportfolio-sorting a').removeClass('active-sort');
					var selector = $(this).attr('data-filter');
					$(this).addClass('active-sort');

					$(this).parents('.dtportfolio-container-wrapper').find('.dtportfolio-container:not(.swiper-wrapper, .portfolio-container-carousel, .dtportfolio-container-gallery-list) .dtportfolio-column').removeClass('animate flash shake bounce tada swing wobble pulse flip flipIn flipOutX flipInY flipOutY fadeIn fadeInUp fadeInDown fadeInLeft fadeInRight fadeInUpBig fadeInDownBig fadeInLeftBig fadeInRightBig fadeOut fadeOutUp fadeOutDown fadeOutLeft fadeOutRight fadeOutUpBig fadeOutDownBig fadeOutLeftBig fadeOutRightBig bounceIn bounceInUp bounceInDown bounceInLeft bounceInRight bounceOut bounceOutUp bounceOutDown bounceOutLeft bounceOutRight rotateIn rotateInUpLeft rotateInDownLeft rotateInUpRight rotateInDownRight rotateOut rotateOutUpLeft rotateOutDownLeft rotateOutUpRight rotateOutDownRight hinge rollIn rollOut lightSpeedIn lightSpeedOut slideDown slideUp slideLeft slideRight slideExpandUp expandUp expandOpen bigEntrance hatch floating tossing pullUp pullDown stretchLeft stretchRight zoomIn');
					$(this).parents('.dtportfolio-container-wrapper').find('.dtportfolio-container:not(.swiper-wrapper, .portfolio-container-carousel, .dtportfolio-container-gallery-list)').isotope({ filter: selector, masonry: {  }, animationEngine : 'jquery' });

					return false;

				});
			}

			animatePortfolioItems();

			// Repeat animation on periodic interval
			portfolioRepeatAnimation();
			function portfolioRepeatAnimation() {
				var divs = $('.dtportfolio-container.repeat-animation .dtportfolio-item.animate');
				if(divs.length) {
				    setTimeout(function() {
				    	var index = Math.floor(Math.random() * divs.length);
				    	divs.eq(index).removeClass('animate');
				    	setTimeout(function() {
				        	divs.eq(index).addClass('animate');
				        	portfolioRepeatAnimation();
				         }, 200);
				    }, ~~(Math.random()*(300-60+1)+2000));
			    } 			
			}	
			
			// Portfolio template fullwidth
			calculatePortfolioTemplateWidth();
			calculatePortfolioMultirowHeight();


			// Image Gallery Swiper - Starts
			var swiperGallery = [];
			var swiperGalleryOptions = [];
			var swiperIterator = 1;

			jQuery('.dtportfolio-image-gallery-container, .dtportfolio-swiper-container').each(function() {

				var $swiperItem = jQuery(this);
				var swiperUniqueId = 'swiperuniqueid-'+swiperIterator;

				swiperGalleryOptions[swiperUniqueId] = [];
				$swiperItem.attr('id', swiperUniqueId);

				// Get swiper options
				var effect = $swiperItem.find('.swiper-wrapper').attr('data-carouseleffect');
				var carouselnumberofrows = 1;
				if(effect == 'multirows') {
					var carouselnumberofrows = ($swiperItem.find('.swiper-wrapper').attr('data-carouselnumberofrows') != '') ? $swiperItem.find('.swiper-wrapper').attr('data-carouselnumberofrows') : 1;
				}

				var autoheight = false;

				var playpausebutton = ($swiperItem.find('.swiper-wrapper').attr('data-carouselplaypausebutton') == 'true') ? true : false;

				var autoplay = parseInt($swiperItem.find('.swiper-wrapper').attr('data-carouselautoplay'), 10);
				if(autoplay > 0) {
					swiperGalleryOptions[swiperUniqueId]['autoplay_enable'] = true;
					autoplay_enable = true;
					if(playpausebutton) {
						swiperGalleryOptions[swiperUniqueId]['autoplay_enable'] = true;	
					}				
				} else {
					swiperGalleryOptions[swiperUniqueId]['autoplay_enable'] = false;
					autoplay_enable = false;
					if(playpausebutton) {
						autoplay = 2000;
						swiperGalleryOptions[swiperUniqueId]['autoplay_enable'] = false;	
					}			
				}

				var slidesperview = parseInt($swiperItem.find('.swiper-wrapper').attr('data-carouselslidesperview'), 10);

				var loopmode = ($swiperItem.find('.swiper-wrapper').attr('data-carouselloopmode') == 'true') ? true : false;
				var mousewheelcontrol = ($swiperItem.find('.swiper-wrapper').attr('data-carouselmousewheelcontrol') == 'true') ? true : false;
				var centermode = ($swiperItem.find('.swiper-wrapper').attr('data-carouselcentermode') == 'true') ? true : false;
				var verticaldirection = ($swiperItem.find('.swiper-wrapper').attr('data-carouselverticaldirection') == 'true') ? true : false;
				var direction = 'horizontal';
				if(verticaldirection) {
					direction = 'vertical';
				}

				var pagination_class = '';
				var pagination_type = '';

				var paginationtype = ($swiperItem.find('.swiper-wrapper').attr('data-carouselpaginationtype') != '') ? $swiperItem.find('.swiper-wrapper').attr('data-carouselpaginationtype') : '';

				if(paginationtype == 'bullets') {
					var pagination_class = $swiperItem.find('.dtportfolio-swiper-bullet-pagination');
					var pagination_type = 'bullets';
				}

				if(paginationtype == 'fraction') {
					var pagination_class =  $swiperItem.find('.dtportfolio-swiper-fraction-pagination');
					var pagination_type = 'fraction';
				}

				if(paginationtype == 'progressbar') {
					var pagination_class =  $swiperItem.find('.dtportfolio-swiper-progress-pagination');
					var pagination_type = 'progressbar';
				}


				var thumbnailpagination = ($swiperItem.find('.swiper-wrapper').attr('data-carouselthumbnailpagination') == 'true') ? true : false;
				if(thumbnailpagination) {
					swiperGalleryOptions[swiperUniqueId]['thumbnailpagination'] = true;
					loopmode = false;
				} else {
					swiperGalleryOptions[swiperUniqueId]['thumbnailpagination'] = false;
				}

				var scrollbar_class = '';
				var	scrollbar_hide = true;
				var carouselscrollbar = ($swiperItem.find('.swiper-wrapper').attr('data-carouselscrollbar') == 'true') ? true : false;
				if(carouselscrollbar) {
					scrollbar_class = $swiperItem.find('.dtportfolio-swiper-scrollbar');
					scrollbar_hide = false;
				}

				var carouselarrowformousepointer = ($swiperItem.find('.swiper-wrapper').attr('data-carouselarrowformousepointer') == 'true') ? true : false;

				var spacebetween = parseInt($swiperItem.find('.swiper-wrapper').attr('data-carouselspacebetween'), 10);
				if(spacebetween) {
					spacebetween = spacebetween;
				} else {
					spacebetween = 0;
				}

				if(direction == 'vertical') {

					effect = '';

					if(slidesperview == 1) {
						var breakpoint_slides_1 = breakpoint_slides_2 = breakpoint_slides_3 = breakpoint_slides_4 = 1;
					} else if(slidesperview == 2) {
						var breakpoint_slides_1 = breakpoint_slides_2 = breakpoint_slides_3 = breakpoint_slides_4 = 2;;
					} else if(slidesperview == 3) {
						var breakpoint_slides_1 = breakpoint_slides_2 = breakpoint_slides_3 = breakpoint_slides_4 = 3;
					} else if(slidesperview >= 4) {
						var breakpoint_slides_1 = breakpoint_slides_2 = breakpoint_slides_3 = breakpoint_slides_4 = 4;
					}

				} else {

					if(slidesperview == 1) {
						var breakpoint_slides_1 = breakpoint_slides_2 = breakpoint_slides_3 = breakpoint_slides_4 = 1;
					} else if(slidesperview == 2) {
						var breakpoint_slides_1 = 2; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
					} else if(slidesperview == 3) {
						var breakpoint_slides_1 = 3; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
					} else if(slidesperview >= 4) {
						var breakpoint_slides_1 = 4; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
					}	

					if(effect == 'cube' || effect == 'fade' || effect == 'flip') {
						var breakpoint_slides_1 = breakpoint_slides_2 = breakpoint_slides_3 = breakpoint_slides_4 = 1;
					}

				}		

				// Generate swiper
			    swiperGallery[swiperUniqueId] = new Swiper($swiperItem, {
	     			
	     			initialSlide: 0,
	                simulateTouch: true,
	                roundLengths: true,
	                spaceBetween: spacebetween,
	                keyboardControl: true,
	                paginationClickable: true,
	                autoHeight: autoheight,

	                centeredSlides: centermode,
	                grabCursor: false,
	                autoplay: {
	                			enabled: autoplay_enable,
							    delay: autoplay,
							},
	                slidesPerView: slidesperview,
	                slidesPerColumn: carouselnumberofrows,
	                loop:loopmode,
	                mousewheel: mousewheelcontrol,
	                direction: direction,

					pagination: {
						el: pagination_class,
						type: pagination_type,
						clickable: true,
						renderFraction: function (currentClass, totalClass) {
							return '<span class="' + currentClass + '"></span>' +
									'<span class="dtportfolio-separator"></span>' +
									'<span class="' + totalClass + '"></span>';
						}					
					},

					scrollbar: {
						el: scrollbar_class,
						hide: scrollbar_hide,
						draggable: true,
					},                  

	                effect: effect,
					coverflowEffect: {
						slideShadows: false,
						rotate: 0,
						stretch: 0,
						depth: 200,
						modifier: 1,
					},
			        cubeEffect: {
			        	slideShadows: true,
			            shadow: true,
			            shadowOffset: 20,
			            shadowScale: 0.94
			        },
			      
			        breakpoints: {
			            1199: {
			                slidesPerView: breakpoint_slides_1,
			            },	            
			            992: {
			                slidesPerView: breakpoint_slides_2,
			            },
			            767: {
			                slidesPerView: breakpoint_slides_3,
			            }
			        },  

					on: {
						init: function () {
							if(carouselarrowformousepointer) {
			                    $swiperItem.find('.dtportfolio-swiper-arrow-click').each(function() {
			                        var arrow = jQuery(this);
			                        jQuery(document).on('mousemove', function(event) {
			                            var arrow_parent = arrow.parent(),
			                                parent_offset = arrow_parent.offset(),
			                                pos_left = Math.min(event.pageX - parent_offset.left, arrow_parent.width()),
			                                pos_top = event.pageY - parent_offset.top;
			                            arrow.css({
			                                'left': pos_left,
			                                'top': pos_top
			                            });
			                        });
			                    });
		                    } 

							$swiperItem.find('a[data-gal^="prettyPhoto[gallery]"], a[data-gal^="prettyPhoto[gallery-listing]"]').iLightBox({
								attr: 'href',
								fullViewPort: 'stretch',
							    controls: {
							      mousewheel: true,
							      swipe: true,
							      thumbnail: true
							    },		
								path: 'vertical',
								infinite: true,
					    	});	

		                }
					},

			    });


			    // Arrow pagination
		    	var arrowpagination = ($swiperItem.find('.swiper-wrapper').attr('data-carouselarrowpagination') == 'true') ? true : false;

		    	if(arrowpagination) {

				    $swiperItem.find('.dtportfolio-swiper-arrow-pagination .dtportfolio-swiper-arrow-prev').on('click', function(e) {			    	
				    	var swiperUniqueId = $swiperItem.attr('id');
				        swiperGallery[swiperUniqueId].slidePrev();
				        if(swiperGalleryOptions[swiperUniqueId]['autoplay_enable']) {
				        	swiperGallery[swiperUniqueId].autoplay.start();
				        }
				        e.preventDefault();		
				    });

				    $swiperItem.find('.dtportfolio-swiper-arrow-pagination .dtportfolio-swiper-arrow-next').on('click', function(e) {
				    	var swiperUniqueId = $swiperItem.attr('id');
				        swiperGallery[swiperUniqueId].slideNext();
				        if(swiperGalleryOptions[swiperUniqueId]['autoplay_enable']) {
				        	swiperGallery[swiperUniqueId].autoplay.start();
				        }
				        e.preventDefault();	
				    });	

				}

				// Play pause button
		    	var playpausebutton = ($swiperItem.find('.swiper-wrapper').attr('data-carouselplaypausebutton') == 'true') ? true : false;

		    	if(playpausebutton) {			

				    $swiperItem.find('.dtportfolio-swiper-playpause').on('click', function(e) {
				    	e.preventDefault();
				    	var swiperUniqueId = $swiperItem.attr('id');
				    	if(jQuery(this).hasClass('play')) {
				    		swiperGallery[swiperUniqueId].autoplay.start();
				    	} else {
				    		swiperGallery[swiperUniqueId].autoplay.stop();
				    	}
				    	jQuery(this).toggleClass('pause play');
				    	jQuery(this).find('span').toggleClass('fa-pause fa-play');  	
				    });			

				}

				if(carouselarrowformousepointer) {

				    $swiperItem.find('.dtportfolio-swiper-arrow-click.left').on('click', function(e) {
				        var swiperUniqueId = $swiperItem.attr('id');
				        swiperGallery[swiperUniqueId].slidePrev();
				        if(swiperGalleryOptions[swiperUniqueId]['autoplay_enable'] > 0 || $swiperItem.find('.dtportfolio-swiper-pagination-holder').find('.dtportfolio-swiper-playpause').hasClass('pause')) {
				        	swiperGallery[swiperUniqueId].startAutoplay();
				        }
				    	e.preventDefault();			        
				    });

				    $swiperItem.find('.dtportfolio-swiper-arrow-click.right').on('click', function(e) {
				    	console.log('asdsad');
				    	var swiperUniqueId = $swiperItem.attr('id');
				        swiperGallery[swiperUniqueId].slideNext();
				        if(swiperGalleryOptions[swiperUniqueId]['autoplay_enable'] > 0 || $swiperItem.find('.dtportfolio-swiper-pagination-holder').find('.dtportfolio-swiper-playpause').hasClass('pause')) {
				        	 swiperGallery[swiperUniqueId].startAutoplay();
				        } 
				    	e.preventDefault();			        
				    });	

				}

			    swiperIterator++;

			});	

			// Generate gallery thumb pagination
			for(i = 1; i < swiperIterator; i++) {
				if(swiperGalleryOptions['swiperuniqueid-'+i]['thumbnailpagination']) {

					var swiperUniqueId = 'swiperuniqueid-'+i;

					var $swiper_gallerythumb_item = jQuery('#'+swiperUniqueId).parents('.dtportfolio-image-gallery-holder').find('.dtportfolio-image-gallery-thumb-container');

					if($swiper_gallerythumb_item.length == 0) {
						var $swiper_gallerythumb_item = jQuery('#'+swiperUniqueId).parents('.dtportfolio-container-wrapper').find('.dtportfolio-swiper-thumbnail-container');
					} 

					if($swiper_gallerythumb_item.length > 0) {

					    var swiperGalleryThumbs = new Swiper($swiper_gallerythumb_item, {
					    	initialSlide: 0,
					        spaceBetween: 10,
					        centeredSlides: true,
					        slidesPerView: 'auto',
					        touchRatio: 0.2,
					        slideToClickedSlide: true
					    });
						
					    swiperGallery[swiperUniqueId].controller.control = swiperGalleryThumbs;
					    swiperGalleryThumbs.controller.control = swiperGallery[swiperUniqueId];		

				    }		    

				}
			}


			// One page navigation scripts
			if($('.dtportfolio-onepage-navigation-title-holder').length) {

				$('.dtportfolio-onepage-navigation-title-holder li a').click(function(event) {
					if($(this).parents('.dtportfolio-onepage-navigation-title-holder').hasClass('rounded')) {

						$('.dtportfolio-onepage-navigation-title-holder li').removeClass('active');
						$(this).parent('li').addClass('active');

						var target = $(this).attr('href'); 
						if( target.length ) { 
							event.preventDefault(); 
							$('html, body').stop().animate({ scrollTop: $(target).offset().top }, 1000); 
						} 

					} else {

						$('.dtportfolio-onepage-navigation-title-holder li a').removeClass('active');
						$(this).addClass('active');

						var target = $(this).attr('href'); 
						if( target.length ) { 
							event.preventDefault(); 
							$('html, body').stop().animate({ scrollTop: $(target).offset().top }, 1000); 
						} 

					}
				});

				$(window).scroll(function() {
					$('.dtportfolio-onepage-navigation-title-holder li a').each(function(){
						var section_id = $(this).attr('href');

						if($(window).scrollTop() == 0) {

							if($(this).parents('.dtportfolio-onepage-navigation-title-holder').hasClass('rounded')) {
								$('.dtportfolio-onepage-navigation-title-holder li').removeClass('active');
								$('.dtportfolio-onepage-navigation-title-holder li:first').addClass('active');
							} else {
								$('.dtportfolio-onepage-navigation-title-holder li a').removeClass('active');
								$('.dtportfolio-onepage-navigation-title-holder li a:first').addClass('active');
							}

						} else {

						    var top_of_element = $(section_id).offset().top+200;
						    var bottom_of_element = $(section_id).offset().top + $(section_id).outerHeight();
						    var bottom_of_screen = $(window).scrollTop() + $(window).height();

						    if((bottom_of_screen > top_of_element) && (bottom_of_screen < bottom_of_element)){
								if($(this).parents('.dtportfolio-onepage-navigation-title-holder').hasClass('rounded')) {
									$('.dtportfolio-onepage-navigation-title-holder li').removeClass('active');
									$('.dtportfolio-onepage-navigation-title-holder li a[href="'+section_id+'"]').parent('li').addClass('active');
								} else {				    	
									$('.dtportfolio-onepage-navigation-title-holder li a').removeClass('active');
									$('.dtportfolio-onepage-navigation-title-holder li a[href="'+section_id+'"]').addClass('active');
								}
						    }

						}
				    });
				});
				
			}	

		});


	    // Calculate portfolio template width
		function calculatePortfolioTemplateWidth() {

			$('.dtportfolio-fullwidth-wrapper').each(function () {

				var windowWidth = $(window).width();
				$fullwidth_next = $(this).next(".dtportfolio-fullwidth-wrapper-fix");
				var offset = 0 - $fullwidth_next.offset().left;

				$(this).css('width', windowWidth);
				$(this).css('left', offset);	

		        $(this).find('.dtportfolio-container:not(.swiper-wrapper, .portfolio-container-carousel, .dtportfolio-container-gallery-list)').isotope({
					itemSelector: '.dtportfolio-item',
					percentPosition: true,
					masonry: {
						columnWidth: '.dtportfolio-grid-sizer'
					}        	
		        });

			});

		}

		// Calculate portfolio multirow height
		function calculatePortfolioMultirowHeight() {

			jQuery('.dtportfolio-image-gallery-container, .dtportfolio-swiper-container').each(function() {

				var swiperItem = jQuery(this);

				var effect = swiperItem.find('.swiper-wrapper').attr('data-carouseleffect');
				var verticaldirection = swiperItem.find('.swiper-wrapper').attr('data-carouselverticaldirection');

				if(effect == 'multirows') {

					var numberofrows = parseInt(swiperItem.find('.swiper-wrapper').attr('data-carouselnumberofrows'), 10);
					var slidespercolumn = 1;
					if(effect == 'multirows') {
						var slidespercolumn = numberofrows;
					}

					var carousel_height = 650;
					var carousel_height_wrapper = 650;

					if(swiperItem.hasClass('details-below-image')) {
						var carousel_height_wrapper = parseInt(carousel_height)+(detailsSectionHeight*slidespercolumn);
					}

					if(effect == 'multirows') {
						var carousel_height = (carousel_height/slidespercolumn);
					}	

					swiperItem.parents('.dtportfolio-container-wrapper').height(carousel_height_wrapper);
					swiperItem.height(carousel_height_wrapper);
					swiperItem.find('.swiper-slide').height(carousel_height);

				} else if(verticaldirection == 'true') {

					var carousel_height = 650;
					var carousel_height_wrapper = 650;

					swiperItem.parents('.dtportfolio-container-wrapper').height(carousel_height_wrapper);
					swiperItem.height(carousel_height_wrapper);
					swiperItem.find('.swiper-slide').height(carousel_height);

				}

			});

			jQuery('.dtportfolio-single-mediaontop-section-holder').each(function () {

				var mheight = jQuery(window.top).height();

				jQuery(this).css('height', mheight);

				var windowWidth = jQuery(window).width();

				var fullwidth_next = jQuery(this).next('.dtportfolio-fullwidth-wrapper-fix');
				var offset = 0 - fullwidth_next.offset().left;

				jQuery(this).css('width', windowWidth);
				jQuery(this).css('left', offset);			

			});

		}

		// Calculate portfolio single item height for media on top, fixed gallery, fixed feature image
		function calculatePortfolioSingleItemHeight() {

			if($('body').hasClass('single-dt_portfolios')) {

				var winHeight = $(window).height();
				var headerHeight = $('#header-wrapper').height();
				var footerHeight = $('#footer').height();

				var height = parseInt((winHeight-headerHeight-footerHeight), 10);

				$('.fixed-gallery').find('.dtportfolio-fixed-content').height(height).css('top', headerHeight);
				$('.fixed-gallery').find('.dtportfolio-details').height(height).niceScroll({zindex: 999999, cursorborder: '1px solid #424242' });

				$('.fixed-feature-image').find('.dtportfolio-fixed-content').height(height).css('top', headerHeight);
				$('.fixed-feature-image').find('.dtportfolio-details').height(height).niceScroll({zindex: 999999, cursorborder: '1px solid #424242' });

			}

		}

		// Function to animate portfolio items	
		function animatePortfolioItems() {	
			jQuery('.dtportfolio-item.animate').each(function(){
				jQuery(this).one('inview', function (event, visible) {	
					if (visible == true) {
						var $this = $(this),
						$animation = ( $this.data('animationeffect') !== undefined ) ? $this.data('animationeffect') : 'slideUp';
						var	$delay = ( $this.data('animationdelay') !== undefined ) ? $this.data('animationdelay') : 400;
						setTimeout(function() { $this.addClass($animation);	},$delay);
					}
				});
			});
			$(window).scroll();
		}
		//animatePortfolioItems();


		// Make portfolio featured video to play on click
	    $('#portfolio-featured-video').on('click', function() {            
	        if($(this).hasClass('play')) {
	        	$(this).get(0).play();
	        } else {
	        	$(this).get(0).pause();
	        }
	        $(this).toggleClass('pause play');
	    });

		// ilightbox for with gallery list, with gallery thumb hover effects
		$('.dtportfolio-item').each( function() {
			var ilightboxid = $(this).find('.dtportfolio-image-overlay').attr('data-ilightboxid');
			if($('.'+ilightboxid).length) {
				$('.'+ilightboxid).iLightBox({
					attr: 'data-ilightboximg',
					fullViewPort: 'stretch',
				    controls: {
				      mousewheel: true,
				      swipe: true,
				      thumbnail: true
				    },		
					path: 'horizontal',
					infinite: true,
		    	});	
	    	}	
		});	

		// ilightbox for portfolio template listing, single page gallery listing
		var $pphoto = $('a[data-gal^="prettyPhoto[gallery]"], a[data-gal^="prettyPhoto[gallery-listing]"]');
		if ($pphoto.length && !$pphoto.parents('.dtportfolio-container, .dtportfolio-container-fullpage').hasClass('swiper-wrapper')) {
			$($pphoto).iLightBox({
				attr: 'href',
				fullViewPort: 'stretch',
			    controls: {
			      mousewheel: true,
			      swipe: true,
			      thumbnail: true
			    },		
				path: 'vertical',
				infinite: true,
	    	});	
		}

		/* Reset Full Page jQuery */
		if(dtportfoliofrontendobject.elementor_preview_mode == 'true') {
			$('.dtportfolio-container-resetter div:first-child').each(function() {
				if($('html').hasClass('fp-enabled')) {
					$(this).fullpage.destroy('all');
				}
			});	
		}


		// Full page jquery
		$('.dtportfolio-container-fullpage:not(.disable-fullpage-jquery)').each(function() {

			if($(this).attr('data-fullpagenavigation') != '') {

				$(this).fullpage({
					navigation: true,
					navigationPosition: $(this).attr('data-fullpagenavigation'),
					css3: true,
					autoScrolling: $(this).attr('data-disableautoscrolling'),
					fitToSection: false,
					scrollBar: true,
				});

			} else {

				$(this).fullpage({
					navigation: false,
					css3: true,
					autoScrolling: $(this).attr('data-disableautoscrolling'),
					fitToSection: false,
					scrollBar: true,
				});

			}

		});	


		// Parallax jquery
		if($('.dtportfolio-parallax').length) {
			$('.dtportfolio-parallax').each(function() {
				if($(this).attr('data-jarallax') !== undefined) {
				    $(this).jarallax({
				        imgWidth: 1366,
				        imgHeight: 768
				    });
				}
		    });	
		}

		// Multiscroll
		if($('.dtportfolio-multiscroll-container').length) {

	    	$('.dtportfolio-multiscroll-container').multiscroll({
				css3: true,
				scrollingSpeed: 800
			});
			
	    	$('.dtportfolio-container-wrapper').height($('.dtportfolio-multiscroll-container .dtportfolio-multiscroll').height());
	    	if($('.multiscroll-button').length) {
		        $('.multiscroll-button.down').on('click', function() {
		            $.fn.multiscroll.moveSectionDown();
		        });
		        $('.multiscroll-button.up').on('click', function() {
		            $.fn.multiscroll.moveSectionUp();
		        });	
	        }

			if(currentWidth <= 767) {

	            var $holder = $('.dtportfolio-multiscroll-container .dtportfolio-multiscroll');

	            $holder.each(function( i, val ) {

	                var $holder_class = $(this).attr('class');
	                var $holder_classes = $holder_class.split(' ');

	                var $holder_modified_classes = new Array();
	                $.each( $holder_classes, function( j, value ) {

	                    if(value.indexOf('dtportfolio-hover-') == 0) {
	                    } else {
	                        $holder_modified_classes.push(value);
	                    }

	                });
	                $holder_modified_classes.push('dtportfolio-hover-overlay');

	                $holder_modified_classes = $holder_modified_classes.join(' ');
	                $(this).attr('class', $holder_modified_classes);

	            });

			}

			/* Reset Multiscroll jQuery */
			if(dtportfoliofrontendobject.elementor_preview_mode == 'true') {
				$('.dtportfolio-container-resetter').multiscroll.destroy();
			}

		}


		/* Reset Multiscroll jQuery - Style Attributes */
		if(dtportfoliofrontendobject.elementor_preview_mode == 'true') {

			if($("body[class*='ms-viewing-']")) {
				$('body').removeAttr('style');
				$('html').removeAttr('style');
			}

		}


		// Infinite portfolio
		
		$('.dtportfolio-infinite-portfolio-load-more').each(function(){

			var $this_global = $(this);
			var $completedata_obj = $this_global.attr('data-complete-data');
			var $completedata_json = $.parseJSON($completedata_obj);

			var $pagination_type = $completedata_json['portfolio-pagination-type'];
			var $paged = $completedata_json['paged'];

			if($pagination_type == 'lazy-load') {

				$(window).scroll(function(){
					if($(window).scrollTop() == $(document).height() - $(window).height()){

						var $wrapper = $this_global.prev('.dtportfolio-infinite-portfolio-wrapper');

						$completedata_json['paged'] = $paged;
						var $completedata_object = JSON.stringify($completedata_json);

						$paged++;

						$.ajax({
							type : "post",
							dataType : "html",
							url : dtportfoliofrontendobject.ajaxurl,
							data : { action: "dtportfolio_ajax_infinite_portfolios", completedata_json: $completedata_object },
							beforeSend: function(){
								jQuery('.dtportfolio-infinite-portfolio-load-more').prepend( '<i class="fa fa-spinner fa-spin"></i>' );
							},						
							success: function (data) {

								if (data.length > 0) {

									$wrapper.find('.dtportfolio-infinite-portfolio-container').isotope('insert', $(data));
									setTimeout(function(){
										$wrapper.find('.dtportfolio-infinite-portfolio-container').isotope('layout');
									}, 400);

									animatePortfolioItems();

								} else {

									$this_global.addClass('disable');
									$wrapper.find(".message").removeClass("hidden");
									setTimeout(function(){
										$wrapper.find(".message").addClass('hidden');
									}, 5000);

								}
								
							},
							error: function (jqXHR, textStatus, errorThrown) {
							},
							complete: function(){
								jQuery(".dtportfolio-infinite-portfolio-load-more i").remove();
							}																		
						});	

					}
				});

			} else if( $pagination_type == 'load-more') {
				
				$this_global.click(function(e){

					e.preventDefault();

					var $this = $(this);
					var $wrapper = $this.prev('.dtportfolio-infinite-portfolio-wrapper');

					$completedata_json['paged'] = $paged;
					var $completedata_object = JSON.stringify($completedata_json);

					$paged++;

					$.ajax({
						type : "post",
						dataType : "html",
						url : dtportfoliofrontendobject.ajaxurl,
						data : { action: "dtportfolio_ajax_infinite_portfolios", completedata_json: $completedata_object },
						beforeSend: function(){
							jQuery('.dtportfolio-infinite-portfolio-load-more').prepend( '<i class="fa fa-spinner fa-spin"></i>' );
						},					
						success: function (data) {

							if (data.length > 0) {

								$wrapper.find('.dtportfolio-infinite-portfolio-container').isotope('insert', $(data));
								setTimeout(function(){
									$wrapper.find('.dtportfolio-infinite-portfolio-container').isotope('layout');
								}, 400);

								animatePortfolioItems();

							} else {

								$this.addClass('disable');
								$wrapper.find(".message").removeClass("hidden");
								setTimeout(function(){
									$wrapper.find(".message").addClass('hidden');
								}, 5000);

							}

						},
						error: function (jqXHR, textStatus, errorThrown) {
						},
						complete: function(){
							jQuery(".dtportfolio-infinite-portfolio-load-more i").remove();
						}					
					});
				});

			}

		});

		$(window).trigger('resize');

	};


    //Elementor JS Hooks
    $(window).on('elementor/frontend/init', function () {

    	elementorFrontend.hooks.addAction('frontend/element_ready/global', dtPortfolioPostsWidgetHandler);

    });

    // Visual Composer JS Hooks
    if(dtportfoliofrontendobject.page_builder == 'visual-composer') {
		dtPortfolioPostsWidgetHandler(jQuery('body'), jQuery);
	}

})(jQuery);