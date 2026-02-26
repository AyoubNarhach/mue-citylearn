<?php
global $post;
$post_id = $post->ID;
echo '<input type="hidden" name="dtlms_packages_meta_nonce" value="'.wp_create_nonce('dtlms_packages_nonce').'" />'; 

$class_plural_label = apply_filters( 'class_label', 'plural' );

$current_user = wp_get_current_user();
$current_user_id = $current_user->ID;
?>

<div class="dtlms-custom-box">
  
    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php echo esc_html__('Sub Title','dtlms');?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <?php $subtitle = get_post_meta($post_id, 'subtitle', true); ?>
        <input id="subtitle" name="subtitle" class="large" type="text" value="<?php echo esc_attr($subtitle); ?>" style="width:100%;" />
        <p class="dtlms-note"> <?php echo esc_html__("Subtitle for this Package.",'dtlms');?> </p>
    </div>
    
</div>

<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Courses Included','dtlms'); ?></label>
    </div>

    <div class="dtlms-column dtlms-five-sixth">
    
        <?php
        $courses_included = get_post_meta($post_id, 'courses-included', true);
        $args = array (
                        'post_type'=> 'dtlms_courses',
                        'numberposts'=> -1,
                        'suppress_filters'  => FALSE,
                    );

        if ( !in_array( 'administrator', (array) $current_user->roles ) ) {
            $args['author'] = $current_user_id;
        }

        $courses = get_posts($args);

        echo '<select style="width:100%;" data-placeholder="'.esc_html__('Select...', 'dtlms').'" class="dtlms-chosen-select" id="courses-included" name="courses-included[]" multiple="multiple">';
            foreach ( $courses as $course ){
                $selected = in_array( $course->ID, $courses_included ) ? ' selected="selected" ' : '';  
                echo '<option value="'.$course->ID.'" '.$selected.'>' . $course->post_title . '</option>';
            }
        echo '</select>';

        wp_reset_postdata();
        ?>    

    </div>
    
</div>

<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php echo sprintf( esc_html__( '%1$s Included', 'dtlms' ), $class_plural_label ); ?></label>
    </div>

    <div class="dtlms-column dtlms-five-sixth">
    
        <?php
        $classes_included = get_post_meta($post_id, 'classes-included', true);
        $args = array (
                        'post_type'=> 'dtlms_classes',
                        'numberposts'=> -1,
                        'suppress_filters'  => FALSE,
                    );

        if ( !in_array( 'administrator', (array) $current_user->roles ) ) {
            $args['author'] = $current_user_id;
        }

        $classes = get_posts($args);

        echo '<select style="width:100%;" data-placeholder="'.esc_html__('Select...', 'dtlms').'" class="dtlms-chosen-select" id="classes-included" name="classes-included[]" multiple="multiple">';
            foreach ( $classes as $class ){
                $selected = in_array( $class->ID, $classes_included ) ? ' selected="selected" ' : '';  
                echo '<option value="'.$class->ID.'" '.$selected.'>' . $class->post_title . '</option>';
            }
        echo '</select>';

        wp_reset_postdata();
        ?>    

    </div>
    
</div>

<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Period', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <div class="dtlms-column dtlms-one-third first">
            <?php 
            $period = get_post_meta($post_id, 'period', true); 
            if($period == '') { $period = 1; }
            ?>
            <input type="number" id="period" name="period" value="<?php echo $period; ?>" class="large" min="1">
        </div>
        <div class="dtlms-column dtlms-two-third">            
            <?php
            $term = get_post_meta($post_id, 'term', true);

            $terms_list = array('D' => 'Day(s)', 'W' => 'Week(s)', 'M' => 'Month(s)', 'Y' => 'Year(s)', 'L' => 'Lifetime');
            echo '<select style="width:20%;" data-placeholder="'.esc_html__('Select...', 'dtlms').'" class="dtlms-chosen-select" id="term" name="term" >';
                foreach ( $terms_list as $term_list_key => $term_list ){
                    echo '<option value="'.$term_list_key.'" '.selected( $term_list_key, $term, false ).'>' . $term_list . '</option>';
                }
            echo '</select>';
            ?>   
        </div>     
        <p class="dtlms-note"> <?php esc_html_e('Add time period for you package.','dtlms');?> </p>
    </div>

</div>