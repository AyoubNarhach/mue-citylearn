<?php

function dtlms_settings_load_import_content() {

	$output = '';

	$output .= '<div class="dtlms-settings-import-container">';
		
		$output .= '<p>';
			$output .= '<label>'.esc_html__('Import Option', 'dtlms').'</label>';
			$output .= '<select name="dtlms-import-type" class="dtlms-import-type">';
					$import_types = array('question' => 'Question', 'quiz' => 'Quiz');
					foreach ($import_types as $import_type => $import_type_label){
						$output .= '<option value="'.$import_type.'">'.$import_type_label.'</option>';
					}
			$output .= '</select>';
		$output .= '</p>';

		$output .= '<input type="button" name="dtlms-chooseupload-file-button" class="dtlms-chooseupload-file-button" value="'.esc_html__('Choose / Upload File', 'dtlms').'">';

		$output .= '<input type="text" name="dtlms-import-file" class="dtlms-import-file" style="width:30%;" value="">';

		$output .= '<a href="#" class="dtlms-button dtlms-import-file-button small">'.esc_html__('Import', 'dtlms').'</a>';
	
	$output .= '</div>';

	$output .= dtlms_generate_loader_html(true);

	$output .= '<div class="dtlms-settings-import-output-container"></div>';

	$output .= '<div class="dtlms-hr-invisible"></div>';

	$output .= '<h4><strong>'.esc_html__('Question CSV Sample File Format', 'dtlms').'</strong></h4>';

	$output .= '<div class="dtlms-settings-data-holder">';

		$output .= '<table class="dtlms-custom-table" style="width:100%">
						<tr>
							<th>'.esc_html__('Title', 'dtlms').'</th>
							<th>'.esc_html__('Question', 'dtlms').'</th>
							<th>'.esc_html__('Question Type', 'dtlms').' (multiple-choice,multiple-choice-image,multiple-correct,boolean,gap-fill,single-line,multi-line)</th>
							<th>'.esc_html__('Answers', 'dtlms').' ( Separate answers with "|" )</th>
							<th>'.esc_html__('Correct Answer', 'dtlms').' ( Separate answers with "|" )</th>
							<th>'.esc_html__('Answer Hint', 'dtlms').'</th>
							<th>'.esc_html__('Answer Explanation', 'dtlms').'</th>
						</tr>
						<tr>
							<td>Question 1</td>
							<td><h4>In this problem, what is 6?</h4>2 X 6 = 12</td>
							<td>multiple-choice</td>
							<td>Sum|Addend|Factor|Product</td>
							<td>Product</td>
							<td>Shop</td>
							<td><p>In this problem, <b>6 is an product.</b></p></td>			    
						</tr>
						<tr>
							<td>Question 2</td>
							<td>Out of which are all contients?</td>
							<td>multiple-correct</td>
							<td>Asia|India|Africa|Mexico|America</td>
							<td>Asia|Africa|America</td>
							<td>Continent</td>
							<td>Africa, the Americas, Antarctica, Asia, Australia together with Oceania, and Europe are considered to be Continents.</td>		    
						</tr>
					</table>';

	$output .= '</div>';

	$output .= '<div class="dtlms-hr-invisible"></div>';

	$output .= '<h4><strong>'.esc_html__('Quiz CSV Sample File Format', 'dtlms').'</strong></h4>';

	$output .= '<div class="dtlms-settings-data-holder"">';

		$output .= '<table class="dtlms-custom-table" style="width:100%">
						<tr>
							<th>'.esc_html__('Title', 'dtlms').'</th>
							<th>'.esc_html__('Content', 'dtlms').'</th>
							<th>'.esc_html__('Free Quiz', 'dtlms').'  ( true / false )</th>
							<th>'.esc_html__('Subttile', 'dtlms').'</th>
							<th>'.esc_html__('Duration', 'dtlms').' ( Number )</th>
							<th>'.esc_html__('Duration Parameter', 'dtlms').' ( seconds, minutes, hours, days, weeks, months, years )</th>
							<th>'.esc_html__('Number of Retakes', 'dtlms').' ( Number )</th>
							<th>'.esc_html__('Post Quiz Message', 'dtlms').'</th>
							<th>'.esc_html__('Randomize Questions', 'dtlms').' ( true / false )</th>
							<th>'.esc_html__('Enable Auto Evaluation', 'dtlms').' ( true / false )</th>
							<th>'.esc_html__('Mark As Graded In Auto Evaluation', 'dtlms').' ( true / false )</th>
							<th>'.esc_html__('Question Type', 'dtlms').'  ( add-questions / add-categories )</th>
							<th>'.esc_html__('Add Questions', 'dtlms').' ( questionno|questionno#questionmark|questionmark#questionnegativemark|questionnegativemark )</th>
							<th>'.esc_html__('Add Categories', 'dtlms').' ( categoryid|categoryid#numberofquestionstopick|numberofquestionstopick#gradeperquestion|gradeperquestion#negativegradeperquestion|negativegradeperquestion )</th>
							<th>'.esc_html__('Pass Percentage', 'dtlms').' ( Number )</th>
							<th>'.esc_html__('Show Questions One By One', 'dtlms').' ( true / false )</th>
							<th>'.esc_html__('Show Correct Answer and Answer Explanation', 'dtlms').' ( true / false )</th>
							<th>'.esc_html__('Show Question Counter', 'dtlms').' ( true / false )</th>
						</tr>
						<tr>
							<td>Quiz 1</td>
							<td>Consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus.Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</td>
							<td>false</td>
							<td>Porttitor eu 123</td>
							<td>45</td>
							<td>minutes</td>
							<td>10</td>
							<td>Aenean leo ligula, porttitor eu</td>
							<td>true</td>
							<td>true</td>
							<td>false</td>
							<td>add-questions</td>
							<td>1001|1002#10|10#-1|-1</td>
							<td></td>
							<td>90</td>
							<td>true</td>
							<td>true</td>
							<td>true</td>
						</tr>
							<td>Quiz 2</td>
							<td>Consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus.Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</td>
							<td>false</td>
							<td>Aliquam lorem ante</td>
							<td>30</td>
							<td>minutes</td>
							<td>10</td>
							<td>Aenean leo ligula, porttitor eu</td>
							<td>true</td>
							<td>true</td>
							<td>false</td>
							<td>add-categories</td>
							<td></td>
							<td>20|21#4|6#10|10#-1|-1</td>
							<td>90</td>
							<td>true</td>
							<td>true</td>
							<td>true</td>
						</tr>
					</table>';

	$output .= '</div>';	
	
	return $output;

}

function myFilter($var){
    return ($var !== NULL && $var !== FALSE && $var !== "");
}

add_action( 'wp_ajax_dtlms_process_imported_file', 'dtlms_process_imported_file' );
add_action( 'wp_ajax_nopriv_dtlms_process_imported_file', 'dtlms_process_imported_file' );
function dtlms_process_imported_file() {
	
	$import_type = $_REQUEST['import_type'];
	$import_file = $_REQUEST['import_file'];

	$extension = pathinfo($import_file, PATHINFO_EXTENSION);

	if($extension == 'csv') {

		if(( $fh = @fopen($import_file, 'r')) !== false) {
			
			$i = 0;
			
			while(( $row = fgetcsv($fh)) !== false) { 

				if($i > 0) {

					// Question
					if($import_type == 'question') {

						$title = $row[0];
						$content = $row[1];
						$question_type = $row[2];
						$answers = $row[3];
						$correct_answer = $row[4];
						$answer_hint = $row[5];
						$answer_explanation = $row[6];

						$question_post = array(
							'post_title' => $title,
							'post_content'  => $content,
							'post_status' => 'publish',
							'post_type' => 'dtlms_questions',
							'post_author' => 1,
						);
						$question_id = wp_insert_post($question_post);
						
						update_post_meta ( $question_id, 'question-type', dtlms_wp_kses($question_type) );

						if($question_type == 'multiple-choice') {

							$answers = explode('|', $answers);
							$answers = array_filter($answers, array($this, 'myFilter'));

							update_post_meta ( $question_id, 'multichoice-answers', $answers );

							update_post_meta ( $question_id, 'multichoice-correct-answer', dtlms_wp_kses($correct_answer) );

						} else if($question_type == 'multiple-choice-image') {

							$answers = explode('|', $answers);
							$answers = array_filter($answers, array($this, 'myFilter'));

							update_post_meta ( $question_id, 'multicorrect-answers', $answers );

							update_post_meta ( $question_id, 'multichoice-image-correct-answer', dtlms_wp_kses($correct_answer) );

						} else if($question_type == 'multiple-correct') {

							$answers = explode('|', $answers);
							update_post_meta ( $question_id, 'multicorrect-answers', array_filter($answers) );	

							$correct_answer = explode('|', $correct_answer);
							$correct_answer = array_filter($correct_answer, array($this, 'myFilter'));

							update_post_meta ( $question_id, 'multicorrect-correct-answer', $correct_answer );	

						} else if($question_type == 'boolean') {

							update_post_meta ( $question_id, 'boolean-answer', dtlms_wp_kses(strtolower($correct_answer)) );

						} else if($question_type == 'gap-fill') {

							$answers = explode('|', $answers);

							update_post_meta ( $question_id, 'text-before-gap', dtlms_wp_kses($answers[0]) );
							update_post_meta ( $question_id, 'text-after-gap', dtlms_wp_kses($answers[1]) );

							update_post_meta ( $question_id, 'gap', dtlms_wp_kses($correct_answer) );

						} else if($question_type == 'single-line') {

							update_post_meta ( $question_id, 'singleline-answer', dtlms_wp_kses($correct_answer) );

						} else if($question_type == 'multi-line') {

							update_post_meta ( $question_id, 'multiline-answer', dtlms_wp_kses($correct_answer) );

						}

						update_post_meta ( $question_id, 'answer-hint', dtlms_wp_kses($answer_hint) );
						update_post_meta ( $question_id, 'answer-explanation', dtlms_wp_kses($answer_explanation) );

					}

					// Quiz
					if($import_type == 'quiz') {
						
						$title = $row[0];
						$content = $row[1];
						$freequiz = $row[2];
						$subtitle = $row[3];
						$duration = $row[4];
						$duration_parameter = $row[5];
						$number_of_retakes = $row[6];
						$post_quiz_message = $row[7];
						$randomize_question = $row[8];
						$enable_auto_evaluation = $row[9];
						$marks_as_graded_in_auto_evaluation = $row[10];
						$question_type = $row[11];
						$add_questions = $row[12];
						$add_categories = $row[13];
						$pass_percentage = $row[14];
						$show_questions_onebyone = $row[15];
						$show_correct_answer_explanation = $row[16];
						$show_question_counter = $row[17];


						$durationparameters = array ('seconds' => '1', 'minutes' => '60', 'hours' => '3600', 'days' => '86400', 'weeks' => '604800', 'months' => '2592000', 'years' => '31536000');

						$duration_parameter = $durationparameters[$duration_parameter];

						if($question_type == 'add-questions') {
							$add_questions = explode('#', $add_questions);
							$questions = explode('|', $add_questions[0]);
							$grade = explode('|', $add_questions[1]);
							$negative_grade = explode('|', $add_questions[2]);
						}

						if($question_type == 'add-categories') {
							$add_categories = explode('#', $add_categories);
							$categories = explode('|', $add_categories[0]);
							$categories_questions = explode('|', $add_categories[1]);
							$grade = explode('|', $add_categories[2]);
							$negative_grade = explode('|', $add_categories[3]);
						}

						$total_grade = array_sum($grade);


						$quiz_post = array(
							'post_title' => $title,
							'post_content'  => $content,
							'post_status' => 'publish',
							'post_type' => 'dtlms_quizzes',
							'post_author' => 1,
						);
						$quiz_id = wp_insert_post($quiz_post);					
						
						update_post_meta ( $quiz_id, 'free-quiz', dtlms_wp_kses($freequiz) );
						update_post_meta ( $quiz_id, 'quiz-subtitle', dtlms_wp_kses($subtitle) );
						update_post_meta ( $quiz_id, 'duration', dtlms_wp_kses($duration) );
						update_post_meta ( $quiz_id, 'duration-parameter', dtlms_wp_kses($duration_parameter) );
						update_post_meta ( $quiz_id, 'quiz-retakes', dtlms_wp_kses($number_of_retakes) );
						update_post_meta ( $quiz_id, 'quiz-postmsg', dtlms_wp_kses($post_quiz_message) );

						if(strtolower(trim($randomize_question)) == 'true') {
							update_post_meta ( $quiz_id, 'quiz-randomize-questions', 'true' );
						}
						if(strtolower(trim($enable_auto_evaluation)) == 'true') {
							update_post_meta ( $quiz_id, 'quiz-auto-evaluation', 'true' );
						}
						if(strtolower(trim($marks_as_graded_in_auto_evaluation)) == 'true') {
							update_post_meta ( $quiz_id, 'quiz-markasgraded-in-autoevaluation', 'true' );
						}

						update_post_meta ( $quiz_id, 'quiz-question-type', dtlms_wp_kses($question_type) );

						if($question_type == 'add-questions') {
							$questions = array_filter($questions, array($this, 'myFilter'));
							$grade = array_filter($grade, array($this, 'myFilter'));
							$negative_grade = array_filter($negative_grade, array($this, 'myFilter'));

							update_post_meta ( $quiz_id, 'quiz-question', $questions );
							update_post_meta ( $quiz_id, 'quiz-question-grade', $grade );
							update_post_meta ( $quiz_id, 'quiz-question-negative-grade', $negative_grade );
						}
						if($question_type == 'add-categories') {
							$categories = array_filter($categories, array($this, 'myFilter'));
							$categories_questions = array_filter($categories_questions, array($this, 'myFilter'));
							$grade = array_filter($grade, array($this, 'myFilter'));
							$negative_grade = array_filter($negative_grade, array($this, 'myFilter'));

							update_post_meta ( $quiz_id, 'quiz-categories', $categories );
							update_post_meta ( $quiz_id, 'quiz-categories-questions', $categories_questions );
							update_post_meta ( $quiz_id, 'quiz-categories-grade', $grade );
							update_post_meta ( $quiz_id, 'quiz-categories-negative-grade', $negative_grade );
						}

						update_post_meta ( $quiz_id, 'quiz-total-grade', dtlms_wp_kses($total_grade) );
						update_post_meta ( $quiz_id, 'quiz-pass-percentage', dtlms_wp_kses($pass_percentage) );

						if(strtolower(trim($show_questions_onebyone)) == 'true') {
							update_post_meta ( $quiz_id, 'quiz-questions-onebyone', 'true' );
						}
						if(strtolower(trim($show_correct_answer_explanation)) == 'true') {
							update_post_meta ( $quiz_id, 'quiz-correctanswer-and-answerexplanation', 'true' );
						}
						if(strtolower(trim($show_question_counter)) == 'true') {
							update_post_meta ( $quiz_id, 'quiz-questions-counter', 'true' );
						}

					}				
					
				}

				$i++;

			}

		}

		echo '<p class="dtlms-note"><strong>'.esc_html__('Data imported successfully!', 'dt_themes').'</strong></p>';

	} else {

		echo '<p class="dtlms-note"><strong>'.esc_html__('Only .csv file format is allowed!', 'dt_themes').'</strong></p>';

	}

	die();

}

?>