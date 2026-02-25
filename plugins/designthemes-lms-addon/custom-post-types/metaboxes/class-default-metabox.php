<?php
global $post;
$post_id = $post->ID;

echo '<input type="hidden" name="dtlms_classes_meta_nonce" value="'.wp_create_nonce('dtlms_classes_nonce').'" />';

$class_title_singular = apply_filters( 'class_label', 'singular' );
$class_title_plural = apply_filters( 'class_label', 'plural' );

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

            $pagelayouts = array ('type1' => 'Type 1', 'type2' => 'Type 2', 'type3' => 'Type 3', 'type4' => 'Type 4');
    
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

<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-half first">
    
        <div class="dtlms-column dtlms-one-third first">
            <label><?php echo sprintf( esc_html__('%1$s Type', 'dtlms'), $class_title_singular ); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $class_type = get_post_meta ( $post_id, 'dtlms-class-type', TRUE );
            $class_type = (isset($class_type) && $class_type != '') ? $class_type: 'default';
			
            if($class_type == 'onsite') {
                $onsite_hide_cls = '';
                $online_hide_cls = 'style="display:none;"';
                $onsiteonline_hide_cls = '';
            } else if($class_type == 'online') {
                $onsite_hide_cls = 'style="display:none;"';
                $online_hide_cls = '';
                $onsiteonline_hide_cls = '';
            } else if($class_type == 'default') {
                $onsite_hide_cls = 'style="display:none;"';
                $online_hide_cls = 'style="display:none;"';  
                $onsiteonline_hide_cls = 'style="display:none;"';        
            }
			
            $class_types = array('default' => esc_html__('Default', 'dtlms'), 'online' => esc_html__('Online', 'dtlms'), 'onsite' => esc_html__('Onsite', 'dtlms'));
    
            $out = '';
            $out .= '<select name="dtlms-class-type" style="width:70%;" data-placeholder="'.sprintf( esc_html__('Select %1$s Type', 'dtlms'), $class_title_singular ).'" class="dtlms-class-type dtlms-chosen-select">' . "\n";
			foreach ($class_types as $class_type_key => $class_type_value){
				$out .= '<option value="' . esc_attr( $class_type_key ) . '"' . selected( $class_type_key, $class_type, false ) . '>' . esc_html( $class_type_value ) . '</option>' . "\n";
			}
            $out .= '</select>' . "\n";
            echo $out;
            ?>

            <p class="dtlms-note"> 
                <?php
                echo esc_html__('Choose item type here.', 'dtlms');
                echo "<br>";
                echo sprintf( esc_html__('%1$s - just for listing your items. Pirce option is not applicable here.', 'dtlms'), '<strong>'.esc_html__('Default', 'dtlms').'</strong>' );
                echo "<br>";
                echo sprintf( esc_html__('%1$s - if you like to sell bunch of courses online with tracking, you can use this option.', 'dtlms'), '<strong>'.esc_html__('Online', 'dtlms').'</strong>' );
                echo "<br>";
                echo sprintf( esc_html__('%1$s - if you like to advertise your onsite items, you can use this option.', 'dtlms'), '<strong>'.esc_html__('Onsite', 'dtlms').'</strong>' );
                ?>
            </p>
        </div>

	</div>
    
</div>

<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-half first">
        <div class="dtlms-column dtlms-one-third first">
            <label><?php echo sprintf( esc_html__('Featured %1$s', 'dtlms'), $class_title_singular ); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php
			$dt_class_featured = get_post_meta($post_id, "dtlms-class-featured", true);
            $switchclass = ($dt_class_featured == 'true') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($dt_class_featured == 'true') ? ' checked="checked"' : '';
            ?>
            <div data-for="dtlms-class-featured" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="dtlms-class-featured" class="hidden" type="checkbox" name="dtlms-class-featured" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php esc_html_e("Make this item as featured one.", 'dtlms'); ?> </p>
            <div class="dtlms-clear"></div>
        </div>
    </div>
    
	<div class="dtlms-column dtlms-one-half">
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Main Tab Title', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $dt_class_maintabtitle = get_post_meta ( $post_id, "dtlms-class-maintabtitle",true);?>
            <input class="dtlms-class-maintabtitle" name="dtlms-class-maintabtitle" type="text" value="<?php echo $dt_class_maintabtitle;?>" style="width:80%;" />
            <p class="dtlms-note"> <?php esc_html_e("Add main tab title for your item here.", 'dtlms'); ?> </p>
            <div class="dtlms-clear"></div>
        </div>
    </div>
    
</div>

<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-half first">
    
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Content Options', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $class_content_options_value = get_post_meta ( $post_id, 'dtlms-class-content-options', TRUE );
			
            $course_hide_cls = 'style="display:none;"';
            $shortcode_hide_cls = 'style="display:none;"';
			if($class_content_options_value == 'shortcode') {
				$shortcode_hide_cls = 'style="display:block;"';
            } else if($class_content_options_value == 'course') {
                $course_hide_cls = 'style="display:block;"';
			}
			
            $class_content_options = array('' => 'None', 'course' => 'Add Course', 'shortcode' => 'Add Shortcodes');
    
            $out = '';
            $out .= '<select id="dtlms-class-content-options" name="dtlms-class-content-options" style="width:70%;" data-placeholder="'.esc_html__('Select Content Options...', 'dtlms').'" class="dtlms-chosen-select">' . "\n";
			foreach ($class_content_options as $class_content_key => $class_content_value){
				$out .= '<option value="' . esc_attr( $class_content_key ) . '"' . selected( $class_content_key, $class_content_options_value, false ) . '>' . esc_html( $class_content_value ) . '</option>' . "\n";
			}
            $out .= '</select>' . "\n";
            echo $out;
            ?>
            <p class="dtlms-note"> <?php esc_html_e('Choose your content type here.', 'dtlms'); ?> </p>
        </div>

	</div>
    
</div>

<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
        <label><?php esc_html_e('Content Title', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <?php $dt_class_content_title = get_post_meta($post_id, "dtlms-class-content-title", true);?>
        <input class="dtlms-class-content-title" name="dtlms-class-content-title" type="text" value="<?php echo $dt_class_content_title;?>" style="width:80%;" />
        <p class="dtlms-note"> <?php esc_html_e("Add title for your content here.", 'dtlms'); ?> </p>
        <div class="dtlms-clear"></div>
    </div>
    
</div>

<div class="dtlms-course-content" <?php echo $course_hide_cls; ?>>

    <div class="dtlms-custom-box">

    	<div class="dtlms-column dtlms-one-sixth first dtlms-add-quiz">
        
           <label><?php esc_html_e('Add Courses', 'dtlms'); ?></label>

    	</div>
    	<div class="dtlms-column dtlms-five-sixth">
        
            <?php
            $courses_args = array( 'post_type' => 'dtlms_courses', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC', 'suppress_filters' => FALSE );
            if ( !in_array( 'administrator', (array) $current_user->roles ) ) {
                $courses_args['author'] = $current_user_id;
            } 
                                        
            $courses_array = get_posts( $courses_args );
    		?>		
        
        	<div id="dtlms-class-courses-container">
            
            	<?php 
    			$class_courses = get_post_meta ( $post_id, "dtlms-class-courses", true);
    			
    			$j = 0;
    			if(isset($class_courses) && is_array($class_courses)) {
    				foreach($class_courses as $class_course) {
    				    ?>
    					<div class="dtlms-course-box">

    						<?php
    						echo '<select class="dtlms-class-courses dtlms-chosen-select" name="dtlms-class-courses[]" data-placeholder="'.sprintf( esc_html__('Choose a %1$s', 'dtlms'), $class_title_singular ).'" style="width:80%;">';
            						echo '<option value="">'.esc_html__('None', 'dtlms').'</option>';
            						if ( count( $courses_array ) > 0 ) {
            							foreach ($courses_array as $course) {
            								echo '<option value="' . esc_attr( $course->ID ) . '"' . selected( $course->ID, $class_course, false ) . '>' . esc_html( $course->post_title ) . '</option>';
            							}
            						}
    						echo '</select>';
    						?>

    						<span class="dtlms-remove-course"><span class="fa fa-close"></span></span>
                            <span class="fa fa-arrows"></span>

    					</div>
    				    <?php
    				    $j++;
    				}
    			}
    			?>
                
            </div>
    		
            <a href="#" class="dtlms-add-course custom-button-style"><?php esc_html_e('Add Course', 'dtlms'); ?></a>
            <p class="dtlms-note"> <?php esc_html_e('You can add course here.', 'dtlms'); ?> </p>
            
        	<div id="dtlms-course-to-clone" class="hidden">
            
    			<?php
                echo '<select data-placeholder="'.esc_html__('Choose a Course...', 'dtlms').'" style="width:80%;">';
                        echo '<option value="">'.esc_html__('None', 'dtlms').'</option>';
                        if ( count( $courses_array ) > 0 ) {
                            foreach ($courses_array as $course) {
                                echo '<option value="' . esc_attr( $course->ID ) . '">' . esc_html( $course->post_title ) . '</option>' ;
                            }
                        }
                echo '</select>'
                ?>
                <span class="dtlms-remove-course"><span class="fa fa-close"></span></span>
                <span class="fa fa-arrows"></span>
            
            </div>
        
    	</div>
        
    </div> 

</div>

<div class="dtlms-shortcode-content" <?php echo $shortcode_hide_cls; ?>>

    <div class="dtlms-custom-box">
    	<div class="dtlms-column dtlms-one-sixth first dtlms-add-quiz">
        
           <label><?php esc_html_e('Add Shortcode', 'dtlms'); ?></label>

    	</div>
    	<div class="dtlms-column dtlms-five-sixth">
        
    		<?php $class_shortcode = get_post_meta ( $post_id, "dtlms-class-shortcode", true); ?>
            <textarea class="dtlms-class-shortcode" name="dtlms-class-shortcode" type="text" style="width:80%; height:100px"><?php echo $class_shortcode; ?></textarea>
            <p class="dtlms-note"> <?php esc_html_e("Add any shortcode here. If you wish you can make use of \"Timetable Wordpress Plugin - Weekly Class Schedule\" plugin shortcode.", 'dtlms'); ?> </p>
            <div class="dtlms-clear"></div>

    	</div>
    </div>
    
</div>

<div class="dtlms-custom-box dtlms-onsiteonline-items" <?php echo $onsiteonline_hide_cls; ?>>

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
            <p class="dtlms-note"> <?php esc_html_e('Would you like to enable certificate for this class?','dtlms');?> </p>
        </div>

        <div class="dtlms-hr-invisible"></div>
        <div class="dtlms-hr-invisible"></div>

        <div class="dtlms-online-items" <?php echo $online_hide_cls; ?>>
        
            <div class="dtlms-column dtlms-one-third first">
               <label><?php esc_html_e('Certificate Percentage (%)', 'dtlms'); ?></label>
            </div>
            <div class="dtlms-column dtlms-two-third">
                <?php $certificate_percentage = get_post_meta ( $post_id, 'certificate-percentage', true ); ?>
                <input type="text" id="certificate-percentage" name="certificate-percentage" value="<?php echo $certificate_percentage; ?>" class="large">
                <p class="dtlms-note"> <?php esc_html_e('Add percentage required to gain this certificate.','dtlms');?> </p>
            </div>

            <div class="dtlms-hr-invisible"></div>
            <div class="dtlms-hr-invisible"></div>
        
        </div>

        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Certificate Template','dtlms');?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $certificate_template = get_post_meta ( $post_id, 'certificate-template', true );
            $certificates_args = array( 'post_type' => 'dtlms_certificates', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC', 'suppress_filters'  => FALSE );
            $certificates_array = get_posts( $certificates_args );
    
            $out = '';
            $out .= '<select id="certificate-template" name="certificate-template" style="width:70%;" data-placeholder="'.esc_html__('Select Certificate Template...', 'dtlms').'" class="dtlms-chosen-select">' . "\n";
            $out .= '<option value="">' . __( 'None', 'dtlms' ) . '</option>';
            if ( count( $certificates_array ) > 0 ) {
                foreach ($certificates_array as $certificate){
                    $out .= '<option value="' . esc_attr( $certificate->ID ) . '"' . selected( $certificate->ID, $certificate_template, false ) . '>' . esc_html( $certificate->post_title ) . '</option>' . "\n";
                }
            }
            $out .= '</select>' . "\n";
            echo $out;
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
            <p class="dtlms-note"> <?php esc_html_e('Would you like to enable badge for this class?','dtlms');?> </p>
        </div>
    
        <div class="dtlms-hr-invisible"></div>
        <div class="dtlms-hr-invisible"></div>

        <div class="dtlms-online-items" <?php echo $online_hide_cls; ?>>
            
            <div class="dtlms-column dtlms-one-third first">
               <label><?php esc_html_e('Badge Percentage (%)', 'dtlms'); ?></label>
            </div>
            <div class="dtlms-column dtlms-two-third">
                <?php $badge_percentage = get_post_meta ( $post_id, 'badge-percentage', true ); ?>
                <input type="text" id="badge-percentage" name="badge-percentage" value="<?php echo $badge_percentage; ?>" class="large">
                <p class="dtlms-note"> <?php esc_html_e('Add percentage required to gain this badge.','dtlms');?> </p>
            </div>
        
            <div class="dtlms-hr-invisible"></div>
            <div class="dtlms-hr-invisible"></div>

        </div>
        
        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Badge Image', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <div class="dtlms-upload-media-items-container">
                <?php 
                $badge_image_url = get_post_meta ( $post_id, 'badge-image-url', true );
                $badge_image_id = get_post_meta ( $post_id, 'badge-image-id', true ); 
                ?>
                <input name="badge-image-url" type="text" class="uploadfieldurl large" readonly value="<?php echo $badge_image_url;?>"/>
                <input name="badge-image-id" type="hidden" class="uploadfieldid large" readonly value="<?php echo $badge_image_id;?>"/>
                <input type="button" value="<?php esc_html_e('Upload','dtlms');?>" class="dtlms-upload-media-item-button show-preview"data-mediatype="image" />
                <input type="button" value="<?php esc_html_e('Remove','dtlms');?>" class="dtlms-upload-media-item-reset" />
                <?php echo dtlms_adminpanel_image_preview($badge_image_url); ?>
            </div>
            <p class="dtlms-note"> <?php esc_html_e('Choose badge image for your class.','dtlms');?> </p>
        </div>

    </div>
    
</div> 


<div class="dtlms-custom-box dtlms-onsite-items" <?php echo $onsite_hide_cls; ?>>

	<div class="dtlms-column dtlms-one-half first">
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Start Date', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $dt_class_start_date = get_post_meta ( $post_id, "dtlms-class-start-date",true);?>
            <input class="dtlms-class-start-date dtlms-datepicker" name="dtlms-class-start-date" type="text" value="<?php echo $dt_class_start_date;?>" style="width:30%;" />
            <p class="dtlms-note"> <?php esc_html_e("Choose class start date here.", 'dtlms'); ?> </p>
            <div class="dtlms-clear"></div>
        </div>
    </div>
    
	<div class="dtlms-column dtlms-one-half">
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Capacity', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $dt_class_capacity = get_post_meta ( $post_id, "dtlms-class-capacity",true);?>
            <input class="dtlms-class-capacity" name="dtlms-class-capacity" type="text" value="<?php echo $dt_class_capacity;?>" style="width:30%;" />
            <p class="dtlms-note"> <?php esc_html_e("Add class total capacity here.", 'dtlms'); ?> </p>
            <div class="dtlms-clear"></div>
        </div>
    </div>
    
</div>

<div class="dtlms-custom-box dtlms-onsite-items" <?php echo $onsite_hide_cls; ?>>

	<div class="dtlms-column dtlms-one-half first">
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Disable Purchases / Registration', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php
			$dt_class_disable_purchases_registration = get_post_meta($post_id, "dtlms-class-disable-purchases-regsitration", true);
            $switchclass = ($dt_class_disable_purchases_registration == 'true') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($dt_class_disable_purchases_registration == 'true') ? ' checked="checked"' : '';
            ?>
            <div data-for="dtlms-class-disable-purchases-regsitration" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="dtlms-class-disable-purchases-regsitration" class="hidden" type="checkbox" name="dtlms-class-disable-purchases-regsitration" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php esc_html_e("Disable purchases / registration if total purchases / registration exceeds class capacity.", 'dtlms'); ?> </p>
            <div class="dtlms-clear"></div>
        </div>
    </div>
    
	<div class="dtlms-column dtlms-one-half">
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Enable Purchases', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php
			$dt_class_enable_purchases = get_post_meta($post_id, "dtlms-class-enable-purchases", true);
            $switchclass = ($dt_class_enable_purchases == 'true') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($dt_class_enable_purchases == 'true') ? ' checked="checked"' : '';
            ?>
            <div data-for="dtlms-class-enable-purchases" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="dtlms-class-enable-purchases" class="hidden" type="checkbox" name="dtlms-class-enable-purchases" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php esc_html_e("Enable purchase option for this class.", 'dtlms'); ?> </p>
            <div class="dtlms-clear"></div>
        </div>
    </div>
    
</div>

<div class="dtlms-custom-box dtlms-onsite-items" <?php echo $onsite_hide_cls; ?>>

	<div class="dtlms-column dtlms-one-half first">
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Enable Registration', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php
			$dt_class_enable_registration = get_post_meta($post_id, "dtlms-class-enable-registration", true);
            $switchclass = ($dt_class_enable_registration == 'true') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($dt_class_enable_registration == 'true') ? ' checked="checked"' : '';
            ?>
            <div data-for="dtlms-class-enable-registration" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="dtlms-class-enable-registration" class="hidden" type="checkbox" name="dtlms-class-enable-registration" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> 
                <?php esc_html_e("Enable registration option for this class.", 'dtlms'); ?> 
                <?php echo "<br>"; ?>
                <?php esc_html_e("Note this option will be enabled only when purchase option is disabled.", 'dtlms'); ?>
            </p>
            <div class="dtlms-clear"></div>
        </div>
    </div>
    
	<div class="dtlms-column dtlms-one-half">
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Syllabus Preview', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
			<?php
			$dt_class_shyllabus_preview = get_post_meta($post_id, "dtlms-class-shyllabus-preview", true);
            $switchclass = ($dt_class_shyllabus_preview == 'true') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($dt_class_shyllabus_preview == 'true') ? ' checked="checked"' : '';
            ?>
            <div data-for="dtlms-class-shyllabus-preview" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="dtlms-class-shyllabus-preview" class="hidden" type="checkbox" name="dtlms-class-shyllabus-preview" value="true" <?php echo $checked;?> />
            <p class="dtlms-note"> <?php esc_html_e("If you don't wish to show the course detail pages for onsite courses, you can disable it. Enabling this option will only show the preview of the courses.", 'dtlms'); ?> </p>
            <div class="dtlms-clear"></div>
        </div>
    </div>
    
</div>

<div class="dtlms-custom-box dtlms-onsite-items" <?php echo $onsite_hide_cls; ?>>
	<div class="dtlms-column dtlms-one-sixth first">
		<label><?php esc_html_e('Address', 'dtlms'); ?></label>
	</div>
	<div class="dtlms-column dtlms-five-sixth">
		<?php $dt_class_address = get_post_meta($post_id, "dtlms-class-address", true);?>
		<textarea class="dtlms-class-address widefat" name="dtlms-class-address"><?php echo $dt_class_address;?></textarea>
		<p class="dtlms-note"> <?php esc_html_e("Add address here", 'dtlms'); ?> </p>
        <div class="dtlms-clear"></div>
	</div>
</div>

<div class="dtlms-custom-box dtlms-onsite-items" <?php echo $onsite_hide_cls; ?>>
	<div class="dtlms-column dtlms-one-sixth first">
		<label><?php esc_html_e('GPS Location', 'dtlms'); ?></label>
	</div>
	<div class="dtlms-column dtlms-five-sixth">
		<?php 
        $dt_class_gps = get_post_meta($post_id, "dtlms-class-gps", true);
        $dt_class_gps = is_array($dt_class_gps) ? $dt_class_gps : array();
        $latitude = array_key_exists('latitude', $dt_class_gps) ? $dt_class_gps['latitude'] : '';
        $longitude = array_key_exists('longitude', $dt_class_gps) ? $dt_class_gps['longitude'] : '';
        ?>
		<input class="dtlms-class-latitude small" name="dtlms-class-gps[latitude]" type="text" placeholder="<?php esc_html_e("Latitude","dtlms");?>" value="<?php echo $latitude;?>"> -
		<input class="dtlms-class-longitude small" name="dtlms-class-gps[longitude]" type="text" placeholder="<?php esc_html_e("Longitude","dtlms");?>" value="<?php echo $longitude;?>" > -
		<a href="#" class="dtlms-generate-gps custom-button-style"><?php esc_html_e( 'Click Here to get GPS Location', 'dtlms' );?> </a>
		<p class="note alert"> <?php esc_html_e("Add GPS location here to enable map.", 'dtlms'); ?> </p>
        <div class="dtlms-clear"></div>
	</div>
</div>

<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-half first">
        <div class="dtlms-column dtlms-one-third first">
            <label><?php esc_html_e('Accessories Tab Title', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $dt_class_accessories_tabtitle = get_post_meta ( $post_id, "dtlms-class-accessories-tabtitle",true);?>
            <input class="dtlms-class-accessories-tabtitle" name="dtlms-class-accessories-tabtitle" type="text" value="<?php echo $dt_class_accessories_tabtitle; ?>" style="width:80%;" />
            <p class="dtlms-note"> <?php esc_html_e("Add title for accessories tab here.", 'dtlms'); ?> </p>
            <div class="dtlms-clear"></div>
        </div>
    </div>
    
	<div class="dtlms-column dtlms-one-half">
    </div>
    
</div>

<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-sixth first dtlms-add-quiz">
    
       <label><?php esc_html_e('Add Accessories', 'dtlms'); ?></label>

	</div>
	<div class="dtlms-column dtlms-five-sixth">
    
        <div id="dtlms-class-accessories-container">
        
            <?php 
            $class_accessories_icon = get_post_meta ( $post_id, "dtlms-class-accessories-icon", true);
            $class_accessories_label = get_post_meta ( $post_id, "dtlms-class-accessories-label", true);
            $class_accessories_value = get_post_meta ( $post_id, "dtlms-class-accessories-value", true);
            $class_accessories_description = get_post_meta ( $post_id, "dtlms-class-accessories-description", true);
            
            $j = 0;
            if(isset($class_accessories_value) && is_array($class_accessories_value)) {
                foreach($class_accessories_value as $classaccessoryvalue) {
                    ?>
                    <div class="dtlms-accessory-box" style="width: 100%;">
                        <?php
                        $classaccessoriesicon = $classaccessorieslabel = $classaccessoriesdescription = '';
                        if(isset($class_accessories_icon[$j])) {
                            $classaccessoriesicon = $class_accessories_icon[$j];
                        }
                        if(isset($class_accessories_label[$j])) {
                            $classaccessorieslabel = $class_accessories_label[$j];
                        }
                        if(isset($class_accessories_description[$j])) {
                            $classaccessoriesdescription = $class_accessories_description[$j];
                        }                        

                        echo '<input id="dtlms-class-accessories-icon" name="dtlms-class-accessories-icon[]" class="large" type="text" value="'.esc_attr($classaccessoriesicon).'" style="width:32.56%;" />';
                        echo '<input id="dtlms-class-accessories-label" name="dtlms-class-accessories-label[]" class="large" type="text" value="'.esc_attr($classaccessorieslabel).'" style="width:32.56%;" />';
                        echo '<input id="dtlms-class-accessories-value" name="dtlms-class-accessories-value[]" class="large" type="text" value="'.esc_attr($classaccessoryvalue).'" style="width:32.56%; margin-right: 0;" />';
                        echo '<input id="dtlms-class-accessories-description" name="dtlms-class-accessories-description[]" class="large" type="text" value="'.esc_attr($classaccessoriesdescription).'" style="width:100%; margin-top:10px;" />';
                        ?>
                        <span class="dtlms-remove-accessory"><span class="fa fa-close"></span></span>
                        <span class="fa fa-arrows"></span>
                    </div>
                    <?php
                    $j++;
                }
            }
            ?>
            
        </div>
		
        <a href="#" class="dtlms-add-accessory custom-button-style"><?php esc_html_e('Add Accessory', 'dtlms'); ?></a>
        
        <p class="dtlms-note"> <?php esc_html_e('You can additional items with icon, label and value here.', 'dtlms'); echo "<br>"; esc_html_e('Among these three its necessary to add value atleast.', 'dtlms'); ?> </p>
        
    	<div id="dtlms-accessory-to-clone" class="hidden" style="width: 100%;">
        
            <?php
            echo '<input id="dtlms-class-accessories-icon" name="dtlms-class-accessories-icon[]" placeholder="'.esc_html__('Icon', 'dtlms').'" class="large" type="text" value="" style="width:32.56%;" />';
            echo '<input id="dtlms-class-accessories-label" name="dtlms-class-accessories-label[]" placeholder="'.esc_html__('Label', 'dtlms').'" class="large" type="text" value="" style="width:32.56%;" />';
            echo '<input id="dtlms-class-accessories-value" name="dtlms-class-accessories-value[]" placeholder="'.esc_html__('Value', 'dtlms').'" class="large" type="text" value="" style="width:32.56%; margin-right: 0;" />';
            echo '<input id="dtlms-class-accessories-description" name="dtlms-class-accessories-description[]" placeholder="'.esc_html__('Description', 'dtlms').'" class="large" type="text" value="" style="width:100%; margin-top:10px;" />';
            ?>
            <span class="dtlms-remove-accessory"><span class="fa fa-close"></span></span>
            <span class="fa fa-arrows"></span>
        
        </div>
    
	</div>
    
</div>

<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-sixth first dtlms-add-quiz">
    
       <label><?php esc_html_e('Add Tabs', 'dtlms'); ?></label>

	</div>
	<div class="dtlms-column dtlms-five-sixth">
    
        <div id="dtlms-class-tabs-container">
        
            <?php 
            $class_tabs_title = get_post_meta ( $post_id, "dtlms-class-tabs-title", true);
            $class_tabs_content = get_post_meta ( $post_id, "dtlms-class-tabs-content", true);
            
            $j = 0;
            if(isset($class_tabs_content) && is_array($class_tabs_content)) {
                foreach($class_tabs_content as $class_tab_content) {
                ?>
                    <div class="dtlms-tab-box">
                        <?php
                        echo '<input id="dtlms-class-tabs-title" name="dtlms-class-tabs-title[]" class="large" type="text" value="'.$class_tabs_title[$j].'" style="width:31.7%;" />';
                        echo '<textarea id="dtlms-class-tabs-content" name="dtlms-class-tabs-content[]" class="large" type="text" style="width:100%;">'.$class_tab_content.'</textarea>';
                        ?>
                        <span class="dtlms-remove-tab"><span class="fa fa-close"></span></span>
                        <span class="fa fa-arrows"></span>
                    </div>
                <?php
                $j++;
                }
            }
            ?>
            
        </div>
		
        <a href="#" class="dtlms-add-tab custom-button-style"><?php esc_html_e('Add Tab', 'dtlms'); ?></a>
        
        <p class="dtlms-note"> <?php esc_html_e('If you wish you can add additional tabs along with content for your item.', 'dtlms'); ?> </p>
        
    	<div id="dtlms-tab-to-clone" class="hidden">
        
            <?php
            echo '<input id="dtlms-class-tabs-title" name="dtlms-class-tabs-title[]" class="large" type="text" placeholder="'.esc_html__('Title', 'dtlms').'" style="width:31.7%;" />';
            echo '<textarea id="dtlms-class-tabs-content" name="dtlms-class-tabs-content[]" class="large" type="text" placeholder="'.esc_html__('Content', 'dtlms').'" style="width:100%; height:100px"></textarea>';
            ?>
            <span class="dtlms-remove-tab"><span class="fa fa-close"></span></span>
            <span class="fa fa-arrows"></span>
        
        </div>
    
	</div>
    
</div>

<div class="dtlms-custom-box">

    <!-- Enable Class Sidebar -->
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
    <!-- Enable Class Sidebar End -->

    <!-- Class Sidebar Content -->
    <div class="dtlms-column dtlms-one-half">

        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Sidebar Content', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php $sidebar_content = get_post_meta($post_id, 'sidebar-content', true); ?>
            <textarea id="sidebar-content" name="sidebar-content" rows="8"><?php echo $sidebar_content; ?></textarea>
            <p class="dtlms-note"> <?php esc_html_e('Sidebar content goes here. You can add any shortcode.', 'dtlms');?> </p>
        </div>

    </div>
    <!-- Class Sidebar Content End -->

</div>