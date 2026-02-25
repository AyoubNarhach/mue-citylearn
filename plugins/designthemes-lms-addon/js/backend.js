var dtLMSBackendUtils = {

	dtLMSCheckboxSwitch : function() {

		jQuery('.dtlms-checkbox-switch:not(.disabled)').each( function() {
			jQuery(this).on('click',function(e) {

				var $ele = '#' + jQuery(this).attr("data-for");
				jQuery(this).toggleClass('checkbox-switch-off checkbox-switch-on');

				if(jQuery(this).hasClass('dtlms-update-revoke-user-submission')) {
					jQuery('.dtlms-revoke-user-submission').toggleClass('disabled');
				}

				if(jQuery(this).hasClass('items-topay-commission')) {
					var total_commission_topay = jQuery('.total-commission-topay').val();
					var course_commission_topay = jQuery('.course-commission-topay').val();
					var class_commission_topay = jQuery('.class-commission-topay').val();
					var amounttopay = jQuery(this).attr('data-amounttopay');
					var class_amounttopay = course_amounttopay = 0;
					if(jQuery(this).hasClass('class-item')) {
						var item_id = jQuery(this).attr('data-classid');
						var selected_commission_items = jQuery(this).parents('.dtlms-paycommission-container').find('.dtlms-commission-markaspaid').attr('data-selectedclasses');
						class_amounttopay = jQuery(this).attr('data-amounttopay');
					} else {
						var item_id = jQuery(this).attr('data-courseid');
						var selected_commission_items = jQuery(this).parents('.dtlms-paycommission-container').find('.dtlms-commission-markaspaid').attr('data-selectedcourses');
						course_amounttopay = jQuery(this).attr('data-amounttopay');
					}
					var commission_details = jQuery(this).attr('data-commissiondetails');
					var overall_commission_details = jQuery(this).parents('.dtlms-paycommission-container').find('.dtlms-commission-markaspaid').attr('data-overallcommissiondetails');
				}				
				
				if (jQuery(this).hasClass('checkbox-switch-on')) {
					jQuery($ele).prop("checked", true);
					if(jQuery(this).hasClass('items-topay-commission')) {
						total_commission_topay = parseFloat(total_commission_topay)+parseFloat(amounttopay);
						if(jQuery(this).hasClass('class-item')) {
							class_commission_topay = parseFloat(class_commission_topay)+parseFloat(class_amounttopay);
						} else {
							course_commission_topay = parseFloat(course_commission_topay)+parseFloat(course_amounttopay);
						}
						selected_commission_items = selected_commission_items+','+item_id;
						overall_commission_details = overall_commission_details+','+commission_details;						
					}					
				} else {
					jQuery($ele).removeAttr("checked");
					if(jQuery(this).hasClass('items-topay-commission')) {
						total_commission_topay = parseFloat(total_commission_topay)-parseFloat(amounttopay);
						if(jQuery(this).hasClass('class-item')) {
							class_commission_topay = parseFloat(class_commission_topay)-parseFloat(class_amounttopay);
						} else {
							course_commission_topay = parseFloat(course_commission_topay)-parseFloat(course_amounttopay);
						}
						selected_commission_items = selected_commission_items.replace(','+item_id, '');
						overall_commission_details = overall_commission_details.replace(','+commission_details, '');
					}					
				}

				if(jQuery(this).hasClass('items-topay-commission')) {
					class_commission_topay = parseFloat(class_commission_topay);
					jQuery('.class-commission-topay').val(class_commission_topay);
					course_commission_topay = parseFloat(course_commission_topay);
					jQuery('.course-commission-topay').val(course_commission_topay);					

					total_commission_topay = parseFloat(total_commission_topay);
					jQuery('.total-commission-topay').val(total_commission_topay);
					jQuery('.other-amounts').attr('data-totalcommissions', total_commission_topay);
					jQuery('#dtlmsPaypalForm').find('.amount').val(total_commission_topay);
					if(jQuery(this).hasClass('class-item')) {
						jQuery(this).parents('.dtlms-paycommission-container').find('.dtlms-commission-markaspaid').attr('data-selectedclasses', selected_commission_items);	
					} else {
						jQuery(this).parents('.dtlms-paycommission-container').find('.dtlms-commission-markaspaid').attr('data-selectedcourses', selected_commission_items);						
					}
					jQuery(this).parents('.dtlms-paycommission-container').find('.dtlms-commission-markaspaid').attr('data-overallcommissiondetails', overall_commission_details);
				}

				e.preventDefault();

			});
		});

	},

	dtLMSNiceScroll : function(itemclass) {

	    jQuery(itemclass).niceScroll({
	        zindex: 999999,
	        cursorborder: "1px solid #424242"
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

	dtLMSQuizCategoryOnChange : function() {

		jQuery('body').delegate('#dtlms-quiz-categories', 'change', function(e){	
			jQuery(this).parents('#dtlms-category-box').find('#dtlms-quiz-categories-questions').attr('max', jQuery(this).find('option:selected').attr('data-count'));
		});

	},

	dtLMSCompletionLockDripFeedSwitch : function() {

		jQuery('body').delegate('#drip-completionlock-switch', 'change', function(e){	

			var drip_completionlock = jQuery(this).val();

			jQuery('.dtlms-completionlock-holder').slideUp();
			jQuery('.dtlms-dripfeed-holder').slideUp();

			if(drip_completionlock == 'completionlock') {

				jQuery('.dtlms-completionlock-holder').slideDown();
				jQuery('.dtlms-dripfeed-holder').slideUp();

			} else if(drip_completionlock == 'dripfeed') {

				jQuery('.dtlms-completionlock-holder').slideUp();
				jQuery('.dtlms-dripfeed-holder').slideDown();

			}

		});

	},

};

var dtLMSBackend = {
	
	dtInit : function() {
		dtLMSBackend.dtMediaUploader();
		dtLMSBackend.dtLMS();
		dtLMSBackend.dtQuizzes();
		dtLMSBackend.dtQuestions();
		dtLMSBackend.dtGradings();
		dtLMSBackend.dtClasses();
		dtLMSBackend.dtUsers();
		dtLMSBackend.dtSettings();
		dtLMSBackend.dtStatistics();
		dtLMSBackend.dtClassRegistration();
		dtLMSBackend.dtImport();
	},

	dtMediaUploader : function() {

		jQuery('body').delegate('.dtlms-upload-media-item-button', 'click', function(e){	

			var file_frame = null;
			var item_clicked = jQuery(this);
			var multiple = false;
			var button_text = "Insert Image";

			var media_type = '';
			if(jQuery(this).attr('data-mediatype') && (jQuery(this).attr('data-mediatype') == 'image')) {
				media_type = 'image';
			}

			if(item_clicked.hasClass('multiple')) {
				multiple = true;
				button_text = "Insert Image(s)";
			}
			
		    file_frame = wp.media.frames.file_frame = wp.media({
		    	multiple: multiple,
		    	title : "Upload / Select Media",
				library : {
					type : media_type,
				},		    	
		    	button :{
		    		text : button_text
		    	}
		    });
		    
		    // When an image is selected, run a callback.
		    file_frame.on( 'select', function() {

		    	var attachments = file_frame.state().get('selection').toJSON();

		    	if(item_clicked.hasClass('multiple')) {

			        var items = '';
			        jQuery.each( attachments, function(key, value) {

				        var id = value.id;
				        var url = value.url;
				        var title = value.title;

			        	items += '<li>'+
                                    '<input name="media-attachment-urls[]" type="text" class="uploadfieldurl large" readonly value="'+url+'" />'+
                                    '<input name="media-attachment-ids[]" type="hidden" class="uploadfieldid hidden" readonly value="'+id+'" />'+
                                    '<input name="media-attachment-titles[]" type="text" class="media-attachment-titles" placeholder="'+lmsbackendobject.attachmentTitle+'" value="'+title+'" />'+
                                    '<input name="media-attachment-icons[]" type="text" class="media-attachment-icons" placeholder="'+lmsbackendobject.attachmentIcon+'" value="" />'+
                                    '<span class="dtlms-remove-media-item"><span class="fa fa-close"></span></span>'+
                                '</li>';
                                
					});

					item_clicked.parents('.dtlms-upload-media-items-container').find('.dtlms-upload-media-items').append(items);

		    	} else {

			        var id = attachments[0].id;
			        var url = attachments[0].url;

			        item_clicked.parents('.dtlms-upload-media-items-container').find('.uploadfieldurl').val(url);
			        item_clicked.parents('.dtlms-upload-media-items-container').find('.uploadfieldid').val(id);

			        if(item_clicked.hasClass('show-preview')) {
			        	item_clicked.parents('.dtlms-upload-media-items-container').find('.dtlms-image-preview-tooltip img').attr('src', url);
			        }

			    }
		        
		    });
		    
		    // Finally, open the modal
		    file_frame.open();

		});

		jQuery('body').delegate('.dtlms-upload-media-item-reset', 'click', function(e) {	
		
			var item_clicked = jQuery(this);

			if(item_clicked.parents('.dtlms-upload-media-items-container').find('.dtlms-upload-media-item-button').hasClass('multiple')) {

				item_clicked.parents('.dtlms-upload-media-items-container').find('.dtlms-upload-media-items').html('');

			} else {

		        item_clicked.parents('.dtlms-upload-media-items-container').find('.uploadfieldurl').val('');
		        item_clicked.parents('.dtlms-upload-media-items-container').find('.uploadfieldid').val('');

		        if(item_clicked.parents('.dtlms-upload-media-items-container').find('.dtlms-upload-media-item-button').hasClass('show-preview')) {
					var $noimage = item_clicked.parents('.dtlms-upload-media-items-container').find('.dtlms-image-preview-tooltip img').attr('data-default');
					item_clicked.parents('.dtlms-upload-media-items-container').find('.dtlms-image-preview-tooltip img').attr('src', $noimage);
				}

			}

			e.preventDefault();

		});

		jQuery('body').delegate('.dtlms-remove-media-item', 'click', function(e) {	
		
			jQuery(this).parents('li').remove();
			e.preventDefault();
			
		});	
		
		jQuery('.dtlms-upload-media-items').sortable({ placeholder: 'sortable-placeholder' });		

	},
	
	dtLMS : function() {

		// Initaialize color picker
		if(jQuery('.dtlms-color-field').length) {
			jQuery('.dtlms-color-field').wpColorPicker();
		}

		// Checkbox switch
		dtLMSBackendUtils.dtLMSCheckboxSwitch();

		// Completion Lock & Drip Feed switch
		dtLMSBackendUtils.dtLMSCompletionLockDripFeedSwitch();


		// Add tabs
		jQuery('a.dtlms-add-curriculum').on('click',function(e){
			
			if(jQuery(this).hasClass('section')) {
				var clone = jQuery("#dtlms-curriculum-section-to-clone").clone();
			}

			if(jQuery(this).hasClass('lesson')) {
				var clone = jQuery("#dtlms-curriculum-lesson-to-clone").clone();
			}

			if(jQuery(this).hasClass('quiz')) {
				var clone = jQuery("#dtlms-curriculum-quiz-to-clone").clone();
			}

			if(jQuery(this).hasClass('assignment')) {
				var clone = jQuery("#dtlms-curriculum-assignment-to-clone").clone();
			}

			clone.attr('id', 'dtlms-curriculum-section-item').removeClass('hidden');

			if(jQuery(this).attr('data-curriculumtype') == 'lesson') {
				clone.find('select').attr('id', 'lesson-curriculum').attr('name', 'lesson-curriculum[]').addClass('curriculum-chosen');
				clone.find('input').attr('id', 'lesson-curriculum').attr('name', 'lesson-curriculum[]');
			} else {
				clone.find('select').attr('id', 'course-curriculum').attr('name', 'course-curriculum[]').addClass('curriculum-chosen');
				clone.find('input').attr('id', 'course-curriculum').attr('name', 'course-curriculum[]');	
			}

			clone.appendTo('#dtlms-curriculum-items-container');

			jQuery('.curriculum-chosen').chosen();
			
			e.preventDefault();
			
		});
		
		jQuery('body').delegate('span.dtlms-remove-curriculum-item','click', function(e){	
		
			jQuery(this).parents('#dtlms-curriculum-section-item').remove();
			e.preventDefault();
			
		});	
		
		jQuery("#dtlms-curriculum-items-container").sortable({ placeholder: 'sortable-placeholder' });


		// Alert for no result in selection box
		/*jQuery('.dtlms-chosen-select').chosen({
			no_results_text: lmsbackendobject.noResult,
		});*/	


		// Comment Ratings
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

		// Date picker
		jQuery('.dtlms-date-field').datepicker();

	},

	dtQuizzes : function() {

		dtLMSBackendUtils.dtLMSQuizCategoryOnChange();

		jQuery('body').delegate('select#dtlms-quiz-question-type', 'change', function(e){
			
			jQuery('.dtlms-add-questions-holder').slideDown();
			jQuery('.dtlms-add-categories-holder').slideUp();
			if(jQuery(this).val() == 'add-categories') {
				jQuery('.dtlms-add-questions-holder').slideUp();
				jQuery('.dtlms-add-categories-holder').slideDown();
			}

			e.preventDefault();
			
		});

		jQuery('a.dtlms-add-questions').on('click',function(e){
			
			var clone = jQuery("#dtlms-questions-to-clone").clone();
			
			clone.attr('id', 'dtlms-question-box').removeClass('hidden');
			clone.find('select').attr('id', 'dtlms-quiz-question').attr('name', 'dtlms-quiz-question[]').attr('class', 'dtlms-new-chosen-select');
			clone.find('.question-grade').attr('id', 'dtlms-quiz-question-grade').attr('name', 'dtlms-quiz-question-grade[]').removeClass('question-grade').attr('required', 'required');
			clone.find('.question-negative-grade').attr('id', 'dtlms-quiz-question-negative-grade').attr('name', 'dtlms-quiz-question-negative-grade[]').removeClass('question-negative-grade');			
			
			clone.appendTo('#dtlms-quiz-questions-container');		
			
			jQuery(".dtlms-new-chosen-select").chosen({
				no_results_text: lmsbackendobject.noResult,
			});
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('span.dtlms-remove-question','click', function(e){	
		
			jQuery(this).parents('#dtlms-question-box').remove();
			jQuery( "#dtlms-quiz-question-grade" ).trigger( "change" );
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('#dtlms-quiz-question-grade', 'change', function(){
			 
			 var total = parseInt(0);
			 jQuery('#dtlms-quiz-questions-container #dtlms-question-box').each(function(){
				var ival = jQuery(this).find('#dtlms-quiz-question-grade').val();
				if(ival == 'NAN' || ival ==''){
					ival = parseInt(0);
				}
				total = parseInt(total) + parseInt(ival);
			 });
			 
			 jQuery(this).parents('.dtlms-column').find('#dtlms-total-marks-container span').html(total);
			 jQuery(this).parents('.dtlms-column').find('#dtlms-total-marks-container input[type="hidden"]').val(total);
			 
		});
		 
		jQuery('#dtlms-quiz-questions-container').sortable({ placeholder: 'sortable-placeholder' });

		/////////////////////

		jQuery('a.dtlms-add-categories').on('click',function(e){
			
			var clone = jQuery("#dtlms-categories-to-clone").clone();
			
			clone.attr('id', 'dtlms-category-box').removeClass('hidden');
			clone.find('select').attr('id', 'dtlms-quiz-categories').attr('name', 'dtlms-quiz-categories[]').attr('class', 'dtlms-new-chosen-select');
			clone.find('.quiz-category-questions').attr('id', 'dtlms-quiz-categories-questions').attr('name', 'dtlms-quiz-categories-questions[]').removeClass('quiz-category-questions').attr('required', 'required');
			clone.find('.quiz-category-grade').attr('id', 'dtlms-quiz-categories-grade').attr('name', 'dtlms-quiz-categories-grade[]').removeClass('quiz-category-grade').attr('required', 'required');
			clone.find('.quiz-category-negative-grade').attr('id', 'dtlms-quiz-categories-negative-grade').attr('name', 'dtlms-quiz-categories-negative-grade[]').removeClass('quiz-category-negative-grade');
			
			clone.appendTo('#dtlms-quiz-categories-container');		
			
			jQuery(".dtlms-new-chosen-select").chosen({
				no_results_text: lmsbackendobject.noResult,
			});

			dtLMSBackendUtils.dtLMSQuizCategoryOnChange();
			
			e.preventDefault();
			
		});	

		jQuery('body').delegate('span.dtlms-remove-category','click', function(e){	
		
			jQuery(this).parents('#dtlms-category-box').remove();
			jQuery( "#dtlms-quiz-categories-grade" ).trigger( "change" );
			
			e.preventDefault();
			
		});

		jQuery('body').delegate('#dtlms-quiz-categories-questions, #dtlms-quiz-categories-grade', 'change', function(){
		 
			 var total = parseInt(0);
			 jQuery('#dtlms-quiz-categories-container #dtlms-category-box').each(function(){
				var questions = jQuery(this).find('#dtlms-quiz-categories-questions').val();
				if(questions == 'NAN' || questions ==''){
					questions = parseInt(0);
				}
				var grade = jQuery(this).find('#dtlms-quiz-categories-grade').val();
				if(grade == 'NAN' || grade ==''){
					grade = parseInt(0);
				}	
				
				total = parseInt(total) + (parseInt(questions)*parseInt(grade));
			 });
	 
			 jQuery(this).parents('.dtlms-column').find('#dtlms-total-marks-container span').html(total);
			 jQuery(this).parents('.dtlms-column').find('#dtlms-total-marks-container input[type="hidden"]').val(total);
			 
		});		

		jQuery('#dtlms-quiz-categories-container').sortable({ placeholder: 'sortable-placeholder' });

	},

	dtQuestions : function() {

		// Multichoice answers script
		jQuery('a.dtlms-add-multichoice-answer').on('click',function(e){
			
			var wrong_ans_cnt = jQuery("div.dtlms-multichoice-answer-clone").find('#dt_multichoice_answers_cnt').val();
			wrong_ans_cnt = parseInt(wrong_ans_cnt)+1;
			jQuery("div.dtlms-multichoice-answer-clone").find('#dt_multichoice_answers_cnt').val(wrong_ans_cnt);
			
			jQuery("div.dtlms-multichoice-answer-clone").find('#dtlms-multichoice-correct-answer').val(wrong_ans_cnt);
			
			jQuery("div.dtlms-multichoice-answer-clone").find('#dtlms-answer-holder').clone().appendTo( "#dtlms-multichoice-answers-container" );
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('span.dtlms-remove-multichoice-answer', 'click', function(e){	
		
			jQuery(this).parents('#dtlms-answer-holder').remove();
			
			var wrong_ans_cnt = jQuery("div.dtlms-multichoice-answer-clone").find('#dt_multichoice_answers_cnt').val();
			wrong_ans_cnt = parseInt(wrong_ans_cnt)-1;
			jQuery("div.dtlms-multichoice-answer-clone").find('#dt_multichoice_answers_cnt').val(wrong_ans_cnt);
			
			var i = 0;
			jQuery('#dtlms-multichoice-answers-container #dtlms-multichoice-correct-answer').each(function() {
				jQuery(this).val(i);
				i++;
			});	
			
			e.preventDefault();
			
		});	
		
		jQuery('#dtlms-multichoice-answers-container').sortable({
			placeholder: 'sortable-placeholder',
			stop: function(event, ui) {
				var i = 0;
				jQuery(this).find('.dtlms-multichoice-correct-answer').each(function() {
					jQuery(this).val(i);
					i++;
				});	
			}
		});


		// Multichoice image answers script
		jQuery('a.dtlms-add-multichoice-image-answer').on('click',function(e){
			
			var wrong_ans_cnt = jQuery("div.dtlms-multichoice-image-answer-clone").find('#dt_multichoice_image_answers_cnt').val();
			wrong_ans_cnt = parseInt(wrong_ans_cnt)+1;
			jQuery("div.dtlms-multichoice-image-answer-clone").find('#dt_multichoice_image_answers_cnt').val(wrong_ans_cnt);
			jQuery("div.dtlms-multichoice-image-answer-clone").find('#dtlms-multichoice_image-correct-answer').val(wrong_ans_cnt);
			jQuery("div.dtlms-multichoice-image-answer-clone").find('#dtlms-answer-holder').clone().appendTo( "#dtlms-multichoice-image-answers-container" );
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('span.dtlms-remove-multichoice-image-answer','click', function(e){	
		
			jQuery(this).parents('#dtlms-answer-holder').remove();			
			var wrong_ans_cnt = jQuery("div.dtlms-multichoice-image-answer-clone").find('#dt_multichoice_image_answers_cnt').val();
			wrong_ans_cnt = parseInt(wrong_ans_cnt)-1;
			jQuery("div.dtlms-multichoice-image-answer-clone").find('#dt_multichoice_image_answers_cnt').val(wrong_ans_cnt);
			var i = 0;
			jQuery('#dtlms-multichoice-image-answers-container #dtlms-multichoice-image-correct-answer').each(function() {
				jQuery(this).val(i);
				i++;
			});	
			
			e.preventDefault();
			
		});	
		
		jQuery("#dtlms-multichoice-image-answers-container").sortable({
			placeholder: 'sortable-placeholder',
			stop: function(event, ui) {
				var i = 0;
				jQuery(this).find('.dtlms-multichoice-image-correct-answer').each(function() {
					jQuery(this).val(i);
					i++;
				});	
			}
		});


		// Multiple correct answers
		jQuery('a.dtlms-add-multicorrect-answer').on('click',function(e){
			
			var wrong_ans_cnt = jQuery("div.dtlms-multicorrect-answer-clone").find('#dt_multicorrect_answers_cnt').val();
			wrong_ans_cnt = parseInt(wrong_ans_cnt)+1;
			jQuery("div.dtlms-multicorrect-answer-clone").find('#dt_multicorrect_answers_cnt').val(wrong_ans_cnt);		
			jQuery("div.dtlms-multicorrect-answer-clone").find('#dtlms-multicorrect-correct-answer').val(wrong_ans_cnt);
			jQuery("div.dtlms-multicorrect-answer-clone").find('#dtlms-answer-holder').clone().appendTo( "#dtlms-multicorrect-answers-container" );
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('span.dtlms-remove-multicorrect-answer','click', function(e){	
		
			jQuery(this).parents('#dtlms-answer-holder').remove();
			var wrong_ans_cnt = jQuery("div.dtlms-multicorrect-answer-clone").find('#dt_multicorrect_answers_cnt').val();
			wrong_ans_cnt = parseInt(wrong_ans_cnt)-1;
			jQuery("div.dtlms-multicorrect-answer-clone").find('#dt_multicorrect_answers_cnt').val(wrong_ans_cnt);
			var i = 0;
			jQuery('#dtlms-multicorrect-answers-container #dtlms-multicorrect-correct-answer').each(function() {
				jQuery(this).val(i);
				i++;
			});	
			
			e.preventDefault();
			
		});	
		
		jQuery('#dtlms-multicorrect-answers-container').sortable({
			placeholder: 'sortable-placeholder',
			stop: function(event, ui) {
				var i = 0;
				jQuery('.dtlms-multicorrect-correct-answer').each(function() {
					jQuery(this).val(i);
					i++;
				});	
			}
		});


		// Question type change
		jQuery('body').delegate('select#dtlms-question-type','change', function(e){	
			jQuery('.dtlms-answers').hide();
			jQuery('.dtlms-' + jQuery(this).val() + '-answers').show();
			e.preventDefault();
		});		

	},
	
	dtGradings : function() {

		jQuery('div.dtlms-quiz-answer-switch:not(.disabled)').each(function() {

		  jQuery(this).on('click',function(){

			  var $marksobtained = jQuery('input#dtlms-marks-obtained').val();
			  if($marksobtained == '') {
			  	jQuery('a#dtlms-reset-grade').addClass('dtlms-reset-grade-auto');
			  	jQuery('a#dtlms-reset-grade').trigger('click');
			  	jQuery('a#dtlms-reset-grade').removeClass('dtlms-reset-grade-auto');
			 	var $marksobtained = jQuery('input#dtlms-marks-obtained').val();
			  }

			  var $ele = '#'+jQuery(this).attr("data-for");
			  var $grade = jQuery(this).parents('.dtlms-answers').attr('data-grade');
			  var $negative_grade = jQuery(this).parents('.dtlms-answers').attr('data-negative-grade');

			  var $totalmarks = jQuery('input#dtlms-maximum-marks').val();
			  var $marksobtained_percent = 0;

			  var $correct_questions = jQuery('input#correct-questions').val();
			  var $wrong_questions = jQuery('input#wrong-questions').val();

				if($negative_grade > 0) {
					$negative_grade_data = '-'+$negative_grade;
				} else {
					$negative_grade_data = 0;
				}

			  jQuery(this).toggleClass('dtlms-quiz-answer-switch-off dtlms-quiz-answer-switch-on');
			  if(jQuery(this).hasClass('dtlms-quiz-answer-switch-on')) {

				  jQuery(this).parents('.dtlms-answers').find('.dtlms-grade-display-field').html($grade + ' / ' + $grade);
				  if($negative_grade > 0) {
				  	$marksobtained = parseInt($marksobtained)+parseInt($grade)+parseInt($negative_grade);
				  } else {
				  	$marksobtained = parseInt($marksobtained)+parseInt($grade);
				  }
				  
				  $marksobtained_percent = +(($marksobtained/$totalmarks)*100).toFixed(2);
				  
				  jQuery('input#dtlms-marks-obtained').val($marksobtained);
				  jQuery('input#dtlms-marks-obtained-percentage').val($marksobtained_percent);

				  jQuery('input#correct-questions').val(parseInt($correct_questions, 10)+1);
				  jQuery('input#wrong-questions').val(parseInt($wrong_questions, 10)-1);
				  
				  jQuery(this).html('Right');
				  jQuery($ele).prop('checked', true);

			  } else {

				  jQuery(this).parents('.dtlms-answers').find('.dtlms-grade-display-field').html($negative_grade_data+' / ' + $grade);
				  if($negative_grade > 0) {
				  	$marksobtained = parseInt($marksobtained)-parseInt($grade)-parseInt($negative_grade);
				  } else {
				  	$marksobtained = parseInt($marksobtained)-parseInt($grade);
				  }
				  $marksobtained_percent = +(($marksobtained/$totalmarks)*100).toFixed(2);
				  
				  jQuery('input#dtlms-marks-obtained').val($marksobtained);
				  jQuery('input#dtlms-marks-obtained-percentage').val($marksobtained_percent);

				  jQuery('input#correct-questions').val(parseInt($correct_questions, 10)-1);
				  jQuery('input#wrong-questions').val(parseInt($wrong_questions, 10)+1);
	
				  jQuery(this).html('Wrong');
				  jQuery($ele).removeAttr('checked');

			  }

			  jQuery('#dtlms-marks-obtained').trigger('change');
			  
		  });
		  
		});
		
		jQuery('a#dtlms-reset-grade').on('click',function(e) {
			
			if(jQuery(this).hasClass('dtlms-reset-grade-auto') || confirm(lmsbackendobject.resetGrade) ) {

				var negative_grade_total = total_grade = 0;
				jQuery('.dtlms-custom-table .dtlms-answers').each(function() {

					var grade = parseInt(jQuery(this).attr('data-grade'));

					if(jQuery(this).hasClass('skipped')) {

						jQuery(this).find('.dtlms-grade-display-field').html('skipped');

					} else {

						var negative_grade = parseInt(jQuery(this).attr('data-negative-grade'));

						if(negative_grade > 0) {
							jQuery(this).find('.dtlms-grade-display-field').html('-'+negative_grade + ' / ' + grade);
						} else {
							jQuery(this).find('.dtlms-grade-display-field').html('0 / ' + grade);
						}	

						jQuery(this).find('.dtlms-grade-option-field .dtlms-quiz-answer-switch').removeAttr('class').addClass('dtlms-quiz-answer-switch dtlms-quiz-answer-switch-off').html('Wrong');
						jQuery(this).find('.dtlms-grade-option-field input').removeAttr('checked').val(false);					

						negative_grade_total = negative_grade_total + negative_grade;

					}

					total_grade = total_grade + grade;

				});
			
				if(negative_grade_total > 0) {
					jQuery('#dtlms-marks-obtained').val('-'+negative_grade_total);
					var grade_percent = ((negative_grade_total/total_grade)*100).toFixed(2);
					jQuery('#dtlms-marks-obtained-percentage').val('-'+grade_percent);
				} else {
					jQuery('#dtlms-marks-obtained').val(0);
					jQuery('#dtlms-marks-obtained-percentage').val(0);
				}

			  	var $total_questions = jQuery('input#total-questions').val();
			  	var $skipped_questions = jQuery('input#skipped-questions').val();

			  	$wrong_questions = parseInt($total_questions)-parseInt($skipped_questions);

				jQuery('input#correct-questions').val(0);
				jQuery('input#wrong-questions').val($wrong_questions);	
				
			}
			
			e.preventDefault();
			
		});
		
		jQuery('a#dtlms-auto-grade').on('click',function(e ){
			
			var total_grade = 0, marks_obtained = 0;
			var $correct_questions = 0, $wrong_questions = 0;	

			jQuery('.dtlms-custom-table .dtlms-answers').each(function() {
			
				if(jQuery(this).find('.dtlms-correct-answer').attr('data-multichoiceimage') == 'true') {
					var correct_answer = jQuery(this).find('.dtlms-correct-answer').find('img').attr('src');
				} else {
					var correct_answer = jQuery(this).find('.dtlms-correct-answer').attr('data-correctanswer');
				}

				if(jQuery(this).find('.dtlms-user-answer').attr('data-multichoiceimage') == 'true') {
					var user_answer = jQuery(this).find('.dtlms-user-answer').find('img').attr('src');
				} else {
					var user_answer = jQuery(this).find('.dtlms-user-answer').attr('data-useranswer');
				}

				if(correct_answer == undefined) {
					correct_answer = '';
				}

				if(user_answer == undefined) {
					user_answer = '';
				}
				
				var grade = parseInt(jQuery(this).attr('data-grade'));
				var negative_grade = parseInt(jQuery(this).attr('data-negative-grade'));

				if(negative_grade > 0) {
					negative_grade_data = '-'+negative_grade;
				} else {
					negative_grade_data = 0;
				}
		
				total_grade = parseInt(total_grade)+grade;

				if(user_answer != '') {

					if(correct_answer.toLowerCase().replace(new RegExp(/\r?\n|\r|<br>| /g),"") == user_answer.toLowerCase().replace(new RegExp(/\r?\n|\r|<br>| /g),"")) {
						marks_obtained = parseInt(marks_obtained)+grade;
						jQuery(this).find('.dtlms-grade-display-field').html(grade + ' / ' + grade);
						jQuery(this).find('.dtlms-grade-option-field .dtlms-quiz-answer-switch').removeAttr('class').addClass('dtlms-quiz-answer-switch dtlms-quiz-answer-switch-on').html('Right');
						jQuery(this).find('.dtlms-grade-option-field input').prop('checked', true).val(true);

						$correct_questions = parseInt($correct_questions)+1;
					} else {
						marks_obtained = parseInt(marks_obtained)-negative_grade;
						jQuery(this).find('.dtlms-grade-display-field').html(negative_grade_data + ' / ' + grade);
						jQuery(this).find('.dtlms-grade-option-field .dtlms-quiz-answer-switch').removeAttr('class').addClass('dtlms-quiz-answer-switch dtlms-quiz-answer-switch-off').html('Wrong');
						jQuery(this).find('.dtlms-grade-option-field input').removeAttr('checked').val(false);		

						$wrong_questions = parseInt($wrong_questions)+1;	
					}

				}					
				
			});
			
			var marks_obtained_percent = +((marks_obtained/total_grade)*100).toFixed(2);

		
			jQuery('input#dtlms-marks-obtained').val(marks_obtained);
			jQuery('input#dtlms-marks-obtained-percentage').val(marks_obtained_percent);

			jQuery('input#correct-questions').val($correct_questions);
			jQuery('input#wrong-questions').val($wrong_questions);				

			//jQuery('#dtlms-marks-obtained').trigger('change');
			
			e.preventDefault();
			
		});

		jQuery('body').delegate('#dtlms-marks-obtained', 'change', function(){
			 
			 var user_mark = jQuery(this).val();
			 var max_mark = jQuery('#dtlms-maximum-marks').val();
			 
			 var percentage = (parseInt(user_mark)/parseInt(max_mark))*100;
			 if(isNaN(percentage)) {
			 	percentage = 0;
			 }
			 percentage = +percentage.toFixed(2);
		 
			 jQuery('#dtlms-marks-obtained-percentage').val(percentage);
			 
		});	

		jQuery( 'body' ).delegate( '.dtlms-revoke-user-submission', 'click', function(e){  
		
			var this_item = jQuery(this),
				class_id = this_item.attr('data-classid'),
				course_id = this_item.attr('data-courseid'),
				user_id = this_item.attr('data-userid'),
				item_type = this_item.attr('data-itemtype');

			if(this_item.hasClass('disabled')) {
				alert(lmsbackendobject.revokeUserSubmissionWarning);
				return false;
			}
			
			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data:
				{
					action: 'dtlms_revoke_user_submission',
					class_id: class_id,
					course_id: course_id,
					user_id: user_id,
					item_type: item_type,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					alert(lmsbackendobject.revokeUserSubmission);
					location.reload();				    	
				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});	

			e.preventDefault();
			
		});		

		// Warning on grading parent post trash
	    jQuery('.post-type-dtlms_gradings.edit-php .trash a.submitdelete').on('click', function( e ) {
	        if( ! confirm( lmsbackendobject.gradingWarningTrash ) ) {
	            e.preventDefault();
	        }           
	    });	

	    // Warning on grading parent post delete
	    jQuery('.post-type-dtlms_gradings.edit-php .delete a.submitdelete').on('click', function( e ) {
	        if( ! confirm( lmsbackendobject.gradingWarningDelete ) ) {
	            e.preventDefault();
	        }           
	    });		    		

	},

	dtClasses : function() {

		// Switch options between onsite and online
		jQuery('body').delegate('.dtlms-class-type', 'change', function(e){
			
			jQuery('.dtlms-online-items').slideUp();
			jQuery('.dtlms-onsite-items').slideUp();
			jQuery('.dtlms-onsiteonline-items').slideUp();
			if(jQuery(this).val() == 'onsite') {
				jQuery('.dtlms-online-items').slideUp();
				jQuery('.dtlms-onsite-items').slideDown();
				jQuery('.dtlms-onsiteonline-items').slideDown();
			} else if(jQuery(this).val() == 'online') {
				jQuery('.dtlms-online-items').slideDown();
				jQuery('.dtlms-onsite-items').slideUp();
				jQuery('.dtlms-onsiteonline-items').slideDown();
			}
			e.preventDefault();
			
		});

		// Switch options between course content and shortcodes content
		jQuery('body').delegate('select#dtlms-class-content-options','change', function(e){
			
			jQuery('.dtlms-course-content').slideUp();
			jQuery('.dtlms-shortcode-content').slideUp();

			if(jQuery(this).val() == 'course') {
				jQuery('.dtlms-course-content').slideDown();
				jQuery('.dtlms-shortcode-content').slideUp();
				jQuery(".chosen-container").attr('style', 'width:80%');
			}
			if(jQuery(this).val() == 'shortcode') {
				jQuery('.dtlms-course-content').slideUp();
				jQuery('.dtlms-shortcode-content').slideDown();
				jQuery(".chosen-container").attr('style', 'width:80%');
			}
			
			e.preventDefault();
			
		});	


		// Generate latitude and longitude values from date
		jQuery('.dtlms-generate-gps').on('click',function(e){
			
			var $address = jQuery('.dtlms-class-address').val();

			if( jQuery.trim($address).length <= 0 ){
				alert(lmsbackendobject.locationAlert1);
			} else {
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({ 'address': jQuery.trim($address) }, function (results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						jQuery('.dtlms-class-latitude').attr('value', results[0].geometry.location.lat());
						jQuery('.dtlms-class-longitude').attr('value', results[0].geometry.location.lng());
					} else {
						alert(lmsbackendobject.locationAlert2);
					}
				});
			}
			
			e.preventDefault();
			
		});		

		// Add course to class
		jQuery('a.dtlms-add-course').on('click',function(e){
			
			var clone = jQuery("#dtlms-course-to-clone").clone();

			clone.attr('class', 'dtlms-course-box').removeClass('hidden').removeAttr('id');
			clone.find('select').attr('class', 'dtlms-class-courses dtlms-chosen-select').attr('name', 'dtlms-class-courses[]');
			clone.appendTo('#dtlms-class-courses-container');	

			jQuery('.dtlms-chosen-select').chosen({
				no_results_text: lmsbackendobject.noResult,
			});
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('span.dtlms-remove-course','click', function(e){	
		
			jQuery(this).parents('.dtlms-course-box').remove();
			
			e.preventDefault();
			
		});	
		
		jQuery('#dtlms-class-courses-container').sortable({ placeholder: 'sortable-placeholder' });		


		// Adding accessories
		jQuery('a.dtlms-add-accessory').on('click',function(e){
			
			var clone = jQuery('#dtlms-accessory-to-clone').clone();
			clone.attr('class', 'dtlms-accessory-box').removeClass('hidden').removeAttr('id');
			clone.appendTo('#dtlms-class-accessories-container');		
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('span.dtlms-remove-accessory','click', function(e){	
		
			jQuery(this).parents('.dtlms-accessory-box').remove();
			e.preventDefault();
			
		});	
		
		jQuery('#dtlms-class-accessories-container').sortable({ placeholder: 'sortable-placeholder' });


		// Adding tabs
		jQuery('a.dtlms-add-tab').on('click',function(e){
			
			var clone = jQuery("#dtlms-tab-to-clone").clone();
			clone.attr('class', 'dtlms-tab-box').removeClass('hidden').removeAttr('id');
			clone.appendTo('#dtlms-class-tabs-container');		
			
			e.preventDefault();
			
		});	
		
		jQuery('body').delegate('span.dtlms-remove-tab','click', function(e){	
		
			jQuery(this).parents('.dtlms-tab-box').remove();
			e.preventDefault();
			
		});	
		
		jQuery('#dtlms-class-tabs-container').sortable({ placeholder: 'sortable-placeholder' });

	},
	
	dtUsers : function() {

		// Adding tabs
		jQuery("a.dtlms-add-user-social").on('click',function(e){
			
			var clone = jQuery("#dtlms-user-section-to-clone").clone();

			clone.attr('id', 'dtlms-user-section-item').removeClass('hidden');
			clone.find('select').attr('id', 'user-social-items').attr('name', 'user-social-items[]').addClass('social-item-chosen');
			clone.find('input').attr('id', 'user-social-items-value').attr('name', 'user-social-items-value[]');
			clone.appendTo('#dtlms-user-details-container');

			jQuery('.social-item-chosen').chosen();
			
			e.preventDefault();
			
		});
		
		jQuery('body').delegate('span.dtlms-remove-user-tab','click', function(e){	
		
			jQuery(this).parents('#dtlms-user-section-item').remove();
			e.preventDefault();
			
		});	
		
		jQuery("#dtlms-user-details-container").sortable({ placeholder: 'sortable-placeholder' });

	},

	dtSettings : function() {

		jQuery( 'body' ).delegate( '.dtlms-setcom-instructor', 'change', function(e){  
		
			var instructor_id = jQuery(this).val();
			
			if(instructor_id != '') {

				jQuery.ajax({
					type: "POST",
					url: lmsbackendobject.ajaxurl,
					data:
					{
						action: 'dtlms_setcom_load_instructor_courses',
						instructor_id: instructor_id,
					},
					beforeSend: function(){
						dtLMSBackendUtils.dtLMSAjaxBeforeSend();
					},					
					success: function (response) {
						jQuery('.dtlms-setcommission-container').html(response);
					},
					complete: function(){
						dtLMSBackendUtils.dtLMSAjaxAfterSend();
					} 
				});	

			} else {

				jQuery('.dtlms-setcommission-container').html(lmsbackendobject.selectInstructor);
				
			}

			e.preventDefault();
			
		});		

		jQuery( 'body' ).delegate( '.dtlms-save-commission-settings', 'click', function(e) {  
		
			var this_item = jQuery(this),
				instructor_id = this_item.attr('data-instructorid');

	        var form = jQuery('.formSetCommission')[0];
	        var data = new FormData(form);
	        data.append('action', 'dtlms_save_commission_settings');
	        data.append('instructor_id', instructor_id);;

			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data: data,
	            processData: false,
	            contentType: false,	
	            cache: false,
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					jQuery('.dtlms-commission-settings-response-holder').html(response);
				},
				complete: function(){
					this_item.find('span').remove();
				} 				
			});

			e.preventDefault();
			
		});

		jQuery( 'body' ).delegate( '.dtlms-save-options-settings', 'click', function(e) {  
		
			var this_item = jQuery(this),
				settings = this_item.attr('data-settings');

	        var form = jQuery('.formOptionSettings')[0];
	        var data = new FormData(form);
	        data.append('action', 'dtlms_save_options_settings');
	        data.append('settings', settings);

			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data: data,
	            processData: false,
	            contentType: false,	
	            cache: false,
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					this_item.parents('.formOptionSettings').find('.dtlms-option-settings-response-holder').html(response);
					this_item.parents('.formOptionSettings').find('.dtlms-option-settings-response-holder').show();
					window.setTimeout(function(){ 
						this_item.parents('.formOptionSettings').find('.dtlms-option-settings-response-holder').fadeOut('slow');
					}, 2000);					
				},
				complete: function(){
					this_item.find('span').remove();
				} 				
			});

			e.preventDefault();
			
		});

		jQuery( 'body' ).delegate( '.dtlms-load-paycom-datas', 'click', function(e) {  
		
			var this_item = jQuery(this)
				instructor_id = this_item.parents('.dtlms-settings-pay-commission-container').find('.dtlms-paycom-instructor').val(),
				startdate = this_item.parents('.dtlms-settings-pay-commission-container').find('.dtlms-paycom-startdate').val(),
				enddate = this_item.parents('.dtlms-settings-pay-commission-container').find('.dtlms-paycom-enddate').val();

			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data:
				{
					action: 'dtlms_load_paycom_datas',
					instructor_id: instructor_id,
					startdate: startdate,
					enddate: enddate,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					jQuery('.dtlms-paycommission-container').html(response);
					dtLMSBackendUtils.dtLMSCheckboxSwitch();
				},
				complete: function(){
					this_item.find('span').remove();
				} 				
			});

			e.preventDefault();
			
		});

		jQuery( 'body' ).delegate( '.other-amounts', 'change', function(e) {  

			var other_amounts = jQuery(this).val();
			other_amounts = (other_amounts != '') ? other_amounts : 0;
			var totalcommissions = jQuery(this).attr('data-totalcommissions');
			totalcommissions = (totalcommissions != '') ? totalcommissions : 0;

			var total_commission_topay = parseFloat(totalcommissions) + parseFloat(other_amounts);

			jQuery('.total-commission-topay').val(total_commission_topay);

			e.preventDefault();

		});			

		jQuery( 'body' ).delegate( '.dtlms-pay-commission-via-paypal', 'click', function(e) {  

			var instructor_paypal_email = jQuery('.instructor-paypal-email').val();
			jQuery('#dtlmsPaypalForm').find('.emailid').val(instructor_paypal_email);
			
			jQuery('#dtlmsPaypalForm').submit();

			e.preventDefault();

		});

		jQuery( 'body' ).delegate( '.dtlms-commission-markaspaid', 'click', function(e) {  
		
			var this_item = jQuery(this),
				instructor_id = this_item.attr('data-instructorid'),
				start_date = this_item.attr('data-startdate'),
				end_date = this_item.attr('data-enddate'),
				selected_courses = this_item.attr('data-selectedcourses'),
				selected_classes = this_item.attr('data-selectedclasses'),
				instructor_paypal_email = jQuery('.instructor-paypal-email').val(),
				other_amounts = jQuery('.other-amounts').val(),
				course_commission_paid = jQuery('.course-commission-topay').val(),
				class_commission_paid = jQuery('.class-commission-topay').val(),
				total_commission_paid = jQuery('.total-commission-topay').val(),
				overall_commission_details = this_item.attr('data-overallcommissiondetails');

			if(parseInt(total_commission_paid, 10) > 0 && !this_item.hasClass('disabled')) {

				this_item.addClass('disabled');

				jQuery.ajax({
					type: "POST",
					url: lmsbackendobject.ajaxurl,
					data:
					{
						action: 'dtlms_paycommission_markaspaid',
						instructor_id: instructor_id,
						start_date: start_date,
						end_date: end_date,
						selected_courses: selected_courses,
						selected_classes: selected_classes,
						instructor_paypal_email: instructor_paypal_email,
						course_commission_paid: course_commission_paid,
						class_commission_paid: class_commission_paid,
						total_commission_paid: total_commission_paid,
						overall_commission_details: overall_commission_details,
						other_amounts: other_amounts,
					},
					beforeSend: function(){
						this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
					},
					success: function (response) {
						window.location.replace(window.location.href + "&paycommission=success");
						this_item.removeClass('disabled');
					},
					complete: function(){
						this_item.find('span').remove();
					} 				
				});

			}

			e.preventDefault();
			
		});	

		// Assigning

		jQuery( 'body' ).delegate( '.dtlms-assigning-students', 'change', function(e){  
		
			var course_id = jQuery(this).val();
			
			//if(course_id != '') {

				jQuery.ajax({
					type: "POST",
					url: lmsbackendobject.ajaxurl,
					data:
					{
						action: 'dtlms_assigning_load_students_data',
						course_id: course_id,
					},
					beforeSend: function(){
						dtLMSBackendUtils.dtLMSAjaxBeforeSend(undefined);
					},					
					success: function (response) {
						jQuery('.dtlms-assign-studentstocourse-container').html(response);
						dtLMSBackendUtils.dtLMSCheckboxSwitch();
					},
					complete: function(){
						dtLMSBackendUtils.dtLMSAjaxAfterSend(undefined);
					} 
				});	
				
			//}

			e.preventDefault();
			
		});	

		jQuery( 'body' ).delegate( '.dtlms-save-assign-students-settings', 'click', function(e) {  
		
			var this_item = jQuery(this),
				course_id = this_item.attr('data-courseid');

			var student_ids = jQuery('.assign-students-to-course:checked').map(function(){
				return this.value;
			}).get();

			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data:
				{
					action: 'dtlms_save_assign_students_settings',
					course_id: course_id,
					student_ids: student_ids,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					jQuery('.dtlms-assign-students-response-holder').html(response);
					window.setTimeout(function(){ 
						jQuery('.dtlms-assign-students-response-holder').fadeOut('slow');
					}, 2000);							    	
				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});	

			e.preventDefault();
			
		});

		jQuery( 'body' ).delegate( '.dtlms-assigning-courses', 'change', function(e){  
		
			var student_id = jQuery(this).val();
			
			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data:
				{
					action: 'dtlms_assigning_load_courses_data',
					student_id: student_id,
				},
				beforeSend: function(){
					dtLMSBackendUtils.dtLMSAjaxBeforeSend(undefined);
				},					
				success: function (response) {
					jQuery('.dtlms-assign-coursestostudent-container').html(response);
					dtLMSBackendUtils.dtLMSCheckboxSwitch();
				},
				complete: function(){
					dtLMSBackendUtils.dtLMSAjaxAfterSend(undefined);
				} 
			});
				
			e.preventDefault();
			
		});	

		jQuery( 'body' ).delegate( '.dtlms-save-assign-courses-settings', 'click', function(e) {  
		
			var this_item = jQuery(this),
				student_id = this_item.attr('data-studentid');

			var course_ids = jQuery('.assign-courses-to-student:checked').map(function(){
				return this.value;
			}).get();

			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data:
				{
					action: 'dtlms_save_assign_courses_settings',
					student_id: student_id,
					course_ids: course_ids,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					jQuery('.dtlms-assign-courses-response-holder').html(response);
					window.setTimeout(function(){ 
						jQuery('.dtlms-assign-courses-response-holder').fadeOut('slow');
					}, 2000);							    	
				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});	

			e.preventDefault();
			
		});

		//

		jQuery( 'body' ).delegate( '.dtlms-assigning-classes-students', 'change', function(e){  
		
			var class_id = jQuery(this).val();
			
			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data:
				{
					action: 'dtlms_assigning_classes_load_students_data',
					class_id: class_id,
				},
				beforeSend: function(){
					dtLMSBackendUtils.dtLMSAjaxBeforeSend(undefined);
				},					
				success: function (response) {
					jQuery('.dtlms-assign-studentstoclass-container').html(response);
					dtLMSBackendUtils.dtLMSCheckboxSwitch();
				},
				complete: function(){
					dtLMSBackendUtils.dtLMSAjaxAfterSend(undefined);
				} 
			});	
				
			e.preventDefault();
			
		});	

		jQuery( 'body' ).delegate( '.dtlms-save-assign-classes-students-settings', 'click', function(e) {  
		
			var this_item = jQuery(this),
				class_id = this_item.attr('data-classid');

			var student_ids = jQuery('.assign-students-to-class:checked').map(function(){
				return this.value;
			}).get();

			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data:
				{
					action: 'dtlms_save_assign_classes_students_settings',
					class_id: class_id,
					student_ids: student_ids,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					jQuery('.dtlms-assign-classes-students-response-holder').html(response);
					window.setTimeout(function(){ 
						jQuery('.dtlms-assign-classes-students-response-holder').fadeOut('slow');
					}, 2000);							    	
				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});	

			e.preventDefault();
			
		});		

		jQuery( 'body' ).delegate( '.dtlms-assigning-classes-classes', 'change', function(e){  
		
			var student_id = jQuery(this).val();
			
			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data:
				{
					action: 'dtlms_assigning_classes_load_classes_data',
					student_id: student_id,
				},
				beforeSend: function(){
					dtLMSBackendUtils.dtLMSAjaxBeforeSend(undefined);
				},					
				success: function (response) {
					jQuery('.dtlms-assign-classestostudent-container').html(response);
					dtLMSBackendUtils.dtLMSCheckboxSwitch();
				},
				complete: function(){
					dtLMSBackendUtils.dtLMSAjaxAfterSend(undefined);
				} 
			});
				
			e.preventDefault();
			
		});	

		jQuery( 'body' ).delegate( '.dtlms-save-assign-classes-classes-settings', 'click', function(e) {  
		
			var this_item = jQuery(this),
				student_id = this_item.attr('data-studentid');

			var class_ids = jQuery('.assign-classes-to-student:checked').map(function(){
				return this.value;
			}).get();

			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data:
				{
					action: 'dtlms_save_assign_classes_classes_settings',
					student_id: student_id,
					class_ids: class_ids,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					jQuery('.dtlms-assign-classes-classes-response-holder').html(response);
					window.setTimeout(function(){ 
						jQuery('.dtlms-assign-classes-classes-response-holder').fadeOut('slow');
					}, 2000);							    	
				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});	

			e.preventDefault();
			
		});

		// POC

		jQuery( 'body' ).delegate( '.dtlms-save-poc-settings', 'click', function(e) {  
		
			var this_item = jQuery(this);

	        var form = jQuery('.formPocSettings')[0];
	        var data = new FormData(form);
	        data.append('action', 'dtlms_save_poc_settings');

			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data: data,
	            processData: false,
	            contentType: false,	
	            cache: false,
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					this_item.parents('.formPocSettings').find('.dtlms-poc-settings-response-holder').html(response);
					window.setTimeout(function(){ 
						this_item.parents('.formPocSettings').find('.dtlms-poc-settings-response-holder').fadeOut('slow');
					}, 2000);					
				},
				complete: function(){
					this_item.find('span').remove();
				} 				
			});

			e.preventDefault();
			
		});		

		// Skin

		jQuery( 'body' ).delegate( '.dtlms-save-skin-settings', 'click', function(e) {  
		
			var this_item = jQuery(this);

	        var form = jQuery('.formSkinSettings')[0];
	        var data = new FormData(form);
	        data.append('action', 'dtlms_save_skin_settings');

			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data: data,
	            processData: false,
	            contentType: false,	
	            cache: false,
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					this_item.parents('.formSkinSettings').find('.dtlms-skin-settings-response-holder').html(response);
					window.setTimeout(function(){ 
						this_item.parents('.formSkinSettings').find('.dtlms-skin-settings-response-holder').fadeOut('slow');
					}, 2000);					
				},
				complete: function(){
					this_item.find('span').remove();
				} 				
			});

			e.preventDefault();
			
		});

	},

	dtStatistics : function() {

	},

	dtClassRegistration : function() {

		jQuery( 'body' ).delegate( '.dtlms-classregistrations-classes', 'change', function(e) {  
		
			var this_item = jQuery(this),
				class_id = this_item.val();

			jQuery.ajax({
				type: "POST",
				url: lmscommonobject.ajaxurl,
				data:
				{
					action: 'dtlms_load_class_registration_details',
					class_id: class_id,
				},
				beforeSend: function() {
					dtLMSBackendUtils.dtLMSAjaxBeforeSend(this_item.parents('.dtlms-classregistrations-container'));
				},					
				success: function (response) {
					this_item.parents('.dtlms-classregistrations-container').find('.dtlms-classregistrations-classes-container').html(response);
					// Checkbox switch
					dtLMSBackendUtils.dtLMSCheckboxSwitch();					
				},
				complete: function() {
					dtLMSBackendUtils.dtLMSAjaxAfterSend(this_item.parents('.dtlms-classregistrations-container'));
				}				
			});

			e.preventDefault();
			
		});

		jQuery( 'body' ).delegate( '.dtlms-save-class-registration-settings', 'click', function(e) {  
		
			var this_item = jQuery(this),
				class_id = this_item.attr('data-classid');

			var registered_users_certificate = jQuery('.approve-registered-users-certificate:checked').map(function(){
				return this.value;
			}).get();

			var registered_users_badge = jQuery('.approve-registered-users-badge:checked').map(function(){
				return this.value;
			}).get();		


			jQuery.ajax({
				type: "POST",
				url: lmsbackendobject.ajaxurl,
				data:
				{
					action: 'dtlms_save_class_registration_settings',
					class_id: class_id,
					registered_users_certificate: registered_users_certificate,
					registered_users_badge: registered_users_badge,
				},
				beforeSend: function(){
					this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
				},
				success: function (response) {
					jQuery('.dtlms-class-registration-response-holder').html(response);
					window.setTimeout(function(){ 
						jQuery('.dtlms-class-registration-response-holder').fadeOut('slow');
					}, 2000);							    	
				},
				complete: function(){
					this_item.find('span').remove();
				} 
			});	

			e.preventDefault();
			
		});

	},

	dtImport : function(){

		var file_frame = attachments_url = '';

		jQuery('.dtlms-chooseupload-file-button').on('click', function(e){

		    if ( file_frame ) {
		      file_frame.open();
		      return;
		    }
		    
		    file_frame = wp.media.frames.file_frame = wp.media({
		    	multiple: false,
		    	title : lmsbackendobject.importUploadTitle,
		    	button :{
		    		text : lmsbackendobject.importInsertFile
		    	}
		    });
		    
		    file_frame.on( 'select', function() {

		        var attachments = file_frame.state().get('selection').toJSON();	
		        var attachments_url	= attachments[0].url;  

		        jQuery('.dtlms-import-file').val(attachments_url);  
		        
		    });
		    
		    file_frame.open();


		});

		jQuery( 'body' ).delegate( '.dtlms-import-file-button', 'click', function(e){  
		    
		    // Ajax call to read csv file
		    var this_item = jQuery(this);
		    var attachments_url = jQuery('.dtlms-import-file').val();
		    var import_type = jQuery('.dtlms-import-type').val();   

		    if(attachments_url != '') {

				jQuery.ajax({
					type: "POST",
					url: lmsbackendobject.ajaxurl,
					data:
					{
						action: 'dtlms_process_imported_file',
						import_file: attachments_url,
						import_type: import_type,
					},
					beforeSend: function(){
						this_item.prepend( '<span><i class="fa fa-spinner fa-spin"></i></span>' );
					},
					success: function (response) {
						jQuery('.dtlms-settings-import-output-container').html(response);		    	
					},
					complete: function(){
						this_item.find('span').remove();
					} 
				});		

		    } else {

		    	jQuery('.dtlms-settings-import-output-container').html(lmsbackendobject.invalidFile);
		    	
		    }

		    e.preventDefault();

		});		

	}	
	
};

jQuery(document).ready(function() {

	dtLMSBackend.dtInit();
	
});