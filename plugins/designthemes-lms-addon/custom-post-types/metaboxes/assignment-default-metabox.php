<?php
global $post;
$post_id = $post->ID;
echo '<input type="hidden" name="dtlms_assignments_meta_nonce" value="'.wp_create_nonce('dtlms_assignments_nonce').'" />'; 
?>

<!-- Unlock Assignment -->
<div class="dtlms-custom-box">
    <div class="dtlms-column dtlms-one-sixth first">
        <label><?php esc_html_e('Unlock Assignment', 'dtlms');?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <?php
        $free_assignment = get_post_meta ( $post_id, 'free-assignment', true );
        $switchclass = ($free_assignment == true) ? 'checkbox-switch-on' : 'checkbox-switch-off';
        $checked = ($free_assignment == true) ? ' checked="checked"' : '';
        ?>        
        <div data-for="free-assignment" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
        <input id="free-assignment" class="hidden" type="checkbox" name="free-assignment" value="true" <?php echo $checked;?> />
        <p class="dtlms-note"> <?php echo esc_html__('YES! to unlock this assignment, so that you can use this assignment as preview. It won\'t be affected by "Curriculum Completion Lock" in course settings.','dtlms');?> </p>
    </div>
</div>
<!-- Unlock Assignment End -->

<!-- Subtitle -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Subtitle', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
		<?php $assignment_subtitle = get_post_meta ( $post_id, 'assignment-subtitle', true ); ?>
        <input id="assignment-subtitle" name="assignment-subtitle" class="large" type="text" value="<?php echo $assignment_subtitle;?>" style="width:50%;" />
        <p class="dtlms-note"> <?php esc_html_e('Add sutitle for your assignment.','dtlms');?> </p>
    </div>
    
</div>
<!-- Subtitle End -->

<!-- Maximum Mark -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Maximum Mark', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
		<?php $assignment_maximum_mark = get_post_meta ( $post_id, 'assignment-maximum-mark', true ); ?>
        <input id="assignment-maximum-mark" name="assignment-maximum-mark" class="large" type="number" value="<?php echo $assignment_maximum_mark; ?>" style="width:10%;"  min="1" />
        <p class="dtlms-note"> <?php esc_html_e('Maximum mark for assignment. Default value is 100.','dtlms');?> </p>
    </div>
    
</div>
<!-- Maximum Mark End -->

<!-- Enable Text Area -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Enable Text Area', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
		<?php
        $assignment_enable_textarea = get_post_meta ( $post_id, 'assignment-enable-textarea', true );
        $switchclass = ($assignment_enable_textarea != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
        $checked = ($assignment_enable_textarea != '') ? ' checked="checked"' : '';
        ?>
        <div data-for="assignment-enable-textarea" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
        <input id="assignment-enable-textarea" class="hidden" type="checkbox" name="assignment-enable-textarea" value="true" <?php echo $checked;?> />
        <p class="dtlms-note"> <?php esc_html_e('If you wish you can enable text area for assignment.','dtlms');?> </p>
    </div>
    
</div>
<!-- Enable Text Area End -->

<!-- Enable File Upload -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Enable Attachment', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
		<?php
        $assignment_enable_attachment = get_post_meta ( $post_id, 'assignment-enable-attachment', true );
        $switchclass = ($assignment_enable_attachment != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
        $checked = ($assignment_enable_attachment != '') ? ' checked="checked"' : '';
        ?>
        <div data-for="assignment-enable-attachment" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
        <input id="assignment-enable-attachment" class="hidden" type="checkbox" name="assignment-enable-attachment" value="true" <?php echo $checked;?> />
        <p class="dtlms-note"> <?php esc_html_e('If you wish you can enable attachment for assignment.','dtlms');?> </p>
    </div>
    
</div>
<!-- Enable File Upload End -->

<!-- Attachment Types -->
<div class="dtlms-custom-box">

	<div class="dtlms-column dtlms-one-sixth first">
    
       <label><?php esc_html_e('Attachment Types','dtlms');?></label>

	</div>
	<div class="dtlms-column dtlms-five-sixth">
    
        <?php
		$assignment_attachment_type = get_post_meta ( $post_id, 'assignment-attachment-type', true);
		
		$attachment_types = dtlms_allowed_filetypes();

        $out = '';
        $out .= '<select id="assignment-attachment-type" name="assignment-attachment-type[]" multiple style="width:70%;" data-placeholder="'.esc_html__('Select Attachment Type...', 'dtlms').'" class="dtlms-chosen-select">' . "\n";
        $out .= '<option value=""></option>';
        if ( count( $attachment_types ) > 0 ) {
            foreach ($attachment_types as $attachment_type){
				if($assignment_attachment_type != '' && in_array($attachment_type, $assignment_attachment_type)) $str = 'selected="selected"'; else $str = '';
                $out .= '<option value="' . esc_attr( $attachment_type ) . '"' . $str . '>' . strtoupper( $attachment_type ) . '</option>' . "\n";
            }
        }
        $out .= '</select>' . "\n";
        echo $out;
        ?>
        <p class="dtlms-note"> <?php esc_html_e('Choose attachment types here.','dtlms');?> </p>

	</div>
    
</div>
<!-- Attachment Types End -->

<!-- Attachment Size -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Attachment Size (MB)', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
		<?php $assignment_attachment_size = get_post_meta ( $post_id, 'assignment-attachment-size', true); ?>
        <input id="assignment-attachment-size" name="assignment-attachment-size" class="large" type="number" value="<?php echo $assignment_attachment_size; ?>" style="width:10%;"  min="1" max="100" />
        <p class="dtlms-note"> <?php esc_html_e('Set maximum size for attachment. Set it less than <strong>'.dtlms_get_upload_size().'MB</strong>. If you like to have more than <strong>'.dtlms_get_upload_size().'MB</strong>, than you have to make changes in php.ini file. ','dtlms');?> </p>
    </div>
    
</div>
<!-- Attachment Size End -->

<div class="dtlms-custom-box">

    <!-- Drip Duration -->
    <div class="dtlms-column dtlms-one-half first">

        <div class="dtlms-column dtlms-one-third first">
           <label><?php esc_html_e('Duration', 'dtlms'); ?></label>
        </div>
        <div class="dtlms-column dtlms-two-third">
            <?php $duration = get_post_meta ( $post_id, 'duration', true ); ?>
            <input type="number" id="duration" name="duration" value="<?php echo $duration; ?>" min="0" class="large">
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
    
            echo '<select name="duration-parameter" data-placeholder="'.esc_html__('Select Drip Duration Parameter...', 'dtlms').'" class="dtlms-chosen-select">';
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