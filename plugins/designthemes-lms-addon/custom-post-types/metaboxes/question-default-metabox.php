<?php
global $post;
$post_id = $post->ID;

echo '<input type="hidden" name="dtlms_questions_meta_nonce" value="'.wp_create_nonce('dtlms_questions_nonce').'" />'; 

$question_type = get_post_meta ( $post->ID, 'question-type', TRUE );
$multichoice_answers = get_post_meta ( $post->ID, 'multichoice-answers', TRUE );
$multichoice_correct_answer = get_post_meta ( $post->ID, 'multichoice-correct-answer', TRUE );
$multichoice_image_answers = get_post_meta ( $post->ID, 'multichoice-image-answers', TRUE );
$multichoice_image_correct_answer = get_post_meta ( $post->ID, 'multichoice-image-correct-answer', TRUE );
$multicorrect_answers = get_post_meta ( $post->ID, 'multicorrect-answers', TRUE );
$multicorrect_correct_answer = get_post_meta ( $post->ID, 'multicorrect-correct-answer', TRUE );
$multicorrect_correct_answer = is_array($multicorrect_correct_answer) ? $multicorrect_correct_answer : array();
$boolean_answer = get_post_meta ( $post->ID, 'boolean-answer', TRUE );
$text_before_gap = get_post_meta ( $post->ID, 'text-before-gap', TRUE );
$gap = get_post_meta ( $post->ID, 'gap', TRUE );
$text_after_gap = get_post_meta ( $post->ID, 'text-after-gap', TRUE );
$singleline_answer = get_post_meta ( $post->ID, 'singleline-answer', TRUE );
$multiline_answer = get_post_meta ( $post->ID, 'multiline-answer', TRUE );
$answer_explanation = get_post_meta ( $post->ID, 'answer-explanation', TRUE );
$answer_hint = get_post_meta ( $post->ID, 'answer-hint', TRUE );

$hide_multichoice = $hide_multichoice_image = $hide_multicorrect = $hide_boolean = $hide_gapfill = $hide_singleline = $hide_multiline = 'hidden';

if($question_type == 'multiple-choice') $hide_multichoice = '';
else if($question_type == 'multiple-choice-image') $hide_multichoice_image = '';
else if($question_type == 'multiple-correct') $hide_multicorrect = '';
else if($question_type == 'boolean') $hide_boolean = '';
else if($question_type == 'gap-fill') $hide_gapfill = '';
else if($question_type == 'single-line') $hide_singleline = '';
else if($question_type == 'multi-line')$hide_multiline = '';
else if($question_type == '') $hide_multichoice = '';

?>

<!-- Question Type -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Question Type', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <select id="dtlms-question-type" name="dt_question_type">
            <option value="multiple-choice" <?php selected( 'multiple-choice', $question_type, true ); ?>><?php esc_html_e('Multiple Choice', 'dtlms'); ?></option>
            <option value="multiple-choice-image" <?php selected( 'multiple-choice-image', $question_type, true ); ?>><?php esc_html_e('Multiple Choice - Image', 'dtlms'); ?></option>
            <option value="multiple-correct" <?php selected( 'multiple-correct', $question_type, true ); ?>><?php esc_html_e('Multiple Correct', 'dtlms'); ?></option>
            <option value="boolean" <?php selected( 'boolean', $question_type, true ); ?>><?php esc_html_e('True / False', 'dtlms'); ?></option>
            <option value="gap-fill" <?php selected( 'gap-fill', $question_type, true ); ?>><?php esc_html_e('Gap Fill', 'dtlms'); ?></option>
            <option value="single-line" <?php selected( 'single-line', $question_type, true ); ?>><?php esc_html_e('Single Line', 'dtlms'); ?></option>
            <option value="multi-line" <?php selected( 'multi-line', $question_type, true ); ?>><?php esc_html_e('Multi Line', 'dtlms'); ?></option>
        </select>
        <p class="dtlms-note"> <?php esc_html_e('Choose type of question here.','dtlms');?> </p>
    </div>
    
</div>
<!-- Question Type End -->

<!-- Anwsers -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Answers', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
    
        <div class="dtlms-answers dtlms-multiple-choice-answers <?php echo $hide_multichoice; ?>">
        
            <div id="dtlms-multichoice-answers-container">
            	<?php 
				if(!empty($multichoice_answers)) {
					$i = 0;
					foreach($multichoice_answers as $answer) { 
						if($answer == $multichoice_correct_answer) $chk_str = 'checked="checked"'; else $chk_str = '';
					?>
						<div id="dtlms-answer-holder">
							<input type="text" id="dt_multichoice_answers" name="dt_multichoice_answers[]" value="<?php echo htmlentities($answer, ENT_QUOTES); ?>" class="large">
							<input id="dtlms-multichoice-correct-answer" class="dtlms-multichoice-correct-answer" type="radio" name="dtlms-multichoice-correct-answer" value="<?php echo $i; ?>" <?php echo $chk_str; ?>>
							<span class="dtlms-remove-multichoice-answer"><span class="fa fa-close"></span></span>
						</div>
					<?php 
					$i++;
					} 
					$multichoice_cnt = $i-1;
				} else {
					for($i = 0; $i <= 3; $i++) {
						if($i == 0) $chk_str = 'checked="checked"'; else $chk_str = '';
					?>
						<div id="dtlms-answer-holder">
							<input type="text" id="dt_multichoice_answers" name="dt_multichoice_answers[]" value="" class="large">
							<input id="dtlms-multichoice-correct-answer" class="dtlms-multichoice-correct-answer" type="radio" name="dtlms-multichoice-correct-answer" value="<?php echo $i; ?>" <?php echo $chk_str; ?>>
							<span class="dtlms-remove-multichoice-answer"><span class="fa fa-close"></span></span>
						</div>
					<?php 
					}
					$multichoice_cnt = 3;
				}
				?>
            </div>
                       
            <a href="#" class="dtlms-add-multichoice-answer custom-button-style"><?php esc_html_e('Add answer', 'dtlms'); ?></a>
            
            <div class="dtlms-multichoice-answer-clone hidden">
                <div id="dtlms-answer-holder">
                    <input type="text" id="dt_multichoice_answers" name="dt_multichoice_answers[]" value="" class="large">
                    <input id="dtlms-multichoice-correct-answer" class="dtlms-multichoice-correct-answer" type="radio" name="dtlms-multichoice-correct-answer" value="<?php echo $multichoice_cnt; ?>">
                    <span class="dtlms-remove-multichoice-answer"><span class="fa fa-close"></span></span>
                </div>     
                <input type="text" name="dt_multichoice_answers_cnt" id="dt_multichoice_answers_cnt" value="<?php echo $multichoice_cnt; ?>" />      
            </div>
            
           	<p class="dtlms-note"> <?php esc_html_e('Type your answers here and select the correct answer.','dtlms');?> </p> 
            
        </div>
        
        
        <div class="dtlms-answers dtlms-multiple-choice-image-answers <?php echo $hide_multichoice_image; ?>">
        
            <div id="dtlms-multichoice-image-answers-container">
            	<?php 
				if(!empty($multichoice_image_answers)) {
					$i = 0;
					foreach($multichoice_image_answers as $answer) { 
						if($answer == $multichoice_image_correct_answer) $chk_str = 'checked="checked"'; else $chk_str = '';
					?>
						<div class="dtlms-upload-media-items-container" id="dtlms-answer-holder">
                            <input id="dt_multichoice_image_answers" name="dt_multichoice_image_answers[]" type="text" class="uploadfieldurl large" readonly value="<?php echo $answer;?>" />
                            <input type="button" value="<?php esc_html_e('Upload','dtlms');?>" class="dtlms-upload-media-item-button show-preview" data-mediatype="image" />
                            <input type="button" value="<?php esc_html_e('Remove','dtlms');?>" class="dtlms-upload-media-item-reset" />
							<input id="dtlms-multichoice-image-correct-answer" class="dtlms-multichoice-image-correct-answer" type="radio" name="dtlms-multichoice-image-correct-answer" value="<?php echo $i; ?>" <?php echo $chk_str; ?>>
							<span class="dtlms-remove-multichoice-image-answer"><span class="fa fa-close"></span></span>
						</div>
					<?php 
					$i++;
					} 
					$multichoice_image_cnt = $i-1;
				} else {
					for($i = 0; $i <= 3; $i++) {
						if($i == 0) $chk_str = 'checked="checked"'; else $chk_str = '';
					?>
						<div class="dtlms-upload-media-items-container" id="dtlms-answer-holder">
                            <input id="dt_multichoice_image_answers" name="dt_multichoice_image_answers[]" type="text" class="uploadfieldurl large" readonly value="" />
                            <input type="button" value="<?php esc_html_e('Upload','dtlms');?>" class="dtlms-upload-media-item-button show-preview" data-mediatype="image" />
                            <input type="button" value="<?php esc_html_e('Remove','dtlms');?>" class="dtlms-upload-media-item-reset" />           
							<input id="dtlms-multichoice-image-correct-answer" class="dtlms-multichoice-image-correct-answer" type="radio" name="dtlms-multichoice-image-correct-answer" value="<?php echo $i; ?>" <?php echo $chk_str; ?>>
							<span class="dtlms-remove-multichoice-image-answer"><span class="fa fa-close"></span></span>
						</div>
					<?php 
					}
					$multichoice_image_cnt = 3;
				}
				?>
            </div>
                       
            <a href="#" class="dtlms-add-multichoice-image-answer custom-button-style"><?php esc_html_e('Add answer', 'dtlms'); ?></a>
            
            <div class="dtlms-multichoice-image-answer-clone hidden">
                <div class="dtlms-upload-media-items-container" id="dtlms-answer-holder">
                    <input id="dt_multichoice_image_answers" name="dt_multichoice_image_answers[]" type="text" class="uploadfieldurl large" readonly value="" />
                    <input type="button" value="<?php esc_html_e('Upload','dtlms');?>" class="dtlms-upload-media-item-button show-preview" data-mediatype="image" />
                    <input type="button" value="<?php esc_html_e('Remove','dtlms');?>" class="dtlms-upload-media-item-reset" />  
                    <input id="dtlms-multichoice-image-correct-answer" class="dtlms-multichoice-image-correct-answer" type="radio" name="dtlms-multichoice-image-correct-answer" value="<?php echo $multichoice_image_cnt; ?>">
                    <span class="dtlms-remove-multichoice-image-answer"><span class="fa fa-close"></span></span>
                </div>     
                <input type="text" name="dt_multichoice_image_answers_cnt" id="dt_multichoice_image_answers_cnt" value="<?php echo $multichoice_image_cnt; ?>" />      
            </div>
            
           	<p class="dtlms-note"> <?php esc_html_e('Type your answers here and select the correct answer.','dtlms');?> </p> 
            
        </div>
        
        
        <div class="dtlms-answers dtlms-multiple-correct-answers  <?php echo $hide_multicorrect; ?>">
        
            <div id="dtlms-multicorrect-answers-container">
            	<?php 
				if(!empty($multicorrect_answers)) {
					$i = 0;
					foreach($multicorrect_answers as $answer) { 
						if(in_array($answer, $multicorrect_correct_answer)) $chk_str = 'checked="checked"'; else $chk_str = '';
					?>
                        <div id="dtlms-answer-holder">
                            <input type="text" id="dt_multicorrect_answers" name="dt_multicorrect_answers[]" value="<?php echo htmlentities($answer, ENT_QUOTES); ?>" class="large" >
                            <input id="dtlms-multicorrect-correct-answer" class="dtlms-multicorrect-correct-answer" type="checkbox" name="dtlms-multicorrect-correct-answer[]" value="<?php echo $i; ?>" <?php echo $chk_str; ?>>
                            <span class="dtlms-remove-multicorrect-answer"><span class="fa fa-close"></span></span>
                        </div>
					<?php 
					$i++;
					} 
					$multicorrect_cnt = $i-1;
				} else {
					for($i = 0; $i <= 3; $i++) {
					?>
                        <div id="dtlms-answer-holder">
                            <input type="text" id="dt_multicorrect_answers" name="dt_multicorrect_answers[]" value="" class="large" >
                            <input id="dtlms-multicorrect-correct-answer" class="dtlms-multicorrect-correct-answer" type="checkbox" name="dtlms-multicorrect-correct-answer[]" value="<?php echo $i; ?>">
                            <span class="dtlms-remove-multicorrect-answer"><span class="fa fa-close"></span></span>
                        </div>
					<?php 
					}
					$multicorrect_cnt = 3;
				}
				?>
            </div>
                       
            <a href="#" class="dtlms-add-multicorrect-answer custom-button-style"><?php esc_html_e('Add answer', 'dtlms'); ?></a>
            
            <div class="dtlms-multicorrect-answer-clone hidden">
                <div id="dtlms-answer-holder">
                    <input type="text" id="dt_multicorrect_answers" name="dt_multicorrect_answers[]" value="" class="large" >
                    <input id="dtlms-multicorrect-correct-answer" class="dtlms-multicorrect-correct-answer" type="checkbox" name="dtlms-multicorrect-correct-answer[]" value="<?php echo $multicorrect_cnt; ?>">
                    <span class="dtlms-remove-multicorrect-answer"><span class="fa fa-close"></span></span>
                </div>     
                <input type="text" name="dt_multicorrect_answers_cnt" id="dt_multicorrect_answers_cnt" value="<?php echo $multicorrect_cnt; ?>" />      
            </div>
            
            <p class="dtlms-note"> <?php esc_html_e('Type your answers here and select the correct answers.','dtlms');?> </p>
            
        </div>
        
        <div class="dtlms-answers dtlms-boolean-answers  <?php echo $hide_boolean; ?>">
        
            <label for="lbl_boolean">
                <input id="dtlms-boolean-answer-true" type="radio" name="dtlms-boolean-answer" value="true" <?php if($boolean_answer == 'true' || empty($boolean_answer)) echo 'checked="checked"'; ?>> <?php esc_html_e('True', 'dtlms'); ?>
            </label>
            <label for="lbl_boolean">
                <input id="dtlms-boolean-answer-false" type="radio" name="dtlms-boolean-answer" value="false" <?php if($boolean_answer == 'false') echo 'checked="checked"'; ?>> <?php esc_html_e('False', 'dtlms'); ?>
            </label>
            
        </div>

        <div class="dtlms-answers dtlms-gap-fill-answers  <?php echo $hide_gapfill; ?>">
            
            <div class="dtlms-column dtlms-one-sixth first">
               <label><?php esc_html_e('Text Before Gap', 'dtlms'); ?></label>
            </div>
            <div class="dtlms-column dtlms-five-sixth">
                <input type="text" id="dt_text_before_gap" name="dt_text_before_gap" value="<?php echo $text_before_gap; ?>" class="large">
            </div>

            <div class="dtlms-column dtlms-one-sixth first">
               <label><?php esc_html_e('Gap', 'dtlms'); ?></label>
            </div>
            <div class="dtlms-column dtlms-five-sixth">
                <input type="text" id="dt_gap" name="dt_gap" value="<?php echo $gap; ?>" class="large">
            </div>

            <div class="dtlms-column dtlms-one-sixth first">
               <label><?php esc_html_e('Text After Gap', 'dtlms'); ?></label>
            </div>
            <div class="dtlms-column dtlms-five-sixth">
                <input type="text" id="dt_text_after_gap" name="dt_text_after_gap" value="<?php echo $text_after_gap; ?>" class="large">
            </div>

        </div>

        <div class="dtlms-answers dtlms-single-line-answers  <?php echo $hide_singleline; ?>">
           
           	<input type="text" id="dt_singleline_answer" name="dt_singleline_answer" value="<?php echo $singleline_answer; ?>" class="large">
            
        </div>

        <div class="dtlms-answers dtlms-multi-line-answers  <?php echo $hide_multiline; ?>">
            
            <textarea id="dt_multiline_answer" name="dt_multiline_answer" class="large" rows="8" cols="8"><?php echo $multiline_answer; ?></textarea>
            
        </div>
        
    </div>
    
</div>
<!-- Anwsers End -->

<!-- Anwser Hint -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Answer Hint', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <textarea id="dt_answer_hint" name="dt_answer_hint" class="large" rows="8" cols="8"><?php echo $answer_hint; ?></textarea>
        <p class="dtlms-note"> <?php esc_html_e('You can provide hint for the answer here.','dtlms');?> </p>
    </div>
    
</div>
<!-- Anwser Explanation End -->

<!-- Anwser Explanation -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Answer Explanation', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <textarea id="dt_answer_explanation" name="dt_answer_explanation" class="large" rows="8" cols="8"><?php echo $answer_explanation; ?></textarea>
        <p class="dtlms-note"> <?php esc_html_e('You can provide explanantion for the answer here.','dtlms');?> </p>
    </div>
    
</div>
<!-- Anwser Explanation End -->
