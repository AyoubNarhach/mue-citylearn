<?php
global $post;
$post_id = $post->ID;

echo '<input type="hidden" name="dtlms_gradings_meta_nonce" value="'.wp_create_nonce('dtlms_gradings_nonce').'" />';

$user_id = get_post_meta ( $post_id, 'dtlms-user-id', true );
$user_info = get_userdata($user_id);

$class_id = get_post_meta ( $post_id, 'dtlms-class-id', true );
$class_grade_id = get_post_meta ( $post_id, 'dtlms-class-grade-id', true );
$course_id = get_post_meta ( $post_id, 'dtlms-course-id', true );
$course_grade_id = get_post_meta ( $post_id, 'dtlms-course-grade-id', true );
$lesson_id = get_post_meta ( $post_id, 'dtlms-lesson-id', true );
$quiz_id = get_post_meta ( $post_id, 'dtlms-quiz-id', true );
$assignment_id = get_post_meta ( $post_id, 'dtlms-assignment-id', true );

$grade_type = get_post_meta ( $post_id, 'grade-type', true );

$course_graded = get_post_meta ($course_grade_id, 'graded', true);
$course_graded_class = $input_graded_attr = '';
if($course_graded == 'true') {
  $course_graded_class = 'disabled';
  $input_graded_attr = 'readonly';
}

$class_graded = get_post_meta ($class_grade_id, 'graded', true);
$class_graded_class = '';
if($grade_type == 'class' && $class_graded == 'true') {
  $class_graded_class = 'disabled';
}

$class_singular_label = apply_filters( 'class_label', 'singular' );

$labels = array (
  '' => '',
  'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
  'lesson' => esc_html__( 'Lesson', 'dtlms' ),
  'quiz' => esc_html__( 'Quiz', 'dtlms' ),
  'assignment' => esc_html__( 'Assignment', 'dtlms' ),
);

if($grade_type == 'class') {

  $submitted = get_post_meta ($post_id, 'submitted', true);
  ?>

  <div class="dtlms-custom-box">
      <div class="dtlms-column dtlms-one-half first">
        <h3><?php echo sprintf( esc_html__( '%1$s Grading', 'dtlms' ), $class_singular_label ); ?></h3>
      </div>
      <div class="dtlms-column dtlms-one-half">
      </div>
  </div>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'User Name', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <strong><?php echo $user_info->display_name; ?></strong>              
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Review or Feedback', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php $review_or_feedback = get_post_meta ($post_id, 'review-or-feedback', true); ?>
            <textarea id="review-or-feedback" name="review-or-feedback" class="large" rows="4" style="width:90%;"><?php echo $review_or_feedback; ?></textarea>
            <p class="dtlms-note"> <?php esc_html_e('You can add feedback or review for this item here, which will displayed to that student.','dtlms');?> </p>
          </div>

      </div>

  </div>

  <?php
  if($submitted != '1') {
    ?>
     <div class="dtlms-custom-box">
      <p class="dtlms-note"><strong><?php echo esc_html__('User haven\'t submitted this class yet!', 'dtlms'); ?></strong></p>
     </div>
    <?php
  }
  ?>

  <div class="dtlms-custom-box">

    <h3><?php esc_html_e('Courses', 'dtlms'); ?></h3>

    <?php

    $class_curriculum_details = get_user_meta($user_id, $class_id, true);

    $class_courses = get_post_meta($class_id, 'dtlms-class-courses', true);

    $total_count = 0;
    if(is_array($class_courses) && !empty($class_courses)) {
      $total_count = count($class_courses);
    }
    

    if(is_array($class_courses) && !empty($class_courses)) {
      
      echo '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <th scope="col">'.esc_html__('#', 'dtlms').'</th>
                <th scope="col">'.esc_html__('Course', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Percentage Achieved', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Status', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Options', 'dtlms').'</th>
              </tr>';
      
      $total_percentage = 0;
      $i = 1;
      foreach($class_courses as $course_id) {

          $curriculum_details = get_user_meta($user_id, $course_id, true);

          $course_grade_id = (isset($curriculum_details['grade-post-id']) && $curriculum_details['grade-post-id'] != '') ? $curriculum_details['grade-post-id'] : -1;

          $course_user_percentage = get_post_meta ($course_grade_id, 'user-percentage', true);
          $total_percentage = $total_percentage + $course_user_percentage;
          if($course_user_percentage != '' && $course_user_percentage >= 0) {
            $course_user_percentage = $course_user_percentage.'%'; 
          }

          $status = esc_html__('Pending', 'dtlms');
          if(isset($curriculum_details['completed']) && $curriculum_details['completed'] == 1) {
            $status = esc_html__('Completed', 'dtlms');
          } else if(isset($curriculum_details['submitted']) && $curriculum_details['submitted'] == 1) {
            $status = esc_html__('Submitted', 'dtlms');
          } else if(isset($curriculum_details['started']) && $curriculum_details['started'] == 1) {
            $status = esc_html__('Started', 'dtlms');
          }

          $option_html = '';
          if($course_grade_id > 0) {
            $option_html = '<a href="'.esc_url(get_edit_post_link($course_grade_id)).'" target="_blank">'.esc_html__('Edit','dtlms').'</a>';
          }          

          echo '<tr>
                  <td>'.$i.'</td>
                  <td>'.get_the_title($course_id).'</td>
                  <td class="aligncenter">'.$course_user_percentage.'</td>
                  <td class="aligncenter">'.$status.'</td>
                  <td class="aligncenter">'.$option_html.'</td>
              </tr>';

          $i++;

      }

      echo '</table>';

    }

    ?>

  </div>  

  <?php
  if($submitted == '1') {
    ?>
    <div class="dtlms-custom-box">
      <div class="dtlms-column dtlms-two-third first">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Graded', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php
            $graded = get_post_meta ($post_id, 'graded', true);
            $switchclass = ($graded != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($graded != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="graded" class="dtlms-checkbox-switch dtlms-update-revoke-user-submission <?php echo $switchclass;?>"></div>
            <input id="graded" class="hidden" type="checkbox" name="graded" value="true" <?php echo $checked; ?> />
            <p class="dtlms-note"><?php esc_html_e('Once you enable this option, then this class will be marked as completed!', 'dtlms');?></p>
          </div>

          <div class="dtlms-hr-invisible"></div>

          <a class="dtlms-button small dtlms-revoke-user-submission <?php echo $class_graded_class; ?>" data-classid="<?php echo $class_id; ?>" data-courseid="-1" data-userid="<?php echo $user_id; ?>" data-itemtype="class" href="#"><?php echo esc_html__('Revoke User Submission', 'dtlms'); ?></a>

      </div>
      <div class="dtlms-column dtlms-one-third">
        <?php

        $user_percentage = 0;
        if($total_count > 0) {
          $user_percentage = round(($total_percentage / $total_count), 2);
        }

        echo '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="aligncenter"></th>
                    <th class="aligncenter"></th>
                    <th class="aligncenter">'.esc_html__('Approve', 'dtlms').'</th>
                </tr>      
                <tr>
                    <td class="aligncenter">'.esc_html__('User Percentage', 'dtlms').'</td>
                    <td class="aligncenter">'.$user_percentage.'%';

                      echo '<input id="user-percentage" name="user-percentage" type="hidden" value="'.$user_percentage.'" />';

              echo '</td>
                    <td class="aligncenter"></td>
                </tr>';   

              $enable_certificate = get_post_meta($class_id, 'enable-certificate', true); 
              if($enable_certificate == 'true') {

                $certificate_percentage = get_post_meta($class_id, 'certificate-percentage', true );

                $certificate_switch_html = '';
                if($user_percentage > $certificate_percentage) {

                  $switchclass = 'checkbox-switch-on';
                  $checked = 'checked="checked"';

                  $certificate_achieved = get_post_meta($post_id, 'certificate-achieved', true);
                  if($certificate_achieved == 'true') {
                    $switchclass = 'checkbox-switch-on';
                    $checked = 'checked="checked"';
                  } else {
                    $switchclass = 'checkbox-switch-off';
                    $checked = '';           
                  }

                  $certificate_switch_html = '<div data-for="certificate-achieved" class="dtlms-checkbox-switch '.$switchclass.'"></div><input id="certificate-achieved" class="hidden" type="checkbox" name="certificate-achieved" value="true" '.$checked.' />';    

                } else {

                  delete_post_meta ( $post_id, 'certificate-achieved' );
                  $certificate_switch_html = '';

                }

                echo '<tr>
                          <td class="aligncenter">'.esc_html__('Certificate Percentage', 'dtlms').'</td>
                          <td class="aligncenter">'.$certificate_percentage.'%</td>
                          <td class="aligncenter">'.$certificate_switch_html.'</td>
                      </tr>';  

              }

              $enable_badge = get_post_meta($class_id, 'enable-badge', true);
              if($enable_badge == 'true') {

                $badge_percentage = get_post_meta($class_id, 'badge-percentage', true);

                $badge_switch_html = '';
                if($user_percentage > $badge_percentage) {

                  $badge_achieved = get_post_meta($post_id, 'badge-achieved', true);
                  if($badge_achieved == 'true') {
                    $switchclass = 'checkbox-switch-on';
                    $checked = 'checked="checked"';
                  } else {
                    $switchclass = 'checkbox-switch-off';
                    $checked = '';           
                  }

                  $badge_switch_html = '<div data-for="badge-achieved" class="dtlms-checkbox-switch '.$switchclass.'"></div><input id="badge-achieved" class="hidden" type="checkbox" name="badge-achieved" value="true" '.$checked.' />';

                } else {

                  delete_post_meta ( $post_id, 'badge-achieved' );
                  $badge_switch_html = '';

                }

                echo '<tr>
                          <td class="aligncenter">'.esc_html__('Badge Percentage', 'dtlms').'</td>
                          <td class="aligncenter">'.$badge_percentage.'%</td>
                          <td class="aligncenter">'.$badge_switch_html.'</td>
                      </tr>';

              }

        echo '</table>';

        ?> 
      </div>
    </div>  

    <div class="dtlms-custom-box">
      <div class="dtlms-column dtlms-two-third first">

      </div>
      <div class="dtlms-column dtlms-one-third">
        <?php

        if($enable_certificate == 'true') {

          echo '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="0">     
                  <tr>
                      <td class="aligncenter">'.esc_html__('Date On Certificate', 'dtlms').'</td>
                      <td class="aligncenter">';

                      $date_on_certificate = get_post_meta($post_id, 'date-on-certificate', true);
                      if($date_on_certificate == '') {
                        $date_on_certificate = get_the_date(get_option('date_format'));
                      }
                      echo '<input id="date-on-certificate" name="date-on-certificate" class="dtlms-date-field" class="large" type="text" value="'.$date_on_certificate.'" readonly />';

                echo '</td>
                  </tr>
                </table>';

        }
        
        ?> 
      </div>
    </div>
    <?php
  }

} else if($grade_type == 'course') {

  $submitted = get_post_meta ($post_id, 'submitted', true);

  $completed_count = get_post_meta($post_id, 'completed-count', true);
  ?>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
        <h3><?php esc_html_e( 'Course Grading', 'dtlms');?></h3>
      </div>
      <div class="dtlms-column dtlms-one-half">
      </div>

  </div>


  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'User Name', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <strong><?php echo $user_info->display_name; ?></strong>               
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Review or Feedback', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php $review_or_feedback = get_post_meta ($post_id, 'review-or-feedback', true); ?>
            <textarea id="review-or-feedback" name="review-or-feedback" class="large" rows="4" style="width:90%;"><?php echo $review_or_feedback; ?></textarea>
            <p class="dtlms-note"> <?php esc_html_e('You can add feedback or review for this item here, which will displayed to that student.','dtlms');?> </p>
          </div>

      </div>

  </div>

  <?php
  if($submitted != '1') {
    ?>
     <div class="dtlms-custom-box">
      <p class="dtlms-note"><strong><?php echo esc_html__('User haven\'t submitted this course yet!', 'dtlms'); ?></strong></p>
     </div>
    <?php
  }
  ?>

  <div class="dtlms-custom-box">

    <h3><?php esc_html_e('Course Curriculum', 'dtlms'); ?></h3>

    <?php

    $user_percentage = $total_percentage = 0;

    $curriculum_details = get_user_meta($user_id, $course_id, true);

    $course_curriculum = get_post_meta ($course_id, 'course-curriculum', true);

    if(is_array($course_curriculum) && !empty($course_curriculum)) {
      
      echo '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <th scope="col">'.esc_html__('#', 'dtlms').'</th>
                <th scope="col">'.esc_html__('Curriculum', 'dtlms').'</th>
                <th scope="col">'.esc_html__('Sub - Curriculum', 'dtlms').'</th>
                <th scope="col">'.esc_html__('Type', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Pass Percentage', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Marks Obtained', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Marks Obtained Percentage', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Status', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Options', 'dtlms').'</th>
              </tr>';
      
      $i = 1; $total_count = 0;
      foreach($course_curriculum as $curriculum) {

        if(is_numeric($curriculum)) {    

          $marks_obtained = isset($curriculum_details['curriculum'][$curriculum]['marks-obtained']) ? $curriculum_details['curriculum'][$curriculum]['marks-obtained'] : '';
          $marks_obtained_percentage = isset($curriculum_details['curriculum'][$curriculum]['marks-obtained-percentage']) ? $curriculum_details['curriculum'][$curriculum]['marks-obtained-percentage'] : '';
          $total_percentage = $total_percentage + $marks_obtained_percentage;
          if($marks_obtained_percentage != '') {
            $marks_obtained_percentage = $marks_obtained_percentage.'%';
          }
          $completed = (isset($curriculum_details['curriculum'][$curriculum]['completed']) && $curriculum_details['curriculum'][$curriculum]['completed'] == 1) ? esc_html__('Completed', 'dtlms') : '';

          $option_html = '';
          $curriculum_grade_id = (isset($curriculum_details['curriculum'][$curriculum]['grade-post-id']) && $curriculum_details['curriculum'][$curriculum]['grade-post-id'] != '') ? $curriculum_details['curriculum'][$curriculum]['grade-post-id'] : -1;

          if($curriculum_grade_id > 0) {
            $option_html = '<a href="'.esc_url(get_edit_post_link($curriculum_grade_id)).'" target="_blank">'.esc_html__('Edit','dtlms').'</a>';
          }

          echo '<tr>
                  <td>'.$i.'</td>
                  <td>'.get_the_title($curriculum).'</td>
                  <td></td>
                  <td>'.dtlms_retrieve_curriculum_post_datas($curriculum, 'name').'</td>
                  <td class="aligncenter">'.dtlms_get_pass_percentage($curriculum, true).'</td>
                  <td class="aligncenter">'.$marks_obtained.'</td>
                  <td class="aligncenter">'.$marks_obtained_percentage.'</td>
                  <td class="aligncenter">'.$completed.'</td>
                  <td class="aligncenter">'.$option_html.'</td>
              </tr>';

          $lesson_curriculums = get_post_meta ($curriculum, 'lesson-curriculum', true);

          if(is_array($lesson_curriculums) && !empty($lesson_curriculums)) {

              $j = 1;
              foreach($lesson_curriculums as $lesson_curriculum) {

                if(is_numeric($lesson_curriculum)) {       

                  $marks_obtained = isset($curriculum_details['curriculum'][$curriculum]['curriculum'][$lesson_curriculum]['marks-obtained']) ? $curriculum_details['curriculum'][$curriculum]['curriculum'][$lesson_curriculum]['marks-obtained'] : '';
                  $marks_obtained_percentage = isset($curriculum_details['curriculum'][$curriculum]['curriculum'][$lesson_curriculum]['marks-obtained-percentage']) ? $curriculum_details['curriculum'][$curriculum]['curriculum'][$lesson_curriculum]['marks-obtained-percentage'] : '';
                  $total_percentage = $total_percentage + $marks_obtained_percentage;
                  if($marks_obtained_percentage != '') {
                    $marks_obtained_percentage = $marks_obtained_percentage.'%';
                  }                  
                  $completed = (isset($curriculum_details['curriculum'][$curriculum]['curriculum'][$lesson_curriculum]['completed']) && $curriculum_details['curriculum'][$curriculum]['curriculum'][$lesson_curriculum]['completed'] == 1) ? esc_html__('Completed', 'dtlms') : '';

                  $option_html = '';
                  $subcurriculum_grade_id = (isset($curriculum_details['curriculum'][$curriculum]['curriculum'][$lesson_curriculum]['grade-post-id']) && $curriculum_details['curriculum'][$curriculum]['curriculum'][$lesson_curriculum]['grade-post-id'] != '') ? $curriculum_details['curriculum'][$curriculum]['curriculum'][$lesson_curriculum]['grade-post-id'] : -1;

                  if($subcurriculum_grade_id > 0) {
                    $option_html = '<a href="'.esc_url(get_edit_post_link($subcurriculum_grade_id)).'" target="_blank">'.esc_html__('Edit','dtlms').'</a>';
                  }   

                  echo '<tr>
                            <td></td>
                            <td>'.$j.'</td>
                            <td>'.get_the_title($lesson_curriculum).'</td>
                            <td>'.dtlms_retrieve_curriculum_post_datas($lesson_curriculum, 'name').'</td>
                            <td class="aligncenter">'.dtlms_get_pass_percentage($curriculum, true).'</td>
                            <td class="aligncenter">'.$marks_obtained.'</td>
                            <td class="aligncenter">'.$marks_obtained_percentage.'</td>
                            <td class="aligncenter">'.$completed.'</td>
                            <td class="aligncenter">'.$option_html.'</td>
                        </tr>';

                  $j++;

                  $total_count = $total_count + 1;

                } else {

                    echo '<tr>
                              <td></td>
                              <td colspan="8" class="section">'.$lesson_curriculum.'</td>
                          </tr>';

                }                

              }

          }            

          $i++;

          $total_count = $total_count + 1;

        } else {

            echo '<tr>
                      <td colspan="9" class="section">'.$curriculum.'</td>
                  </tr>';

        }

      }

      echo '</table>';

    }

    ?>

  </div>  

  <?php
  if($submitted == '1') {
    ?>
    <div class="dtlms-custom-box">
      <div class="dtlms-column dtlms-two-third first">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Graded', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php
            $graded = get_post_meta ($post_id, 'graded', true);
            $switchclass = ($graded != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($graded != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="graded" class="dtlms-checkbox-switch dtlms-update-revoke-user-submission <?php echo $switchclass;?>"></div>
            <input id="graded" class="hidden" type="checkbox" name="graded" value="true" <?php echo $checked; ?> />
            <p class="dtlms-note"><?php esc_html_e('Once you enable this option, then this course will be marked as completed!', 'dtlms');?></p>
          </div>

          <div class="dtlms-hr-invisible"></div>

          <a class="dtlms-button small dtlms-revoke-user-submission <?php echo $course_graded_class; ?>" data-classid="-1" data-courseid="<?php echo $course_id; ?>" data-userid="<?php echo $user_id; ?>" data-itemtype="course" href="#"><?php echo esc_html__('Revoke User Submission', 'dtlms'); ?></a>

      </div>
      <div class="dtlms-column dtlms-one-third">
        <?php

        $user_percentage = round(($total_percentage / $total_count), 2);

        echo '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="aligncenter"></th>
                    <th class="aligncenter"></th>
                    <th class="aligncenter">'.esc_html__('Approve', 'dtlms').'</th>
                </tr>      
                <tr>
                    <td class="aligncenter">'.esc_html__('User Percentage', 'dtlms').'</td>
                    <td class="aligncenter">'.$user_percentage.'%';

                      echo '<input id="user-percentage" name="user-percentage" type="hidden" value="'.$user_percentage.'" />';

              echo '</td>
                    <td class="aligncenter"></td>
                </tr>';   

              $post_metas = get_post_meta($post_id);
              $post_meta_keys = array_keys($post_metas);
         

              $enable_certificate = get_post_meta($course_id, 'enable-certificate', true); 
              if($enable_certificate == 'true') {

                $certificate_percentage = get_post_meta($course_id, 'certificate-percentage', true );

                $certificate_switch_html = '';
                if($user_percentage >= $certificate_percentage) {

                  $switchclass = 'checkbox-switch-on';
                  $checked = 'checked="checked"';
 
                  $certificate_achieved = get_post_meta($post_id, 'certificate-achieved', true);
                  if($certificate_achieved == 'true' || !in_array('certificate-achieved', $post_meta_keys)) {
                    $switchclass = 'checkbox-switch-on';
                    $checked = 'checked="checked"';
                  } else {
                    $switchclass = 'checkbox-switch-off';
                    $checked = '';           
                  }

                  $certificate_switch_html = '<div data-for="certificate-achieved" class="dtlms-checkbox-switch '.$switchclass.'"></div><input id="certificate-achieved" class="hidden" type="checkbox" name="certificate-achieved" value="true" '.$checked.' />';    

                } else {

                  delete_post_meta ( $post_id, 'certificate-achieved' );
                  $certificate_switch_html = '';

                }

                echo '<tr>
                          <td class="aligncenter">'.esc_html__('Certificate Percentage', 'dtlms').'</td>
                          <td class="aligncenter">'.$certificate_percentage.'%</td>
                          <td class="aligncenter">'.$certificate_switch_html.'</td>
                      </tr>';  

              }

              $enable_badge = get_post_meta($course_id, 'enable-badge', true);
              if($enable_badge == 'true') {

                $badge_percentage = get_post_meta($course_id, 'badge-percentage', true);

                $badge_switch_html = '';
                if($user_percentage >= $badge_percentage) {

                  $badge_achieved = get_post_meta($post_id, 'badge-achieved', true);
                  if($badge_achieved == 'true' || !in_array('badge-achieved', $post_meta_keys)) {
                    $switchclass = 'checkbox-switch-on';
                    $checked = 'checked="checked"';
                  } else {
                    $switchclass = 'checkbox-switch-off';
                    $checked = '';           
                  }

                  $badge_switch_html = '<div data-for="badge-achieved" class="dtlms-checkbox-switch '.$switchclass.'"></div><input id="badge-achieved" class="hidden" type="checkbox" name="badge-achieved" value="true" '.$checked.' />';

                } else {

                  delete_post_meta ( $post_id, 'badge-achieved' );
                  $badge_switch_html = '';

                }

                echo '<tr>
                          <td class="aligncenter">'.esc_html__('Badge Percentage', 'dtlms').'</td>
                          <td class="aligncenter">'.$badge_percentage.'%</td>
                          <td class="aligncenter">'.$badge_switch_html.'</td>
                      </tr>';

              }

        echo '</table>';

        ?> 
      </div>
    </div>  

    <div class="dtlms-custom-box">
      <div class="dtlms-column dtlms-two-third first">

      </div>
      <div class="dtlms-column dtlms-one-third">
        <?php

        echo '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="0">     
                <tr>
                    <td class="aligncenter">'.esc_html__('Date On Certificate', 'dtlms').'</td>
                    <td class="aligncenter">';

                    $date_on_certificate = get_post_meta($post_id, 'date-on-certificate', true);
                    if($date_on_certificate == '') {
                      $date_on_certificate = get_the_date(get_option('date_format'));
                    }
                    echo '<input id="date-on-certificate" name="date-on-certificate" class="dtlms-date-field" class="large" type="text" value="'.$date_on_certificate.'" readonly />';

              echo '</td>
                </tr>
              </table>';


        ?> 
      </div>
    </div>
    <?php
  }

} else if($grade_type == 'lesson') {
  ?>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Item Type', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <?php echo isset($labels[$grade_type]) ? $labels[$grade_type] : ''; ?>                 
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

      </div>

  </div>    

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Course', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <strong><?php echo get_the_title($course_id); ?></strong>                
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'User Name', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <strong><?php echo $user_info->display_name; ?></strong>               
          </div>

      </div>

  </div>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Marks Obtained', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php $marks_obtained = get_post_meta ($post_id, 'marks-obtained', true);  ?>
            <input id="dtlms-marks-obtained" name="dtlms-marks-obtained" class="large" type="number" value="<?php echo $marks_obtained; ?>" style="width:20%;" <?php echo $input_graded_attr; ?> />
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Maximum Marks', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php 
            $maximum_mark = get_post_meta ($lesson_id, 'lesson-maximum-mark', true);
            if($maximum_mark == '') {
              $maximum_mark = 100;
            }
            ?>
            <?php echo $maximum_mark; ?>
            <input id="dtlms-maximum-marks" name="dtlms-maximum-marks" class="large" type="hidden" value="<?php echo $maximum_mark; ?>" />
          </div>

      </div>

  </div>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Percentage Obtained (%)', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php $marks_obtained_percent = get_post_meta ( $post_id, 'marks-obtained-percentage', true);  ?>
            <input type="text" name="dtlms-marks-obtained-percentage" id="dtlms-marks-obtained-percentage" value="<?php echo $marks_obtained_percent; ?>" readonly="readonly"  />
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Pass Percentage (%)', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php 
            $pass_percentage = get_post_meta ($lesson_id, 'lesson-pass-percentage', true);
            if($pass_percentage == '') {
              $pass_percentage = 100;
            }
            ?>
            <?php echo $pass_percentage; ?>
          </div>

      </div>

  </div>  

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Review or Feedback', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php $review_or_feedback = get_post_meta ($post_id, 'review-or-feedback', true); ?>
            <textarea id="review-or-feedback" name="review-or-feedback" class="large" rows="6" style="width:90%;"><?php echo $review_or_feedback; ?></textarea>
            <p class="dtlms-note"> <?php esc_html_e('You can add feedback or review for this item here, which will displayed to that student.','dtlms');?> </p>
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Graded', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php
            $graded = get_post_meta ($post_id, 'graded', true);
            $switchclass = ($graded != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($graded != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="graded" class="dtlms-checkbox-switch <?php echo $switchclass.' '.$course_graded_class;?>"></div>
            <input id="graded" class="hidden" type="checkbox" name="graded" value="true" <?php echo $checked; ?> />
            <p class="dtlms-note"><?php esc_html_e('Once you enable this option, then this user can\'t resubmit this item and it will be marked as completed!','dtlms');?></p>          
          </div>

      </div>

  </div>  

  <div class="dtlms-custom-box">
    <p class="dtlms-note"><?php esc_html_e('Once course to which this lesson belongs to is graded you can\'t regrade this item.','dtlms');?></p> 
  </div> 

  <?php

} else if($grade_type == 'assignment') {
	?>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Item Type', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <?php echo isset($labels[$grade_type]) ? $labels[$grade_type] : ''; ?>                 
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

      </div>

  </div>    

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Course', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <strong><?php echo get_the_title($course_id); ?></strong>       
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'User Name', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <strong><?php echo $user_info->display_name; ?></strong>
          </div>

      </div>

  </div>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Notes', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php
            $assignment_notes = get_post_meta ($post_id, 'assignment-notes', true); 
            if($assignment_notes != '') {
              echo nl2br($assignment_notes);
            } 
            ?>
          </div>
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Attachment', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <?php

              $attachment_id = get_post_meta ($post_id, 'attachment-id', true);
              $attachment_name = get_post_meta ($post_id, 'attachment-name', true);

              if(is_array($attachment_id) && !empty($attachment_id)) {
                $i = 0;
                foreach($attachment_id as $attachmentid) {
                  echo '<div class="dtlms-attachments-link">';
					echo '<img src="'.plugin_dir_url ( __FILE__ ) . 'images/attachment.png'.'" />';
					echo '<p>';
					echo $attachment_name[$i];
					echo '</p>';
                    echo '<a href="'.wp_get_attachment_url($attachmentid).'" target="_blank">'.esc_html__('View Attachment', 'dtlms').'</a>';
                  echo '</div>';

                  $i++;

                }
              } else {
                echo esc_html__('No attachments found!', 'dtlms');
              }

              ?>
          </div>

      </div>

  </div>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Marks Obtained', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php $marks_obtained = get_post_meta ($post_id, 'marks-obtained', true);  ?>
            <input id="dtlms-marks-obtained" name="dtlms-marks-obtained" class="large" type="number" value="<?php echo $marks_obtained; ?>" style="width:20%;" <?php echo $input_graded_attr; ?> />
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Maximum Marks', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php 
            $maximum_mark = get_post_meta ($assignment_id, 'assignment-maximum-mark', true); 
            if($maximum_mark == '') {
              $maximum_mark = 100;
            }
            ?>
            <?php echo $maximum_mark; ?>
            <input id="dtlms-maximum-marks" name="dtlms-maximum-marks" class="large" type="hidden" value="<?php echo $maximum_mark; ?>" />
          </div>

      </div>

  </div>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Percentage Obtained (%)', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php $marks_obtained_percent = get_post_meta ( $post_id, 'marks-obtained-percentage', true);  ?>
            <input type="text" name="dtlms-marks-obtained-percentage" id="dtlms-marks-obtained-percentage" value="<?php echo $marks_obtained_percent; ?>" readonly="readonly"  />
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Pass Percentage (%)', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php 
            $pass_percentage = get_post_meta ($assignment_id, 'assignment-pass-percentage', true);
            if($pass_percentage == '') {
              $pass_percentage = 100;
            }
            ?>
            <?php echo $pass_percentage; ?>
          </div>

      </div>

  </div>  
    
  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Review or Feedback', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php $review_or_feedback = get_post_meta ($post_id, 'review-or-feedback', true); ?>
            <textarea id="review-or-feedback" name="review-or-feedback" class="large" rows="6" style="width:90%;"><?php echo $review_or_feedback; ?></textarea>
            <p class="dtlms-note"> <?php esc_html_e('You can add feedback or review for this item here, which will displayed to that student.','dtlms');?> </p>
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Graded', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php
            $graded = get_post_meta ($post_id, 'graded', true);
            $switchclass = ($graded != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($graded != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="graded" class="dtlms-checkbox-switch <?php echo $switchclass.' '.$course_graded_class;?>"></div>
            <input id="graded" class="hidden" type="checkbox" name="graded" value="true" <?php echo $checked; ?> />
            <p class="dtlms-note"><?php esc_html_e('Once you enable this option, then this user can\'t resubmit this item and it will be marked as completed!','dtlms');?></p>
          </div>

      </div>

  </div> 

  <div class="dtlms-custom-box">
    <p class="dtlms-note"><?php esc_html_e('Once course to which this assignment belongs to is graded you can\'t regrade this item.','dtlms');?></p> 
  </div> 

  <?php

} else if($grade_type == 'quiz') {

  ?>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Item Type', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <strong><?php echo isset($labels[$grade_type]) ? $labels[$grade_type] : ''; ?></strong>    
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

      </div>

  </div>    

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Course', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <strong><?php echo get_the_title($course_id); ?></strong>            
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'User Name', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
              <strong><?php echo $user_info->display_name; ?></strong>                
          </div>

      </div>

  </div>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Marks Obtained', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php $marks_obtained = get_post_meta ($post_id, 'marks-obtained', true);  ?>
            <input id="dtlms-marks-obtained" name="dtlms-marks-obtained" class="large" type="number" value="<?php echo $marks_obtained; ?>" style="width:20%;" <?php echo $input_graded_attr; ?> />
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Maximum Marks', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php 
            $maximum_mark = get_post_meta ($quiz_id, 'quiz-total-grade', true); 
            ?>
            <?php echo $maximum_mark; ?>
            <input id="dtlms-maximum-marks" name="dtlms-maximum-marks" class="large" type="hidden" value="<?php echo $maximum_mark; ?>" />
          </div>

      </div>

  </div>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Percentage Obtained (%)', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php $marks_obtained_percent = get_post_meta ( $post_id, 'marks-obtained-percentage', true);  ?>
            <input type="text" name="dtlms-marks-obtained-percentage" id="dtlms-marks-obtained-percentage" value="<?php echo $marks_obtained_percent; ?>" readonly="readonly"  />
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Pass Percentage (%)', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php 
            $pass_percentage = get_post_meta ($quiz_id, 'quiz-pass-percentage', true);
            ?>
            <?php echo $pass_percentage; ?>
          </div>

      </div>

  </div>  
    
  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Review or Feedback', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php $review_or_feedback = get_post_meta ($post_id, 'review-or-feedback', true); ?>
            <textarea id="review-or-feedback" name="review-or-feedback" class="large" rows="6" style="width:90%;"><?php echo $review_or_feedback; ?></textarea>
            <p class="dtlms-note"> <?php esc_html_e('You can add feedback or review for this item here, which will displayed to that student.','dtlms');?> </p>
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Graded', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php
            $graded = get_post_meta ($post_id, 'graded', true);
            $switchclass = ($graded != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($graded != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="graded" class="dtlms-checkbox-switch <?php echo $switchclass.' '.$course_graded_class;?>"></div>
            <input id="graded" class="hidden" type="checkbox" name="graded" value="true" <?php echo $checked; ?> />
            <p class="dtlms-note"><?php esc_html_e('Once you enable this option, then this user can\'t resubmit this item and it will be marked as completed!','dtlms');?></p>
          </div>

      </div>

  </div> 

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Attempt', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php 
            $user_attempts = get_post_meta ( $post_id, 'user-attempts', true );
            echo $user_attempts;  
            ?>
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Attempts Remaining', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php 
            $quiz_retakes = get_post_meta ( $quiz_id, 'quiz-retakes', true );
            echo ($quiz_retakes-$user_attempts);  
            ?>
          </div>

      </div>

  </div>  

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Time Taken', 'dtlms');?></div>
          <div class="dtlms-column dtlms-two-third">
            <?php 
            $timings = get_post_meta ( $post_id, 'timings', true );

            $duration = get_post_meta ( $quiz_id, 'duration', true );
            $duration_parameter = get_post_meta ( $quiz_id, 'duration-parameter', true );
            $duration_in_seconds = ($duration * $duration_parameter); 

            $time_taken = ($duration_in_seconds - $timings);
            $time_taken = gmdate('H:i:s', $time_taken);
            echo $time_taken;  
            ?>
          </div>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

      </div>

  </div>

  <div class="dtlms-custom-box">

      <div class="dtlms-column dtlms-one-half first">
      
          <a class="custom-button-style" id="dtlms-reset-grade" href="#"><?php echo esc_html__('Reset', 'dtlms'); ?></a>
          <a class="custom-button-style" id="dtlms-auto-grade" href="#"><?php echo esc_html__('Auto Grade', 'dtlms'); ?></a>
          
      </div>

      <div class="dtlms-column dtlms-one-half">

      </div>

  </div> 

  <div class="dtlms-custom-box">
    <p class="dtlms-note"><?php esc_html_e('Once course to which this quiz belongs to is graded you can\'t regrade this item.','dtlms');?></p> 
  </div>       

  <div class="dtlms-custom-box">

    <h3><?php esc_html_e('Questions & Answers', 'dtlms'); ?></h3>

    <?php

    if($quiz_id > 0) {

      $quiz_question_type = get_post_meta($quiz_id, 'quiz-question-type', true);

      $dtlms_question_ids = get_post_meta ( $post_id, 'question-ids',  true );
      $dtlms_question_grades = get_post_meta ( $post_id, 'question-grades',  true );
      $dtlms_question_negative_grades = get_post_meta ( $post_id, 'question-negative-grades',  true );
      
      $quiz_question = explode(',', $dtlms_question_ids);
      $quiz_question_grade = explode(',', $dtlms_question_grades);
      $quiz_question_negative_grade = explode(',', $dtlms_question_negative_grades);
      
      echo '<table class="dtlms-custom-table" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <th scope="col">'.esc_html__('#', 'dtlms').'</th>';

                if($quiz_question_type == 'add-categories') {
                  echo '<th scope="col">'.esc_html__('Categories', 'dtlms').'</th>';
                }

          echo '<th scope="col">'.esc_html__('Question', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Answer Options', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Correct Answer', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('User Answer', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Grade', 'dtlms').'</th>
                <th scope="col" class="aligncenter">'.esc_html__('Option', 'dtlms').'</th>
              </tr>';
      
      $i = 1;
      foreach($quiz_question as $question_id) {
        
        $question_args = array( 'post_type' => 'dtlms_questions', 'p' => $question_id );
        $question = get_posts( $question_args );

        if($quiz_question_type == 'add-categories') {
          $categories_array = array ();
          $categories = get_the_terms( $question_id, 'question_category' );
          if(is_array($categories) && !empty($categories)) {
            foreach( $categories as $category ) {
                array_push($categories_array, $category->name);
            }
          }
        }

        $multichoice_image = '';

        $question_type = get_post_meta ( $question_id, 'question-type', true );
        
        if($question_type == 'multiple-choice') {
          
          $answers_op = get_post_meta ( $question_id, 'multichoice-answers', true );
          $answers_op = (is_array($answers_op) && !empty($answers_op)) ? $answers_op : array ();
          $answers = '<ul><li>';
          $answers .= implode('</li><li>', $answers_op);
          $answers .= '</li></ul>';

          $correct_answer_op = get_post_meta ( $question_id, 'multichoice-correct-answer', true );
          $correct_answer = '<ul><li>'.$correct_answer_op.'</li></ul>'; 

        } else if($question_type == 'multiple-choice-image') {
          
          $answers_op = get_post_meta ( $question_id, 'multichoice-image-answers', true );
          $answers = '<ul>';
          foreach($answers_op as $answer) {
            $answers .= '<li><img src="'.$answer.'" width="60" height="60" /></li>';
          }
          $answers .= '</ul>';

          $correct_answer_op = get_post_meta ( $question_id, 'multichoice-image-correct-answer', true );
          $correct_answer = '<ul><li><img src="'.$correct_answer_op.'" width="60" height="60" /></li></ul>';

          $multichoice_image = 'data-multichoiceimage="true"';
          
        } else if($question_type == 'multiple-correct') {
          
          $answers_op = get_post_meta ( $question_id, 'multicorrect-answers', true );
          $answers = '<ul><li>';
          $answers .= implode('</li><li>', $answers_op);
          $answers .= '</li></ul>';

          $correct_answer_op = get_post_meta ( $question_id, 'multicorrect-correct-answer', true );
          $correct_answer = '<ul><li>';
          $correct_answer .= implode('</li><li>', $correct_answer_op);
          $correct_answer .= '</li></ul>';
          
        } else if($question_type == 'boolean') {
          
          $answers = '<ul><li>'.esc_html__('true', 'dtlms').'</li><li>'.esc_html__('false', 'dtlms').'</li></ul>';

          $correct_answer_op = get_post_meta ( $question_id, 'boolean-answer', true );
          $correct_answer = '<ul><li>'.$correct_answer_op.'</li></ul>';
          
        } else if($question_type == 'gap-fill') {

          $text_before_gap = get_post_meta ( $question_id, 'text-before-gap', true );
          $text_before_gap = !empty($text_before_gap) ? $text_before_gap : '';
          $text_gap = get_post_meta ( $question_id, 'gap', true );
          $text_gap = !empty($text_gap) ? $text_gap : '';
          $text_after_gap = get_post_meta ( $question_id, 'text-after-gap', true );
          $text_after_gap = !empty($text_after_gap) ? $text_after_gap : '';
          
          $answers = $text_before_gap.' <strong>'.$text_gap.'</strong> '.$text_after_gap;
          $correct_answer_op = $text_gap;
          $correct_answer = '<ul><li>'.$correct_answer_op.'</li></ul>';
        
        } else if($question_type == 'single-line') {
                
          $answers = '';
          $correct_answer_op = get_post_meta ( $question_id, 'singleline-answer', true );
          $correct_answer = '<ul><li>'.$correct_answer_op.'</li></ul>';

        } else if($question_type == 'multi-line') {
                
          $answers = '';
          $correct_answer_op = get_post_meta ( $question_id, 'multiline-answer', true );
          $correct_answer = '<ul><li>'.$correct_answer_op.'</li></ul>';
          
        }
        
        $question_name = 'dtlms-question-'.$question_id;
        
        $user_answer = '';
        $user_answer_op = get_post_meta ( $post_id, $question_name, true );
        if($question_type == 'multiple-choice-image') {
          if($user_answer_op != '') {
            $user_answer .= '<ul><li><img src="'.$user_answer_op.'" width="60" height="60" /></li></ul>';
          }
        } else {
          $user_answer .= '<ul><li>';
          if(is_array($user_answer_op) && !empty($user_answer_op)) {
            $user_answer .= implode('</li><li>',$user_answer_op);  
          } else {
            $user_answer .= $user_answer_op;  
          }
          $user_answer .= '</li></ul>';
        }
    
        
        $question_grade = get_post_meta ( $post_id, 'question-id-'.$question_id.'-grade',true);
        
        if(isset($quiz_question_negative_grade[$i-1]) && $quiz_question_negative_grade[$i-1] > 0) {
          $quiz_question_negative_grade_data = '-'.$quiz_question_negative_grade[$i-1];
          $quiz_question_negative_grade_value = $quiz_question_negative_grade[$i-1];
        } else {
          $quiz_question_negative_grade_data = 0;
          $quiz_question_negative_grade_value = 0;
        }

        $skipped_class = '';
        if($user_answer == '') {
          $skipped_class = 'skipped';
        }

        if( defined( 'DOING_AJAX' ) && DOING_AJAX && class_exists('WPBMap') && method_exists('WPBMap', 'addAllMappedShortcodes') ) {
          WPBMap::addAllMappedShortcodes();
        }

        echo '<tr class="dtlms-answers '.$skipped_class.'" data-grade="'.$quiz_question_grade[$i-1].'" data-negative-grade="'.$quiz_question_negative_grade_value.'">
                <td>'.$i.'</td>';
                if($quiz_question_type == 'add-categories') {
                  echo '<td>'.implode(',', $categories_array).'</td>';  
                }
          echo '<td>'.do_shortcode($question[0]->post_content).'</td>
                <td class="aligncenter">'.do_shortcode($answers).'</td>
                <td class="aligncenter dtlms-correct-answer" '.$multichoice_image.' data-correctanswer="'.htmlentities($correct_answer, ENT_QUOTES).'">'.do_shortcode($correct_answer).'</td>
                <td class="aligncenter dtlms-user-answer" '.$multichoice_image.' data-useranswer="'.htmlentities($user_answer, ENT_QUOTES).'">'.do_shortcode($user_answer).'</td>';
                  if($user_answer == '') {
                    echo '<td class="aligncenter">'.esc_html__('Skipped', 'dtlms').'</td>';
                    echo '<td></td>';
                  } else {
                    if(isset($question_grade) && $question_grade == true) {               
                      echo '<td class="aligncenter dtlms-grade-display-field">'.$quiz_question_grade[$i-1].' / '.$quiz_question_grade[$i-1].'</td>
                          <td class="aligncenter dtlms-grade-option-field">
                            <div data-for="dtlms-question-id-'.$question_id.'-grade" data-quesid="'.$question_id.'" class="dtlms-quiz-answer-switch dtlms-quiz-answer-switch-on '.$course_graded_class.'">Right</div>
                            <input class="hidden" id="dtlms-question-id-'.$question_id.'-grade" type="checkbox" name="dtlms-question-id-'.$question_id.'-grade" value="true" checked="checked" />
                          </td>';
                    } else {
                      echo '<td class="aligncenter dtlms-grade-display-field">'.$quiz_question_negative_grade_data.' / '.$quiz_question_grade[$i-1].'</td>
                          <td class="aligncenter dtlms-grade-option-field">
                            <div data-for="dtlms-question-id-'.$question_id.'-grade" data-quesid="'.$question_id.'" class="dtlms-quiz-answer-switch dtlms-quiz-answer-switch-off '.$course_graded_class.'">Wrong</div>
                            <input class="hidden" id="dtlms-question-id-'.$question_id.'-grade" type="checkbox" name="dtlms-question-id-'.$question_id.'-grade" value="false" />
                          </td>';
                    }
                  }
          echo '</tr>';
            
        $i++;

      }
      
      echo '</table>';

      echo '<input id="question-ids" name="question-ids" type="hidden" value="'.$dtlms_question_ids.'" />';

    }

    $total_questions = get_post_meta ( $post_id, 'total-questions', true );
    $skipped_questions = get_post_meta ( $post_id, 'skipped-questions', true );
    $correct_questions = get_post_meta ( $post_id, 'correct-questions', true );
    $wrong_questions = get_post_meta ( $post_id, 'wrong-questions', true );

    echo '<input id="total-questions" name="total-questions" type="hidden" value="'.$total_questions.'" />';
    echo '<input id="skipped-questions" name="skipped-questions" type="hidden" value="'.$skipped_questions.'" />';
    echo '<input id="correct-questions" name="correct-questions" type="hidden" value="'.$correct_questions.'" />';
    echo '<input id="wrong-questions" name="wrong-questions" type="hidden" value="'.$wrong_questions.'" />';

    ?>  

  </div>  

  <?php 
  $prev_gradings = get_post_meta ( $post_id, 'prev-gradings', true );
  if(isset($prev_gradings) && !empty($prev_gradings)) { 
    ?>
    <div class="dtlms-custom-box">
        <h3><?php esc_html_e('Previous Attempts', 'dtlms'); ?></h3>
        <?php
        echo '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
                  <tr>
                    <th scope="col" class="aligncenter">'.esc_html__('Attempt', 'dtlms').'</th>
                    <th scope="col" class="aligncenter">'.esc_html__('Mark', 'dtlms').'</th>
                    <th scope="col" class="aligncenter">'.esc_html__('Percentage', 'dtlms').'</th>
                    <th scope="col" class="aligncenter">'.esc_html__('Timings', 'dtlms').'</th>
                  </tr>';
        
            foreach($prev_gradings as $grading) {

              $timings = $grading['timings'];
              if($timings == 'undefined') {
                $time_taken = 'N/A';
              } else {
                $duration = get_post_meta ( $quiz_id, 'duration', true );
                $duration_parameter = get_post_meta ( $quiz_id, 'duration-parameter', true );
                $duration_in_seconds = ($duration * $duration_parameter); 

                $time_taken = ($duration_in_seconds - $timings);
                $time_taken = gmdate('H:i:s', $time_taken);  
              }

              echo '<tr>
                      <td class="aligncenter">'.$grading['attempts'].'</td>
                      <td class="aligncenter">'.$grading['mark'].'</td>
                      <td class="aligncenter">'.$grading['percentage'].'%</td>
                      <td class="aligncenter">'.$time_taken.'</td>
                  </tr>';
                  
            }
        
        echo '</table>';
        ?>
    </div>
    <?php 
  } 
  ?>

  <?php
}
?>