var dtLMSCommonUtils = {

	dtLMSNiceScroll : function(itemclass) {

		var primarColor = lmsfrontendobject.primarColor;

	    jQuery(itemclass).niceScroll({
			cursorwidth:4,
			cursoropacitymin:0.4,
			cursorcolor:primarColor,
			cursorborder:'none',
			cursorborderradius:2,
			autohidemode:'leave'
	    });	

	},	

	dtLMSPrintContent :  function(data) 
	{

	    var printWindow = window.open('', lmsfrontendobject.printerTitle, 'height=600,width=1900');
	    printWindow.document.write('<html><head><title>'+lmsfrontendobject.printerTitle+'</title>');
	    printWindow.document.write('<link rel="stylesheet" href="'+lmsfrontendobject.pluginPath+'css/print.css" type="text/css" media="all" />');
	    printWindow.document.write('</head><body >');
	    printWindow.document.write(data);
	    printWindow.document.write('</body></html>');
	    printWindow.document.close();

	    setTimeout(function() { 
	    	dtLMSCommonUtils.checkReadyState(printWindow);
	    }, 1200);

	    return true;

	},

    checkReadyState :  function(printWindow) {

        if (printWindow.document.readyState == "complete") {
            printWindow.focus(); // necessary for IE >= 10
            printWindow.print();
            printWindow.close();
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

	dtLMSExpandCourseResultPopupDetails : function() {

		jQuery( 'body' ).delegate( '.dtlms-expand-course-result-main-details', 'click', function(e) { 

			if(!jQuery(this).hasClass('active')) {
				jQuery(this).addClass('active'); 
				jQuery(this).parents('#dtlms-course-result-popup').find('.dtlms-course-result-popup-container').addClass('active');
				jQuery(this).parents('#dtlms-course-result-popup').find('.dtlms-course-results-main-detail-wrapper').addClass('active');
			} else { 
				jQuery(this).removeClass('active');
				jQuery(this).parents('#dtlms-course-result-popup').find('.dtlms-course-result-popup-container').removeClass('active'); 
				jQuery(this).parents('#dtlms-course-result-popup').find('.dtlms-course-results-main-detail-wrapper').removeClass('active'); 
			}			
			
			e.preventDefault();
			
		});	

	},

	dtLMSExpandClassResultPopupDetails : function() {

		jQuery( 'body' ).delegate( '.dtlms-expand-class-result-main-details', 'click', function(e){  
		
			if(!jQuery(this).hasClass('active')) {
				jQuery(this).addClass('active'); 
				jQuery(this).parents('#dtlms-class-result-popup').find('.dtlms-class-result-popup-container').addClass('active');
				jQuery(this).parents('#dtlms-class-result-popup').find('.dtlms-class-results-main-detail-wrapper').addClass('active');
			} else { 
				jQuery(this).removeClass('active');
				jQuery(this).parents('#dtlms-class-result-popup').find('.dtlms-class-result-popup-container').removeClass('active'); 
				jQuery(this).parents('#dtlms-class-result-popup').find('.dtlms-class-results-main-detail-wrapper').removeClass('active'); 
			}
			
			e.preventDefault();
			
		});	

	},

	dtLMSAjaxPagination : function() {

		jQuery( 'body' ).delegate( '.dtlms-pagination a', 'click', function(e){	
			
			var this_item = jQuery(this);

			if(jQuery(this).parent().hasClass('prev-post')) {
				current_page = parseInt(jQuery(this).attr('data-currentpage'), 10)-1;
			} else if(jQuery(this).parent().hasClass('next-post')) {
				current_page = parseInt(jQuery(this).attr('data-currentpage'), 10)+1;
			} else {
				current_page = jQuery(this).text();
			}

			var post_per_page = jQuery(this).parents('.dtlms-pagination').attr('data-postperpage');

			if(current_page == 1) { 
				var offset = 0; 
			} else if(current_page > 1) { 
				var offset = ((current_page-1)*post_per_page); 
			}

			var function_call = jQuery(this).parents('.dtlms-pagination').attr('data-functioncall');
			var output_div = jQuery(this).parents('.dtlms-pagination').attr('data-outputdiv');

			var instructor_id = jQuery(this).parents('.dtlms-pagination').attr('data-instructorid');
			var course_id = jQuery(this).parents('.dtlms-pagination').attr('data-courseid');
			var class_id = jQuery(this).parents('.dtlms-pagination').attr('data-classid');
			var student_id = jQuery(this).parents('.dtlms-pagination').attr('data-studentid');
			var user_id = jQuery(this).parents('.dtlms-pagination').attr('data-userid');
			var commission_content = jQuery(this).parents('.dtlms-pagination').attr('data-commissioncontent');


			// ajax call
			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: function_call,
					ajax_call: true,
					current_page: current_page,
					offset: offset,
					post_per_page: post_per_page,
					function_call: function_call,
					output_div: output_div,
					instructor_id: instructor_id,
					course_id: course_id,
					class_id: class_id,
					student_id: student_id,
					user_id: user_id,
					commission_content: commission_content,
				},
				success: function (response) {
					this_item.parents('.'+output_div).html(response);
					dtLMSBackendUtils.dtLMSCheckboxSwitch();
				},
			});

			e.preventDefault();
							
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
			console.log(jQuery('#dtlms-ajax-load-image').attr('style'));
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

	dtLMSEnableResultPopupScroll : function() {

		if(jQuery('.dtlms-course-result-popup-container').length) {

			var currentWidth = window.innerWidth || document.documentElement.clientWidth;

			if(currentWidth > 1199) {

				jQuery('.dtlms-course-result-popup-container').height(5000);

				setTimeout(function() { 

					var windowHeight = jQuery(window.top).height();
					var height = parseInt((parseInt(windowHeight, 10)-parseInt(213, 10)), 10);

					jQuery('.dtlms-course-result-popup-container').height(height);

					dtLMSCommonUtils.dtLMSNiceScroll('.dtlms-course-result-popup-container');	

				}, 400);

			}		

		}

	},

	dtLMSEnableResultPopupResponsiveScroll : function() {

		if(jQuery('.dtlms-course-result-popup-container').length) {

			var currentWidth = window.innerWidth || document.documentElement.clientWidth;

			if(currentWidth <= 1199) {

				jQuery('.dtlms-course-result-popup-container').height(5000);

				setTimeout(function() { 

					var windowHeight = jQuery(window.top).height();
					var height = parseInt((parseInt(windowHeight, 10)-parseInt(165, 10)), 10);	

					jQuery('.dtlms-course-result-popup-container').height(height);

					dtLMSCommonUtils.dtLMSNiceScroll('.dtlms-course-result-popup-container');

				}, 1000);

			}

		}

	},	

	dtLMSEnableClassResultPopupScroll : function() {

		/*if(jQuery('#dtlms-class-result-popup').length) {

			jQuery('#dtlms-class-result-popup').height(5000);

			setTimeout(function() {

				var windowHeight = jQuery(window.top).height();
				jQuery('#dtlms-class-result-popup').height(windowHeight);

			}, 400);

		}*/

	},


};

var dtLMSCommon = {
	
	dtInit : function() {
		dtLMSCommon.dtDefault();
		dtLMSCommon.dtStatistics();
		dtLMSCommon.dtDashboard();
	},

	dtDefault : function() {

		dtLMSCommonUtils.dtLMSExpandCourseResultPopupDetails();	
		dtLMSCommonUtils.dtLMSExpandClassResultPopupDetails();

		// Start Date
		if(jQuery('.dtlms-datepicker').length) {
			jQuery('.dtlms-datepicker').datepicker({
				minDate: '0d'
			});	
		}
		
		// Pagination
		dtLMSCommonUtils.dtLMSAjaxPagination();

		// Chosen jQuery
		if(jQuery('.dtlms-chosen-select').length) {
			jQuery('.dtlms-chosen-select').chosen({
				no_results_text: lmscommonobject.noResult,
			});		
		}

		// Caluculate popup height on resize
		jQuery(window).bind('resize', function() {
			dtLMSCommonUtils.dtLMSEnableResultPopupScroll();
			dtLMSCommonUtils.dtLMSEnableResultPopupResponsiveScroll();
		});		

	},

	dtStatistics : function() {

		jQuery( 'body' ).delegate( '.dtlms-commissions-overview-chart-options a', 'click', function(e){  
			
			var this_item = jQuery(this),
				overviewchartoption = this_item.attr('data-overviewchartoption'),
				charttitle = this_item.attr('data-charttitle'),
				instructorearnings = this_item.parents('.dtlms-commissions-overview-chart-options').attr('data-instructorearnings'),
				contentfilter = this_item.parents('.dtlms-commissions-overview-chart-options').attr('data-contentfilter'),
				charttype = this_item.parents('.dtlms-commissions-overview-chart-options').attr('data-charttype'),
				timelinefilter = this_item.parents('.dtlms-commissions-overview-chart-options').attr('data-timelinefilter'),
				includecoursecommission = this_item.parents('.dtlms-commissions-overview-chart-options').attr('data-includecoursecommission'),
				includeclasscommission = this_item.parents('.dtlms-commissions-overview-chart-options').attr('data-includeclasscommission'),
				includeothercommission = this_item.parents('.dtlms-commissions-overview-chart-options').attr('data-includeothercommission'),
				includetotalcommission = this_item.parents('.dtlms-commissions-overview-chart-options').attr('data-includetotalcommission'),				
				instructorid = this_item.parents('.dtlms-commissions-overview-chart-options').attr('data-instructorid');


			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: 'dtlms_generate_commissions_overview_chart',
					overviewchartoption: overviewchartoption,
					charttitle: charttitle,
					instructorearnings: instructorearnings,
					contentfilter: contentfilter,
					charttype: charttype,
					timelinefilter: timelinefilter,
					includecoursecommission: includecoursecommission,
					includeclasscommission: includeclasscommission,
					includeothercommission: includeothercommission,
					includetotalcommission: includetotalcommission,					
					instructorid: instructorid,
				},
				beforeSend: function(){
					dtLMSCommonUtils.dtLMSAjaxBeforeSend(this_item.parents('.dtlms-chart-holder'));
				},					
				success: function (response) {
					this_item.parents('.dtlms-chart-holder').find('.dtlms-overview-chart-container').html(response);
					this_item.parents('.dtlms-commissions-overview-chart-options').find('a').removeClass('active');
					this_item.addClass('active');					
				},
				complete: function(){
					dtLMSCommonUtils.dtLMSAjaxAfterSend(this_item.parents('.dtlms-chart-holder'));
				} 
			});	

			e.preventDefault();
			
		});	

		jQuery( 'body' ).delegate( '.dtlms-commissions-overview-instructor-filter', 'change', function(e){  
			
			var this_item = jQuery(this),
				instructor_id = this_item.val();

			jQuery(this).parents('.dtlms-chart-holder').find('.dtlms-commissions-overview-chart-options').attr('data-instructorid', instructor_id);
			jQuery(this).parents('.dtlms-chart-holder').find('.dtlms-commissions-overview-chart-options a:first').trigger('click');	

			e.preventDefault();
			
		});			

		jQuery( '.dtlms-commissions-overview-chart-options' ).each( function() {
			jQuery(this).find('a:first').trigger('click');
		});


		//Purchases chart
		jQuery( 'body' ).delegate( '.dtlms-purchases-overview-chart-options a', 'click', function(e){  
			
			var this_item = jQuery(this),
				includeclasspurchases = this_item.parents('.dtlms-purchases-overview-chart-options').attr('data-includeclasspurchases'),
				includecoursepurchases = this_item.parents('.dtlms-purchases-overview-chart-options').attr('data-includecoursepurchases'),
				includepackagepurchases = this_item.parents('.dtlms-purchases-overview-chart-options').attr('data-includepackagepurchases'),
				includedata = this_item.parents('.dtlms-purchases-overview-chart-options').attr('data-includedata'),
				charttitle = this_item.parents('.dtlms-purchases-overview-chart-options').attr('data-charttitle'),
				firstcolor = this_item.parents('.dtlms-purchases-overview-chart-options').attr('data-firstcolor'),
				secondcolor = this_item.parents('.dtlms-purchases-overview-chart-options').attr('data-secondcolor'),
				thirdcolor = this_item.parents('.dtlms-purchases-overview-chart-options').attr('data-thirdcolor'),
				overviewchartoption = this_item.attr('data-overviewchartoption'),
				instructorid = this_item.attr('data-instructorid');

			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: 'dtlms_generate_purchases_overview_chart',
					includeclasspurchases: includeclasspurchases,
					includecoursepurchases: includecoursepurchases,
					includepackagepurchases: includepackagepurchases,
					includedata: includedata,
					charttitle: charttitle,
					firstcolor: firstcolor,
					secondcolor: secondcolor,
					thirdcolor: thirdcolor,
					overviewchartoption: overviewchartoption,
					instructorid: instructorid,
				},
				beforeSend: function(){
					dtLMSCommonUtils.dtLMSAjaxBeforeSend(this_item.parents('.dtlms-chart-container'));
				},					
				success: function (response) {
					this_item.parents('.dtlms-chart-container').find('.dtlms-overview-chart-container').html(response);
					this_item.parents('.dtlms-purchases-overview-chart-options').find('a').removeClass('active');
					this_item.addClass('active');
				},
				complete: function(){
					dtLMSCommonUtils.dtLMSAjaxAfterSend(this_item.parents('.dtlms-chart-container'));
				} 
			});	

			e.preventDefault();
			
		});	

		jQuery( 'body' ).delegate( '.dtlms-purchases-overview-instructor-filter', 'change', function(e){  
			
			var this_item = jQuery(this),
				instructor_id = this_item.val();

			jQuery(this).parents('.dtlms-chart-container').find('.dtlms-purchases-overview-chart-options a').attr('data-instructorid', instructor_id);
			jQuery(this).parents('.dtlms-chart-container').find('.dtlms-purchases-overview-chart-options a:first').trigger('click');	

			e.preventDefault();
			
		});	

		jQuery( '.dtlms-purchases-overview-chart-options').each( function(e) {
			jQuery(this).find( 'a:first' ).trigger('click');
		});
						
	},

	dtDashboard : function() {

		jQuery( 'body' ).delegate( '.dtlms-generate-certificate-content', 'click', function(e) {  
		
			var this_item = jQuery(this),
				certificate_id = this_item.attr('data-certificateid'),
				item_id = this_item.attr('data-itemid'),
				grade_id = this_item.attr('data-gradeid'),
				user_id = this_item.attr('data-userid'),
				parent_item = this_item.parents('.dtlms-student-certificate-holder');

			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: 'dtlms_generate_certificate_content',
					certificate_id: certificate_id,
					item_id: item_id,
					grade_id: grade_id,
					user_id: user_id,
				},
				beforeSend: function() {
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					dtLMSCommonUtils.dtLMSPrintContent(response);				    	
				},
				complete: function() {
					this_item.find('span').remove();
				} 
			});	

			e.preventDefault();
			
		});		

	    //dtLMSCommonUtils.dtLMSNiceScroll('.dtlms-enable-scroll');

		jQuery( 'body' ).delegate( '.dtlms-view-course-result', 'click', function(e) {  
		
			var this_item = jQuery(this),
				course_id = this_item.attr('data-courseid'),
				user_id = this_item.attr('data-userid'),
				parent_item = this_item.parents('.dtlms-course-result-overview');
			
			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: 'dtlms_generate_course_result',
					course_id: course_id,
					user_id: user_id,
				},
				beforeSend: function(){
					dtLMSCommonUtils.dtLMSAjaxBeforeSend(parent_item);
				},
				success: function (response) {

					if(this_item.hasClass('dtlms-dashboard')) {
						jQuery('body').addClass('single single-dtlms');
					}

					jQuery('body').find('#dtlms-course-result-popup').remove();
					jQuery('body').append(response);
					jQuery('body,html').css('overflow', 'hidden');

					dtLMSCommonUtils.dtLMSProgressBar(false);

					var parent_curriculum_id = jQuery('.dtlms-view-curriculum-details:first').attr('data-parentcurriculumid');
					var curriculum_id = jQuery('.dtlms-view-curriculum-details:first').attr('data-curriculumid');
					var row_class = '.dtlms-item-' + parent_curriculum_id + '-' + curriculum_id;
					jQuery('.dtlms-view-curriculum-details:first').trigger('click');
					jQuery('.dtlms-curriculum-items').removeClass('active');
					jQuery(row_class).addClass('active');

					// Curriculum Pagination
					dtLMSCommonUtils.dtLMSAjaxPagination();		

					dtLMSCommonUtils.dtLMSEnableResultPopupScroll();
					dtLMSCommonUtils.dtLMSEnableResultPopupResponsiveScroll();
				    	
				},
				complete: function(){
					dtLMSCommonUtils.dtLMSAjaxAfterSend(parent_item);
				} 
			});	

			e.preventDefault();
			
		});

		jQuery( 'body' ).delegate( '.dtlms-close-course-result-popup', 'click', function(e){  
		
			jQuery('body,html').css('overflow', '');
			jQuery('#dtlms-course-result-popup').remove();

			e.preventDefault();
			
		});	

		jQuery( 'body' ).delegate( '.dtlms-refresh-course-result', 'click', function(e) {  

			var course_id = jQuery(this).attr('data-courseid'),
				user_id = jQuery(this).attr('data-userid')

			jQuery('.dtlms-view-course-result[data-courseid="'+course_id+'"][data-userid="'+user_id+'"]').trigger('click');
			dtLMSCommonUtils.dtLMSAjaxPagination();		

			dtLMSCommonUtils.dtLMSEnableResultPopupScroll();
			dtLMSCommonUtils.dtLMSEnableResultPopupResponsiveScroll();

			e.preventDefault();

		});

		jQuery( 'body' ).delegate( '.dtlms-view-curriculum-details', 'click', function(e) {  
		
			var parent_item = jQuery(this).parents('#dtlms-class-result-popup, .dtlms-course-result-popup-container'),
				parent_curriculum_id = jQuery(this).attr('data-parentcurriculumid'),
				curriculum_id = jQuery(this).attr('data-curriculumid'),
				curriculum_grade_id = jQuery(this).attr('data-curriculumgradeid');
			
			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: 'dtlms_view_curriculum_details',
					curriculum_id: curriculum_id,
					curriculum_grade_id: curriculum_grade_id,
				},
				beforeSend: function(){
					dtLMSCommonUtils.dtLMSAjaxBeforeSend(parent_item);
				},
				error: function (xhr, status, error) {
					//jQuery('body').html('Something went wrong!');
				},
				success: function (response) {

					jQuery('body').find('.dtlms-view-curriculum-details-holder').html('');
					jQuery('body').find('.dtlms-view-curriculum-details-holder').append(response);

					dtLMSCommonUtils.dtLMSProgressBar(false);	

					var row_class = '.dtlms-item-' + parent_curriculum_id + '-' + curriculum_id;
					jQuery('.dtlms-curriculum-items').removeClass('active');
					jQuery(row_class).addClass('active');		

									
					dtLMSCommonUtils.dtLMSEnableResultPopupScroll();
					dtLMSCommonUtils.dtLMSEnableResultPopupResponsiveScroll();

				},
				complete: function(){
					dtLMSCommonUtils.dtLMSAjaxAfterSend(parent_item);
				} 
			});	

			e.preventDefault();
			
		});

		jQuery( 'body' ).delegate( '.dtlms-statistics-courses-instructor', 'change', function(e) {  
		
			var this_item = jQuery(this),
				instructor_id = this_item.val();

			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: 'dtlms_load_instructorwise_courses',
					instructor_id: instructor_id,
					ajax_call: true,
					function_call: 'dtlms_load_instructorwise_courses',
					output_div: 'dtlms-instructor-courses-container',			
				},
				beforeSend: function(){
					dtLMSCommonUtils.dtLMSAjaxBeforeSend(this_item.parents('.dtlms-statistics-container'));
				},					
				success: function (response) {
					this_item.parents('.dtlms-statistics-container').find('.dtlms-instructor-courses-container').html(response);
				},
				complete: function(){
					dtLMSCommonUtils.dtLMSAjaxAfterSend(this_item.parents('.dtlms-statistics-container'));
				}				
			});

			e.preventDefault();
			
		});	

		//jQuery( '.dtlms-statistics-courses-instructor' ).trigger('change');	


		jQuery( 'body' ).delegate( '.dtlms-statistics-commission-instructor', 'change', function(e) {  
		
			var this_item = jQuery(this),
				instructor_id = this_item.val(),
				commission_content = this_item.attr('data-commissioncontent');

			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: 'dtlms_load_instructorwise_commissions',
					instructor_id: instructor_id,
					commission_content: commission_content,
					ajax_call: true,
					function_call: 'dtlms_load_instructorwise_commissions',
					output_div: 'dtlms-instructor-commissions-container',					
				},
				beforeSend: function(){
					dtLMSCommonUtils.dtLMSAjaxBeforeSend(this_item.parents('.dtlms-statistics-container'));
				},					
				success: function (response) {
					this_item.parents('.dtlms-statistics-container').find('.dtlms-instructor-commissions-container').html(response);
				},
				complete: function(){
					dtLMSCommonUtils.dtLMSAjaxAfterSend(this_item.parents('.dtlms-statistics-container'));
				}			
			});

			e.preventDefault();
			
		});	

		//jQuery( '.dtlms-statistics-commissions-instructor' ).trigger('change');	


		// Class

		jQuery( 'body' ).delegate( '.dtlms-view-class-result', 'click', function(e) {  
		
			var this_item = jQuery(this),
				class_id = this_item.attr('data-classid'),
				user_id = this_item.attr('data-userid');
			
			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: 'dtlms_generate_class_result',
					class_id: class_id,
					user_id: user_id,
				},
				beforeSend: function(){
					dtLMSCommonUtils.dtLMSAjaxBeforeSend(undefined);
				},
				success: function (response) {

					if(this_item.hasClass('dtlms-dashboard')) {
						jQuery('body').addClass('single single-dtlms');
					}

					jQuery('body').find('#dtlms-class-result-popup').remove();
					jQuery('body').append(response);
					jQuery('body, html').css('overflow', 'hidden');

									
					dtLMSCommonUtils.dtLMSProgressBar(false);

					jQuery('.dtlms-view-class-curriculum-details:first').trigger('click');
				    	
				},
				complete: function(){
					dtLMSCommonUtils.dtLMSAjaxAfterSend(undefined);
				} 
			});	

			e.preventDefault();
			
		});

		jQuery( 'body' ).delegate( '.dtlms-close-class-result-popup', 'click', function(e){  
		
			jQuery('body, html').css('overflow', '');
			jQuery('#dtlms-class-result-popup').remove();

			e.preventDefault();
			
		});	

		jQuery( 'body' ).delegate( '.dtlms-refresh-class-result', 'click', function(e) {  

			var class_id = jQuery(this).attr('data-classid'),
				user_id = jQuery(this).attr('data-userid')

			jQuery('.dtlms-view-class-result[data-classid="'+class_id+'"][data-userid="'+user_id+'"]').trigger('click');

			dtLMSCommonUtils.dtLMSEnableResultPopupScroll();
			dtLMSCommonUtils.dtLMSEnableResultPopupResponsiveScroll();

			e.preventDefault();

		});	

		jQuery( 'body' ).delegate( '.dtlms-view-class-curriculum-details', 'click', function(e) {  
		
			var this_item = jQuery(this),
				course_id = this_item.attr('data-courseid'),
				user_id = this_item.attr('data-userid'),
				parent_item = this_item.parents('.dtlms-class-result-popup-container');
			
			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: 'dtlms_view_class_curriculum_details',
					course_id: course_id,
					user_id: user_id,
				},
				beforeSend: function(){
					dtLMSCommonUtils.dtLMSAjaxBeforeSend(parent_item);
				},
				error: function (xhr, status, error) {
					//jQuery('body').html('Something went wrong!');
				},
				success: function (response) {

					jQuery('body').find('#dtlms-class-result-popup .dtlms-view-class-curriculum-details-holder').html(response);
					jQuery('.dtlms-view-curriculum-details:first').trigger('click');	

					dtLMSCommonUtils.dtLMSProgressBar(false);	

					dtLMSCommonUtils.dtLMSEnableResultPopupScroll();
					dtLMSCommonUtils.dtLMSEnableResultPopupResponsiveScroll();

					jQuery('.dtlms-class-curriculum-table tr').removeClass('active');
					this_item.parents('tr').addClass('active');					
				    	
				},
				complete: function(){
					dtLMSCommonUtils.dtLMSAjaxAfterSend(parent_item);
				} 
			});	

			e.preventDefault();
			
		});

		jQuery( 'body' ).delegate( '.dtlms-statistics-classes-instructor', 'change', function(e) {  
		
			var this_item = jQuery(this),
				instructor_id = this_item.val();

			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: 'dtlms_load_instructorwise_classes',
					instructor_id: instructor_id,
					ajax_call: true,
					function_call: 'dtlms_load_instructorwise_classes',
					output_div: 'dtlms-instructor-classes-container',						
				},
				beforeSend: function(){
					dtLMSCommonUtils.dtLMSAjaxBeforeSend(this_item.parents('.dtlms-statistics-container'));
				},					
				success: function (response) {
					this_item.parents('.dtlms-statistics-container').find('.dtlms-instructor-classes-container').html(response);
				},
				complete: function(){
					dtLMSCommonUtils.dtLMSAjaxAfterSend(this_item.parents('.dtlms-statistics-container'));
				}				
			});

			e.preventDefault();
			
		});	

		//jQuery( '.dtlms-statistics-classes-instructor' ).trigger('change');			


	}	
	
};

jQuery(document).ready(function() {

	dtLMSCommon.dtInit();
	
});