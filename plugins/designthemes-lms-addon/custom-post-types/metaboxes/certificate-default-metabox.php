<?php
global $post;
$post_id = $post->ID;

echo '<input type="hidden" name="dtlms_certififcates_meta_nonce" value="'.wp_create_nonce('dtlms_certififcates_nonce').'" />'; 
?>

<!-- Shortcodes -->
<div class="dtlms-custom-box">

    <div class="dtlms-column dtlms-one-sixth first">
       <label><?php esc_html_e('Shortcode', 'dtlms'); ?></label>
    </div>
    <div class="dtlms-column dtlms-five-sixth">
        <strong><?php echo esc_html__('Use below shortcode to add certificate content dynamically.', 'dtlms'); ?></strong><br />
        <p><strong><i>[dtlms_certificate_details item_type="*" /]</i></strong></p>
        <p><?php echo esc_html__('Instead of "*" you can make use of below keywords', 'dtlms'); ?>
        <ul>
            <li><strong><i><?php echo 'student_name'; ?></i></strong> - <?php echo esc_html__('To display student name', 'dtlms'); ?></li>
            <li><strong><i><?php echo 'item_name'; ?></i></strong> - <?php echo esc_html__('To display corresponding class name or course name', 'dtlms'); ?></li>
            <li><strong><i><?php echo 'student_percent'; ?></i></strong> - <?php echo esc_html__('To display student percentage', 'dtlms'); ?></li>
            <li><strong><i><?php echo 'date'; ?></i></strong> - <?php echo esc_html__('To display certificate date', 'dtlms'); ?></li>
        </ul>
    </div>
    
</div>
<!-- Shortcodes End -->