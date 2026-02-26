<?php
global $post;
$post_id = $post->ID;
echo '<input type="hidden" name="dttheme_quizzes_meta_nonce" value="'.wp_create_nonce('dttheme_quizzes_nonce').'" />';

$current_user = wp_get_current_user();
$current_user_id = $current_user->ID;
?>

<!-- Free Quiz -->
<div class="dtlms-custom-box">
    <div class="dtlms-column dtlms-one-sixth first">
        <label><?php echo esc_html__('Unlock Quiz','dtlms');?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <?php
        $free_quiz = get_post_meta ( $post_id, 'free-quiz', true );
        $switchclass = ($free_quiz == true) ? 'checkbox-switch-on' : 'checkbox-switch-off';
        $checked = ($free_quiz == true) ? ' checked="checked"' : '';
        ?>        
        <div data-for="free-quiz" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
        <input id="free-quiz" class="hidden" type="checkbox" name="free-quiz" value="true" <?php echo $checked;?>/>
        <p class="dtlms-note"> <?php echo esc_html__('YES! to unlock this quiz, so that you can use this quiz as preview. It won\'t be affected by "Curriculum Completion Lock" in course settings.','dtlms');?> </p>
    </div>
</div><!-- Free Quiz End -->

<!-- Subtitle & Duration -->
<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-half first">
    
        <div class="dtlms-column dtlms-one-third first">
           <label><?php echo esc_html__('Subtitle','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php $quiz_subtitle = get_post_meta ( $post_id, "quiz-subtitle",true); ?>
            <input id="quiz-subtitle" name="quiz-subtitle" class="large" type="text" value="<?php echo $quiz_subtitle;?>" style="width:100%;" />
            <p class="dtlms-note"> <?php echo esc_html__("Subtitle for this Quiz.",'dtlms');?> </p>
        </div>

	</div>
	<div class="dtlms-column dtlms-one-half">
	</div>
    
</div>
<!-- Subtitle & Duration End -->


<div class="dtlms-custom-box">

    <!-- Duration -->
    <div class="dtlms-column dtlms-one-half first">

        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Duration', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $duration = get_post_meta ( $post_id, 'duration', true ); ?>
            <input type="number" id="duration" name="duration" value="<?php echo $duration; ?>" class="large" min="1">
            <p class="dtlms-note"> <?php esc_html_e('Add duration here.','dtlms');?> </p>
        </div>

    </div>
    <!-- Duration End -->

    <!-- Duration Parameter -->
    <div class="dtlms-column dtlms-one-half">

        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Duration Parameter','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $duration_parameter = get_post_meta ( $post_id, 'duration-parameter', true );
            $durationparameters = array ('60' => 'Minutes', '3600' => 'Hours');
    
            echo '<select name="duration-parameter" data-placeholder="'.esc_html__('Select Duration Parameter...', 'dtlms').'" class="dtlms-chosen-select">';
            echo '<option value="">' . esc_html__( 'None', 'dtlms' ) . '</option>';
            foreach ($durationparameters as $durationparameter_key => $ddurationparameter){
                echo '<option value="' . esc_attr( $durationparameter_key ) . '"' . selected( $durationparameter_key, $duration_parameter, false ) . '>' . esc_html( $ddurationparameter ) . '</option>';
            }
            echo '</select>' ;
            ?>
            <p class="dtlms-note"> <?php esc_html_e('Choose duration parameter here.','dtlms');?> </p>
        </div>

    </div>
    <!-- Duration Parameter End -->

</div>


<!-- Number Of Retakes & Post Quiz Message -->
<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-half first">
    
        <div class="dtlms-column dtlms-one-third first">
           <label><?php echo esc_html__('Number Of Retakes','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php $quiz_retakes = get_post_meta ( $post_id, "quiz-retakes",true); ?>
            <input id="quiz-retakes" name="quiz-retakes" class="large" type="number" min="1" max="1000" value="<?php echo $quiz_retakes;?>" />
            <p class="dtlms-note"> <?php echo esc_html__("Number of retakes allowed for student.",'dtlms');?> </p>
        </div>

	</div>
	<div class="dtlms-column dtlms-one-half">
    
        <div class="dtlms-column dtlms-one-third first">
            <label><?php echo esc_html__('Post Quiz Message','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php $quiz_postmsg = get_post_meta ( $post_id, "quiz-postmsg",true); ?>
            <textarea id="quiz-postmsg" name="quiz-postmsg" style="width:100%;"><?php echo $quiz_postmsg;?></textarea>
            <p class="dtlms-note"> <?php echo esc_html__("Message to display once quiz submitted.",'dtlms');?> </p>
        </div>

	</div>
    
</div>
<!-- Number Of Retakes & Post Quiz Message End -->

<!-- Randomize Questions & Auto Evaluation -->
<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-half first">
    
        <div class="dtlms-column dtlms-one-third first">
           <label><?php echo esc_html__('Randomize Questions','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php
			$quiz_randomize_questions = get_post_meta ( $post_id, "quiz-randomize-questions",true);
            $switchclass = ($quiz_randomize_questions != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($quiz_randomize_questions != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="quiz-randomize-questions" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="quiz-randomize-questions" class="hidden" type="checkbox" name="quiz-randomize-questions" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php echo esc_html__('Would you like to randomize the questions order automatically everytime ?','dtlms');?> </p>
        </div>

	</div>
	<div class="dtlms-column dtlms-one-half">
    
        <div class="dtlms-column dtlms-one-third first">
            <label><?php echo esc_html__('Enable Auto Evaluation','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php
			$quiz_auto_evaluation = get_post_meta ( $post_id, "quiz-auto-evaluation",true);
            $switchclass = ($quiz_auto_evaluation != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($quiz_auto_evaluation != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="quiz-auto-evaluation" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="quiz-auto-evaluation" class="hidden" type="checkbox" name="quiz-auto-evaluation" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php echo esc_html__('Would you like to enable auto evaluate questions ?.','dtlms');?> </p>
        </div>

	</div>
    
</div>
<!-- Randomize Questions & Auto Evaluation End -->

<!-- Mark As Graded In Auto Evaluation -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-half first">
    
        <div class="dtlms-column dtlms-one-third first">
           <label><?php echo esc_html__('Mark As Graded In Auto Evaluation','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $quiz_markasgraded_in_autoevaluation = get_post_meta ( $post_id, 'quiz-markasgraded-in-autoevaluation', true);
            $switchclass = ($quiz_markasgraded_in_autoevaluation != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($quiz_markasgraded_in_autoevaluation != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="quiz-markasgraded-in-autoevaluation" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="quiz-markasgraded-in-autoevaluation" class="hidden" type="checkbox" name="quiz-markasgraded-in-autoevaluation" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php echo esc_html__('Set this option to "Yes" if you want to mark quiz as graded ( ie. completed ) in auto evaluation. User won\'t be able to retake the quiz if this option is set.','dtlms');?> </p>
        </div>

    </div>
    <div class="dtlms-column dtlms-one-half">
    
    </div>
    
</div>
<!-- Mark As Graded In Auto Evaluation End -->


<!-- Question Type -->
<div class="dtlms-custom-box">
    <div class="dtlms-column dtlms-one-sixth first">
        <label><?php echo esc_html__('Question Type','dtlms');?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <?php
        $question_type = get_post_meta ( $post_id, 'quiz-question-type', true );

        if($question_type == 'add-categories') {
            $addquestions_hide_cls = 'style="display:none;"';
            $addcategories_hide_cls = '';
        } else {
            $addquestions_hide_cls = '';
            $addcategories_hide_cls = 'style="display:none;"';
        }

        $question_types = array('add-questions' => 'Add Questions Individually', 'add-categories' => 'Add Questions From Categories');
        $out = '';
        $out .= '<select id="dtlms-quiz-question-type" name="dtlms-quiz-question-type" data-placeholder="'.esc_html__('Choose Question Type...', 'dtlms').'" class="dtlms-chosen-select">' . "\n";
            foreach ($question_types as $question_type_key => $question_type_value){
                $out .= '<option value="' . esc_attr( $question_type_key ) . '"' . selected( $question_type_key, $question_type, false ) . '>' . esc_html( $question_type_value ) . '</option>' . "\n";
            }
        $out .= '</select>' . "\n";
        echo $out;
        ?>        
        <p class="dtlms-note"> <?php echo esc_html__('Choose which question type you like to use for your quiz.','dtlms');?> </p>
    </div>
</div><!-- Question Type End -->


<!-- Add Questions -->
<div class="dtlms-custom-box dtlms-add-questions-holder"  <?php echo $addquestions_hide_cls; ?>>

	<div class="dtlms-column dtlms-one-sixth first dtlms-add-quiz">
    
       <label><?php echo esc_html__('Add Questions','dtlms');?></label>

	</div>
	<div class="dtlms-column dtlms-five-sixth">
    
        <?php
        $questions_args = array( 'post_type' => 'dtlms_questions', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC', 'suppress_filters' => FALSE );
        if ( !in_array( 'administrator', (array) $current_user->roles ) ) {
            $questions_args['author'] = $current_user_id;
        } 
        
        $questions_array = get_posts( $questions_args );
		?>		
    
    	<div id="dtlms-quiz-questions-container">
        
        	<?php 
			$quiz_question = get_post_meta ( $post_id, "quiz-question",true); 
			$quiz_question_grade = get_post_meta ( $post_id, "quiz-question-grade",true); 
            $quiz_question_negative_grade = get_post_meta ( $post_id, "quiz-question-negative-grade",true); 
			$quiz_total_grade = get_post_meta ( $post_id, "quiz-total-grade",true); 
			
			$j = 0;
			if(isset($quiz_question) && is_array($quiz_question)) {
				foreach($quiz_question as $sel_question) {
				?>
					<div id="dtlms-question-box">
						<?php
						$out = '';
						$out .= '<select id="dtlms-quiz-question" name="dtlms-quiz-question[]" data-placeholder="'.esc_html__('Choose a Question...', 'dtlms').'" class="dtlms-chosen-select" style="width:40%;">' . "\n";
						$out .= '<option value=""></option>';
						if ( count( $questions_array ) > 0 ) {
							foreach ($questions_array as $question){
								$out .= '<option value="' . esc_attr( $question->ID ) . '"' . selected( $question->ID, $sel_question, false ) . '>' . esc_html( $question->post_title ) . '</option>' . "\n";
							}
						}
						$out .= '</select>' . "\n";
						echo $out;
						?>
						<input type="number" id="dtlms-quiz-question-grade" name="dtlms-quiz-question-grade[]" value="<?php echo isset($quiz_question_grade[$j]) ? $quiz_question_grade[$j] : ''; ?>"  placeholder="<?php echo esc_html__('Grade', 'dtlms'); ?>"   min="1" max="100" required />
                        <input type="number" id="dtlms-quiz-question-negative-grade" name="dtlms-quiz-question-negative-grade[]" value="<?php echo isset($quiz_question_negative_grade[$j]) ? $quiz_question_negative_grade[$j] : ''; ?>" min="0.5" max="100" step="0.5"  placeholder="<?php echo esc_html__('Negative Grade', 'dtlms'); ?>" />
						<span class="dtlms-remove-question"><span class="fa fa-close"></span></span>
                        <span class="fa fa-arrows"></span>
					</div>
				<?php
				$j++;
				}
			}
			?>
            
        </div>
		
        <a href="#" class="dtlms-add-questions custom-button-style"><?php echo esc_html__('Add Questions', 'dtlms'); ?></a>
        
        <p class="dtlms-note"> <?php echo esc_html__('You can add question along with its mark here.','dtlms');?> </p>
        <div class="hr_invisible"></div>
        
        <div id="dtlms-total-marks-container"><?php echo esc_html__('Total Marks : ', 'dtlms'); ?><span><?php echo $quiz_total_grade; ?></span></div>
        
    	<div id="dtlms-questions-to-clone" class="hidden">
        
			<?php
            $out = '';
            $out .= '<select data-placeholder="'.esc_html__('Choose a Question...', 'dtlms').'" style="width:40%;">' . "\n";
            $out .= '<option value=""></option>';
            if ( count( $questions_array ) > 0 ) {
                foreach ($questions_array as $question){
                    $out .= '<option value="' . esc_attr( $question->ID ) . '">' . esc_html( $question->post_title ) . '</option>' . "\n";
                }
            }
            $out .= '</select>' . "\n";
            echo $out;
            ?>
            <input type="number" class="question-grade" value="" placeholder="<?php echo esc_html__('Grade', 'dtlms'); ?>" min="1" max="100" />
            <input type="number" class="question-negative-grade" value="" placeholder="<?php echo esc_html__('Negative Grade', 'dtlms'); ?>" min="0.5" max="100" step="0.5" />
            <span class="dtlms-remove-question"><span class="fa fa-close"></span></span>
            <span class="fa fa-arrows"></span>
        
        </div>
    
	</div>
    
</div>
<!-- Add Questions End -->

<!-- Add Categories -->
<div class="dtlms-custom-box dtlms-add-categories-holder"  <?php echo $addcategories_hide_cls; ?>>

    <div class="dtlms-column dtlms-one-sixth first dtlms-add-quiz">
    
       <label><?php echo esc_html__('Add Categories','dtlms');?></label>

    </div>
    <div class="dtlms-column dtlms-five-sixth">
    
        <?php
        $question_categories = get_categories('taxonomy=question_category&hide_empty=1');     
        ?>      
    
        <div id="dtlms-quiz-categories-container">
        
            <?php 

            $quiz_categories = get_post_meta($post_id, 'quiz-categories', true); 
            $quiz_categories_questions = get_post_meta($post_id, 'quiz-categories-questions', true);            
            $quiz_categories_grade = get_post_meta($post_id, 'quiz-categories-grade', true);
            $quiz_categories_negative_grade = get_post_meta($post_id, 'quiz-categories-negative-grade', true);
            $quiz_total_grade = get_post_meta ( $post_id, "quiz-total-grade",true); 

            $i = 0;
            if(is_array($quiz_categories) && !empty($quiz_categories)) { 
                foreach($quiz_categories as $quiz_category) {

                    echo '<div id="dtlms-category-box">';

                        $max_count = 100;

                        echo '<select id="dtlms-quiz-categories" name="dtlms-quiz-categories[]" data-placeholder="'.esc_html__('Choose Category...', 'dtlms').'" class="dtlms-chosen-select" style="width:40%;">';
                        echo '<option value=""></option>';
                        if ( count( $question_categories ) > 0 ) {
                            foreach ($question_categories as $question_category){
                                echo '<option value="' . esc_attr( $question_category->term_id ) . '"' . selected( $question_category->term_id, $quiz_category, false ) . ' data-count="' . esc_attr( $question_category->category_count ) . '">' . esc_html( $question_category->name ) . '</option>';
                                if($question_category->term_id == $quiz_category) {
                                    $max_count = $question_category->category_count;
                                }
                            }
                        }
                        echo '</select>';

                        $quiz_categories_negative_grade_value = '';
                        if(isset($quiz_categories_negative_grade[$i])) {
                            $quiz_categories_negative_grade_value = $quiz_categories_negative_grade[$i];
                        }

                        echo '<input type="number" id="dtlms-quiz-categories-questions" name="dtlms-quiz-categories-questions[]" value="'.$quiz_categories_questions[$i].'" placeholder="'.esc_html__('Number Of Questions', 'dtlms').'" min="1" max="'.$max_count.'" required />';
                        echo '<input type="number" id="dtlms-quiz-categories-grade" name="dtlms-quiz-categories-grade[]" value="'.$quiz_categories_grade[$i].'" placeholder="'.esc_html__('Grade Per Question', 'dtlms').'" min="1" max="100" required />';
                        echo '<input type="number" id="dtlms-quiz-categories-negative-grade" name="dtlms-quiz-categories-negative-grade[]" value="'.$quiz_categories_negative_grade_value.'" placeholder="'.esc_html__('Negative Grade Per Question', 'dtlms').'" min="0" max="100" />';
                        echo '<span class="dtlms-remove-category"><span class="fa fa-close"></span></span>';
                        echo '<span class="fa fa-arrows"></span>';

                     echo '</div>';

                     $i++;

                }
            }

            ?>
            
        </div>
        
        <a href="#" class="dtlms-add-categories custom-button-style"><?php echo esc_html__('Add Categories', 'dtlms'); ?></a>
        
        <p class="dtlms-note"> 
            <?php echo esc_html__('You can add categories along with number of questions to pick from each category and also each question grade.','dtlms');?>
            <?php echo esc_html__('No need to provide negative symbol for negative grading.','dtlms');?>   
        </p>
        <div class="hr_invisible"></div>
        
        <div id="dtlms-total-marks-container">
            <?php echo esc_html__('Total Marks : ', 'dtlms'); ?><span><?php echo $quiz_total_grade; ?></span>
        </div>
        
        <div id="dtlms-categories-to-clone" class="hidden">
        
            <?php
            echo '<select data-placeholder="'.esc_html__('Choose Category...', 'dtlms').'" style="width:40%;">' . "\n";
                echo '<option value=""></option>';
                if ( count( $question_categories ) > 0 ) {
                    foreach ($question_categories as $question_category){
                       echo '<option value="' . esc_attr( $question_category->term_id ) . '" data-count="' . esc_attr( $question_category->category_count ) . '">' . esc_html( $question_category->name ) . '</option>';
                    }
                }
            echo '</select>';
            ?>
            <input type="number" class="quiz-category-questions" placeholder="<?php echo esc_html__('Number Of Questions', 'dtlms'); ?>" value="" min="1" max="100" />
            <input type="number" class="quiz-category-grade" placeholder="<?php echo esc_html__('Grade Per Question', 'dtlms'); ?>" value="" min="1" max="100" />
            <input type="number" class="quiz-category-negative-grade" placeholder="<?php echo esc_html__('Negative Grade Per Question', 'dtlms'); ?>" value="" min="0" max="100" />
            <span class="dtlms-remove-category"><span class="fa fa-close"></span></span>
            <span class="fa fa-arrows"></span>
        
        </div>
    
    </div>
    
</div>
<!-- Add Categories End -->

<!-- Show single question at a time & Show correct answer and answer explanation -->
<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-half first">
    
        <div class="dtlms-column dtlms-one-third first">
           <label><?php echo esc_html__('Show Questions One At A Time','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php
			$quiz_questions_onebyone = get_post_meta ( $post_id, "quiz-questions-onebyone",true);
            $switchclass = ($quiz_questions_onebyone != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($quiz_questions_onebyone != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="quiz-questions-onebyone" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="quiz-questions-onebyone" class="hidden" type="checkbox" name="quiz-questions-onebyone" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php echo esc_html__('Check this option to show one question at a time.','dtlms');?> </p>
        </div>

	</div>
	<div class="dtlms-column dtlms-one-half">
    
        <div class="dtlms-column dtlms-one-third first">
            <label><?php echo esc_html__('Show Correct Answer and Answer Explanation','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php
			$quiz_correctanswer_and_answerexplanation = get_post_meta ( $post_id, "quiz-correctanswer-and-answerexplanation",true);
            $switchclass = ($quiz_correctanswer_and_answerexplanation != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($quiz_correctanswer_and_answerexplanation != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="quiz-correctanswer-and-answerexplanation" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="quiz-correctanswer-and-answerexplanation" class="hidden" type="checkbox" name="quiz-correctanswer-and-answerexplanation" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php echo esc_html__('Check this option to show correct answer and answer explanation immediately if user answer is wrong. Applicable for "Show Questions One By One" option.','dtlms');?> </p>
        </div>

	</div>
    
</div>
<!-- Show single question at a time & Show correct answer and answer explanation End -->

<!-- Show question counter -->
<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-half first">
    
        <div class="dtlms-column dtlms-one-third first">
           <label><?php echo esc_html__('Show Question Counter','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php
			$quiz_questions_counter = get_post_meta ( $post_id, "quiz-questions-counter",true);
            $switchclass = ($quiz_questions_counter != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($quiz_questions_counter != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="quiz-questions-counter" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="quiz-questions-counter" class="hidden" type="checkbox" name="quiz-questions-counter" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> 
                <?php echo esc_html__('Check this option to show current question number in sidebar.','dtlms'); ?> 
                <?php echo "<br>"; ?>
                <?php echo esc_html__('This option will work only with "Show Questions One By One" option','dtlms');?>
            </p>
        </div>

	</div>
	<div class="dtlms-column dtlms-one-half">
    
	</div>
    
</div>
<!-- Show question counter End -->

<div class="dtlms-custom-box">
    <p class="dtlms-note"> 
        <?php echo '<strong>'.esc_html__('Notes', 'dtlms').'</strong>'; ?>
        <?php echo esc_html__( 'If you are going to calculate grade its necessary to add mark for each questions.', 'dtlms' ); ?>
    </p>
</div>