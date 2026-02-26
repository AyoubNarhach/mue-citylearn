<?php
global $post;
$post_id = $post->ID;
echo '<input type="hidden" name="dtlms_lessons_meta_nonce" value="'.wp_create_nonce('dtlms_lessons_nonce').'" />'; 

$current_user = wp_get_current_user();
$current_user_id = $current_user->ID;
?>

<!-- Free Lesson -->
<div class="dtlms-custom-box">
    <div class="dtlms-column dtlms-one-sixth first">
        <label><?php esc_html_e( 'Unlock Lesson','dtlms');?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <?php
        $free_lesson = get_post_meta ( $post_id, 'free-lesson', true );
        $switchclass = ($free_lesson == true) ? 'checkbox-switch-on' : 'checkbox-switch-off';
        $checked = ($free_lesson == true) ? ' checked="checked"' : '';
        ?>        
        <div data-for="free-lesson" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
        <input id="free-lesson" class="hidden" type="checkbox" name="free-lesson" value="true" <?php echo $checked;?>/>
        <p class="dtlms-note"> <?php echo esc_html__('YES! to unlock this lesson, so that you can use this lesson as preview. It won\'t be affected by "Curriculum Completion Lock" in course settings.','dtlms');?> </p>
    </div>
</div>
<!-- Free Lesson End -->

<!-- Lesson Curriculum -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Curriculum','dtlms'); ?></label>
    </div>

    <div class="dtlms-column dtlms-five-sixth">
    
        <div id="dtlms-curriculum-items-container">
        
            <?php 
            $lesson_curriculum = get_post_meta ( $post_id, 'lesson-curriculum', true);

            if(isset($lesson_curriculum) && is_array($lesson_curriculum)) {

                foreach($lesson_curriculum as $curriculum) {
                    ?>
                    <div id="dtlms-curriculum-section-item">
                        <?php
                        if(is_numeric($curriculum)) {

                            $args = array ();

                            if(get_post_type($curriculum) == 'dtlms_lessons') {

                                echo '<label>'.esc_html__('Lessons', 'dtlms').'</label>';

                                $args = array (
                                                'post_type'=> 'dtlms_lessons',
                                                'numberposts'=> -1,
                                                'suppress_filters'  => FALSE,
                                            );

                            }
                            
                            if(get_post_type($curriculum) == 'dtlms_quizzes') {

                                echo '<label>'.esc_html__('Quizzes', 'dtlms').'</label>';

                                $args = array (
                                                'post_type'=> 'dtlms_quizzes',
                                                'numberposts'=> -1,
                                                'suppress_filters'  => FALSE,
                                            );
                                
                            }

                            if(get_post_type($curriculum) == 'dtlms_assignments') {

                                echo '<label>'.esc_html__('Assignments', 'dtlms').'</label>';

                                $args = array (
                                                'post_type'=> 'dtlms_assignments',
                                                'numberposts'=> -1,
                                                'suppress_filters'  => FALSE,
                                            );
                                
                            }        

                            if ( !in_array( 'administrator', (array) $current_user->roles ) ) {
                                $args['author'] = $current_user_id;
                            }                                                  
                                
                            $post_types = get_posts($args);

                            echo '<select data-placeholder="'.esc_html__('Select...', 'dtlms').'" class="cc-select" id="lesson-curriculum" name="lesson-curriculum[]">';
                                foreach ( $post_types as $post_type ){
                                    echo '<option value="'.$post_type->ID.'" '.selected( $post_type->ID, $curriculum, false ).'>' . $post_type->post_title . '</option>';
                                }
                            echo '</select>';

                            wp_reset_postdata();

                        } else {

                            echo '<label>'.esc_html__('Section', 'dtlms').'</label>';
                            echo '<input type="text" value="'.$curriculum.'" id="lesson-curriculum" name="lesson-curriculum[]" />';

                        }
                        ?>
                        <span class="dtlms-remove-curriculum-item"><span class="fa fa-close"></span></span>
                        <span class="fa fa-arrows"></span>
                    </div>
                    <?php
                }

            }
            ?>
            
        </div>
        
        <a href="#" class="dtlms-add-curriculum section custom-button-style" data-curriculumtype="lesson"><?php esc_html_e('Add Section', 'dtlms'); ?></a>
        <a href="#" class="dtlms-add-curriculum quiz custom-button-style" data-curriculumtype="lesson"><?php esc_html_e('Add Quiz', 'dtlms'); ?></a>
        <a href="#" class="dtlms-add-curriculum assignment custom-button-style" data-curriculumtype="lesson"><?php esc_html_e('Add Assignment', 'dtlms'); ?></a>
        
        <p class="dtlms-note"> 
            <?php 
            esc_html_e('Add sections, lessons, quiz, lessons here. Make sure you have created them already.','dtlms'); 
            echo "<br>"; 
            esc_html_e('Leave empty if you don\'t like to use sub items.','dtlms');
            echo "<br>"; 
            esc_html_e('Make sure you haven\'t repeated any curriculum.', 'dtlms');            
            ?> 
        </p>
        
        <div id="dtlms-curriculum-section-to-clone" class="hidden">
            
            <label><?php echo esc_html__('Section', 'dtlms'); ?></label>

            <?php
            echo '<input type="text" placeholder="'.esc_html__('Section Title', 'dtlms').'" />';
            ?>

            <span class="dtlms-remove-curriculum-item"><span class="fa fa-close"></span></span>
            <span class="fa fa-arrows"></span>
        
        </div>

        <div id="dtlms-curriculum-lesson-to-clone" class="hidden">
            
            <label><?php echo esc_html__('Lesson', 'dtlms'); ?></label>

            <?php

            $args = array (
                            'post_type'=> 'dtlms_lessons',
                            'numberposts'=> -1,
                            'suppress_filters'  => FALSE,
                        );
            if ( !in_array( 'administrator', (array) $current_user->roles ) ) {
                $args['author'] = $current_user_id;
            }             
            $lessons = get_posts($args);

            echo '<select data-placeholder="'.esc_html__('Select Lesson...', 'dtlms').'" class="cc-select">';
            foreach ( $lessons as $lesson ){
                echo '<option value="' . $lesson->ID . '">' . $lesson->post_title . '</option>';
            }
            echo '</select>';

            wp_reset_postdata();

            ?>

            <span class="dtlms-remove-curriculum-item"><span class="fa fa-close"></span></span>
            <span class="fa fa-arrows"></span>
        
        </div>

        <div id="dtlms-curriculum-quiz-to-clone" class="hidden">
            
            <label><?php echo esc_html__('Quiz', 'dtlms'); ?></label>

            <?php

            $args = array (
                            'post_type'=> 'dtlms_quizzes',
                            'numberposts'=> -1,
                            'suppress_filters'  => FALSE,
                        );
            if ( !in_array( 'administrator', (array) $current_user->roles ) ) {
                $args['author'] = $current_user_id;
            }

            $quizzes = get_posts($args);

            echo '<select data-placeholder="'.esc_html__('Select Quiz...', 'dtlms').'" class="cc-select">';
            foreach ( $quizzes as $quiz ){
                echo '<option value="' . $quiz->ID . '">' . $quiz->post_title . '</option>';
            }
            echo '</select>';

            wp_reset_postdata();

            ?>

            <span class="dtlms-remove-curriculum-item"><span class="fa fa-close"></span></span>
            <span class="fa fa-arrows"></span>
        
        </div>

        <div id="dtlms-curriculum-assignment-to-clone" class="hidden">
            
            <label><?php echo esc_html__('Assignment', 'dtlms'); ?></label>

            <?php

            $args = array (
                            'post_type'=> 'dtlms_assignments',
                            'numberposts'=> -1,
                            'suppress_filters'  => FALSE,
                        );
            if ( !in_array( 'administrator', (array) $current_user->roles ) ) {
                $args['author'] = $current_user_id;
            }

            $assignments = get_posts($args);

            echo '<select data-placeholder="'.esc_html__('Select Assignment...', 'dtlms').'" class="cc-select">';
            foreach ( $assignments as $assignment ) {
                echo '<option value="' . $assignment->ID . '">' . $assignment->post_title . '</option>';
            }
            echo '</select>';

            wp_reset_postdata();

            ?>

            <span class="dtlms-remove-curriculum-item"><span class="fa fa-close"></span></span>
            <span class="fa fa-arrows"></span>
        
        </div>

    </div>
    
</div>
<!-- Lesson Curriculum End -->

<div class="dtlms-custom-box">

    <!-- Drip Duration -->
    <div class="dtlms-column dtlms-one-half first">

        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Duration', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $duration = get_post_meta ( $post_id, 'duration', true ); ?>
            <input type="number" id="duration" name="duration" value="<?php echo $duration; ?>" min="0" >
            <p class="dtlms-note"> <?php esc_html_e('Add duration here.','dtlms');?> </p>
        </div>

    </div>
    <!-- Drip Duration End -->

    <!-- Drip Duration Parameter -->
    <div class="dtlms-column dtlms-one-half">

        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Duration Parameter','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $duration_parameter = get_post_meta ( $post_id, 'duration-parameter', true );
            $durationparameters = array ('60' => 'Minutes', '3600' => 'Hours', '86400' => 'Days', '604800' => 'Weeks', '2592000' => 'Months');
    
            echo '<select name="duration-parameter" data-placeholder="'.esc_html__('Select Duration Parameter...', 'dtlms').'" class="dtlms-chosen-select">';
            echo '<option value="">' . esc_html__( 'None', 'dtlms' ) . '</option>';
            foreach ($durationparameters as $durationparameter_key => $durationparameter){
                echo '<option value="' . esc_attr( $durationparameter_key ) . '"' . selected( $durationparameter_key, $duration_parameter, false ) . '>' . esc_html( $durationparameter ) . '</option>';
            }
            echo '</select>' ;
            ?>
            <p class="dtlms-note"> <?php esc_html_e('Choose duration parameter here.','dtlms');?> </p>
        </div>

    </div>
    <!-- Drip Duration Parameter End -->

</div>

<div class="dtlms-custom-box">

    <!-- Maximum Mark -->
    <div class="dtlms-column dtlms-one-half first">
        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Maximum Mark', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $lesson_maximum_mark = get_post_meta ( $post_id, 'lesson-maximum-mark', true ); ?>
            <input id="lesson-maximum-mark" name="lesson-maximum-mark" type="number" value="<?php echo $lesson_maximum_mark; ?>" style="width:10%;" min="1" />
            <p class="dtlms-note"> <?php esc_html_e('Maximum mark for lesson. Default value is 100.','dtlms');?> </p>
        </div>
    </div>
    <!-- Maximum Mark End -->

    <!-- Pass Percentage -->

    <div class="dtlms-column dtlms-one-half">            
    </div>
    <!-- Pass Percentage End -->

</div>