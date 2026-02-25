<?php
global $post;
$post_id = $post->ID;

?>

<div class="dtlms-custom-box">

    <!-- Page Layout -->
    <div class="dtlms-column dtlms-one-column first">

        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Page Layout', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $page_layout = get_post_meta($post_id, 'page-layout', true);
            $page_layout = ($page_layout != '') ? $page_layout : 'type1';

            $pagelayouts = array ('type1' => 'Type 1', 'type2' => 'Type 2', 'type3' => 'Type 3', 'type4' => 'Type 4');
    
            echo '<select name="page-layout" data-placeholder="'.esc_html__('Choose Header Design ...', 'dtlms').'" class="dtlms-chosen-select">';
                foreach ($pagelayouts as $pagelayout_key => $pagelayout) {
                    echo '<option value="'.esc_attr($pagelayout_key).'" '.selected($pagelayout_key, $page_layout, false).'>'.esc_html($pagelayout).'</option>';
                }
            echo '</select>';
            ?>
        </div>

    </div>
    <!-- Page Layout End -->

</div>

<div class="dtlms-custom-box">

    <!-- Header Design -->
    <div class="dtlms-column dtlms-one-half first">

        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Header Design', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $header_design = get_post_meta($post_id, 'header-design', true);
            $header_design = ($header_design != '') ? $header_design : 'type1';

            $headerdesigns = array ('type1' => 'Type 1', 'type2' => 'Type 2', 'type3' => 'Type 3', 'type4' => 'Type 4');
    
            echo '<select name="header-design" data-placeholder="'.esc_html__('Choose Header Design ...', 'dtlms').'" class="dtlms-chosen-select">';
                foreach ($headerdesigns as $headerdesign_key => $headerdesign) {
                    echo '<option value="'.esc_attr($headerdesign_key).'" '.selected($headerdesign_key, $header_design, false).'>'.esc_html($headerdesign).'</option>';
                }
            echo '</select>';
            ?>
        </div>

    </div>
    <!-- Header Design End -->

    <!-- Course Info Design -->
    <div class="dtlms-column dtlms-one-half">

        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Course Info Design', 'dtlms');?></div>
        <div class="dtlms-column dtlms-two-third">
            <?php
            $courseinfo_design = get_post_meta($post_id, 'courseinfo-design', true);
            $courseinfo_design = ($courseinfo_design != '') ? $courseinfo_design : 'type1';

            $courseinfodesigns = array ('type1' => 'Type 1', 'type2' => 'Type 2', 'type3' => 'Type 3', 'type4' => 'Type 4');
    
            echo '<select name="courseinfo-design" data-placeholder="'.esc_html__('Choose Course Info Design ...', 'dtlms').'" class="dtlms-chosen-select">';
                foreach ($courseinfodesigns as $courseinfodesign_key => $courseinfodesign) {
                    echo '<option value="'.esc_attr($courseinfodesign_key).'" '.selected($courseinfodesign_key, $courseinfo_design, false).'>'.esc_html($courseinfodesign).'</option>';
                }
            echo '</select>';
            ?>
        </div>

    </div>
    <!-- Course Info Design End -->    

</div>