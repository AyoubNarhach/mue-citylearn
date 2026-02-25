var dtLMSFrontendUtils = {

	dtLMSInitializeDonutChart : function() {

	  	jQuery('.dtlms-donutchart').each(function() {
			var $this = jQuery(this);
		 	var $bgColor =  ( $this.data('bgcolor') !== undefined ) ? $this.data('bgcolor') : '#5D18D6';
		 	var $fgColor =  ( $this.data('fgcolor') !== undefined ) ? $this.data('fgcolor') : '#000000';
		 	var $size = ( $this.data('size') !== undefined ) ? $this.data('size') : '100';
		 
		 	$this.donutchart({'size': $size, 'fgColor': $fgColor, 'bgColor': $bgColor , 'donutwidth' : 5 });
		 	$this.donutchart('animate');
		});	

	},

	dtLMSEnableCourseCurriculumContentScroll : function() {

		if(jQuery('.dtlms-curriculum-content-holder').length) {

			var currentWidth = window.innerWidth || document.documentElement.clientWidth;

			if(currentWidth > 1199) {

				jQuery('.dtlms-curriculum-content-holder').height(5000);

				setTimeout(function() { 

					var windowHeight = jQuery(window.top).height();
					var height = parseInt((parseInt(windowHeight, 10)-parseInt(184, 10)), 10);		

					jQuery('.dtlms-curriculum-content-holder').height(height);

					dtLMSFrontendUtils.dtLMSNiceScroll('.dtlms-curriculum-content-holder');

				}, 400);

			}

		}

	},				
					
	dtLMSEnableCourseCurriculumDetailsScroll : function() {

		if(jQuery('.dtlms-curriculum-detailed-links').length) {

			var currentWidth = window.innerWidth || document.documentElement.clientWidth;

			if(currentWidth > 1199) {

				jQuery('.dtlms-curriculum-detailed-links').height(5000);

				setTimeout(function() { 

					var windowHeight = jQuery(window.top).height();
					var height = parseInt((parseInt(windowHeight, 10)-parseInt(184, 10)), 10);	

					jQuery('.dtlms-curriculum-detailed-links').height(height);

					dtLMSFrontendUtils.dtLMSNiceScroll('.dtlms-curriculum-detailed-links');	

				}, 400);

			}

		}

	},

	dtLMSEnableCourseCurriculumResponsiveScroll : function() {

		if(jQuery('.dtlms-course-curriculum-popup-container').length) {

			var currentWidth = window.innerWidth || document.documentElement.clientWidth;

			if(currentWidth <= 1199) {

				jQuery('.dtlms-course-curriculum-popup-container').height(5000);

				setTimeout(function() { 
					
					var windowHeight = jQuery(window.top).height();
					var height = parseInt((parseInt(windowHeight, 10)-parseInt(184, 10)), 10);	

					jQuery('.dtlms-course-curriculum-popup-container').height(height);

					dtLMSFrontendUtils.dtLMSNiceScroll('.dtlms-course-curriculum-popup-container');

				}, 400);

			}

		}

	},

	dtLMSToggleItems : function() {

		if(jQuery('.dtlms-toggle').length) {

			jQuery('.dtlms-toggle').toggleClick(function() {
				jQuery(this).removeClass('active');  
			}, function() { 
				jQuery(this).addClass('active'); 
			});

			jQuery('.dtlms-toggle').on('click',function(){ 
				jQuery(this).next('.dtlms-toggle-content').slideToggle(); 
			});

		}

	},		

	dtLMSNiceScroll : function(itemclass) {

		if(jQuery(itemclass).length) {

			var primarColor = lmsfrontendobject.primarColor;

		    jQuery(itemclass).niceScroll({
				cursorwidth:4,
				cursoropacitymin:0.4,
				cursorcolor:primarColor,
				cursorborder:'none',
				cursorborderradius:2,
				autohidemode:'leave'	        
		    });	

		}

	},	

	dtLMSProgressBar : function(inview) {

		if(inview) {

			jQuery('.dtlms-progressbar').each(function() {

				jQuery(this).one('inview', function (event, visible) {	
					if (visible == true) {
						 var progressBar = jQuery(this),
						 	 progressValue = progressBar.find('.dtlms-bar').attr('data-value');
							 
						if (!progressBar.hasClass('animated')) {
						 	progressBar.addClass('animated');
							progressBar.find('.dtlms-bar').animate({
									width: progressValue + "%"
								}, 600, function() { 
									progressBar.find('.dtlms-bar-text').fadeIn(400);
							});
						}
					}
				});

			});

		} else {

			jQuery('.dtlms-progressbar').each(function() {

				var progressBar = jQuery(this),
				 	 progressValue = progressBar.find('.dtlms-bar').attr('data-value');
					 
				if (!progressBar.hasClass('animated')) {
				 	progressBar.addClass('animated');
					progressBar.find('.dtlms-bar').animate({
							width: progressValue + "%"
						}, 600, function() { 
							progressBar.find('.dtlms-bar-text').fadeIn(400);
					});
				}

			});

		}

	},

	dtLMSCountDownTimer : function(itemId) {

		if(itemId.find('.dtlms-countdown-holder').length) {

			itemId.find('.dtlms-countdown-holder').downCount({
				date	: itemId.find('.dtlms-countdown-holder').attr('data-date'),
				offset	: itemId.find('.dtlms-countdown-holder').attr('data-offset')
			}, function () {
				var curriculum_id = itemId.find('.dtlms-countdown-holder').attr('data-curriculumid');
				var parent_curriculum_id = itemId.find('.dtlms-countdown-holder').attr('data-parentcurriculumid');
				jQuery( '.dtlms-curriculum-meta-title a[data-curriculumid='+curriculum_id+'][data-parentcurriculumid='+parent_curriculum_id+']' ).trigger('click');
				return false;
			});

		}

	},

	dtLMSCoursesListingIsotope : function() {

	    jQuery('.dtlms-apply-isotope .dtlms-courses-listing-items').each(function() {

	    	//jQuery(this).isotope('destroy');
	        jQuery(this).isotope({
				itemSelector: '.dtlms-column',
				percentPosition: true,
				masonry: {
					columnWidth: '.grid-sizer'
				}        	
	        });

	    });

	    jQuery('.dtlms-apply-isotope .dtlms-courses-listing-isotope-filter').each(function() {

	    	var isotope_filter = jQuery(this);

			isotope_filter.find('a').on('click', function() {

				isotope_filter.find('a').removeClass('active-sort');
				var selector = jQuery(this).attr('data-filter');
				jQuery(this).addClass('active-sort');

				jQuery(this).parents('.dtlms-apply-isotope').find('.dtlms-courses-listing-items').isotope({ filter: selector, masonry: {  }, animationEngine : 'jquery' });

				return false;

			});

	    });

	},

	dtLMSClassesListingIsotope : function() {

	    jQuery('.dtlms-apply-isotope .dtlms-classes-listing-items').each(function() {

	    	//jQuery(this).isotope('destroy');
	        jQuery(this).isotope({
				itemSelector: '.dtlms-column',
				percentPosition: true,
				masonry: {
					columnWidth: '.grid-sizer'
				}        	
	        });

	    });    

	},

	dtLMSPackagesListingIsotope : function() {

	    jQuery('.dtlms-apply-isotope .dtlms-packages-listing-items').each(function() {
	        jQuery(this).isotope({
				itemSelector: '.dtlms-column',
				percentPosition: true,
				masonry: {
					columnWidth: '.grid-sizer'
				}        	
	        });
	    });

	},

	dtLMSAjaxBeforeSend : function(this_item) {

		if(this_item != undefined) {
			if(!this_item.find('#dtlms-ajax-load-image').hasClass('first')) {
				this_item.find('#dtlms-ajax-load-image').show();
			} else {
				this_item.find('#dtlms-ajax-load-image').removeClass('first');
			}
		} else {
			if(!jQuery('#dtlms-ajax-load-image').hasClass('first')) {
				jQuery('#dtlms-ajax-load-image').show();			
			} else {
				jQuery('#dtlms-ajax-load-image').removeClass('first');
			}
		}

	},	

	dtLMSAjaxAfterSend : function(this_item) {

		if(this_item != undefined) {
			this_item.find('#dtlms-ajax-load-image').hide();
		} else {
			jQuery('#dtlms-ajax-load-image').hide();
		}

	},	

	dtLMSTabScroll : function() {

		if(jQuery('.dtlms-course-detail .dtlms-tabs-horizontal').length) {
			jQuery('.dtlms-course-detail .dtlms-tabs-horizontal').scrollTabs();
		}
		if(jQuery('.dtlms-class-detail .dtlms-tabs-horizontal').length) {
			jQuery('.dtlms-class-detail .dtlms-tabs-horizontal').scrollTabs();
		}

	},				

};

var dtLMSFrontend = {

	dtInit : function() {

		var isMobile = (navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i)) || (navigator.userAgent.match(/Android/i)) || (navigator.userAgent.match(/Blackberry/i)) || (navigator.userAgent.match(/Windows Phone/i)) ? true : false;
		var currentWidth = window.innerWidth || document.documentElement.clientWidth;

		dtLMSFrontend.dtLMS(isMobile, currentWidth);
		dtLMSFrontend.dtClasses();
		dtLMSFrontend.dtClassesListing();
		dtLMSFrontend.dtCurriculum();
		dtLMSFrontend.dtCourses();
		dtLMSFrontend.dtCoursesListing();
		dtLMSFrontend.dtLessons();
		dtLMSFrontend.dtQuizzes();
		dtLMSFrontend.dtAssignments();
		dtLMSFrontend.dtPackagesListing();

	},

	dtLMS : function(isMobile, currentWidth) {

		// For mega menu fullwidth issue fix
		if(jQuery('body').hasClass('single-dtlms_courses') || jQuery('body').hasClass('single-dtlms_classes')) {
			setTimeout(function() { 
				jQuery(window).trigger('resize');
			}, 800);
		}

		dtLMSFrontendUtils.dtLMSToggleItems();
		dtLMSFrontendUtils.dtLMSCountDownTimer(jQuery('.dtlms-class-dynamic-section-holder, .dtlms-course-dynamic-section-holder'));
		dtLMSFrontendUtils.dtLMSTabScroll();


		if(jQuery('ul.dtlms-tabs-horizontal').length > 0){
			jQuery('ul.dtlms-tabs-horizontal').each(function(){
				jQuery(this).dtLMSTabs('> .dtlms-tabs-horizontal-content', {
					effect: 'fade'
				});
			});
		}

		if(jQuery('ul.dtlms-tabs-vertical').length > 0){
			jQuery('ul.dtlms-tabs-vertical').each(function() {
				var this_item = jQuery('ul.dtlms-tabs-vertical');
				jQuery(this).dtLMSTabs('> .dtlms-tabs-vertical-content', {
					effect: 'fade',
				});
			});
		}		

		jQuery( 'body' ).delegate( '.dtlms-login-link', 'click', function(e){  
		
			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_show_login_form_popup',
				},
				success: function (response) {

					jQuery('body').find('.dtlms-login-form-container').remove();
					jQuery('body').find('.dtlms-login-form-overlay').remove();
					jQuery('body').append(response);	

					jQuery('#user_login').focus();

				}
			});	

			e.preventDefault();
			
		});

		jQuery( 'body' ).delegate( '.dtlms-login-form-overlay', 'click', function(e){  

			jQuery('body').find('.dtlms-login-form-container').fadeOut();
			jQuery('body').find('.dtlms-login-form-overlay').fadeOut();

			e.preventDefault;
			
		});

		jQuery(window).bind('resize', function() {
			dtLMSFrontendUtils.dtLMSEnableCourseCurriculumContentScroll();
			dtLMSFrontendUtils.dtLMSEnableCourseCurriculumDetailsScroll();
			dtLMSFrontendUtils.dtLMSEnableCourseCurriculumResponsiveScroll();

			dtLMSFrontendUtils.dtLMSCoursesListingIsotope();
			dtLMSFrontendUtils.dtLMSClassesListingIsotope();
			dtLMSFrontendUtils.dtLMSPackagesListingIsotope();
		});

		dtLMSFrontendUtils.dtLMSProgressBar(false);
		
	},

	dtClasses : function() {

		jQuery( 'body' ).delegate( '.dtlms-start-class-button', 'click', function(e){ 

			var this_item = jQuery(this),
				startclass_nonce = this_item.attr('data-start-class-nonce'),
				class_id = this_item.attr('data-classid'),
				user_id = this_item.attr('data-userid'),
				author_id = this_item.attr('data-authorid');

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_start_class_ajax_initialize',
					startclass_nonce: startclass_nonce,
					class_id: class_id,
					user_id: user_id,
					author_id: author_id,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					location.reload();
				},
				complete: function() {
				} 
			});	

			e.preventDefault();

		});

		jQuery( 'body' ).delegate( '.dtlms-submit-class-button', 'click', function(e) { 

			e.preventDefault();

			var this_item = jQuery(this),
				submitclass_nonce = this_item.attr('data-submit-class-nonce'),
				class_id = this_item.attr('data-classid'),
				user_id = this_item.attr('data-userid'),
				author_id = this_item.attr('data-authorid'),
				total_curriculumcount = this_item.attr('data-totalcurriculumcount'),
				submitted_curriculum_count = this_item.attr('data-submittedcurriculumcount');

			if(total_curriculumcount != submitted_curriculum_count)	 {
				alert(lmsfrontendobject.submitClass);
				return false;
			}

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_submit_class_initialize',
					submitclass_nonce: submitclass_nonce,
					class_id: class_id,
					user_id: user_id,
					author_id: author_id,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					location.reload();
				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});		

		});		

		jQuery( 'body' ).delegate( '.dtlms-apply-onsite-class', 'click', function(e) { 

			e.preventDefault();

			var this_item = jQuery(this),
				class_id = this_item.attr('data-classid'),
				user_id = this_item.attr('data-userid');

			if(!confirm(lmsfrontendobject.confirmRegistration)) {
				return false;
			}

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_apply_onsite_class',
					class_id: class_id,
					user_id: user_id,
				},
				beforeSend: function() {
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					var response = response.split('|');
					this_item.parents('.dtlms-item-status-details').html('<span class="dtlms-applied"><span class="fa fa-check"></span>'+response[0]+'</span>');
					jQuery('.dtlms-classes-details-holder').find('span').html(response[1]);
				},
				complete: function() {
					this_item.find('span').remove();
				} 
			});		

		});		

		jQuery( 'body' ).delegate( '.dtlms-register-onsite-class', 'click', function(e) { 

			e.preventDefault();

			var this_item = jQuery(this),
				class_id = this_item.attr('data-classid');

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_register_onsite_class',
					class_id: class_id,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					jQuery('body').find('.dtlms-class-registration-form-container').remove();
					jQuery('body').append(response);					
				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});		

		});	

		jQuery( 'body' ).delegate( '.dtlms-class-registration-form', 'submit', function(){
			
			var this_item = jQuery(this);
	        var form = jQuery('.dtlms-class-registration-form')[0];
	        var data = new FormData(form);
	        data.append('action', 'dttheme_submit_class_registration_form');

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data: data,
	            processData: false,
	            contentType: false,	
	            cache: false,
				beforeSend: function(){
					this_item.find('.dtlms-submit-registration-form').prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {

					var response = response.split('|');

					this_item.parents('.dtlms-class-registration-form-holder').find('.dtlms-error-box,.dtlms-success-box').remove();

					if(response[0] == 'invalid') {

						this_item.parents('.dtlms-class-registration-form-holder').append( '<div class="dtlms-error-box">' + response[1] + '</div>' ).find('.dtlms-success-box').hide().fadeIn('slow');

					} else {

						jQuery('.dtlms-classes-details-holder').find('span').html(response[1]);
						if(response[0] == 'true') {
							jQuery('.dtlms-register-onsite-class').attr('disabled', 'disabled');
							jQuery('.dtlms-register-onsite-class').val(lmsfrontendobject.closedRegistration);
						}

						this_item.parents('.dtlms-class-registration-form-holder').append( '<div class="dtlms-success-box">' + lmsfrontendobject.registrationSuccess + '</div>' ).find('.dtlms-success-box').hide().fadeIn('slow');
						this_item.find(':input').prop('disabled', true);
						setTimeout(function() { 
							jQuery('body').css('overflow', '');
							jQuery('body').find('.dtlms-class-registration-form-container').fadeOut();
							jQuery('body').find('.dtlms-class-registration-form-overlay').fadeOut();
						}, 800);

					}
					
				},
				complete: function(){
					this_item.find('.dtlms-submit-registration-form').find('span').remove();
				}
			});
			
			return false;
			
		});	

		jQuery( 'body' ).delegate( '.dtlms-class-registration-form-overlay', 'click', function(e){  

			jQuery('body').find('.dtlms-class-registration-form-container').fadeOut();
			jQuery('body').find('.dtlms-class-registration-form-overlay').fadeOut();

			e.preventDefault;
			
		});							

	},

	dtClassesListing : function() {

		jQuery( 'body' ).delegate( '.classfilter-orderby:not(.dtlms-without-ajax-load), .classfilter-classtype:not(.dtlms-without-ajax-load), .classfilter-instructor:not(.dtlms-without-ajax-load), .classfilter-cost:not(.dtlms-without-ajax-load), .classfilter-date:not(.dtlms-without-ajax-load)', 'change', function() {  

			var this_item = jQuery(this).parents('.dtlms-classes-listing-holder');
			var offset = 0;
			var current_page = 1;

			// list class ajax call
			dtlmsListClasses(offset, current_page, this_item);

		});

		jQuery('.dtlms-classes-search-text:not(.dtlms-without-ajax-load)').on('input', function() {

			var this_item = jQuery(this).parents('.dtlms-classes-listing-holder');
			var offset = 0;
			var current_page = 1;

			// list class ajax call
			setTimeout( function() {
				dtlmsListClasses(offset, current_page, this_item);
			}, 1600);			

		});

		jQuery( 'body' ).delegate( '.dtlms-classes-display-type:not(.dtlms-without-ajax-load)', 'click', function(e) {  

			jQuery('.dtlms-classes-display-type').removeClass('active');
			jQuery(this).addClass('active');
			var list_type = jQuery(this).attr('data-displaytype');
			jQuery(this).parents('.dtlms-classes-listing-holder').find('.dtlms-classes-listing-containers').removeClass('list grid');
			jQuery(this).parents('.dtlms-classes-listing-holder').find('.dtlms-classes-listing-containers').addClass(list_type);
			jQuery(this).trigger('change');	

			var this_item = jQuery(this).parents('.dtlms-classes-listing-holder');
			var offset = 0;
			var current_page = 1;

			// list class ajax call
			dtlmsListClasses(offset, current_page, this_item);

			e.preventDefault();		

		});

		jQuery( 'body' ).delegate( '.dtlms-classes-listing-containers .dtlms-pagination a', 'click', function(e){	
			
			var this_item = jQuery(this).parents('.dtlms-classes-listing-holder');

			if(jQuery(this).parent().hasClass('prev-post')) {
				current_page = parseInt(jQuery(this).attr('data-currentpage'), 10)-1;
			} else if(jQuery(this).parent().hasClass('next-post')) {
				current_page = parseInt(jQuery(this).attr('data-currentpage'), 10)+1;
			} else {
				current_page = jQuery(this).text();
			}

			var post_per_page = jQuery(this).parents('.dtlms-classes-listing-holder').attr('data-postperpage');

			if(current_page == 1) { 
				var offset = 0; 
			} else if(current_page > 1) { 
				var offset = ((current_page-1)*post_per_page); 
			}

			// list class ajax call
			dtlmsListClasses(offset, current_page, this_item);

			e.preventDefault();
							
		});	

		function dtlmsListClasses(offset, current_page, this_item) {

			// gathering all default values
			var search_text = this_item.find('.dtlms-classes-search-text').val();

			var display_type = this_item.find('.dtlms-classes-display-type.active').attr('data-displaytype');

			var order_by = this_item.find('.classfilter-orderby').val();

			var class_type = this_item.find('.classfilter-classtype:checked').map(function(){
				return this.value;
			}).get();

			var instructor = this_item.find('.classfilter-instructor:checked').map(function(){
				return this.value;
			}).get();

			var cost_type = this_item.find('.classfilter-cost:checked').map(function(){
				return this.value;
			}).get();

			var start_date = this_item.find('.classfilter-date').val();


			var post_per_page = this_item.attr('data-postperpage');
			var columns = this_item.attr('data-columns');
			var enable_carousel = this_item.attr('data-enablecarousel');	

			var disable_filters = this_item.attr('data-disablefilters');

			var apply_isotope = this_item.attr('data-applyisotope');

			var default_filter = this_item.attr('data-defaultfilter');
			var default_display_type = this_item.attr('data-defaultdisplaytype');
			var class_item_ids = this_item.attr('data-classitemids');
			var instructor_ids = this_item.attr('data-instructorids');
			var enable_fullwidth = this_item.attr('data-enablefullwidth');
			var type = this_item.attr('data-type');


			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_generate_classes_listing',
					display_type: display_type,
					order_by: order_by,
					class_type: class_type,
					instructor: instructor,
					cost_type: cost_type,
					start_date: start_date,
					post_per_page: post_per_page,
					columns: columns,
					offset: offset,
					current_page: current_page,
					search_text: search_text,
					enable_carousel: enable_carousel,
					disable_filters: disable_filters,
					apply_isotope: apply_isotope,
					default_filter: default_filter,
					default_display_type: default_display_type,
					class_item_ids: class_item_ids,
					instructor_ids: instructor_ids,
					enable_fullwidth: enable_fullwidth,
					type: type,			
				},
				beforeSend: function(){
					dtLMSFrontendUtils.dtLMSAjaxBeforeSend(this_item);
				},
				error: function (xhr, status, error) {
					this_item.find('.dtlms-classes-listing-containers').html('Something went wrong!');
				},
				success: function (response) {
					this_item.find('.dtlms-classes-listing-containers').html(response);
					if(enable_carousel == 'true') {
						dtlmsClassesSwiper(this_item);
					} else {
						dtLMSFrontendUtils.dtLMSClassesListingIsotope();
						setTimeout( function() {
							jQuery(window).trigger('resize');
						}, 1200);						
					}
				},
				complete: function(){
					dtLMSFrontendUtils.dtLMSAjaxAfterSend(this_item);
				} 
			});
		
		}

		jQuery('.dtlms-classes-listing-holder:not(.dtlms-without-ajax-load)').each(function() {
			if(jQuery(this).length) {
				dtlmsListClasses(0, 1, jQuery(this));
			}
		});
		
		
		// Classes listing carousel

		function dtlmsClassesSwiper(this_item) {

			var swiperGallery = [];
			var swiperGalleryOptions = [];
			var swiperIterator = 1;
			this_item.find('.dtlms-classes-swiper-listing').each(function() {

				var $swiperItem = jQuery(this);
				var swiperUniqueId = 'swiperuniqueid-'+swiperIterator;
				swiperGalleryOptions[swiperUniqueId] = [];
				$swiperItem.attr('id', swiperUniqueId);

				// Get swiper options
				var effect = $swiperItem.attr('data-carouseleffect');
				var autoplay = parseInt($swiperItem.attr('data-carouselautoplay'), 10);
				if(autoplay > 0) {
					swiperGalleryOptions[swiperUniqueId]['autoplay'] = autoplay;
				} else {
					swiperGalleryOptions[swiperUniqueId]['autoplay'] = 0;
				}

				var slidesperview = parseInt($swiperItem.attr('data-carouselslidesperview'), 10);
				var loopmode = ($swiperItem.attr('data-carouselloopmode') == 'true') ? true : false;
				var mousewheelcontrol = ($swiperItem.attr('data-carouselmousewheelcontrol') == 'true') ? true : false;

				var pagination_type = pagination_class = '';
				var bulletpagination = ($swiperItem.attr('data-carouselbulletpagination') == 'true') ? true : false;
				if(bulletpagination) {
					var pagination_class = '.dtlms-swiper-bullet-pagination';
					var pagination_type = 'bullets';
				}

				var spacebetween = parseInt($swiperItem.attr('data-carouselspacebetween'), 10);
				if(spacebetween == '') {
					spacebetween = 0;
				}

				if(slidesperview == 1) {
					var breakpoint_slides_1 = breakpoint_slides_2 = breakpoint_slides_3 = breakpoint_slides_4 = 1;
				} else if(slidesperview == 2) {
					var breakpoint_slides_1 = 2; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
				} else if(slidesperview == 3) {
					var breakpoint_slides_1 = 3; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
				} else if(slidesperview >= 4) {
					var breakpoint_slides_1 = 4; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
				}				

				// Generate swiper
			    swiperGallery[swiperUniqueId] = new Swiper($swiperItem, {
	     			
	                simulateTouch: true,
	                roundLengths: true,
	                keyboardControl: true,
	                paginationClickable: true,
	                autoHeight: true,

	                spaceBetween: spacebetween,
	                autoplay: autoplay,
	                slidesPerView: slidesperview,
	                loop:loopmode,
	                mousewheelControl: mousewheelcontrol,
	                direction: 'horizontal',

					pagination: {
						el: pagination_class,
						type: pagination_type,
						clickable: true
					},                 

	                effect: effect,
					coverflow: {
					  rotate: 0,
					  stretch: 10,
					  depth: 200,
					  modifier: 1,
					},
			        cube: {
			            shadow: true,
			            slideShadows: true,
			            shadowOffset: 20,
			            shadowScale: 0.94
			        },
			      
			        breakpoints: {
			            1199: {
			                slidesPerView: breakpoint_slides_1,
			            },
			            991: {
			                slidesPerView: breakpoint_slides_2,
			            },
			            767: {
			                slidesPerView: breakpoint_slides_3,
			            },
			            320: {
			                slidesPerView: breakpoint_slides_4,
			            }
			        },             

			    });
		    	
				swiperGallery[swiperUniqueId].on('transitionEnd', function () {
			        if(swiperGalleryOptions[swiperUniqueId]['autoplay'] > 0) {
			        	swiperGallery[swiperUniqueId].startAutoplay();
			        }
				});

		    	var arrowpagination = ($swiperItem.attr('data-carouselarrowpagination') == 'true') ? true : false;

		    	if(arrowpagination) {

				    $swiperItem.find('.dtlms-swiper-arrow-pagination .dtlms-swiper-arrow-prev').on('click', function(e) {			    	
				    	var swiperUniqueId = $swiperItem.attr('id');
				        swiperGallery[swiperUniqueId].slidePrev();
				        if(swiperGalleryOptions[swiperUniqueId]['autoplay'] > 0) {
				        	swiperGallery[swiperUniqueId].startAutoplay();
				        }
				        e.preventDefault();		
				    });

				     $swiperItem.find('.dtlms-swiper-arrow-pagination .dtlms-swiper-arrow-next').on('click', function(e) {
				    	var swiperUniqueId = $swiperItem.attr('id');
				        swiperGallery[swiperUniqueId].slideNext();
				        if(swiperGalleryOptions[swiperUniqueId]['autoplay'] > 0 ) {
				        	swiperGallery[swiperUniqueId].startAutoplay();
				        }
				        e.preventDefault();	
				    });	

				}

			    swiperIterator++;

			});	

		}

	},

	dtCurriculum : function() {

		jQuery( 'body' ).delegate( '.dtlms-curriculum-list li:not(.locked) .dtlms-curriculum-meta-title a', 'click', function(e){  
		
			if(jQuery('#dtlms-course-curriculum-popup').hasClass('dtlms-curriculum-quiz-lock')) {
				if(confirm(lmsfrontendobject.onRefreshCurriculum)) {
					jQuery('#dtlms-complete-quiz').trigger('click');
					return false;
				} else {
					return false;
				}
			}

			var this_item = jQuery(this),
				parent_item = this_item.parents('.dtlms-curriculum-details, .dtlms-tabs-horizontal-content, .dtlms-class-course-curriculum-holder'),
				course_id = this_item.attr('data-courseid'),
				parent_curriculum_id = this_item.attr('data-parentcurriculumid'),
				curriculum_id = this_item.attr('data-curriculumid');

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_generate_curriculum_page_contents',
					course_id: course_id,
					parent_curriculum_id: parent_curriculum_id,
					curriculum_id: curriculum_id,
				},
				beforeSend: function(){
					dtLMSFrontendUtils.dtLMSAjaxBeforeSend(parent_item);
				},
				error: function (xhr, status, error) {
					//jQuery('body').html('Something went wrong!');
				},
				success: function (response) {

					jQuery('body').find('#dtlms-course-curriculum-popup').remove();
					jQuery('body').append(response);

					jQuery('#dtlms-course-curriculum-popup .dtlms-curriculum-list li').removeClass('active');
					jQuery('#dtlms-course-curriculum-popup .dtlms-curriculum-list li a[data-curriculumid="' + curriculum_id + '"][ data-parentcurriculumid="' + parent_curriculum_id + '"]').parents('li').addClass('active');

					jQuery('body, html').css('overflow', 'hidden');
					dtLMSFrontendUtils.dtLMSToggleItems();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumContentScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumDetailsScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumResponsiveScroll();
		
					if(jQuery('#dtlms-course-curriculum-popup').find('.dtlms-countdown-holder').length) {
						dtLMSFrontendUtils.dtLMSCountDownTimer(jQuery('#dtlms-course-curriculum-popup').find('.dtlms-curriculum-content-holder'));
					}

				},
				complete: function(){
					dtLMSFrontendUtils.dtLMSAjaxAfterSend(parent_item);
				} 
			});	

			e.preventDefault();
			
		});

		jQuery( 'body' ).delegate( '.dtlms-refresh-course-curriculum', 'click', function(e){  
		
			if(jQuery('#dtlms-course-curriculum-popup').hasClass('dtlms-curriculum-quiz-lock')) {
				if(confirm(lmsfrontendobject.onRefreshCurriculum)) {
					jQuery('#dtlms-complete-quiz').trigger('click');
					return false;
				} else {
					return false;
				}
			}
		
			if(jQuery('.dtlms-curriculum-list li.active li.active .dtlms-curriculum-meta-title a').length) {
				jQuery('.dtlms-curriculum-list li.active li.active .dtlms-curriculum-meta-title a:first').trigger('click');
			} else {
				jQuery('.dtlms-curriculum-list li.active .dtlms-curriculum-meta-title a:first').trigger('click');
			}

			e.preventDefault();
			
		});	

		jQuery( 'body' ).delegate( '.dtlms-close-course-curriculum-popup', 'click', function(e){  
		
			if(jQuery('#dtlms-course-curriculum-popup').hasClass('dtlms-curriculum-quiz-lock')) {
				if(confirm(lmsfrontendobject.onRefreshCurriculum)) {
					jQuery('#dtlms-complete-quiz').trigger('click');
					return false;
				} else {
					return false;
				}
			}

			jQuery('body, html').css('overflow', '');
			jQuery('#dtlms-course-curriculum-popup').remove();

			location.reload();

			e.preventDefault();
			
		});	

	},

	dtCourses : function() {

		jQuery('.dtlms-class-toggle-switch').toggleClick(function(){ jQuery(this).addClass('active'); },function(){ jQuery(this).removeClass('active'); });
		jQuery('.dtlms-class-toggle-switch').on('click',function(){ jQuery(this).parents('.dtlms-class-toggle-frame').find('.dtlms-class-toggle-content').slideToggle(); });


		jQuery('.ratings span').mouseover(function(e) {
			if(!jQuery(this).parents('.ratings').hasClass('rated')) {
				jQuery('.ratings span').removeClass('icon-moon icon-moon-star-full');
				jQuery( this ).prevAll( 'span' ).andSelf().addClass('icon-moon icon-moon-star-full');
				jQuery( this ).nextAll( 'span' ).addClass('icon-moon icon-moon-star-empty');
			} else {
				setTimeout(function() { jQuery('.ratings').removeClass('rated'); },100);
			}		
			e.preventDefault();
		}).mouseout(function(e) {
			if(!jQuery(this).parents('.ratings').hasClass('rated')) {
				jQuery('.ratings span').removeClass('icon-moon icon-moon-star-full');
				jQuery( this ).prevAll( 'span' ).andSelf().addClass('icon-moon icon-moon-star-full');
				jQuery( this ).nextAll( 'span' ).addClass('icon-moon icon-moon-star-empty');
			} else {
				setTimeout(function() { jQuery('.ratings').removeClass('rated'); },100);
			}
			e.preventDefault();
		});

		jQuery('.ratings span').on('click',function(e) {
			if(!jQuery(this).parents('.ratings').hasClass('rated')) {
				jQuery(this).prevAll('span').andSelf().addClass('icon-moon icon-moon-star-full');
				jQuery(this).parents('.ratings-holder').find('#lms_rating').val(parseInt(jQuery(this).attr('data-value'), 10));
				jQuery(this).parents('.ratings').addClass('rated');
			}
			e.preventDefault();
		});	


	    dtLMSFrontendUtils.dtLMSNiceScroll('.dtlms-students-enrolled-list');

		jQuery( 'body' ).delegate( '.dtlms-start-course-button', 'click', function(e){ 

			var this_item = jQuery(this),
				startcourse_nonce = this_item.attr('data-start-course-nonce'),
				course_id = this_item.attr('data-courseid'),
				user_id = this_item.attr('data-userid'),
				author_id = this_item.attr('data-authorid');

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_start_course_initialize',
					startcourse_nonce: startcourse_nonce,
					course_id: course_id,
					user_id: user_id,
					author_id: author_id,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					location.reload();
				},
				complete: function() {
				} 
			});	

			e.preventDefault();

		});

		jQuery( 'body' ).delegate( '.dtlms-submit-course-button', 'click', function(e) { 

			e.preventDefault();

			var this_item = jQuery(this),
				submitcourse_nonce = this_item.attr('data-submit-course-nonce'),
				course_id = this_item.attr('data-courseid'),
				user_id = this_item.attr('data-userid'),
				author_id = this_item.attr('data-authorid'),
				total_curriculumcount = this_item.attr('data-totalcurriculumcount'),
				submitted_curriculum_count = this_item.attr('data-submittedcurriculumcount');

			if(total_curriculumcount != submitted_curriculum_count)	 {
				alert(lmsfrontendobject.submitCourse);
				return false;
			}

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_submit_course_initialize',
					submitcourse_nonce: submitcourse_nonce,
					course_id: course_id,
					user_id: user_id,
					author_id: author_id,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					location.reload();
				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});		

		});		

	},

	dtCoursesListing : function() {

		jQuery( 'body' ).delegate( '.coursefilter-orderby:not(.dtlms-without-ajax-load), .coursefilter-category:not(.dtlms-without-ajax-load), .coursefilter-instructor:not(.dtlms-without-ajax-load), .coursefilter-cost:not(.dtlms-without-ajax-load), .coursefilter-date:not(.dtlms-without-ajax-load)', 'change', function() {  

			var this_item = jQuery(this).parents('.dtlms-courses-listing-holder');
			var offset = 0;
			var current_page = 1;

			// list course ajax call
			dtlmsListCourses(offset, current_page, this_item);

		});

		jQuery('.dtlms-courses-search-text:not(.dtlms-without-ajax-load)').on('input', function() {

			var this_item = jQuery(this).parents('.dtlms-courses-listing-holder');
			var offset = 0;
			var current_page = 1;

			// list course ajax call
			setTimeout( function() {
				dtlmsListCourses(offset, current_page, this_item);
			}, 1600);			

		});

		jQuery( 'body' ).delegate( '.dtlms-courses-display-type:not(.dtlms-without-ajax-load)', 'click', function(e) {  

			jQuery('.dtlms-courses-display-type').removeClass('active');
			jQuery(this).addClass('active');
			var list_type = jQuery(this).attr('data-displaytype');
			jQuery(this).parents('.dtlms-courses-listing-holder').find('.dtlms-courses-listing-containers').removeClass('list grid');
			jQuery(this).parents('.dtlms-courses-listing-holder').find('.dtlms-courses-listing-containers').addClass(list_type);
			jQuery(this).trigger('change');

			var this_item = jQuery(this).parents('.dtlms-courses-listing-holder');
			var offset = 0;
			var current_page = 1;

			// list course ajax call
			dtlmsListCourses(offset, current_page, this_item);

			e.preventDefault();

		});

		jQuery( 'body' ).delegate( '.dtlms-courses-listing-containers .dtlms-pagination a', 'click', function(e){	
			
			var this_item = jQuery(this).parents('.dtlms-courses-listing-holder');

			if(jQuery(this).parent().hasClass('prev-post')) {
				current_page = parseInt(jQuery(this).attr('data-currentpage'), 10)-1;
			} else if(jQuery(this).parent().hasClass('next-post')) {
				current_page = parseInt(jQuery(this).attr('data-currentpage'), 10)+1;
			} else {
				current_page = jQuery(this).text();
			}

			var post_per_page = jQuery(this).parents('.dtlms-courses-listing-holder').attr('data-postperpage');

			if(current_page == 1) { 
				var offset = 0; 
			} else if(current_page > 1) { 
				var offset = ((current_page-1)*post_per_page); 
			}

			// list course ajax call
			dtlmsListCourses(offset, current_page, this_item);

			e.preventDefault();
							
		});	

		function dtlmsListCourses(offset, current_page, this_item) {
		
			// gathering all default values
			var search_text = this_item.find('.dtlms-courses-search-text').val();

			var display_type = this_item.find('.dtlms-courses-display-type.active').attr('data-displaytype');

			var order_by = this_item.find('.coursefilter-orderby').val();

			var category = this_item.find('.coursefilter-category:checked').map(function(){
				return this.value;
			}).get();

			var instructor = this_item.find('.coursefilter-instructor:checked').map(function(){
				return this.value;
			}).get();

			var cost_type = this_item.find('.coursefilter-cost:checked').map(function(){
				return this.value;
			}).get();

			var start_date = this_item.find('.coursefilter-date').val();


			var post_per_page = this_item.attr('data-postperpage');
			var columns = this_item.attr('data-columns');
			var enable_carousel = this_item.attr('data-enablecarousel');
			var show_author_details = this_item.attr('data-showauthordetails');
			var disable_filters = this_item.attr('data-disablefilters');

			var apply_isotope = this_item.attr('data-applyisotope');
			var enable_category_isotope_filter = this_item.attr('data-enablecategoryisotopefilter');

			var default_filter = this_item.attr('data-defaultfilter');
			var default_display_type = this_item.attr('data-defaultdisplaytype');
			var course_item_ids = this_item.attr('data-courseitemids');
			var course_category_ids = this_item.attr('data-coursecategoryids');
			var instructor_ids = this_item.attr('data-instructorids');
			var enable_fullwidth = this_item.attr('data-enablefullwidth');
			var type = this_item.attr('data-type');
			var show_description = this_item.attr('data-showdescription');
			var hide_labels = this_item.attr('data-hidelabels');
			var hide_category = this_item.attr('data-hidecategory');
			var hide_addtocartbutton = this_item.attr('data-hideaddtocartbutton');
			var dclass = this_item.attr('data-class');

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_generate_courses_listing',
					display_type: display_type,
					order_by: order_by,
					category: category,
					instructor: instructor,
					cost_type: cost_type,
					start_date: start_date,
					post_per_page: post_per_page,
					columns: columns,
					offset: offset,
					current_page: current_page,
					search_text: search_text,
					enable_carousel: enable_carousel,
					show_author_details: show_author_details,
					disable_filters: disable_filters,
					apply_isotope: apply_isotope,
					enable_category_isotope_filter: enable_category_isotope_filter,
					default_filter: default_filter,
					default_display_type: default_display_type,
					course_item_ids: course_item_ids,
					course_category_ids: course_category_ids,
					instructor_ids: instructor_ids,
					enable_fullwidth: enable_fullwidth,
					type: type,
					show_description: show_description,
					hide_labels: hide_labels,
					hide_category: hide_category,
					hide_addtocartbutton: hide_addtocartbutton,
					class: dclass,
				},
				beforeSend: function(){
					dtLMSFrontendUtils.dtLMSAjaxBeforeSend(this_item);				
				},
				error: function (xhr, status, error) {
					this_item.find('.dtlms-courses-listing-containers').html('Something went wrong!');
				},
				success: function (response) {
					this_item.find('.dtlms-courses-listing-containers').html(response);
					if(enable_carousel == 'true') {
						dtlmsCoursesSwiper(this_item);
					} else {
						dtLMSFrontendUtils.dtLMSCoursesListingIsotope();	
						if(this_item.find('.dtlms-courselist-item-wrapper').hasClass('list-item type6')) { // Isotope fix
							this_item.find('.dtlms-courses-listing-isotope-filter a.active-sort').trigger('click');
						}
						setTimeout( function() {
							jQuery(window).trigger('resize');	
						}, 3000);											
					}
				},
				complete: function(){
					dtLMSFrontendUtils.dtLMSAjaxAfterSend(this_item);
				} 
			});
		
		}

		jQuery('.dtlms-courses-listing-holder:not(.dtlms-without-ajax-load)').each(function() {
			if(jQuery(this).length) {
				dtlmsListCourses(0, 1, jQuery(this));
			}
		});


		
		// Courses listing carousel

		function dtlmsCoursesSwiper(this_item) {

			var swiperGallery = [];
			var swiperGalleryOptions = [];
			var swiperIterator = 1;
			this_item.find('.dtlms-courses-swiper-listing').each(function() {

				var $swiperItem = jQuery(this);
				var swiperUniqueId = 'swiperuniqueid-'+swiperIterator;
				swiperGalleryOptions[swiperUniqueId] = [];
				$swiperItem.attr('id', swiperUniqueId);

				// Get swiper options
				var effect = $swiperItem.attr('data-carouseleffect');
				var autoplay = parseInt($swiperItem.attr('data-carouselautoplay'), 10);
				if(autoplay > 0) {
					swiperGalleryOptions[swiperUniqueId]['autoplay'] = autoplay;
				} else {
					swiperGalleryOptions[swiperUniqueId]['autoplay'] = 0;
				}

				var slidesperview = parseInt($swiperItem.attr('data-carouselslidesperview'), 10);
				var loopmode = ($swiperItem.attr('data-carouselloopmode') == 'true') ? true : false;		
				var mousewheelcontrol = ($swiperItem.attr('data-carouselmousewheelcontrol') == 'true') ? true : false;

				var pagination_type = pagination_class = '';
				var bulletpagination = ($swiperItem.attr('data-carouselbulletpagination') == 'true') ? true : false;
				if(bulletpagination) {
					var pagination_class = '.dtlms-swiper-bullet-pagination';
					var pagination_type = 'bullets';
				}

				var spacebetween = parseInt($swiperItem.attr('data-carouselspacebetween'), 10);
				if(spacebetween == '') {
					spacebetween = 0;
				}

				if(slidesperview == 1) {
					var breakpoint_slides_1 = breakpoint_slides_2 = breakpoint_slides_3 = breakpoint_slides_4 = 1;
				} else if(slidesperview == 2) {
					var breakpoint_slides_1 = 2; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
				} else if(slidesperview == 3) {
					var breakpoint_slides_1 = 3; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
				} else if(slidesperview >= 4) {
					var breakpoint_slides_1 = 4; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
				}							

				// Generate swiper
			    swiperGallery[swiperUniqueId] = new Swiper($swiperItem, {
	     			
	                simulateTouch: true,
	                roundLengths: true,
	                keyboardControl: true,
	                paginationClickable: true,
	                autoHeight: true,

	                spaceBetween: spacebetween,
	                autoplay: autoplay,
	                slidesPerView: slidesperview,
	                loop:loopmode,
	                mousewheelControl: mousewheelcontrol,
	                direction: 'horizontal',                

					pagination: {
						el: pagination_class,
						type: pagination_type,
						clickable: true
					},

	                effect: effect,
					coverflow: {
					  rotate: 0,
					  stretch: 10,
					  depth: 200,
					  modifier: 1,
					},
			        cube: {
			            shadow: true,
			            slideShadows: true,
			            shadowOffset: 20,
			            shadowScale: 0.94
			        },
			      
			        breakpoints: {
			            1199: {
			                slidesPerView: breakpoint_slides_1,
			            },
			            991: {
			                slidesPerView: breakpoint_slides_2,
			            },
			            767: {
			                slidesPerView: breakpoint_slides_3,
			            },
			            320: {
			                slidesPerView: breakpoint_slides_4,
			            }
			        },  

			    });
		    	
				swiperGallery[swiperUniqueId].on('transitionEnd', function () {
			        if(swiperGalleryOptions[swiperUniqueId]['autoplay'] > 0) {
			        	swiperGallery[swiperUniqueId].startAutoplay();
			        }
				});		    	

		    	var arrowpagination = ($swiperItem.attr('data-carouselarrowpagination') == 'true') ? true : false;

		    	if(arrowpagination) {

				    $swiperItem.find('.dtlms-swiper-arrow-pagination .dtlms-swiper-arrow-prev').on('click', function(e) {			    	
				    	var swiperUniqueId = $swiperItem.attr('id');
				        swiperGallery[swiperUniqueId].slidePrev();
				        if(swiperGalleryOptions[swiperUniqueId]['autoplay'] > 0) {
				        	swiperGallery[swiperUniqueId].startAutoplay();
				        }
				        e.preventDefault();		
				    });

				     $swiperItem.find('.dtlms-swiper-arrow-pagination .dtlms-swiper-arrow-next').on('click', function(e) {
				    	var swiperUniqueId = $swiperItem.attr('id');
				        swiperGallery[swiperUniqueId].slideNext();
				        if(swiperGalleryOptions[swiperUniqueId]['autoplay'] > 0 ) {
				        	swiperGallery[swiperUniqueId].startAutoplay();
				        }
				        e.preventDefault();	
				    });	

				}

			    swiperIterator++;

			});	

		}	

	},

	dtLessons : function() {

		jQuery('body').delegate('.dtlms-lesson-complete-button', 'click', function(e){ 

			var this_item = jQuery(this),
				complete_nonce = this_item.attr('data-complete-nonce'),
				course_id = this_item.attr('data-courseid'),
				lesson_id = this_item.attr('data-lessonid'),
				user_id = this_item.attr('data-userid'),
				author_id = this_item.attr('data-authorid'),
				parent_curriculum_id = this_item.attr('data-parentcurriculumid'),
				next_curriculum_id = this_item.attr('data-nextcurriculumid'),
				enable_next_curriculum = this_item.attr('data-enablenextcurriculum');

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_complete_the_lesson',
					complete_nonce: complete_nonce,
					course_id: course_id,
					lesson_id: lesson_id,
					user_id: user_id,
					author_id: author_id,
					parent_curriculum_id: parent_curriculum_id,
					next_curriculum_id: next_curriculum_id,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {

					jQuery('.dtlms-completed').removeClass('hidden');
					this_item.addClass('hidden');
					if(parent_curriculum_id > 0) {
						jQuery('ul.dtlms-curriculum-list li a[data-curriculumid="' + lesson_id + '"][ data-parentcurriculumid="' + parent_curriculum_id + '"]').append('<span class="dtlms-completed"><span class="fa fa-check"></span></span>');						
					} else {
						jQuery('ul.dtlms-curriculum-list li a[data-curriculumid="' + lesson_id + '"][ data-parentcurriculumid="-1"]').append('<span class="dtlms-completed"><span class="fa fa-check"></span></span>');						
					}

					// Enable next curriculum item
					if(enable_next_curriculum == 'true') {
						if(next_curriculum_id > 0) {
							jQuery('ul.dtlms-curriculum-list li a[data-curriculumid="' + next_curriculum_id + '"]').parents('li').removeClass('locked');
						}
					}

				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});	

			e.preventDefault();

		}); 

	},

	dtQuizzes : function() {

		function start_timer() {

			var $quiztime = parseInt(jQuery('.dtlms-quiz-timer').attr('data-time'));
			var $quiztimer = jQuery('.dtlms-quiz-timer').find('.dtlms-timer');
			var $this = jQuery('.dtlms-quiz-timer');
			
			var $timercolors = {};
			$fgcolor = lmsfrontendobject.quizTimerForegroundColor;
			$bgcolor = lmsfrontendobject.quizTimerBackgroundColor;
		
			$quiztimer.timer({
				'timer': $quiztime,
				'width' : 160 ,
				'height' : 160 ,
				'fgColor' : $fgcolor,
				'bgColor' : $bgcolor
			});

			var prevval = '';
			
			$quiztimer.on('change',function(){
				var $countdown= $this.find('.dtlms-countdown');
				var val = parseInt( $quiztimer.attr('data-timer'));
				
				if(val > 0){
					val--;
					$quiztimer.attr('data-timer',val);
					var $text='';
					if(val > 60){
						$text = Math.floor(val/60) + ':' + ((parseInt(val%60) < 10)?'0'+parseInt(val%60):parseInt(val%60)) + '';
					}else{
						$text = '00:'+ ((val < 10)?'0'+val:val);
					}
					$countdown.html($text);
				} else{
					$countdown.html(lmsfrontendobject.quizTimeout);
					jQuery('#dtlms-complete-quiz').trigger('click');
					$quiztimer.off();
				}  
				
			});
		}

		jQuery( 'body' ).delegate( "#dtlms-start-quiz", 'click', function(){  
		
			var this_item = jQuery(this),
				startquiz_nonce = this_item.attr('data-startquiz-nonce'),
				course_id = this_item.attr('data-courseid'),
				user_id = this_item.attr('data-userid'),
				lesson_id = this_item.attr('data-lessonid'),
				quiz_id = this_item.attr('data-quizid'),
				assignment_id = this_item.attr('data-assignmentid'),			
				author_id = this_item.attr('data-authorid'),
				parent_curriculum_id = this_item.attr('data-parentcurriculumid');
			
			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_start_quiz',
					startquiz_nonce: startquiz_nonce,
					course_id: course_id,
					user_id: user_id,
					lesson_id: lesson_id,
					quiz_id: quiz_id,
					assignment_id: assignment_id,
					author_id: author_id,
					parent_curriculum_id: parent_curriculum_id,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				error: function (xhr, status, error) {
					jQuery('.dtlms-curriculum-content-holder').html('Something went wrong!');
				},
				success: function (response) {

					jQuery('.dtlms-curriculum-content-holder').html(response);
					jQuery(window).on('beforeunload', function(){
						return lmsfrontendobject.onRefresh;
					});
							
					jQuery(window).on('unload',function(){
						jQuery('#dtlms-complete-quiz').trigger('click');
					});
					start_timer();

					jQuery('#dtlms-course-curriculum-popup').addClass('dtlms-curriculum-quiz-lock');

					jQuery('.dtlms-quiz-questions ul li img').on('click',function(e){

						if(!jQuery(this).parents('ul').hasClass('disabled')) {
							jQuery(this).parents('.dtlms-quiz-questions').find('li').removeClass('selected');
							jQuery(this).parents('.dtlms-quiz-questions').find('.multichoice-image').removeAttr('checked');
							jQuery(this).parents('li').addClass('selected');
							jQuery(this).next('input').prop('checked', true);
						}
						e.preventDefault();

					});

					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumContentScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumDetailsScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumResponsiveScroll();
				},
				complete: function(){
					this_item.find('span').remove();
				}
			});
			
		});

		jQuery( 'body' ).delegate( "#dtlms-submit-question", 'click', function(){  

			var this_item = jQuery(this),
				course_id = this_item.attr('data-courseid'),
				user_id = this_item.attr('data-userid'),
				lesson_id = this_item.attr('data-lessonid'),
				quiz_id = this_item.attr('data-quizid'),
				assignment_id = this_item.attr('data-assignmentid'),
				author_id = this_item.attr('data-authorid'),

				current_question_num = jQuery('#dtlms-current-question-number').val(),
				current_question_id = jQuery('.dtlms-question:visible').find('#dtlms-current-question-id').val(),
				total_questions = jQuery('#dtlms-total-questions').val();

	        var form = jQuery('.formQuiz')[0];
	        var data = new FormData(form);
	        data.append('action', 'dtlms_show_answers_with_explanation');
	        //data.append('submitassignment_nonce', submitassignment_nonce);
	        data.append('course_id', course_id);
	        data.append('user_id', user_id);
	        data.append('lesson_id', lesson_id);
	        data.append('quiz_id', quiz_id);
	        data.append('assignment_id', assignment_id);
	        data.append('author_id', author_id);
	        data.append('question_id', current_question_id);

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data: data,
	            processData: false,
	            contentType: false,	
	            cache: false,
				beforeSend: function(){
					dtLMSFrontendUtils.dtLMSAjaxBeforeSend(undefined);
				},
				error: function (xhr, status, error) {
					jQuery('.dtlms-curriculum-content-holder').html('Something went wrong!');
				},
				success: function (response) {

					if(jQuery.trim(response) == 'passed') {

						if(parseInt(current_question_num, 10) == parseInt(total_questions, 10)) {
							jQuery('#dtlms-complete-quiz').trigger('click');
							return;	
						}
						
						jQuery('.dtlms-question-'+current_question_num).slideUp();
						next_question_num = parseInt(current_question_num, 10) + 1;
						jQuery('#dtlms-current-question-number').val(next_question_num);
						
						if(parseInt(next_question_num, 10) <= parseInt(total_questions, 10)) {
							jQuery('.dtlms-question-'+next_question_num).slideDown();
						}
						if(parseInt(next_question_num, 10) >= parseInt(total_questions, 10)) {
							jQuery('#dtlms-submit-question').removeClass('hidden');
							jQuery('#dtlms-next-question').addClass('hidden');
						}
						
						jQuery('.dtlms-question-counter-container .dtlms-current-question').html(next_question_num);
						
					} else {

						jQuery('.dtlms-question-'+current_question_num).find('input,textarea').attr('readonly', 'readonly').next('label').css( 'pointer-events', 'none' ).prev('img').css( 'pointer-events', 'none' );
						jQuery('.dtlms-question-'+current_question_num).find('input,textarea').prev('img').css( 'pointer-events', 'none' );
						
						jQuery('#dtlms-answer-holder').html(response);
						
						if(parseInt(current_question_num, 10) >= parseInt(total_questions, 10)) {
							jQuery('#dtlms-submit-question').addClass('hidden');
							jQuery('#dtlms-next-question').addClass('hidden');
							jQuery('#dtlms-complete-quiz').removeClass('hidden');
						} else {
							jQuery('#dtlms-submit-question').addClass('hidden');
							jQuery('#dtlms-next-question').removeClass('hidden');
						}
						
					}

					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumContentScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumDetailsScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumResponsiveScroll();
					
				},
				complete: function() {
					dtLMSFrontendUtils.dtLMSAjaxAfterSend(undefined);
				} 
			});
		
		});	

		jQuery( 'body' ).delegate( "#dtlms-next-question", 'click', function(){  

			var this_item = jQuery(this),
				course_id = this_item.attr('data-courseid'),
				quiz_id = this_item.attr('data-quizid'),
				user_id = this_item.attr('data-userid'),
				author_id = this_item.attr('data-authorid'),

				current_question_num = jQuery('#dtlms-current-question-number').val(),
				current_question_id = jQuery('#dtlms-current-question-id').val(),
				total_questions = jQuery('#dtlms-total-questions').val(),
				correctanswer_and_answerexplanation = jQuery('#dtlms-correctanswer-and-answerexplanation').val();

			jQuery('.dtlms-question-'+current_question_num).slideUp();
			next_question_num = parseInt(current_question_num, 10) + 1;
			jQuery('#dtlms-current-question-number').val(next_question_num);
			if(parseInt(next_question_num, 10) <= parseInt(total_questions, 10)) {
				jQuery('.dtlms-question-'+next_question_num).slideDown();
			}
			
			if(correctanswer_and_answerexplanation == 'true') {
				jQuery('#dtlms-submit-question').removeClass('hidden');
				jQuery('#dtlms-next-question').addClass('hidden');
			} else if(parseInt(next_question_num, 10) >= parseInt(total_questions, 10)) {
				jQuery('#dtlms-next-question').addClass('hidden');
				jQuery('#dtlms-complete-quiz').removeClass('hidden');
			}
			
			jQuery.scrollTo('.dtlms-questions-list', 800, { offset: { top: -250 }});
			
			jQuery('#dtlms-answer-holder').html('');
			
			jQuery('.dtlms-question-counter-container .dtlms-current-question').html(next_question_num);

		});	

		jQuery( 'body' ).delegate( "#dtlms-complete-quiz", 'click', function(){  

			jQuery( window ).off( "beforeunload" );
			jQuery( window ).off( "unload" );

			var this_item = jQuery(this),
				course_id = this_item.attr('data-courseid'),
				user_id = this_item.attr('data-userid'),
				lesson_id = this_item.attr('data-lessonid'),
				quiz_id = this_item.attr('data-quizid'),
				assignment_id = this_item.attr('data-assignmentid'),
				author_id = this_item.attr('data-authorid'),
				markasgraded = this_item.attr('data-markasgraded'),
				parent_curriculum_id = this_item.attr('data-parentcurriculumid'),
				timings = jQuery('.dtlms-quiz-timer .dtlms-timer').attr('data-timer'),
				next_curriculum_id = this_item.attr('data-nextcurriculumid'),
				enable_next_curriculum = this_item.attr('data-enablenextcurriculum');

	        var form = jQuery('.formQuiz')[0];
	        var data = new FormData(form);
	        data.append('action', 'dtlms_ajax_validate_quiz');
	        //data.append('submitassignment_nonce', submitassignment_nonce);
	        data.append('course_id', course_id);
	        data.append('user_id', user_id);
	        data.append('lesson_id', lesson_id);
	        data.append('quiz_id', quiz_id);
	        data.append('assignment_id', assignment_id);
	        data.append('author_id', author_id);
	        data.append('parent_curriculum_id', parent_curriculum_id);
	        data.append('next_curriculum_id', next_curriculum_id);
	        data.append('timings', timings);

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data: data,
	            processData: false,
	            contentType: false,	
	            cache: false,
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				error: function (xhr, status, error) {
					jQuery('.dtlms-curriculum-content-holder').html('Something went wrong!');
				},
				success: function (response) {

					jQuery('.dtlms-curriculum-content-holder').html(response);

					if(markasgraded == 'true') {
						if(parent_curriculum_id > 0) {
							jQuery('ul.dtlms-curriculum-list li a[data-curriculumid="' + quiz_id + '"][ data-parentcurriculumid="' + parent_curriculum_id + '"]').append('<span class="dtlms-completed"><span class="fa fa-check"></span></span>');						
						} else {
							jQuery('ul.dtlms-curriculum-list li a[data-curriculumid="' + quiz_id + '"][ data-parentcurriculumid="-1"]').append('<span class="dtlms-completed"><span class="fa fa-check"></span></span>');						
						}
					}
					//jQuery.scrollTo('#primary', 800, { offset: { top: -150 }});
					jQuery('#dtlms-course-curriculum-popup').removeClass('dtlms-curriculum-quiz-lock');

					// Enable next curriculum item
					if(enable_next_curriculum == 'true') {
						if(next_curriculum_id > 0) {
							if(parent_curriculum_id > 0) {
								jQuery('ul.dtlms-curriculum-list li a[data-curriculumid="' + next_curriculum_id + '"][ data-parentcurriculumid="' + parent_curriculum_id + '"]').parents('li').removeClass('locked');
							} else {
								jQuery('ul.dtlms-curriculum-list li a[data-curriculumid="' + next_curriculum_id + '"][ data-parentcurriculumid="-1"]').parents('li').removeClass('locked');
							}
						}
					}

					dtLMSFrontendUtils.dtLMSProgressBar(false);

				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});

		});

		jQuery('body').delegate('.dtlms-view-quiz-result', 'click', function(e){ 

			var this_item = jQuery(this),
				quiz_id = this_item.attr('data-quizid'),
				grade_id = this_item.attr('data-gradeid');

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_view_quiz_results',
					quiz_id: quiz_id,
					grade_id: grade_id,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					jQuery('.dtlms-curriculum-content-holder').html(response);
					dtLMSFrontendUtils.dtLMSInitializeDonutChart();
					dtLMSFrontendUtils.dtLMSProgressBar(false);
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumContentScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumDetailsScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumResponsiveScroll();					
				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});	

			e.preventDefault();

		});

	},

	dtAssignments : function() {

		jQuery( 'body' ).delegate( "#dtlms-upload-assignment", 'click', function(){  
		
			var this_item = jQuery(this),
				uploadassignment_nonce = this_item.attr('data-uploadassignment-nonce'),
				course_id = this_item.attr('data-courseid'),
				user_id = this_item.attr('data-userid'),
				lesson_id = this_item.attr('data-lessonid'),
				quiz_id = this_item.attr('data-quizid'),
				assignment_id = this_item.attr('data-assignmentid'),			
				author_id = this_item.attr('data-authorid'),
				assignment_grade_id = this_item.attr('data-assignmentgradeid'),
				parent_curriculum_id = this_item.attr('data-parentcurriculumid');
			
			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_upload_assignment',
					uploadassignment_nonce: uploadassignment_nonce,
					course_id: course_id,
					user_id: user_id,
					lesson_id: lesson_id,
					quiz_id: quiz_id,
					assignment_id: assignment_id,
					author_id: author_id,
					assignment_grade_id: assignment_grade_id,
					parent_curriculum_id: parent_curriculum_id,
				},
				beforeSend: function(){
					dtLMSFrontendUtils.dtLMSAjaxBeforeSend(undefined);
				},
				error: function (xhr, status, error) {
					jQuery('.dtlms-curriculum-content-holder').html('Something went wrong!');
					jQuery('.dtlms-upload-assignment-holder').sortable({ placeholder: 'sortable-placeholder' });
				},
				success: function (response) {
					jQuery('.dtlms-curriculum-content-holder').html(response);
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumContentScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumDetailsScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumResponsiveScroll();
				},
				complete: function(){
					dtLMSFrontendUtils.dtLMSAjaxAfterSend(undefined);
				} 
			});
			
		});

		jQuery( 'body' ).delegate( ".dtlms-add-upload-assignment-field", 'click', function(){  
		
			var clone = jQuery("#dtlms-upload-assignment-section-to-clone").clone();
			clone.attr('class', 'dtlms-upload-assignment').removeAttr('id').removeClass('hidden');
			clone.find('input').attr('class', 'assignment-attachment').attr('name', 'assignment-attachment[]');	
			clone.appendTo('.dtlms-upload-assignment-holder');

			jQuery('.dtlms-upload-assignment-holder').sortable({ placeholder: 'sortable-placeholder' });

			dtLMSFrontendUtils.dtLMSEnableCourseCurriculumContentScroll();
			dtLMSFrontendUtils.dtLMSEnableCourseCurriculumDetailsScroll();			
			dtLMSFrontendUtils.dtLMSEnableCourseCurriculumResponsiveScroll();

		});		

		jQuery('body').delegate('span.dtlms-remove-upload-assignment-field','click', function(e){	
		
			jQuery(this).parents('.dtlms-upload-assignment').remove();
			e.preventDefault();
			
		});		

		if(jQuery('.dtlms-upload-assignment-holder').length) {
			jQuery('.dtlms-upload-assignment-holder').sortable({ placeholder: 'sortable-placeholder' });
		}


		jQuery( 'body' ).delegate( ".dtlms-submit-assignment", 'click', function(){  
		
			var assignment_attachment = jQuery('input[name^=assignment-attachment]').map(function(idx, elem) {
				if(this.value != '') {
					return this.value;
				}
			}).get();

			if((jQuery('.assignment-textarea').length && jQuery('.assignment-textarea').val() == '') || (jQuery('.assignment-attachment').length && assignment_attachment.length === 0)) {
				alert(lmsfrontendobject.assignmentNotification);
				return;
			}

			var this_item = jQuery(this),
				submitassignment_nonce = this_item.attr('data-submitassignment-nonce'),
				course_id = this_item.attr('data-courseid'),
				user_id = this_item.attr('data-userid'),
				lesson_id = this_item.attr('data-lessonid'),
				quiz_id = this_item.attr('data-quizid'),
				assignment_id = this_item.attr('data-assignmentid'),			
				author_id = this_item.attr('data-authorid'),
				parent_curriculum_id = this_item.attr('data-parentcurriculumid'),
				next_curriculum_id = this_item.attr('data-nextcurriculumid'),
				enable_next_curriculum = this_item.attr('data-enablenextcurriculum');
			
	        var form = jQuery('.formAssignment')[0];
	        var data = new FormData(form);
	        data.append('action', 'dtlms_submit_assignment');
	        data.append('submitassignment_nonce', submitassignment_nonce);
	        data.append('course_id', course_id);
	        data.append('user_id', user_id);
	        data.append('lesson_id', lesson_id);
	        data.append('quiz_id', quiz_id);
	        data.append('assignment_id', assignment_id);
	        data.append('author_id', author_id);
	        data.append('parent_curriculum_id', parent_curriculum_id);
	        data.append('next_curriculum_id', next_curriculum_id);

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				enctype: 'multipart/form-data',
				data: data,
	            processData: false,
	            contentType: false,	
	            cache: false,		
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					if(response == 'error') {
						jQuery('body').find('.dtlms-assignment-errors').removeClass('hidden');
					} else {
						jQuery('body').find('#dtlms-course-curriculum-popup').remove();
						jQuery('body').append(response);
					}

					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumContentScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumDetailsScroll();
					dtLMSFrontendUtils.dtLMSEnableCourseCurriculumResponsiveScroll();

					// Enable next curriculum item
					if(enable_next_curriculum == 'true') {
						if(next_curriculum_id > 0) {
							if(parent_curriculum_id > 0) {
								jQuery('ul.dtlms-curriculum-list li a[data-curriculumid="' + next_curriculum_id + '"][ data-parentcurriculumid="' + parent_curriculum_id + '"]').parents('li').removeClass('locked');
							} else {
								jQuery('ul.dtlms-curriculum-list li a[data-curriculumid="' + next_curriculum_id + '"][ data-parentcurriculumid="-1"]').parents('li').removeClass('locked');
							}
						}	
					}	

				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});
			
		});

		jQuery( 'body' ).delegate( '.dtlms-view-assignment', 'click', function(){  
		
			var this_item = jQuery(this),
				assignment_grade_id = this_item.attr('data-assignmentgradeid'),
				assignment_id = this_item.attr('data-assignmentid');
			
			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,		
				data:
				{
					action: 'dtlms_view_assignment',
					assignment_grade_id: assignment_grade_id,
					assignment_id: assignment_id,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					jQuery('.dtlms-curriculum-content-holder').html(response);
				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});
			
		});	

	},	

	dtPackagesListing : function() {

		jQuery( 'body' ).delegate( '.dtlms-packages-listing-containers .dtlms-pagination a', 'click', function(e){	
			
			var this_item = jQuery(this).parents('.dtlms-packages-listing-holder');

			if(jQuery(this).parent().hasClass('prev-post')) {
				current_page = parseInt(jQuery(this).attr('data-currentpage'), 10)-1;
			} else if(jQuery(this).parent().hasClass('next-post')) {
				current_page = parseInt(jQuery(this).attr('data-currentpage'), 10)+1;
			} else {
				current_page = jQuery(this).text();
			}

			var post_per_page = jQuery(this).parents('.dtlms-packages-listing-holder').attr('data-postperpage');

			if(current_page == 1) { 
				var offset = 0; 
			} else if(current_page > 1) { 
				var offset = ((current_page-1)*post_per_page); 
			}

			// list package ajax call
			dtlmsListPackages(offset, current_page, this_item);

			e.preventDefault();
							
		});	


		function dtlmsListPackages(offset, current_page, this_item) {
		
			var post_per_page = this_item.attr('data-postperpage');
			var columns = this_item.attr('data-columns');
			var apply_isotope = this_item.attr('data-applyisotope');
			var display_type = this_item.attr('data-displaytype');
			var type = this_item.attr('data-type');
			var package_item_ids = this_item.attr('data-packageitemids');

			var enable_carousel = this_item.attr('data-enablecarousel');		

			jQuery.ajax({
				type: "POST",
				url: lmsfrontendobject.ajaxurl,
				data:
				{
					action: 'dtlms_generate_packages_listing',
					offset: offset,
					current_page: current_page,
					post_per_page: post_per_page,
					columns: columns,
					apply_isotope: apply_isotope,
					display_type: display_type,
					type: type,
					package_item_ids: package_item_ids,
					enable_carousel: enable_carousel,
				},
				beforeSend: function(){
					dtLMSFrontendUtils.dtLMSAjaxBeforeSend(this_item);
				},
				error: function (xhr, status, error) {
					this_item.find('.dtlms-packages-listing-containers').html('Something went wrong!');
				},
				success: function (response) {
					this_item.find('.dtlms-packages-listing-containers').html(response);
					if(enable_carousel == 'true') {
						dtlmsPackagesSwiper(this_item);
					} else {
						dtLMSFrontendUtils.dtLMSPackagesListingIsotope();
						setTimeout( function() {
							jQuery('.dtlms-apply-isotope .dtlms-packages-listing-items').isotope('layout');
						}, 600);						
					}
				},
				complete: function(){
					dtLMSFrontendUtils.dtLMSAjaxAfterSend(this_item);
				} 
			});
		
		}

		jQuery('.dtlms-packages-listing-holder').each(function() {
			if(jQuery(this).length) {
				dtlmsListPackages(0, 1, jQuery(this));
			}
		});
		
		// Packages listing carousel
		function dtlmsPackagesSwiper(this_item) {

			var swiperGallery = [];
			var swiperGalleryOptions = [];
			var swiperIterator = 1;
			this_item.find('.dtlms-packages-swiper-listing').each(function() {

				var $swiperItem = jQuery(this);
				var swiperUniqueId = 'swiperuniqueid-'+swiperIterator;
				swiperGalleryOptions[swiperUniqueId] = [];
				$swiperItem.attr('id', swiperUniqueId);

				// Get swiper options
				var effect = $swiperItem.attr('data-carouseleffect');
				var autoplay = parseInt($swiperItem.attr('data-carouselautoplay'), 10);
				if(autoplay > 0) {
					swiperGalleryOptions[swiperUniqueId]['autoplay'] = autoplay;
				} else {
					swiperGalleryOptions[swiperUniqueId]['autoplay'] = 0;
				}

				var slidesperview = parseInt($swiperItem.attr('data-carouselslidesperview'), 10);
				var loopmode = ($swiperItem.attr('data-carouselloopmode') == 'true') ? true : false;
				var mousewheelcontrol = ($swiperItem.attr('data-carouselmousewheelcontrol') == 'true') ? true : false;

				var pagination_type = pagination_class = '';
				var bulletpagination = ($swiperItem.attr('data-carouselbulletpagination') == 'true') ? true : false;
				if(bulletpagination) {
					var pagination_class = '.dtlms-swiper-bullet-pagination';
					var pagination_type = 'bullets';
				}

				var spacebetween = parseInt($swiperItem.attr('data-carouselspacebetween'), 10);
				if(spacebetween == '') {
					spacebetween = 0;
				}

				if(slidesperview == 1) {
					var breakpoint_slides_1 = breakpoint_slides_2 = breakpoint_slides_3 = breakpoint_slides_4 = 1;
				} else if(slidesperview == 2) {
					var breakpoint_slides_1 = 2; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
				} else if(slidesperview == 3) {
					var breakpoint_slides_1 = 3; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
				} else if(slidesperview >= 4) {
					var breakpoint_slides_1 = 4; var breakpoint_slides_2 = 2; var breakpoint_slides_3 = 1; var breakpoint_slides_4 = 1;
				}	

				// Generate swiper
			    swiperGallery[swiperUniqueId] = new Swiper($swiperItem, {
	     			
	                simulateTouch: true,
	                roundLengths: true,
	                keyboardControl: true,
	                paginationClickable: true,
	                autoHeight: true,

	                spaceBetween: spacebetween,
	                autoplay: autoplay,
	                slidesPerView: slidesperview,
	                loop:loopmode,
	                mousewheelControl: mousewheelcontrol,
	                direction: 'horizontal',

					pagination: {
						el: pagination_class,
						type: pagination_type,
						clickable: true
					},                 

	                effect: effect,
					coverflow: {
					  rotate: 0,
					  stretch: 10,
					  depth: 200,
					  modifier: 1,
					},
			        cube: {
			            shadow: true,
			            slideShadows: true,
			            shadowOffset: 20,
			            shadowScale: 0.94
			        },
			      
			        breakpoints: {
			            1199: {
			                slidesPerView: breakpoint_slides_1,
			            },
			            991: {
			                slidesPerView: breakpoint_slides_2,
			            },
			            767: {
			                slidesPerView: breakpoint_slides_3,
			            },
			            320: {
			                slidesPerView: breakpoint_slides_4,
			            }
			        },             

			    });

				swiperGallery[swiperUniqueId].on('transitionEnd', function () {
			        if(swiperGalleryOptions[swiperUniqueId]['autoplay'] > 0) {
			        	swiperGallery[swiperUniqueId].startAutoplay();
			        }
				});		    	

		    	var arrowpagination = ($swiperItem.attr('data-carouselarrowpagination') == 'true') ? true : false;

		    	if(arrowpagination) {

				    $swiperItem.find('.dtlms-swiper-arrow-pagination .dtlms-swiper-arrow-prev').on('click', function(e) {			    	
				    	var swiperUniqueId = $swiperItem.attr('id');
				        swiperGallery[swiperUniqueId].slidePrev();
				        if(swiperGalleryOptions[swiperUniqueId]['autoplay'] > 0) {
				        	swiperGallery[swiperUniqueId].startAutoplay();
				        }
				        e.preventDefault();		
				    });

				     $swiperItem.find('.dtlms-swiper-arrow-pagination .dtlms-swiper-arrow-next').on('click', function(e) {
				    	var swiperUniqueId = $swiperItem.attr('id');
				        swiperGallery[swiperUniqueId].slideNext();
				        if(swiperGalleryOptions[swiperUniqueId]['autoplay'] > 0 ) {
				        	swiperGallery[swiperUniqueId].startAutoplay();
				        }
				        e.preventDefault();	
				    });	

				}

			    swiperIterator++;

			});	

		}	

	},		

};

jQuery(document).ready(function() {

	dtLMSFrontend.dtInit();

	if(jQuery('.dtlms-onsite-map-container').length) {
		initClassLocationMap();
	}

});

function ClassLocationMap () {
	if(typeof initClassLocationMap === 'function') { initClassLocationMap(); }
}