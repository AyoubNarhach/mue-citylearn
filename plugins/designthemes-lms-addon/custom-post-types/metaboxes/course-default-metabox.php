<?php
global $post;
$post_id = $post->ID;

echo '<input type="hidden" name="dtlms_courses_meta_nonce" value="'.wp_create_nonce('dtlms_courses_nonce').'" />';

$current_user = wp_get_current_user();
$current_user_id = $current_user->ID;

?>

<div class="dtlms-custom-box">

    <!-- Page Layout -->
    <div class="dtlms-column dtlms-one-half first">

        <div class="dtlms-column dtlms-one-third first"><?php echo esc_html__( 'Page Layout', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $page_layout = get_post_meta($post_id, 'page-layout', true);
            $page_layout = ($page_layout != '') ? $page_layout : 'type1';

            $pagelayouts = array ('type1' => 'Type 1', 'type2' => 'Type 2', 'type3' => 'Type 3', 'type4' => 'Type 4', 'type5' => 'Type 5');
    
            echo '<select name="page-layout" data-placeholder="'.esc_html__('Choose Page Layout...', 'dtlms').'" class="dtlms-chosen-select">';
                foreach ($pagelayouts as $pagelayout_key => $pagelayout) {
                    echo '<option value="'.esc_attr($pagelayout_key).'" '.selected($pagelayout_key, $page_layout, false).'>'.esc_html($pagelayout).'</option>';
                }
            echo '</select>';
            ?>
        </div>

    </div>
    <!-- Page Layout End -->

    <div class="dtlms-column dtlms-one-half"></div>

</div>

<!-- Course Curriculum -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Curriculum','dtlms'); ?></label>
    </div>

    <div class="dtlms-column dtlms-five-sixth">
    
        <div id="dtlms-curriculum-items-container">
        
            <?php 
            $course_curriculum = get_post_meta ( $post_id, 'course-curriculum', true);

            if(isset($course_curriculum) && is_array($course_curriculum)) {

                foreach($course_curriculum as $curriculum) {
                    ?>
                    <div id="dtlms-curriculum-section-item">
                        <?php
                        if(is_numeric($curriculum)) {

                            if(get_post_type($curriculum) == 'dtlms_lessons') {

                                echo '<label>'.esc_html__('Lesson', 'dtlms').'</label>';

                                $args = array (
                                                'post_type'=> 'dtlms_lessons',
                                                'numberposts'=> -1,
                                                'suppress_filters'  => FALSE,
                                            );

                            }

                            if(get_post_type($curriculum) == 'dtlms_quizzes') {

                                echo '<label>'.esc_html__('Quiz', 'dtlms').'</label>';

                                $args = array (
                                                'post_type'=> 'dtlms_quizzes',
                                                'numberposts'=> -1,
                                                'suppress_filters'  => FALSE,
                                            );
                                
                            }

                            if(get_post_type($curriculum) == 'dtlms_assignments') {

                                echo '<label>'.esc_html__('Assignment', 'dtlms').'</label>';

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

                            echo '<select data-placeholder="'.esc_html__('Select...', 'dtlms').'" class="course-curriculum-chosen" id="course-curriculum" name="course-curriculum[]">';
                                foreach ( $post_types as $post_type ){
                                    echo '<option value="'.$post_type->ID.'" '.selected( $post_type->ID, $curriculum, false ).'>' . $post_type->post_title . '</option>';
                                }
                            echo '</select>';

                            wp_reset_postdata();

                        } else {

                            echo '<label>'.esc_html__('Section', 'dtlms').'</label>';
                            echo '<input type="text" value="'.$curriculum.'" id="course-curriculum" name="course-curriculum[]" />';

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
        
        <a href="#" class="dtlms-add-curriculum section custom-button-style" data-curriculumtype="course"><?php esc_html_e('Add Section', 'dtlms'); ?></a>
        <a href="#" class="dtlms-add-curriculum lesson custom-button-style" data-curriculumtype="course"><?php esc_html_e('Add Lesson', 'dtlms'); ?></a>
        <a href="#" class="dtlms-add-curriculum quiz custom-button-style" data-curriculumtype="course"><?php esc_html_e('Add Quiz', 'dtlms'); ?></a>
        <a href="#" class="dtlms-add-curriculum assignment custom-button-style" data-curriculumtype="course"><?php esc_html_e('Add Assignment', 'dtlms'); ?></a>
        
        <p class="dtlms-note"> 
            <?php 
            esc_html_e('Add sections, lessons, quiz, assignments here. Make sure you have created them already.', 'dtlms');
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
<!-- Course Curriculum End -->

<div class="dtlms-custom-box">

    <!-- Co Instructors -->

        <div class="dtlms-column dtlms-one-sixth first">
            <label><?php esc_html_e('Co Instructors','dtlms'); ?></label>
        </div>

        <div class="dtlms-column dtlms-five-sixth">
            <?php
            $coinstructors = get_post_meta ( $post_id, 'coinstructors', true );
            $coinstructors = (isset($coinstructors) && !empty($coinstructors)) ? $coinstructors : array();

            echo '<select id="coinstructors" name="coinstructors[]" data-placeholder="'.__('Select Co-Instructors...', 'dtlms').'" class="dtlms-chosen-select" multiple="multiple">';

                    $out .= '<option value="">' . esc_html__( 'None', 'dtlms' ) . '</option>';
                    $args = array( 'role' => 'instructor' );
                    $user_query = new WP_User_Query( $args );
                    if ( !empty( $user_query->results ) ) {
                        foreach ( $user_query->results as $user ) {
                            $selected = in_array($user->ID , $coinstructors ) ? 'selected="selected"' : '';
                            echo '<option value="' . esc_attr( $user->ID ) . '"' . $selected . '>' . esc_html( $user->display_name ) . '</option>';
                        }    
                    }  

            echo '</select>';
            ?>
            <p class="dtlms-note"> <?php esc_html_e('Add co instructors for this course.', 'dtlms');?> </p>
        </div>
    <!-- Co Instructors End -->

</div>



<div class="dtlms-custom-box">

	<!-- Featured Course -->
	<div class="dtlms-column dtlms-one-half first">
    
        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Featured Course', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $current = get_post_meta ( $post_id, 'featured-course', true);
            $switchclass = ( $current === "true") ? 'checkbox-switch-on' :'checkbox-switch-off';	
            $checked = ( $current === "true") ? ' checked="checked" ' : '';
            ?>
            <div data-for="featured-course" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="featured-course" class="hidden" type="checkbox" name="featured-course" value="true" <?php echo $checked;?>/>
            <p class="dtlms-note"> <?php esc_html_e('YES! to make this as featured course.', 'dtlms');?> </p>
        </div>
        
    </div>
    <!-- Featured Course End -->

    <!-- Show Social Share -->
    <div class="dtlms-column dtlms-one-half">

        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Social Share Items','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $socialshare_items = get_post_meta ( $post_id, 'socialshare-items', true );
            $socialshare_items = (isset($socialshare_items) && !empty($socialshare_items)) ? $socialshare_items : array();

            $socialshare_array = array('facebook' => 'Facebook', 'delicious' => 'Delicious', 'digg' => 'Digg', 'stumbleupon' => 'StumbleUpon', 'twitter' => 'Twitter', 'googleplus' => 'Google Plus', 'linkedin' => 'LinkedIn', 'pinterest' => 'Pinterest');
    
            $out = '';
            $out .= '<select id="socialshare-items" name="socialshare-items[]" data-placeholder="'.esc_html__('Select Social Share Items...', 'dtlms').'" class="dtlms-chosen-select" multiple="multiple">' . "\n";
            $out .= '<option value="">' . esc_html__( 'None', 'dtlms' ) . '</option>';
            if ( count( $socialshare_array ) > 0 ) {
                foreach ($socialshare_array as $socialshare_key => $socialshare){
                    $selected = in_array( $socialshare_key , $socialshare_items ) ? 'selected="selected"' : '';
                    $out .= '<option value="' . esc_attr( $socialshare_key ) . '"' . $selected . '>' . esc_html( $socialshare ) . '</option>' . "\n";
                }
            }
            $out .= '</select>' . "\n";
            echo $out;
            ?>
            <p class="dtlms-note"> <?php esc_html_e('Choose social share items here.','dtlms');?> </p>
        </div>

    </div>
    <!-- Show Social Share End -->

</div>

<div class="dtlms-custom-box">

	<!-- Show Related Courses -->
	<div class="dtlms-column dtlms-one-half first">
    
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Show Related Courses','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $show_related_course = get_post_meta ( $post_id, 'show-related-course', true );
            $switchclass = ($show_related_course == true) ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($show_related_course == true) ? ' checked="checked"' : '';
            ?>            
            <div data-for="show-related-course" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="show-related-course" class="hidden" type="checkbox" name="show-related-course" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php esc_html_e('Would you like to show the related courses.','dtlms');?> </p>
        </div>
        
    </div>
    <!-- Show Related Courses End -->

    <!-- Referrence URL -->
    <div class="dtlms-column dtlms-one-half">

        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Referrence URL', 'dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $reference_url = get_post_meta ( $post_id, "reference-url", true );?>
            <input id="reference-url" name="reference-url" type="text" value="<?php echo $reference_url;?>" />
            <p class="dtlms-note"> <?php esc_html_e('You can add referrence url for your course here.', 'dtlms');?> </p>
            <div class="dtlms-clear"></div>
        </div>

    </div>
    <!-- Referrence URL End -->  

</div>

<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-half first">
    
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Enable Certificate','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $enable_certificate = get_post_meta ( $post_id, 'enable-certificate', true );
            $switchclass = ($enable_certificate == true) ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($enable_certificate == true) ? ' checked="checked"' : '';
            ?>
            <div data-for="enable-certificate" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="enable-certificate" class="hidden" type="checkbox" name="enable-certificate" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php esc_html_e('Would you like to enable certificate for this course?','dtlms');?> </p>
        </div>

		<div class="dtlms-hr-invisible"></div>
        <div class="dtlms-hr-invisible"></div>
        
        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Certificate Percentage (%)', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $certificate_percentage = get_post_meta ( $post_id, 'certificate-percentage', true ); ?>
            <input type="text" id="certificate-percentage" name="certificate-percentage" value="<?php echo $certificate_percentage; ?>" />
            <p class="dtlms-note"> <?php esc_html_e('Add percentage required to gain this certificate.','dtlms');?> </p>
        </div>

		<div class="dtlms-hr-invisible"></div>
        <div class="dtlms-hr-invisible"></div>
        
        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Certificate Template','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $certificate_template = get_post_meta ( $post_id, 'certificate-template', true );
            $certificates_args = array( 'post_type' => 'dtlms_certificates', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC', 'suppress_filters'  => FALSE );
            $certificates_array = get_posts( $certificates_args );
    
            echo '<select name="certificate-template" data-placeholder="'.esc_html__('Select Certificate Template...', 'dtlms').'" class="dtlms-chosen-select">';
            echo '<option value="">' . esc_html__( 'None', 'dtlms' ) . '</option>';
            if ( count( $certificates_array ) > 0 ) {
                foreach ($certificates_array as $certificate){
                    echo '<option value="' . esc_attr( $certificate->ID ) . '"' . selected( $certificate->ID, $certificate_template, false ) . '>' . esc_html( $certificate->post_title ) . '</option>';
                }
            }
            echo '</select>';
            ?>
            <p class="dtlms-note"> <?php esc_html_e('Choose certificate template here.','dtlms');?> </p>
        </div>

	</div>
	<div class="dtlms-column dtlms-one-half">
    
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Enable Badge','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $enable_badge = get_post_meta ( $post_id, 'enable-badge', true );
            $switchclass = ($enable_badge == true) ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($enable_badge == true) ? ' checked="checked"' : '';
            ?>
            <div data-for="enable-badge" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="enable-badge" class="hidden" type="checkbox" name="enable-badge" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php esc_html_e('Would you like to enable badge for this course?','dtlms');?> </p>
        </div>
    
    	<div class="dtlms-hr-invisible"></div>
        <div class="dtlms-hr-invisible"></div>
        
        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Badge Percentage (%)', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $badge_percentage = get_post_meta ( $post_id, 'badge-percentage', true ); ?>
            <input type="text" id="badge-percentage" name="badge-percentage" value="<?php echo $badge_percentage; ?>" />
            <p class="dtlms-note"> <?php esc_html_e('Add percentage required to gain this badge.','dtlms');?> </p>
        </div>
    
    	<div class="dtlms-hr-invisible"></div>
        <div class="dtlms-hr-invisible"></div>
        
        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Badge Image', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <div class="dtlms-upload-media-items-container">
				<?php 
                $badge_image_url = get_post_meta ( $post_id, 'badge-image-url', true );
                $badge_image_id = get_post_meta ( $post_id, 'badge-image-id', true ); 
                ?>
                <input name="badge-image-url" type="text" class="uploadfieldurl" readonly value="<?php echo $badge_image_url;?>"/>
                <input name="badge-image-id" type="hidden" class="uploadfieldid" readonly value="<?php echo $badge_image_id;?>"/>
                <input type="button" value="<?php esc_html_e('Upload','dtlms');?>" class="dtlms-upload-media-item-button show-preview"data-mediatype="image" />
                <input type="button" value="<?php esc_html_e('Remove','dtlms');?>" class="dtlms-upload-media-item-reset" />
                <?php echo dtlms_adminpanel_image_preview($badge_image_url); ?>
            </div>
            <p class="dtlms-note"> <?php esc_html_e('Choose badge image for your course.','dtlms');?> </p>
        </div>

	</div>
    
</div>

<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-sixth first">
		<label><?php esc_html_e('Attachments','dtlms');?> </label>
	</div>
	<div class="dtlms-column dtlms-five-sixth">
    
        <div class="dtlms-upload-media-items-container">

            <div class="dtlms-upload-media-items-holder">
                <ul class="dtlms-upload-media-items">

                    <?php
                    $media_attachments_urls = get_post_meta($post_id, 'media-attachment-urls', true);
                    $media_attachments_ids = get_post_meta($post_id, 'media-attachment-ids', true);
                    $media_attachments_titles = get_post_meta($post_id, 'media-attachment-titles', true);
                    $media_attachments_icons = get_post_meta($post_id, 'media-attachment-icons', true);
                    
                    if(isset($media_attachments_urls) && !empty($media_attachments_urls)) {
                        $i = 0;
                        foreach($media_attachments_urls as $media_attachments_url) {
                            if($media_attachments_url != '') {
                                $media_title = '';
                                if(isset($media_attachments_titles[$i])) {
                                    $media_title = $media_attachments_titles[$i];
                                }
                                $media_icon = '';
                                if(isset($media_attachments_icons[$i])) {
                                    $media_icon = $media_attachments_icons[$i];
                                }                                
                                ?>
                                <li>
                                    <input name="media-attachment-urls[]" type="text" class="uploadfield" readonly value="<?php echo $media_attachments_url; ?>"/>
                                    <input name="media-attachment-ids[]" type="hidden" class="uploadfieldid hidden" readonly value="<?php echo $media_attachments_ids[$i]; ?>"/>
                                    <input name="media-attachment-titles[]" type="text" class="media-attachment-titles" value="<?php echo $media_title; ?>" placeholder="<?php echo esc_html__('Attachment Title', 'dtlms'); ?>" />
                                    <input name="media-attachment-icons[]" type="text" class="media-attachment-icons" value="<?php echo $media_icon; ?>"placeholder="<?php echo esc_html__('Attachment Icon', 'dtlms'); ?>" />
                                    <span class="dtlms-remove-media-item"><span class="fa fa-close"></span></span>
                                </li>
                                <?php
                                $i++;
                            }
                        }
                    }
                    ?>

                </ul>
            </div>
                  
            <input type="button" value="<?php esc_html_e('Upload Attachments','dtlms');?>" class="dtlms-upload-media-item-button multiple" />
            <input type="button" value="<?php esc_html_e('Remove Attachments','dtlms');?>" class="dtlms-upload-media-item-reset" />
                   
        </div>

		<p class="dtlms-note"> <?php esc_html_e("You can add any number of media attachments for this course.",'dtlms');?> </p>
	</div>

</div>


<div class="dtlms-custom-box">

    <!-- Course Start Date -->
    <div class="dtlms-column dtlms-one-half first">

        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Course Start Date', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $course_start_date = get_post_meta ( $post_id, 'course-start-date', true );?>
            <input class="course-start-date dtlms-datepicker" name="course-start-date" type="text" value="<?php echo $course_start_date;?>" />
            <p class="dtlms-note"> <?php esc_html_e("Choose course start date here.", 'dtlms'); ?> </p>
            <div class="dtlms-clear"></div>
        </div>

    </div>
    <!-- Course Start Date End -->

    <!-- Allow Purchases Before Course Start Date -->
    <div class="dtlms-column dtlms-one-half">

        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Allow Purchases Before Course Start Date', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $current = get_post_meta($post_id, 'allowpurchases-before-course-startdate', true);
            $switchclass = ( $current === "true") ? 'checkbox-switch-on' :'checkbox-switch-off';    
            $checked = ( $current === "true") ? ' checked="checked" ' : '';
            ?>
            <div data-for="allowpurchases-before-course-startdate" class="dtlms-checkbox-switch <?php echo $switchclass; ?>"></div>
            <input id="allowpurchases-before-course-startdate" class="hidden" type="checkbox" name="allowpurchases-before-course-startdate" value="true" <?php echo $checked; ?> />
            <p class="dtlms-note"> <?php esc_html_e('If you like to allow student to make purchases before course start date, but they won\'t be able to take course until course start date', 'dtlms');?> </p>
        </div>

    </div>
    <!-- Allow Purchases Before Course Start Date End -->

</div>


<div class="dtlms-custom-box">

    <!-- Enable Course Sidebar -->
    <div class="dtlms-column dtlms-one-half first">

        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Enable Sidebar Content', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $current = get_post_meta($post_id, 'enable-sidebar', true);
            $switchclass = ( $current === "true") ? 'checkbox-switch-on' :'checkbox-switch-off';    
            $checked = ( $current === "true") ? ' checked="checked" ' : '';
            ?>
            <div data-for="enable-sidebar" class="dtlms-checkbox-switch <?php echo $switchclass; ?>"></div>
            <input id="enable-sidebar" class="hidden" type="checkbox" name="enable-sidebar" value="true" <?php echo $checked; ?> />
            <p class="dtlms-note"> <?php esc_html_e('If you like to display any additional content in sidebar.', 'dtlms');?> </p>
        </div>

    </div>
    <!-- Enable Course Sidebar End -->

    <!-- Course Sidebar Content -->
    <div class="dtlms-column dtlms-one-half">

        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Sidebar Content', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php $sidebar_content = get_post_meta($post_id, 'sidebar-content', true); ?>
            <textarea id="sidebar-content" name="sidebar-content" rows="8"><?php echo $sidebar_content; ?></textarea>
            <p class="dtlms-note"> <?php esc_html_e('Sidebar content goes here. You can add any shortcode.', 'dtlms');?> </p>
        </div>

    </div>
    <!-- Course Sidebar Content End -->

</div>


<div class="dtlms-custom-box">

    <!-- Capacity -->
    <div class="dtlms-column dtlms-one-half first">

        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Capacity', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $capacity = get_post_meta ( $post_id, 'capacity', true ); ?>
            <input type="number" id="capacity" name="capacity" value="<?php echo $capacity; ?>"  min="1" max="100" />
            <p class="dtlms-note"> <?php esc_html_e('If you wish you can specify course capacity here.','dtlms');?> </p>
        </div>

    </div>
    <!-- Capacity End -->

    <!-- Disable Purchases -->
    <div class="dtlms-column dtlms-one-half">

        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Disable Purchases Over Capacity', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $current = get_post_meta($post_id, 'disable-purchases-over-capacity', true);
            $switchclass = ( $current === "true") ? 'checkbox-switch-on' :'checkbox-switch-off';    
            $checked = ( $current === "true") ? ' checked="checked" ' : '';
            ?>
            <div data-for="disable-purchases-over-capacity" class="dtlms-checkbox-switch <?php echo $switchclass; ?>"></div>
            <input id="disable-purchases-over-capacity" class="hidden" type="checkbox" name="disable-purchases-over-capacity" value="true" <?php echo $checked; ?> />
            <p class="dtlms-note"> <?php esc_html_e('Disable purchases if course capacity is reached.', 'dtlms');?> </p>
        </div>

    </div>
    <!-- Disable Purchases End -->

</div>


<div class="dtlms-custom-box">

    <!-- Course Prerequisite -->
    <div class="dtlms-column dtlms-one-half first">

        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Course Prerequisite', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php 
            $course_prerequisite = get_post_meta ( $post_id, 'course-prerequisite', true ); 
            $args = array (
                            'post_type'=> 'dtlms_courses',
                            'numberposts'=> -1,
                            'suppress_filters'  => FALSE,
                            'exclude' => $post_id
                        );
                                                
            $post_types = get_posts($args);

            echo '<select data-placeholder="'.esc_html__('Select...', 'dtlms').'" class="course-prerequisite dtlms-chosen-select" name="course-prerequisite">';
                echo '<option value="">' .esc_html__('None', 'dtlms') . '</option>';
                foreach ( $post_types as $post_type ){
                    echo '<option value="'.$post_type->ID.'" '.selected( $post_type->ID, $course_prerequisite, false ).'>' . $post_type->post_title . '</option>';
                }
            echo '</select>';

            wp_reset_postdata();            
            ?>
            <p class="dtlms-note"> 
                <?php esc_html_e('Course pre reuired to take this course.','dtlms');?> 
                <?php echo "<br>"; ?>
                <?php esc_html_e('You can do further configuration in Settings -> General -> Course Settings.','dtlms');?>
            </p>
        </div>

    </div>
    <!-- Course Prerequisite End -->

    <!-- Allow Purchases Before Course Prerequisite -->
    <div class="dtlms-column dtlms-one-half">

        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Allow Purchases Before Course Prerequisite', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $current = get_post_meta($post_id, 'allowpurchases-before-course-prerequisite', true);
            $switchclass = ( $current === "true") ? 'checkbox-switch-on' :'checkbox-switch-off';    
            $checked = ( $current === "true") ? ' checked="checked" ' : '';
            ?>
            <div data-for="allowpurchases-before-course-prerequisite" class="dtlms-checkbox-switch <?php echo $switchclass; ?>"></div>
            <input id="allowpurchases-before-course-prerequisite" class="hidden" type="checkbox" name="allowpurchases-before-course-prerequisite" value="true" <?php echo $checked; ?> />
            <p class="dtlms-note"> <?php esc_html_e('If you like to allow student to make purchases before submitting or completing prerequisite course, but they won\'t be able to take course until course prerequisite submitted or completed.', 'dtlms');?> </p>
        </div>

    </div>
    <!-- Allow Purchases Before Course Prerequisite End -->

</div>


<!-- Curriculum Completion Lock & Drip Feed Switch -->

<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-half first">

        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Curriculum Completion Lock & Drip Feed Switch', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $drip_completionlock_switch = get_post_meta ( $post_id, 'drip-completionlock-switch', true );
            $completionlock_class =  $dripfeed_class = 'hidden';            
            if($drip_completionlock_switch == 'completionlock') {
                $completionlock_class = '';
                $dripfeed_class = 'hidden';
            } else if($drip_completionlock_switch == 'dripfeed') {
                $completionlock_class = 'hidden';
                $dripfeed_class = '';
            }

            $drip_completionlock_options = array ('' => esc_html__('None', 'dtlms'), 'completionlock' => esc_html__('Curriculum Completion Lock', 'dtlms'), 'dripfeed' => esc_html__('Drip Feed', 'dtlms'));
    
            echo '<select id="drip-completionlock-switch" name="drip-completionlock-switch" data-placeholder="'.esc_html__('Select...', 'dtlms').'" class="dtlms-chosen-select">';
            foreach ($drip_completionlock_options as $drip_completionlock_option_key => $drip_completionlock_option){
                echo '<option value="' . esc_attr( $drip_completionlock_option_key ) . '"' . selected( $drip_completionlock_option_key, $drip_completionlock_switch, false ) . '>' . esc_html( $drip_completionlock_option ) . '</option>';
            }
            echo '</select>' ;
            ?>
            <p class="dtlms-note"> <?php esc_html_e('If you wish you can switch betwen Curriculum Completion Lock & Drip Feed.', 'dtlms');?> </p>
        </div>

    </div>

    <div class="dtlms-column dtlms-one-half">

    </div>

</div>
<!-- Curriculum Completion Lock & Drip Feed Switch End -->


<!-- Curriculum Completion Lock -->

<div class="dtlms-completionlock-holder <?php echo esc_attr($completionlock_class); ?>">

    <div class="dtlms-custom-box">

        <!-- Curriculum Completion Lock -->
        <div class="dtlms-column dtlms-one-half first">

            <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Curriculum Completion Lock', 'dtlms');?></div>
            <div class="dtlms-column dtlms-two-third">
                <?php
                $current = get_post_meta($post_id, 'curriculum-completion-lock', true);
                $switchclass = ( $current === "true") ? 'checkbox-switch-on' :'checkbox-switch-off';    
                $checked = ( $current === "true") ? ' checked="checked" ' : '';
                ?>
                <div data-for="curriculum-completion-lock" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="curriculum-completion-lock" class="hidden" type="checkbox" name="curriculum-completion-lock" value="true" <?php echo $checked;?>/>
                <p class="dtlms-note"> <?php esc_html_e('User will be able to take next curriculum only when previous curriculums are completed ( ie. evaluated ).', 'dtlms'); echo "<br>"; esc_html_e('If some quizzes are marked as manual evaluation than student have to wait for evalution to take next curriculum item.', 'dtlms');?> </p>
            </div>

        </div>
        <!-- Curriculum Completion Lock End -->

        <!-- Curriculum Completion Lock - Allow On Submission -->
        <div class="dtlms-column dtlms-one-half">

            <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Open Curriculum On Submission', 'dtlms');?></div>
            <div class="dtlms-column dtlms-two-third">
                <?php
                $current = get_post_meta($post_id, 'open-curriculum-on-submission', true);
                $switchclass = ( $current === "true") ? 'checkbox-switch-on' :'checkbox-switch-off';    
                $checked = ( $current === "true") ? ' checked="checked" ' : '';
                ?>
                <div data-for="open-curriculum-on-submission" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="open-curriculum-on-submission" class="hidden" type="checkbox" name="open-curriculum-on-submission" value="true" <?php echo $checked;?>/>
                <p class="dtlms-note"> <?php esc_html_e('User will be able to take next curriculum when they just submit current curriculum. No need for current curriculum to be evaluated.', 'dtlms');?> </p>
            </div>

        </div>
        <!-- Curriculum Completion Lock - Allow On Submission End -->    

    </div>

</div>
<!-- Curriculum Completion Lock End -->

<!-- Drip Feed -->
<div class="dtlms-dripfeed-holder <?php echo esc_attr($dripfeed_class); ?>">

    <div class="dtlms-custom-box">

        <!-- Drip Feed -->
        <div class="dtlms-column dtlms-one-half first">

            <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Drip Feed', 'dtlms');?></div>
            <div class="dtlms-column dtlms-two-third">
                <?php
                $current = get_post_meta($post_id, 'drip-feed', true);
                $switchclass = ( $current === "true") ? 'checkbox-switch-on' :'checkbox-switch-off';    
                $checked = ( $current === "true") ? ' checked="checked" ' : '';
                ?>
                <div data-for="drip-feed" class="dtlms-checkbox-switch <?php echo $switchclass; ?>"></div>
                <input id="drip-feed" class="hidden" type="checkbox" name="drip-feed" value="true" <?php echo $checked; ?> />
                <p class="dtlms-note"> <?php esc_html_e('If you like to enable drip feed for your course enable it here.', 'dtlms');?> </p>
            </div>

        </div>
        <!-- Drip Feed End -->

        <div class="dtlms-column dtlms-one-half">

        </div>

    </div>

    <div class="dtlms-custom-box">

        <!-- Drip Content Type -->
        <div class="dtlms-column dtlms-one-half first">

            <div class="dtlms-column dtlms-one-third first">
               <label><?php esc_html_e('Drip Content Type','dtlms');?></label>
            </div>
            <div class="dtlms-column dtlms-two-third">
                <?php
                $drip_content_type = get_post_meta ( $post_id, 'drip-content-type', true );
                $dripcontenttypes = array ('curriculum' => 'Curriculum', 'section' => 'Section');
        
                echo '<select name="drip-content-type" data-placeholder="'.esc_html__('Select Drip Content Type...', 'dtlms').'" class="dtlms-chosen-select">';
                echo '<option value="">' . esc_html__( 'None', 'dtlms' ) . '</option>';
                foreach ($dripcontenttypes as $dripcontenttype_key => $dripcontenttype){
                    echo '<option value="' . esc_attr( $dripcontenttype_key ) . '"' . selected( $dripcontenttype_key, $drip_content_type, false ) . '>' . esc_html( $dripcontenttype ) . '</option>';
                }
                echo '</select>' ;
                ?>
                <p class="dtlms-note"> <?php esc_html_e('Choose how you like to drip content based on curriculum or section.','dtlms');?> </p>
            </div>

        </div>
        <!-- Drip Content Type End -->

        <!-- Drip Duration Type -->
        <div class="dtlms-column dtlms-one-half">

            <div class="dtlms-column dtlms-one-third first">
               <label><?php esc_html_e('Drip Duration Type','dtlms');?></label>
            </div>
            <div class="dtlms-column dtlms-two-third">
                <?php
                $drip_duration_type = get_post_meta ( $post_id, 'drip-duration-type', true );
                $dripdurationtypes = array ('static' => 'Static', 'dynamic' => 'Dynamic');
        
                echo '<select name="drip-duration-type" data-placeholder="'.esc_html__('Select Drip Duration Type...', 'dtlms').'" class="dtlms-chosen-select">';
                echo '<option value="">' . esc_html__( 'None', 'dtlms' ) . '</option>';
                foreach ($dripdurationtypes as $dripdurationtype_key => $dripdurationtype){
                    echo '<option value="' . esc_attr( $dripdurationtype_key ) . '"' . selected( $dripdurationtype_key, $drip_duration_type, false ) . '>' . esc_html( $dripdurationtype ) . '</option>';
                }
                echo '</select>' ;
                ?>
                <p class="dtlms-note"> <?php esc_html_e('Static - Specify drip duration below which will be the consecutive drip duration between items ( curriculum or section ).','dtlms'); echo "<br>"; esc_html_e('Dynamic - Duration specified in each curriculum will be taken as drip duration.','dtlms');?> </p>
            </div>

        </div>
        <!-- Drip Duration Type End -->

    </div>


    <div class="dtlms-custom-box">

        <!-- Drip Duration -->
        <div class="dtlms-column dtlms-one-half first">

            <div class="dtlms-column dtlms-one-third first">
               <label><?php esc_html_e('Static Drip Duration', 'dtlms'); ?></label>
            </div>
            <div class="dtlms-column dtlms-two-third">
                <?php $drip_duration = get_post_meta ( $post_id, 'drip-duration', true ); ?>
                <input type="number" id="drip-duration" name="drip-duration" value="<?php echo $drip_duration; ?>" />
                <p class="dtlms-note"> <?php esc_html_e('Add drip feed duration parameter here.','dtlms');?> </p>
            </div>

        </div>
        <!-- Drip Duration End -->

        <!-- Drip Duration Parameter -->
        <div class="dtlms-column dtlms-one-half">

            <div class="dtlms-column dtlms-one-third first">
               <label><?php esc_html_e('Static Drip Duration Parameter','dtlms');?></label>
            </div>
            <div class="dtlms-column dtlms-two-third">
                <?php
                $drip_duration_parameter = get_post_meta ( $post_id, 'drip-duration-parameter', true );
                $dripdurationparameters = array ('3600' => 'Hours', '86400' => 'Days', '604800' => 'Weeks', '2592000' => 'Months');
        
                echo '<select name="drip-duration-parameter" data-placeholder="'.esc_html__('Select Drip Duration Parameter...', 'dtlms').'" class="dtlms-chosen-select">';
                echo '<option value="">' . esc_html__( 'None', 'dtlms' ) . '</option>';
                foreach ($dripdurationparameters as $dripdurationparameter_key => $dripdurationparameter){
                    echo '<option value="' . esc_attr( $dripdurationparameter_key ) . '"' . selected( $dripdurationparameter_key, $drip_duration_parameter, false ) . '>' . esc_html( $dripdurationparameter ) . '</option>';
                }
                echo '</select>' ;
                ?>
                <p class="dtlms-note"> <?php esc_html_e('Choose drip duration parameter here.','dtlms');?> </p>
            </div>

        </div>
        <!-- Drip Duration Parameter End -->

    </div>

</div>
<!-- Drip Feed End -->