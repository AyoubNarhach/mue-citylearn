<?php

if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'DTLmsDesignThemes' ) ) {

	class DTLmsDesignThemes {

		function __construct() {

			add_filter( 'body_class', array( $this, 'dtlms_dt_body_class' ), 20 );

			add_filter( 'elearni_header_footer_default_cpt', array( $this, 'dtlms_dt_header_footer_default_cpt' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'dtlms_dt_enqueue_styles' ), 104 );

			add_action( 'dtlms_before_main_content', array( $this, 'dtlms_dt_before_main_content' ), 10 );
			add_action( 'dtlms_after_main_content', array( $this, 'dtlms_dt_after_main_content' ), 10 );

			add_action( 'dtlms_before_content', array( $this, 'dtlms_dt_before_content' ), 10 );
			add_action( 'dtlms_after_content', array( $this, 'dtlms_dt_after_content' ), 10 );

			$post_types = array ( 'dtlms_classes', 'dtlms_courses', 'dtlms_packages' );
			foreach( $post_types as $post_type ) {
				add_action( 'add_meta_boxes_'.$post_type, array( $this, 'dtlms_dt_breadcrumb_option_metabox' ) );
			}

			add_action ( 'save_post', array ( $this, 'dtlms_dt_breadcrumb_save_post_meta' ) );

		}

		function dtlms_dt_breadcrumb_option_metabox( $post ) {

			add_meta_box ( 'dtlms-bredcrumb-option-metabox', esc_html__( 'Breadcrumb Options', 'dtlms' ), array ( $this, 'dtlms_breadcrumb_option_metabox' ), $post->post_type, 'normal', 'high' );

		}

		function dtlms_breadcrumb_option_metabox( ) {

			global $post;
			$post_id = $post->ID;

			echo '<input type="hidden" name="dtlms_breadcrumb_meta_nonce" value="'.wp_create_nonce('dtlms_breadcrumb_nonce').'" />';

			$dtlms_breadcrumb = get_post_meta($post_id, 'dtlms-breadcrumb', true);
			?>

			<div class="dtlms-custom-box">

			    <div class="dtlms-column dtlms-one-half first">

			        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Show Breadcrumb', 'dtlms');?></div>
			        <div class="dtlms-column dtlms-two-third">
			            <?php
			            $current = get_post_meta($post_id, 'dtlms-show-breadcrumb', true);
			            $switchclass = ( $current === "true") ? 'checkbox-switch-on' :'checkbox-switch-off';    
			            $checked = ( $current === "true") ? ' checked="checked" ' : '';
			            ?>
			            <div data-for="dtlms-show-breadcrumb" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
			            <input id="dtlms-show-breadcrumb" class="hidden" type="checkbox" name="dtlms-show-breadcrumb" value="true" <?php echo $checked;?>/>
			            <p class="dtlms-note"> <?php esc_html_e('Choose "Yes" if you like to enable breadcrumb.', 'dtlms');?> </p>
			        </div>

			    </div>

			    <div class="dtlms-column dtlms-one-half"></div>

			</div>

			<div class="dtlms-custom-box">

			    <div class="dtlms-column dtlms-one-half first">

			        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Breadcrumb Position', 'dtlms');?></div>
			        <div class="dtlms-column dtlms-two-third">
			            <?php
			            $breadcrumb_position = (isset($dtlms_breadcrumb['breadcrumb_position']) && $dtlms_breadcrumb['breadcrumb_position'] != '') ? $dtlms_breadcrumb['breadcrumb_position'] : 'header-top-relative';

			            $breadcrumbpositions = array ('header-top-absolute' => esc_html__('Behind the Header','dtlms'), 'header-top-relative' => esc_html__('Default','dtlms'));
			    
			            echo '<select name="dtlms-breadcrumb[breadcrumb_position]" class="dtlms-chosen-select">';
			                foreach ($breadcrumbpositions as $breadcrumbposition_key => $breadcrumbposition) {
			                    echo '<option value="'.esc_attr($breadcrumbposition_key).'" '.selected($breadcrumbposition_key, $breadcrumb_position, false).'>'.esc_html($breadcrumbposition).'</option>';
			                }
			            echo '</select>';
			            ?>
			        </div>

			    </div>

			    <div class="dtlms-column dtlms-one-half">

			        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Breadcrumb Image', 'dtlms');?></div>
			        <div class="dtlms-column dtlms-two-third">
			            <div class="dtlms-upload-media-items-container">
							<?php 
							$breadcrumb_image_url = (isset($dtlms_breadcrumb['breadcrumb_background']['breadcrumb_image_url']) && $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_image_url'] != '') ? $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_image_url'] : '';
							$breadcrumb_image_id = (isset($dtlms_breadcrumb['breadcrumb_background']['breadcrumb_image_id']) && $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_image_id'] != '') ? $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_image_id'] : '';
			                ?>
			                <input name="dtlms-breadcrumb[breadcrumb_background][breadcrumb_image_url]" type="text" class="uploadfieldurl" readonly value="<?php echo esc_url($breadcrumb_image_url);?>"/>
			                <input name="dtlms-breadcrumb[breadcrumb_background][breadcrumb_image_id]" type="hidden" class="uploadfieldid" readonly value="<?php echo esc_attr($breadcrumb_image_id);?>"/>
			                <input type="button" value="<?php esc_html_e('Upload','dtlms');?>" class="dtlms-upload-media-item-button show-preview" />
			                <input type="button" value="<?php esc_html_e('Remove','dtlms');?>" class="dtlms-upload-media-item-reset" />
			                <?php echo dtlms_adminpanel_image_preview($breadcrumb_image_url); ?>
			            </div>
			        </div>

			    </div>

			</div>

			<div class="dtlms-custom-box">

			    <div class="dtlms-column dtlms-one-half first">

			        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Breadcrumb Background Repeat', 'dtlms');?></div>
			        <div class="dtlms-column dtlms-two-third">
			            <?php
			            $breadcrumb_repeat = (isset($dtlms_breadcrumb['breadcrumb_background']['breadcrumb_repeat']) && $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_repeat'] != '') ? $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_repeat'] : 'repeat';

			            $breadcrumbrepeats = array ('repeat' => 'repeat', 'repeat-x' => 'repeat-x', 'repeat-y' => 'repeat-y', 'no-repeat' => 'no-repeat', 'inherit' => 'inherit' );

			            echo '<select name="dtlms-breadcrumb[breadcrumb_background][breadcrumb_repeat]" class="dtlms-chosen-select">';
			                foreach ($breadcrumbrepeats as $breadcrumbrepeat_key => $breadcrumbrepeat) {
			                    echo '<option value="'.esc_attr($breadcrumbrepeat_key).'" '.selected($breadcrumbrepeat_key, $breadcrumb_repeat, false).'>'.esc_html($breadcrumbrepeat).'</option>';
			                }
			            echo '</select>';
			            ?>
			        </div>

			    </div>

			    <div class="dtlms-column dtlms-one-half">

			        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Breadcrumb Background Position', 'dtlms');?></div>
			        <div class="dtlms-column dtlms-two-third">
			            <?php
			            $breadcrumb_position = (isset($dtlms_breadcrumb['breadcrumb_background']['breadcrumb_position']) && $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_position'] != '') ? $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_position'] : 'left top';

			            $breadcrumbpositions = array ('left top' => 'left top', 'left center' => 'left center', 'left bottom' => 'left bottom', 'right top' => 'right top', 'right center' => 'right center', 'right bottom' => 'right bottom', 'center top' => 'center top', 'center center' => 'center center', 'center bottom' => 'center bottom');

			            echo '<select name="dtlms-breadcrumb[breadcrumb_background][breadcrumb_position]" class="dtlms-chosen-select">';
			                foreach ($breadcrumbpositions as $breadcrumbposition_key => $breadcrumbposition) {
			                    echo '<option value="'.esc_attr($breadcrumbposition_key).'" '.selected($breadcrumbposition_key, $breadcrumb_position, false).'>'.esc_html($breadcrumbposition).'</option>';
			                }
			            echo '</select>';			            
			            ?>
			        </div>

			    </div>

			</div>

			<div class="dtlms-custom-box">

			    <div class="dtlms-column dtlms-one-half first">

			        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Breadcrumb Background Attachment', 'dtlms');?></div>
			        <div class="dtlms-column dtlms-two-third">
			            <?php
			            $breadcrumb_attachment = (isset($dtlms_breadcrumb['breadcrumb_background']['breadcrumb_attachment']) && $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_attachment'] != '') ? $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_attachment'] : 'scroll';

			            $breadcrumbattachments = array ('scroll' => 'scroll', 'fixed' => 'fixed');

			            echo '<select name="dtlms-breadcrumb[breadcrumb_background][breadcrumb_attachment]" class="dtlms-chosen-select">';
			                foreach ($breadcrumbattachments as $breadcrumbattachment_key => $breadcrumbattachment) {
			                    echo '<option value="'.esc_attr($breadcrumbattachment_key).'" '.selected($breadcrumbattachment_key, $breadcrumb_attachment, false).'>'.esc_html($breadcrumbattachment).'</option>';
			                }
			            echo '</select>';
			            ?>
			        </div>

			    </div>

			    <div class="dtlms-column dtlms-one-half">

			        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Breadcrumb Background Size', 'dtlms');?></div>
			        <div class="dtlms-column dtlms-two-third">
			            <?php
			            $breadcrumb_size = (isset($dtlms_breadcrumb['breadcrumb_background']['breadcrumb_size']) && $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_size'] != '') ? $dtlms_breadcrumb['breadcrumb_background']['breadcrumb_size'] : 'auto';

			            $breadcrumbsizes = array ('size' => 'size', 'cover' => 'cover', 'contain' => 'contain', 'inherit' => 'inherit', 'initial' => 'initial');

			            echo '<select name="dtlms-breadcrumb[breadcrumb_background][breadcrumb_size]" class="dtlms-chosen-select">';
			                foreach ($breadcrumbsizes as $breadcrumbsize_key => $breadcrumbsize) {
			                    echo '<option value="'.esc_attr($breadcrumbsize_key).'" '.selected($breadcrumbsize_key, $breadcrumb_size, false).'>'.esc_html($breadcrumbsize).'</option>';
			                }
			            echo '</select>';			            
			            ?>
			        </div>

			    </div>

			</div>

			<div class="dtlms-custom-box">

			    <div class="dtlms-column dtlms-one-half first">

			        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Breadcrumb Background Color', 'dtlms');?></div>
			        <div class="dtlms-column dtlms-two-third">
			            <?php
			            $breadcrumb_color = (isset($dtlms_breadcrumb['breadcrumb_background']['color']) && $dtlms_breadcrumb['breadcrumb_background']['color'] != '') ? $dtlms_breadcrumb['breadcrumb_background']['color'] : '';

			            echo '<input name="dtlms-breadcrumb[breadcrumb_background][color]" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="'.$breadcrumb_color.'" />';
			            ?>
			        </div>

			    </div>

			    <div class="dtlms-column dtlms-one-half"></div>

			</div>

			<div class="dtlms-custom-box">

			    <div class="dtlms-column dtlms-one-half first">

			        <div class="dtlms-column dtlms-one-third first"><?php esc_html_e( 'Remove Top Space', 'dtlms');?></div>
			        <div class="dtlms-column dtlms-two-third">
			            <?php
			            $current = get_post_meta($post_id, 'dtlms-remove-top-space', true);
			            $switchclass = ( $current === "true") ? 'checkbox-switch-on' :'checkbox-switch-off';    
			            $checked = ( $current === "true") ? ' checked="checked" ' : '';
			            ?>
			            <div data-for="dtlms-remove-top-space" class="dtlms-checkbox-switch <?php echo $switchclass;?>"></div>
			            <input id="dtlms-remove-top-space" class="hidden" type="checkbox" name="dtlms-remove-top-space" value="true" <?php echo $checked;?>/>
			            <p class="dtlms-note"> <?php esc_html_e('Choose "Yes" if you like to remove top space.', 'dtlms');?> </p>
			        </div>

			    </div>

			    <div class="dtlms-column dtlms-one-half"></div>

			</div>
			<?php

		}

		function dtlms_dt_breadcrumb_save_post_meta($post_id) {
			
			if( key_exists ( '_inline_edit', $_POST )) :
				if ( wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) return;
			endif;
			
			if( key_exists( 'dtlms_breadcrumb_meta_nonce',$_POST ) ) :
				if ( ! wp_verify_nonce( $_POST['dtlms_breadcrumb_meta_nonce'], 'dtlms_breadcrumb_nonce' ) ) return;
			endif;
		 
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

			if (!current_user_can('edit_post', $post_id)) :
				return;
			endif;

			if ( (key_exists('post_type', $_POST)) && (in_array($_POST['post_type'], array ('dtlms_courses', 'dtlms_classes', 'dtlms_packages')))) :

				if(isset($_POST['dtlms-show-breadcrumb']) && $_POST['dtlms-show-breadcrumb'] != '') {
					update_post_meta($post_id, 'dtlms-show-breadcrumb', stripslashes($_POST['dtlms-show-breadcrumb']));
				} else {
					delete_post_meta($post_id, 'dtlms-show-breadcrumb' );
				}
				
				if(isset($_POST['dtlms-breadcrumb']) && !empty($_POST['dtlms-breadcrumb'])) {
					update_post_meta ( $post_id, 'dtlms-breadcrumb', array_unique( $_POST ['dtlms-breadcrumb'] ) );
				} else {
					delete_post_meta ( $post_id, 'dtlms-breadcrumb' );
				}

				if(isset($_POST['dtlms-remove-top-space']) && $_POST['dtlms-remove-top-space'] != '') {
					update_post_meta($post_id, 'dtlms-remove-top-space', stripslashes($_POST['dtlms-remove-top-space']));
				} else {
					delete_post_meta($post_id, 'dtlms-remove-top-space' );
				}

			endif;
			
		}

		function dtlms_dt_body_class( $classes ) {

			if (is_singular( 'dtlms_classes' ) || is_singular( 'dtlms_courses' ) || is_singular( 'dtlms_packages' )) {

				global $post;

				$dtlms_remove_top_space = get_post_meta($post->ID, 'dtlms-remove-top-space', true);

				if($dtlms_remove_top_space != '') {
					$classes[] = 'dtlms-remove-top-space';
				}

			}

			return $classes;

		}	

		function dtlms_dt_header_footer_default_cpt( $custom_posts ) {

			$custom_posts[] = 'dtlms_classes';
			$custom_posts[] = 'dtlms_courses';
			$custom_posts[] = 'dtlms_packages';

			return $custom_posts;

		}	

		function dtlms_dt_enqueue_styles() {

			wp_enqueue_style ( 'dtlms-designthemes', plugins_url ('designthemes-lms-addon') . '/css/themes/designthemes.css' );

		}

		function dtlms_dt_before_main_content() {

			if (is_singular( 'dtlms_classes' ) || is_singular( 'dtlms_courses' ) || is_singular( 'dtlms_lessons' ) || is_singular( 'dtlms_quizzes' ) || is_singular( 'dtlms_questions' ) || is_singular( 'dtlms_assignments' ) || is_singular( 'dtlms_certificates' ) || is_singular( 'dtlms_packages' )) {	

				global $post;
				$post_id = $post->ID;

				$global_breadcrumb = cs_get_option( 'show-breadcrumb' );
				$header_class	   = cs_get_option( 'breadcrumb-position' ); 

				$dtlms_show_breadcrumb = get_post_meta($post_id, 'dtlms-show-breadcrumb', true);
				?>

				<div id="header-wrapper" class="<?php echo esc_attr($header_class); ?>">

					<header id="header">
						<div class="container">
							<?php do_action( 'elearni_header' ); ?>
					    </div>
					</header>

				    <?php
			        if( !empty( $global_breadcrumb ) ) {

			            if( isset( $dtlms_show_breadcrumb ) && $dtlms_show_breadcrumb == 'true' ) {

			                $bstyle = elearni_cs_get_option( 'breadcrumb-style', 'default' );

			                $breadcrumbs = array ();

			                if( $post->post_parent ) {

			                    $parent_id  = $post->post_parent;
			                    $parents = array();

			                    while( $parent_id ) {
			                        $page = get_page( $parent_id );
			                        $parents[] = '<a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a>';
			                        $parent_id  = $page->post_parent;
			                    }

			                    $parents = array_reverse( $parents );
			                    $breadcrumbs = array_merge_recursive($breadcrumbs, $parents);

			                }

			                if(is_singular( 'dtlms_classes' )) {
			                	$breadcrumbs[] = '<a href="'.esc_url(get_post_type_archive_link('dtlms_classes')).'">'.esc_html__('Classes', 'dtlms').'</a>';
			                } else if(is_singular( 'dtlms_courses' )) {
			                	$breadcrumbs[] = '<a href="'.esc_url(get_post_type_archive_link('dtlms_courses')).'">'.esc_html__('Courses', 'dtlms').'</a>';
			                }

			                $breadcrumbs[] = the_title( '<span class="current">', '</span>', false );
			                $style = dtlms_breadcrumb_css($post_id);

			                elearni_breadcrumb_output ( the_title( '<h1>', '</h1>',false ), $breadcrumbs, $bstyle, $style );

			            }
			        }			        
				    ?>
				</div>

				<?php

			}


			if(is_post_type_archive('dtlms_classes') || is_post_type_archive('dtlms_courses') || is_post_type_archive('dtlms_lessons') || is_post_type_archive('dtlms_quizzes') || is_post_type_archive('dtlms_questions') || is_post_type_archive('dtlms_assignments') || is_post_type_archive('dtlms_certificates') || is_post_type_archive('dtlms_packages') || is_tax ( 'course_category' ) || is_tax ( 'question_category' ) || is_author()) {

				$global_breadcrumb = cs_get_option( 'show-breadcrumb' );
				$header_class	   = cs_get_option( 'breadcrumb-position' ); 
				?>

				<div id="header-wrapper" class="<?php echo esc_attr($header_class); ?>">

					<header id="header">
						<div class="container">
							<?php do_action( 'elearni_header' ); ?>
					    </div>
					</header>

				    <?php
				    if( !empty( $global_breadcrumb ) ) {

				    	$bstyle = elearni_cs_get_option( 'breadcrumb-style', 'default' );
				    	$style = elearni_breadcrumb_css();

				        $title = '<h1>'.get_the_archive_title().'</h1>';
				        $breadcrumbs = array();

				        if ( is_category() ) {
				            $breadcrumbs[] = '<a href="'. get_category_link( get_query_var('cat') ) .'">' . single_cat_title('', false) . '</a>';
				        } elseif ( is_tag() ) {
				            $breadcrumbs[] = '<a href="'. get_tag_link( get_query_var('tag_id') ) .'">' . single_tag_title('', false) . '</a>';
				        } elseif( is_author() ) {
				        	
				        	$author_id = get_queried_object_id();
				            $breadcrumbs[] = '<a href="'.get_the_author_meta( 'user_url', $author_id ).'">' . get_the_author_meta('display_name', $author_id) . '</a>';
				            $title = '<h1>'.get_the_author_meta('display_name', $author_id).'</h1>';

				        } elseif( is_day() || is_time() ){
				            $breadcrumbs[] = '<a href="'. get_year_link( get_the_time('Y') ) . '">'. get_the_time('Y') .'</a>';
				            $breadcrumbs[] = '<a href="'. get_month_link( get_the_time('Y'), get_the_time('m') ) .'">'. get_the_time('F') .'</a>';
				            $breadcrumbs[] = '<a href="'. get_day_link( get_the_time('Y'), get_the_time('m'), get_the_time('d') ) .'">'. get_the_time('d') .'</a>';
				        } elseif( is_month() ){
				            $breadcrumbs[] = '<a href="'. get_year_link( get_the_time('Y') ) . '">' . get_the_time('Y') . '</a>';
				            $breadcrumbs[] = '<a href="'. get_month_link( get_the_time('Y'), get_the_time('m') ) .'">'. get_the_time('F') .'</a>';
				        } elseif( is_year() ){
				            $breadcrumbs[] = '<a href="'. get_year_link( get_the_time('Y') ) .'">'. get_the_time('Y') .'</a>';
				        }

				        elearni_breadcrumb_output ( $title, $breadcrumbs, $bstyle, $style );
				        
				    }
				    ?>

				</div>

				<?php

			}


		}

		function dtlms_dt_after_main_content() {
			
		}

		function dtlms_dt_before_content() {

			if (is_singular( 'dtlms_classes' ) || is_singular( 'dtlms_courses' ) || is_singular( 'dtlms_lessons' ) || is_singular( 'dtlms_quizzes' ) || is_singular( 'dtlms_questions' ) || is_singular( 'dtlms_assignments' ) || is_singular( 'dtlms_certificates' ) || is_singular( 'dtlms_packages' ) || is_author()) {

				echo '<div id="main">';
						echo '<div class="container">';
							echo '<section id="primary" class="content-full-width">';

			}

			if(is_post_type_archive('dtlms_classes') || is_post_type_archive('dtlms_courses') || is_post_type_archive('dtlms_lessons') || is_post_type_archive('dtlms_quizzes') || is_post_type_archive('dtlms_questions') || is_post_type_archive('dtlms_assignments') || is_post_type_archive('dtlms_certificates') || is_post_type_archive('dtlms_packages') || is_tax ( 'course_category' ) || is_tax ( 'question_category' )) {

				echo '<div id="main">';
						echo '<div class="container">';
							echo '<section id="primary" class="content-full-width">';

			}

		}

		function dtlms_dt_after_content() {

			if (is_singular( 'dtlms_classes' ) || is_singular( 'dtlms_courses' ) || is_singular( 'dtlms_lessons' ) || is_singular( 'dtlms_quizzes' ) || is_singular( 'dtlms_questions' ) || is_singular( 'dtlms_assignments' ) || is_singular( 'dtlms_certificates' ) || is_singular( 'dtlms_packages' ) || is_author()) {

						echo '</section>';
					echo '</div>';
				echo '</div>';

			}

			if(is_post_type_archive('dtlms_classes') || is_post_type_archive('dtlms_courses') || is_post_type_archive('dtlms_lessons') || is_post_type_archive('dtlms_quizzes') || is_post_type_archive('dtlms_questions') || is_post_type_archive('dtlms_assignments') || is_post_type_archive('dtlms_certificates') || is_post_type_archive('dtlms_packages') || is_tax ( 'course_category' ) || is_tax ( 'question_category' )) {

						echo '</section>';
					echo '</div>';
				echo '</div>';

		    }


		}

	}

	new DTLmsDesignThemes();

}

?>