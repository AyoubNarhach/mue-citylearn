<?php
class DTLMSShortcodes {
	
	function __construct() {

		add_shortcode ( 'dtlms_columns', array ( $this, 'dtlms_columns' ) );
		add_shortcode ( 'dtlms_vertical_tabs', array ( $this, 'dtlms_vertical_tabs' ) );
		add_shortcode ( 'dtlms_vertical_tab', array ( $this, 'dtlms_vertical_tab' ) );

		add_shortcode ( 'dtlms_login_logout_links', array ( $this, 'dtlms_login_logout_links' ) );
		add_shortcode ( 'dtlms_certificate_details', array ( $this, 'dtlms_certificate_details' ) );
		add_shortcode ( 'dtlms_certificate', array ( $this, 'dtlms_certificate' ) );

		add_shortcode ( 'dtlms_courses_listing', array ( $this, 'dtlms_courses_listing' ) );
		add_shortcode ( 'dtlms_classes_listing', array ( $this, 'dtlms_classes_listing' ) );
		add_shortcode ( 'dtlms_packages_listing', array ( $this, 'dtlms_packages_listing' ) );

		add_shortcode ( 'dtlms_course_categories', array ( $this, 'dtlms_course_categories' ) );
		add_shortcode ( 'dtlms_instructor_list', array ( $this, 'dtlms_instructor_list' ) );

		// Dashboard Shortcodes - Admin & Instructor
		add_shortcode ( 'dtlms_total_items', array ( $this, 'dtlms_total_items' ) );
		add_shortcode ( 'dtlms_total_items_chart', array ( $this, 'dtlms_total_items_chart' ) );
		add_shortcode ( 'dtlms_purchases_overview_chart', array ( $this, 'dtlms_purchases_overview_chart' ) );
		add_shortcode ( 'dtlms_instructor_commission_earnings', array ( $this, 'dtlms_instructor_commission_earnings' ) );

		add_shortcode ( 'dtlms_instructor_courses', array ( $this, 'dtlms_instructor_courses' ) );
		add_shortcode ( 'dtlms_instructor_added_courses', array ( $this, 'dtlms_instructor_added_courses' ) );
		add_shortcode ( 'dtlms_instructor_commissions', array ( $this, 'dtlms_instructor_commissions' ) );
		add_shortcode ( 'dtlms_student_courses', array ( $this, 'dtlms_student_courses' ) );
		add_shortcode ( 'dtlms_package_details', array ( $this, 'dtlms_package_details' ) );
		add_shortcode ( 'dtlms_class_details', array ( $this, 'dtlms_class_details' ) );
		

		// Dashboard Shortcodes - Student
		add_shortcode ( 'dtlms_student_purchased_items', array ( $this, 'dtlms_student_purchased_items' ) );
		add_shortcode ( 'dtlms_student_assigned_items', array ( $this, 'dtlms_student_assigned_items' ) );
		add_shortcode ( 'dtlms_student_undergoing_items', array ( $this, 'dtlms_student_undergoing_items' ) );
		add_shortcode ( 'dtlms_student_underevaluation_items', array ( $this, 'dtlms_student_underevaluation_items' ) );
		add_shortcode ( 'dtlms_student_completed_items', array ( $this, 'dtlms_student_completed_items' ) );

		add_shortcode ( 'dtlms_student_badges', array ( $this, 'dtlms_student_badges' ) );
		add_shortcode ( 'dtlms_student_certificates', array ( $this, 'dtlms_student_certificates' ) );

		add_shortcode ( 'dtlms_student_purchased_items_list', array ( $this, 'dtlms_student_purchased_items_list' ) );
		add_shortcode ( 'dtlms_student_assigned_items_list', array ( $this, 'dtlms_student_assigned_items_list' ) );
		add_shortcode ( 'dtlms_student_undergoing_items_list', array ( $this, 'dtlms_student_undergoing_items_list' ) );
		add_shortcode ( 'dtlms_student_underevaluation_items_list', array ( $this, 'dtlms_student_underevaluation_items_list' ) );
		add_shortcode ( 'dtlms_student_completed_items_list', array ( $this, 'dtlms_student_completed_items_list' ) );

		add_shortcode ( 'dtlms_student_course_curriculum_details', array ( $this, 'dtlms_student_course_curriculum_details' ) );
		add_shortcode ( 'dtlms_student_course_events', array ( $this, 'dtlms_student_course_events' ) );	

		add_shortcode ( 'dtlms_student_class_curriculum_details', array ( $this, 'dtlms_student_class_curriculum_details' ) );	

	}

	function dtlms_shortcodeHelper($content = null) {
		$content = do_shortcode ( shortcode_unautop ( $content ) );
		$content = preg_replace ( '#^<\/p>|^<br \/>|<p>$#', '', $content );
		$content = preg_replace ( '#<br \/>#', '', $content );
		return trim ( $content );
	}

	function dtlms_columns( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'columns' => '',
					'first_item' => '',
					'class' => '',

				), $attrs, 'dtlms_columns' );


		$output = '';

		$column_class = 'dtlms-one-fourth';
		if($attrs['columns'] == 1) {
			$column_class = 'dtlms-one';
		} else if($attrs['columns'] == 2) {
			$column_class = 'dtlms-one-half';
		} else if($attrs['columns'] == 3) {
			$column_class = 'dtlms-one-third';
		} else if($attrs['columns'] == 4) {
			$column_class = 'dtlms-one-fourth';						
		}

		$first_class = '';
		if($attrs['first_item'] == 'true') {
			$first_class = 'first';
		}


		$output .= '<div class="dtlms-columns '.esc_attr($column_class).' '.esc_attr($first_class).' '.esc_attr($attrs['class']).'">';
			$output .= DTLMSShortcodes::dtlms_shortcodeHelper ( $content );
		$output .= '</div>';

		return $output;

	}
	
	function dtlms_vertical_tabs( $attrs, $content = null ) {

		extract ( shortcode_atts ( array (
			'class' => '',
		), $attrs ) );


		preg_match_all( '/dtlms_vertical_tab([^\]]+)/i', $content, $matches, PREG_OFFSET_CAPTURE );
		$tab_titles = array();
		if ( isset( $matches[1] ) ) {
			$tab_titles = $matches[1];
		}

		$tabs_nav = '<ul class="dtlms-tabs-vertical">';

			foreach ( $tab_titles as $tab ) {

				$tab_atts = shortcode_parse_atts( $tab[0] );

				$icon = '';

				if( isset($tab_atts['icon_type']) && $tab_atts['icon_type'] === 'fontawesome' ) {
					$icon = isset( $tab_atts['icon'] ) ? $tab_atts['icon'] : '';
				} elseif( isset($tab_atts['icon_type']) && $tab_atts['icon_type'] === 'custom' ){
					$icon = isset( $tab_atts['icon_class'] ) ? $tab_atts['icon_class'] : '';
				}

				$icon = !empty( $icon ) ? '<span class="'.$icon.'"></span>' : '';
				$subtitle = !empty( $tab_atts['sub_title'] ) ? DTCoreShortcodesDefination::dtShortcodeHelper ( $tab_atts['sub_title'] ) : '';

				$tabs_nav .= '<li><a href="javascript:void(0);">'.$icon.$tab_atts['title'].'</a>'.$subtitle.'</li>';

			}

		$tabs_nav .= '</ul>';

		$a = '[dtlms_vertical_tab class="dtlms-tabs-vertical-content" ';
		$content = str_replace( '[dtlms_vertical_tab', $a, $content);
		$out = do_shortcode( $content );

		return '<div class="dtlms-tabs-vertical-container '.$class.'">'.$tabs_nav.$out.'</div>';

	}

	function dtlms_vertical_tab( $attrs, $content = null ) {

		extract ( shortcode_atts ( array (
			'class' => '',
		), $attrs ) );
		
		$content = do_shortcode( $content );
		
		return '<div class="'.esc_attr($class).'">'.$content.'</div>';

	}

	function dtlms_login_logout_links( $attrs, $content = null ) {

		extract ( shortcode_atts ( array (
			'class' => '',
			'show_registration' => 'true',
		), $attrs ) );

		$out = '';

		if(is_user_logged_in()) {

			$current_user = wp_get_current_user();
			$user_info = get_userdata($current_user->ID);

			$redirect_link = dtlms_get_login_redirect_url($user_info);

			$out .= '<ul class="dtlms-custom-login '.esc_attr($class).'">';

				$out .= '<li><a href="'.$redirect_link.'">'.get_avatar( $current_user->ID, 150).'<span>'.'&nbsp;'.$current_user->display_name.' </span></a></li>';

				$out .= '<li><a href="'.wp_logout_url(home_url()).'" title="'.esc_html__('Logout', 'dtlms').'"><i class="fa fa-lock"></i>'.esc_html__('Logout', 'dtlms').'</a></li>';

			$out .= '</ul>';
			
		} else {

			$out .= '<ul class="dtlms-custom-login '.esc_attr($class).'">';

				$out .= '<li><a href="#" title="'.esc_html__('Login', 'dtlms').'" class="dtlms-login-link" onclick="return false"><i class="fa fa-unlock-alt"></i>'.esc_html__('Login', 'dtlms').'</a></li>';	

				if($show_registration == 'true') {

					$out .= '<li><a href="'.wp_registration_url().'" title="'.esc_html__('Register', 'dtlms').'"><i class="fa fa-user-times"></i>'.esc_html__('Register', 'dtlms').'</a></li>';

				}

            $out .= '</ul>';

            //$out .= dtlms_generate_loader_html(false);
			
		}	
			
		return $out;

	}

	function dtlms_certificate_details($attrs, $content = null) {

		extract ( shortcode_atts ( array (
				'item_type' => 'student_name',
		), $attrs ) );

		$out = '';
		
		$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : get_current_user_id();
		$item_id = isset($_REQUEST['item_id']) ? $_REQUEST['item_id'] : -1;
		$grade_id = isset($_REQUEST['grade_id']) ? $_REQUEST['grade_id'] : -1;
	
		if($item_type == 'student_name') {

			$user_info = get_userdata($user_id);
			$out .=  isset($user_info->display_name) ? '<div class="dtlms-certificate-studentname">'.$user_info->display_name.'</div>' : '';

		} else if($item_type == 'item_name') {

			$out .= '<div class="dtlms-certificate-itemname">'.get_the_title($item_id).'</div>';

		} else if($item_type == 'student_percent') {

			$user_percentage = get_post_meta($grade_id, 'user-percentage', true); 
			if($user_percentage != '') {
				$out .=  '<div class="dtlms-certificate-userpercentage">'.$user_percentage.esc_html__('%', 'dtlms').'</div>';
			}
			
		} else if($item_type == 'date') {

			$date_on_certificate = get_post_meta($grade_id, 'date-on-certificate', true); 
			if($date_on_certificate != '') {
				$out .=  '<div class="dtlms-certificate-date">'.$date_on_certificate.'</div>';
			} else {
				$out .=  '<div class="dtlms-certificate-date">'.get_the_date(get_option('date_format'), $grade_id).'</div>';
			}

		}
		
		return $out;

	}

	function dtlms_certificate($attrs, $content = null) {

		extract ( shortcode_atts ( array (
				'type' => 'type1',
				'logo1' => '',
				'logo2' => '',
				'heading' => '',
				'subheading' => '',
				'footer_logo' => '',
				'signature' => '',
		), $attrs ) );


		$logo1_attachment = wp_get_attachment_image_src($logo1, 'full');
		$logo2_attachment = wp_get_attachment_image_src($logo2, 'full');
		$footer_logo_attachment = wp_get_attachment_image_src($footer_logo, 'full');
		$signature_attachment = wp_get_attachment_image_src($signature, 'full');


		$output = '';

		$student_name = do_shortcode('[dtlms_certificate_details item_type="student_name" /]');
		$item_name = do_shortcode('[dtlms_certificate_details item_type="item_name" /]');
		$student_percent = do_shortcode('[dtlms_certificate_details item_type="student_percent" /]');
		$date = do_shortcode('[dtlms_certificate_details item_type="date" /]');
		
		if($type == 'type1') {

			$output .= '<div class="dtlms-certificate-body">';
				$output .= '<div class="dtlms-certificate-container type1">';
					$output .= '<div class="dtlms-certificate-wrapper">';
						$output .= '<div class="dtlms-certificate-inner-wrapper">';
							$output .= '<div class="dtlms-certificate-header">';
								$output .= '<h2>'.esc_html($heading).'</h2>';
								$output .= '<h3>'.esc_html($subheading).'</h3>';    
							$output .= '</div>';
							$output .= '<div class="dtlms-certificate-content-holder">';
								$output .= sprintf( esc_html__( '%1$s Who Has Successfully Completed %2$s with %3$s', 'dtlms' ), $student_name, $item_name, $student_percent );
								$output .= '<div class="dtlms-certificate-content">'.DTLMSShortcodes::dtlms_shortcodeHelper($content).'</div>';
							$output .= '</div>';
							$output .= '<div class="dtlms-certificate-footer">';
								$output .= '<div class="dtlms-certificate-date">';
									$output .= '<span>'.esc_html__('Date', 'dtlms').'</span>';
									$output .= $date;
								$output .= '</div>';
								if(isset($footer_logo_attachment[0]) && $footer_logo_attachment[0] != '') {
									$output .= '<div class="dtlms-certificate-footer-logo">';
										$output .= '<img src="'.esc_url($footer_logo_attachment[0]).'" alt="'.esc_html__('Footer Logo', 'dtlms').'" title="'.esc_html__('Footer Logo', 'dtlms').'">';
									$output .= '</div>';
								}					          
								if(isset($signature_attachment[0]) && $signature_attachment[0] != '') {
									$output .= '<div class="dtlms-certificate-sign">';          
										$output .= '<span>'.esc_html__('Signature', 'dtlms').'</span>';				          	
										$output .= '<img src="'.esc_url($signature_attachment[0]).'" alt="'.esc_html__('Signature', 'dtlms').'" title="'.esc_html__('Signature', 'dtlms').'">';
									$output .= '</div>';
								}
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';

		} else if($type == 'type2') {

			$output .= '<div class="dtlms-certificate-container type2">';
				$output .= '<div class="dtlms-certificate-wrapper">';
					$output .= '<div class="dtlms-certificate-inner-wrapper">';
						$output .= '<div class="dtlms-certificate-header">';
							if(isset($logo1_attachment[0]) && $logo1_attachment[0] != '') {
								$output .= '<img src="'.esc_url($logo1_attachment[0]).'" alt="'.esc_html__('Logo 1', 'dtlms').'" title="'.esc_html__('Logo 1', 'dtlms').'">';
							}
							if(isset($logo2_attachment[0]) && $logo2_attachment[0] != '') {
								$output .= '<img src="'.esc_url($logo2_attachment[0]).'" alt="'.esc_html__('Logo 2', 'dtlms').'" title="'.esc_html__('Logo 2', 'dtlms').'">';
							}
							$output .= '<h2>'.esc_html($heading).'</h2>';
							$output .= '<h3>'.esc_html($subheading).'</h3>';    
						$output .= '</div>';
						$output .= '<div class="dtlms-certificate-content-holder">';
							$output .= sprintf( esc_html__( '%1$s Who Has Successfully Completed %2$s with %3$s', 'dtlms' ), $student_name, $item_name, $student_percent );
							$output .= '<div class="dtlms-certificate-content">'.DTLMSShortcodes::dtlms_shortcodeHelper($content).'</div>';
						$output .= '</div>';
						$output .= '<div class="dtlms-certificate-footer">';
							$output .= '<div class="dtlms-certificate-date">';
								$output .= $date;
								$output .= '<span>'.esc_html__('Date', 'dtlms').'</span>';
							$output .= '</div>';
							if(isset($footer_logo_attachment[0]) && $footer_logo_attachment[0] != '') {
								$output .= '<div class="dtlms-certificate-footer-logo">';
									$output .= '<img src="'.esc_url($footer_logo_attachment[0]).'" alt="'.esc_html__('Footer Logo', 'dtlms').'" title="'.esc_html__('Footer Logo', 'dtlms').'">';
								$output .= '</div>';
							}					          
							if(isset($signature_attachment[0]) && $signature_attachment[0] != '') {
								$output .= '<div class="dtlms-certificate-sign">';          
									$output .= '<img src="'.esc_url($signature_attachment[0]).'" alt="'.esc_html__('Signature', 'dtlms').'" title="'.esc_html__('Signature', 'dtlms').'">';
									$output .= '<span>'.esc_html__('Signature', 'dtlms').'</span>';
								$output .= '</div>';
							}							
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';

		} else if($type == 'type3') {

			$output .= '<div class="dtlms-certificate-container type3">';
				$output .= '<div class="dtlms-certificate-wrapper">';
					$output .= '<div class="dtlms-certificate-inner-wrapper">';
						$output .= '<div class="dtlms-certificate-header">';
							if(isset($logo1_attachment[0]) && $logo1_attachment[0] != '') {
								$output .= '<img src="'.esc_url($logo1_attachment[0]).'" alt="'.esc_html__('Logo 1', 'dtlms').'" title="'.esc_html__('Logo 1', 'dtlms').'">';
							}
							$output .= '<h2>'.esc_html($heading).'</h2>';
							$output .= '<h3>'.esc_html($subheading).'</h3>'; 
							if(isset($logo2_attachment[0]) && $logo2_attachment[0] != '') {
								$output .= '<img src="'.esc_url($logo2_attachment[0]).'" alt="'.esc_html__('Logo 2', 'dtlms').'" title="'.esc_html__('Logo 2', 'dtlms').'">';
							}							   
						$output .= '</div>';
						$output .= '<div class="dtlms-certificate-content-holder">';
							$output .= sprintf( esc_html__( '%1$s Who Has Successfully Completed %2$s with %3$s', 'dtlms' ), $student_name, $item_name, $student_percent );
							$output .= '<div class="dtlms-certificate-content">'.DTLMSShortcodes::dtlms_shortcodeHelper($content).'</div>';
						$output .= '</div>';
						$output .= '<div class="dtlms-certificate-footer">';
							$output .= '<div class="dtlms-certificate-date">';
								$output .= $date;
								$output .= '<span>'.esc_html__('Date', 'dtlms').'</span>';
							$output .= '</div>';				          
							if(isset($signature_attachment[0]) && $signature_attachment[0] != '') {
								$output .= '<div class="dtlms-certificate-sign">';          
									$output .= '<img src="'.esc_url($signature_attachment[0]).'" alt="'.esc_html__('Signature', 'dtlms').'" title="'.esc_html__('Signature', 'dtlms').'">';
									$output .= '<span>'.esc_html__('Signature', 'dtlms').'</span>';
								$output .= '</div>';
							}
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';

		}
		
		return $output;

	}

	function dtlms_courses_listing($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
						'disable-all-filters'            => '',

						'enable-search-filter'           => 'true',
						'enable-display-filter'          => 'true',
						'enable-orderby-filter'          => 'true',
						'enable-category-filter'         => 'true',
						'enable-instructor-filter'       => 'true',
						'enable-cost-filter'             => 'true',
						'enable-date-filter'             => 'true',

						'listing-output-page'            => '',

						'default-filter'                 => '',
						'default-display-type'           => 'grid',
						'course-item-ids'                => '',
						'course-category-ids'            => '',
						'instructor-ids'                 => '',

						'apply-isotope'                  => '',
						'enable-category-isotope-filter' => '',

						'show-author-details'            => 'true',
						
						'post-per-page'                  => '-1',
						'columns'                        => 1,

						'enable-fullwidth'               => '',
						'type'                           => 'type1',
						'show-description'               => '',
						'hide-labels'                    => '',
						'hide-category'                  => '',
						'hide-addtocartbutton'           => '',

						'class'                          => '',

						'enable-carousel'                => '',
						'carousel-effect'                => '',
						'carousel-autoplay'              => 0,
						'carousel-slidesperview'         => 2,
						'carousel-loopmode'              => '',
						'carousel-mousewheelcontrol'     => '',
						'carousel-bulletpagination'      => 'true',
						'carousel-arrowpagination'       => '',
						'carousel-spacebetween'          => 0,

				), $attrs, 'dtlms_courses_listing' );

		$out = dtlms_courses_listing_content($attrs);
		
		return $out;

	}	

	function dtlms_classes_listing($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
						'disable-all-filters' => '',

						'enable-search-filter' => 'true',
						'enable-display-filter' => 'true',
						'enable-classtype-filter' => 'true',
						'enable-orderby-filter' => 'true',
						'enable-instructor-filter' => 'true',
						'enable-cost-filter' => 'true',
						'enable-date-filter' => 'true',

						'listing-output-page' => '',

						'default-filter' => '',
						'default-display-type' => 'grid',
						'class-item-ids' => '',
						'instructor-ids' => '',

						'apply-isotope' => '',

						'post-per-page' => '-1',
						'columns' => 1,

						'enable-fullwidth' => '',
						'type' => 'type1',

						'class' => '',

						'enable-carousel' => '',
						'carousel-effect' => '',
						'carousel-autoplay' => 0,
						'carousel-slidesperview' => 2,
						'carousel-loopmode' => '',
						'carousel-mousewheelcontrol' => '',
						'carousel-bulletpagination' => 'true',
						'carousel-arrowpagination' => '',
						'carousel-spacebetween' => 0,

				), $attrs, 'dtlms_classes_listing' );

		$out = dtlms_classes_listing_content($attrs);
		
		return $out;

	}	

	function dtlms_packages_listing($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
						'display-type' => 'grid',
						'post-per-page' => '-1',
						'columns' => 1,
						'apply-isotope' => '',	
						'type' => 'type1',
						'package-item-ids' => '',

						'enable-carousel' => '',
						'carousel-effect' => '',
						'carousel-autoplay' => 0,
						'carousel-slidesperview' => 2,
						'carousel-loopmode' => '',
						'carousel-mousewheelcontrol' => '',
						'carousel-bulletpagination' => 'true',
						'carousel-arrowpagination' => '',
						'carousel-spacebetween' => 0,

				), $attrs, 'dtlms_packages_listing' );

		$out = dtlms_packages_listing_content($attrs);
		
		return $out;

	}	

	function dtlms_course_categories($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
						'type' => 'type1',
						'columns' => '',
						'include' => '',
						'use-icon-image' => '',
						'class' => '',

				), $attrs, 'dtlms_course_categories' );
		
		$output = '';

		$column_class = '';
		if($attrs['columns'] == 1) {
			$column_class = 'dtlms-column dtlms-one-column';
		} else if($attrs['columns'] == 2) {
			$column_class = 'dtlms-column dtlms-one-half';
		} else if($attrs['columns'] == 3) {
			$column_class = 'dtlms-column dtlms-one-third';
		}


		$cat_args = array (
						'taxonomy' => 'course_category', 
						'hide_empty' => 1,
					);
		if($attrs['include'] != '') {
			$cat_args['include'] = $attrs['include'];
		}

		$categories = get_categories($cat_args);	


		if( is_array($categories) && !empty($categories) ) {

			$i = 1;
    		foreach( $categories as $category ) {

				if($i == 1) { $first_class = 'first';  } else { $first_class = ''; }
				if($i == $attrs['columns']) { $i = 1; } else { $i = $i + 1; }	
   			
    			$image_url = get_term_meta($category->term_id, 'dtlms-category-image-url', true);
    			$iconimage_url = get_term_meta($category->term_id, 'dtlms-category-iconimage-url', true);
    			$icon = get_term_meta($category->term_id, 'dtlms-category-icon', true);
    			$icon_color = get_term_meta($category->term_id, 'dtlms-category-icon-color', true);
    			$background_color = get_term_meta($category->term_id, 'dtlms-background-color', true);

    			if($image_url == '') {
    				$image_url = plugins_url ('designthemes-lms-addon').'/images/no-image.jpg';
    			}

    			if($attrs['type'] == 'type1') {
        			$output .= '<div class="dtlms-course-category-item type1 '.$column_class.' '.$first_class.' '.$attrs['class'].'">';
        				$output .= '<img src="'.esc_url($image_url).'" alt="'.esc_html__('Course Category Image', 'dtlms').'" title="'.esc_html__('Course Category Image', 'dtlms').'" />';
        				$output .= '<div class="dtlms-course-category-meta-data">';
        					if($attrs['use-icon-image'] == 'true') {
        						if($iconimage_url != '') {
	        						$output .= '<img src="'.esc_url($iconimage_url).'" alt="'.esc_html__('Course Category Icon Image', 'dtlms').'" title="'.esc_html__('Course Category Icon Image', 'dtlms').'" />';
	        					}
        					} else {
		        				if($icon != '') {
		            				$output .= '<span class="'.$icon.'"></span>';
		            			}
		            		}
	            			$output .= '<h3><a href="'.get_term_link($category->term_id).'">'.esc_html($category->cat_name).'</a></h3>';
							$output .= '<div class="dtlms-category-total-items">'.sprintf(_n('%1$s course', '%1$s courses', $category->count, 'dtlms'), '<span>'.$category->count).'</span>'.'</div>';
            			$output .= '</div>';
            		$output .= '</div>';
            	}

    			if($attrs['type'] == 'type2') {			
        			$output .= '<div class="dtlms-course-category-item type2 '.$column_class.' '.$first_class.' '.$attrs['class'].'">';
        				$output .= '<img src="'.esc_url($image_url).'" alt="'.esc_html__('Course Category Image', 'dtlms').'" title="'.esc_html__('Course Category Image', 'dtlms').'" />';
            			$output .= '<h3><a href="'.get_term_link($category->term_id).'">'.esc_html($category->cat_name).'</a></h3>';
            		$output .= '</div>';
            	}

    			if($attrs['type'] == 'type3') {
    				$icon_style = '';
    				if($icon_color != '') {
    					$icon_style = 'style="color:'.$icon_color.'"';
    				}
        			$output .= '<div class="dtlms-course-category-item type3 '.$column_class.' '.$first_class.' '.$attrs['class'].'">';
        				$output .= '<div class="dtlms-course-category-meta-data">';
        					if($attrs['use-icon-image'] == 'true') {
        						if($iconimage_url != '') {
	        						$output .= '<img src="'.esc_url($iconimage_url).'" alt="'.esc_html__('Course Category Icon Image', 'dtlms').'" title="'.esc_html__('Course Category Icon Image', 'dtlms').'" />';
	        					}
        					} else {
		        				if($icon != '') {
		            				$output .= '<span class="'.$icon.'" '.$icon_style.'></span>';
		            			}
		            		}	            			
	            			$output .= '<h3><a href="'.get_term_link($category->term_id).'">'.esc_html($category->cat_name).'</a></h3>';
            			$output .= '</div>';
            		$output .= '</div>';
            	}            	            	

    			if($attrs['type'] == 'type4') {
    				$background_style = 'style="';
    				if($background_color != '') {
    					$background_style .= 'background-color:'.$background_color.'; ';
    				} 
	    			$background_style .= '"';

        			$output .= '<div class="dtlms-course-category-item type4 '.$column_class.' '.$first_class.' '.$attrs['class'].'" '.$background_style.'>';
        				$output .= '<div class="dtlms-course-category-meta-data">';
	            			$output .= '<h3><a href="'.get_term_link($category->term_id).'">'.esc_html($category->cat_name).'</a></h3>';
							$output .= '<div class="dtlms-category-total-items">'.sprintf(_n('%1$s course', '%1$s courses', $category->count, 'dtlms'), '<span>'.$category->count).'</span>'.'</div>';
            			$output .= '</div>';
            		$output .= '</div>';
            	} 

    			if($attrs['type'] == 'type5') {
        			$output .= '<div class="dtlms-course-category-item type5 '.$column_class.' '.$first_class.' '.$attrs['class'].'">';
        				$output .= '<img src="'.esc_url($image_url).'" alt="'.esc_html__('Course Category Image', 'dtlms').'" title="'.esc_html__('Course Category Image', 'dtlms').'" />';
        				$output .= '<div class="dtlms-course-category-meta-data">';
        					if($attrs['use-icon-image'] == 'true') {
        						if($iconimage_url != '') {
	        						$output .= '<span><img src="'.esc_url($iconimage_url).'" alt="'.esc_html__('Course Category Icon Image', 'dtlms').'" title="'.esc_html__('Course Category Icon Image', 'dtlms').'" /></span>';
	        					}
        					} else {
		        				if($icon != '') {
		            				$output .= '<span class="'.$icon.'"></span>';
		            			}
		            		}	            			
	            			$output .= '<h3><a href="'.get_term_link($category->term_id).'">'.esc_html($category->cat_name).'</a></h3>';
	            			$output .= '<div class="dtlms-category-total-items">'.sprintf(_n('%1$s course', '%1$s courses', $category->count, 'dtlms'), '<span>'.$category->count).'</span>'.'</div>';
            			$output .= '</div>';
            		$output .= '</div>';
            	}  

    			if($attrs['type'] == 'type6') {
        			$output .= '<div class="dtlms-course-category-item type6 '.$column_class.' '.$first_class.' '.$attrs['class'].'">';
        				$output .= '<img src="'.esc_url($image_url).'" alt="'.esc_html__('Course Category Image', 'dtlms').'" title="'.esc_html__('Course Category Image', 'dtlms').'" />';
        				$output .= '<div class="dtlms-course-category-meta-data">';
        					if($attrs['use-icon-image'] == 'true') {
        						if($iconimage_url != '') {
	        						$output .= '<img src="'.esc_url($iconimage_url).'" alt="'.esc_html__('Course Category Icon Image', 'dtlms').'" title="'.esc_html__('Course Category Icon Image', 'dtlms').'" />';
	        					}
        					} else {
		        				if($icon != '') {
		            				$output .= '<span class="'.$icon.'"></span>';
		            			}
		            		}
	            			$output .= '<h3><a href="'.get_term_link($category->term_id).'">'.esc_html($category->cat_name).'</a></h3>';
	            			$output .= '<div class="dtlms-category-total-items">'.sprintf(_n('%1$s course', '%1$s courses', $category->count, 'dtlms'), '<span>'.$category->count).'</span>'.'</div>';
            			$output .= '</div>';
            		$output .= '</div>';
            	}

    			if($attrs['type'] == 'type7') {
        			$output .= '<div class="dtlms-course-category-item type7 '.$column_class.' '.$first_class.' '.$attrs['class'].'">';
        				$output .= '<img src="'.esc_url($image_url).'" alt="'.esc_html__('Course Category Image', 'dtlms').'" title="'.esc_html__('Course Category Image', 'dtlms').'" />';
        				$output .= '<div class="dtlms-course-category-meta-data">';
        					if($attrs['use-icon-image'] == 'true') {
        						if($iconimage_url != '') {
	        						$output .= '<img src="'.esc_url($iconimage_url).'" alt="'.esc_html__('Course Category Icon Image', 'dtlms').'" title="'.esc_html__('Course Category Icon Image', 'dtlms').'" />';
	        					}
        					} else {
		        				if($icon != '') {
		            				$output .= '<span class="'.$icon.'"></span>';
		            			}
		            		}
	            			$output .= '<h3><a href="'.get_term_link($category->term_id).'">'.esc_html($category->cat_name).'</a></h3>';
	            			$output .= '<div class="dtlms-category-total-items">'.sprintf(_n('%1$s course', '%1$s courses', $category->count, 'dtlms'), '<span>'.$category->count).'</span>'.'</div>';
            			$output .= '</div>';
            		$output .= '</div>';
            	}

    			if($attrs['type'] == 'type8') {
        			$output .= '<div class="dtlms-course-category-item type8 '.$column_class.' '.$first_class.' '.$attrs['class'].'">';
        				$output .= '<div class="dtlms-course-category-meta-data">';
        					if($attrs['use-icon-image'] == 'true') {
        						if($iconimage_url != '') {
		        					$background_style = '';
				    				if($background_color != '') {
				    					$background_style = 'style="background-color:'.$background_color.';"';
				    				}         							
	        						$output .= '<span '.$background_style.'><img src="'.esc_url($iconimage_url).'" alt="'.esc_html__('Course Category Icon Image', 'dtlms').'" title="'.esc_html__('Course Category Icon Image', 'dtlms').'" /></span>';
	        					}
        					} else {
		        				if($icon != '') {
		        					$background_style = '';
				    				if($background_color != '') {
				    					$background_style = 'style="background-color:'.$background_color.';"';
				    				} 
		            				$output .= '<span class="'.$icon.'" '.$background_style.'></span>';
		            			}
		            		}	            			
							$output .= '<h3><a href="'.get_term_link($category->term_id).'">'.esc_html($category->cat_name).'</a></h3>';
							if(isset($category->description) && $category->description != '') {
								$output .= '<p>'.$category->description.'</p>';
							}
							$output .= '<div class="dtlms-category-total-items"><a href="'.get_term_link($category->term_id).'">'.sprintf(_n('%1$s course', '%1$s courses', $category->count, 'dtlms'), '<span>'.$category->count).'</span>'.'</a></div>';
            			$output .= '</div>';
            		$output .= '</div>';
            	}            	

    			if($attrs['type'] == 'type9') {
        			$output .= '<div class="dtlms-course-category-item type9 '.$column_class.' '.$first_class.' '.$attrs['class'].'">';
        				$output .= '<img src="'.esc_url($image_url).'" alt="'.esc_html__('Course Category Image', 'dtlms').'" title="'.esc_html__('Course Category Image', 'dtlms').'" />';
        				$output .= '<div class="dtlms-course-category-meta-data">';
        					if($attrs['use-icon-image'] == 'true') {
        						if($iconimage_url != '') {
	        						$output .= '<img src="'.esc_url($iconimage_url).'" alt="'.esc_html__('Course Category Icon Image', 'dtlms').'" title="'.esc_html__('Course Category Icon Image', 'dtlms').'" />';
	        					}
        					} else {
		        				if($icon != '') {
		            				$output .= '<span class="'.$icon.'"></span>';
		            			}
		            		}
	            			$output .= '<h3><a href="'.get_term_link($category->term_id).'">'.esc_html($category->cat_name).'</a></h3>';
							$output .= '<div class="dtlms-category-total-items">'.sprintf(_n('%1$s course', '%1$s courses', $category->count, 'dtlms'), '<span>'.$category->count).'</span>'.'</div>';
            			$output .= '</div>';
            		$output .= '</div>';
            	}

    			if($attrs['type'] == 'type10') {
        			$output .= '<div class="dtlms-course-category-item type10 '.$column_class.' '.$first_class.' '.$attrs['class'].'">';
        				$output .= '<img src="'.esc_url($image_url).'" alt="'.esc_html__('Course Category Image', 'dtlms').'" title="'.esc_html__('Course Category Image', 'dtlms').'" />';
        				$output .= '<div class="dtlms-course-category-meta-data">';
        					if($attrs['use-icon-image'] == 'true') {
        						if($iconimage_url != '') {
	        						$output .= '<span><img src="'.esc_url($iconimage_url).'" alt="'.esc_html__('Course Category Icon Image', 'dtlms').'" title="'.esc_html__('Course Category Icon Image', 'dtlms').'" /></span>';
	        					}
        					} else {
		        				if($icon != '') {
		            				$output .= '<span class="'.$icon.'"></span>';
		            			}
		            		}	            			
	            			$output .= '<h3><a href="'.get_term_link($category->term_id).'">'.esc_html($category->cat_name).'</a></h3>';
            			$output .= '</div>';
            		$output .= '</div>';
            	}

        	}
		}


		return $output;

	}

	function dtlms_instructor_list($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
						'type' => 'type1',
						'social-icon-types' => 'default',
						'image-types' => '',
						'columns' => '',
						'include' => '',
						'number' => -1,
						'class' => '',

				), $attrs, 'dtlms_instructor_list' );
	
		
		$output = '';

		$column_class = '';
		if($attrs['columns'] == 1) {
			$column_class = 'dtlms-column dtlms-one-column';
		} else if($attrs['columns'] == 2) {
			$column_class = 'dtlms-column dtlms-one-half';
		} else if($attrs['columns'] == 3) {
			$column_class = 'dtlms-column dtlms-one-third';
		} else if($attrs['columns'] == 4 && $attrs['type'] == 'type2') {
			$column_class = 'dtlms-column dtlms-one-fourth';			
		}


		$instructor_args = array (
								'role' => 'instructor',
								'number' => $attrs['number']
							);
		if($attrs['include'] != '') {
			$instructor_args['include'] = $attrs['include'];
		}

		$instructors = get_users ( $instructor_args );

        if( is_array($instructors) && !empty($instructors) ) {

        	$i = 1;
            foreach ($instructors as $instructor) {

				if($i == 1) { $first_class = 'first';  } else { $first_class = ''; }
				if($i == $attrs['columns']) { $i = 1; } else { $i = $i + 1; }	

				$instructor_id = $instructor->data->ID;


				$user_social_items = get_the_author_meta('user-social-items', $instructor_id);
				$user_social_items = (isset($user_social_items) && is_array($user_social_items)) ? $user_social_items : array();

				$user_social_items_value = get_the_author_meta('user-social-items-value', $instructor_id);
				$user_social_items_value = (isset($user_social_items_value) && is_array($user_social_items_value)) ? $user_social_items_value : array();

				$social_links_str = '';
				if(is_array($user_social_items) && !empty($user_social_items)) {
					$social_links_str .= '<div class="dtlms-team-social-links">';
						$social_links_str .= '<ul class="dtlms-team-social">';
							$j = 0;
							foreach($user_social_items as $user_social_item) {
								$social_links_str .= '<li><a class="'.$user_social_item.'" href="'.$user_social_items_value[$j].'"></a></li>';
								$j++;
							}
						$social_links_str .= '</ul>';
					$social_links_str .= '</div>';
				}


	  			$output .= '<div class="dtlms-instructor-item '.$column_class.' '.$first_class.' '.$attrs['type'].' '.$attrs['class'].' '.$attrs['social-icon-types'].' '.$attrs['image-types'].'">';

	  				if($attrs['type'] == 'type6') {
	  					$output .= '<div class="dtlms-instructor-item-meta-data-container">';
	  				}

		    			//$output .= get_avatar($instructor_id, 600);

		    			$data = get_avatar_url($instructor_id, array ( 'size' => 600) );

		    			if($data != '') {
							if($attrs['type'] == 'type2') {
								$output .= '<div class="dtlms-instructor-item-thumb">';
							}
								$output .= '<img src="'.esc_url($data).'" alt="'.esc_html__('Instructor Image', 'dtlms').'" title="'.esc_html__('Instructor Image', 'dtlms').'" />';
							if($attrs['type'] == 'type2') {
									$output .= '<div class="dtlms-instructor-item-overlay"><a href="'.get_author_posts_url($instructor_id).'">'.esc_html__('View Profile', 'dtlms').'</a></div>';
								$output .= '</div>';
							}							
		    			}
						
		    			$output .= '<div class="dtlms-instructor-item-meta-data">';

			    			$output .= '<h4>
											<a href="'.get_author_posts_url($instructor_id).'" rel="author">
												'.esc_html($instructor->data->display_name).'
											</a>
										</h4>';

							$user_specialization = get_the_author_meta('user-specialization', $instructor_id);
							if(isset($user_specialization) && $user_specialization != '') {
								if($attrs['type'] == 'type7') {
									$output .= '<p>'.$user_specialization.'</p>';
								} else {
									$output .= '<h5>'.$user_specialization.'</h5>';
								}								
							}

							if($attrs['type'] == 'type9') {
								$output .= '<p>'.get_the_author_meta('website', $instructor_id).'</p>';
							}

							if($attrs['type'] != 'type6') {
								$output .= $social_links_str;
							}

						$output .= '</div>';

	  				if($attrs['type'] == 'type6') {

	  					$output .= '</div>';
	    				$output .= '<div class="dtlms-instructor-item-meta-data-detailed">';
		    				$output .= '<p>'.get_the_author_meta('description', $instructor_id).'</p>';
		    				$output .= $social_links_str;
	    				$output .= '</div>'; 
	    				  
	    			}


	    		$output .= '</div>';              

            }

        }

		return $output;

	}


	function dtlms_total_items( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-type' => '',
					'item-title' => '',
					'content-type' => 'all-items',

				), $attrs, 'dtlms_total_items' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'administrator', (array) $current_user->roles ) || in_array( 'instructor', (array) $current_user->roles ) ) {

			$item_title = $item_data = '';

			if ( in_array( 'administrator', (array) $current_user->roles ) && $attrs['content-type'] == 'all-items' ) {
			   if($attrs['item-type'] == 'classes') {
			   		$class_plural_label = apply_filters( 'class_label', 'plural' );
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : sprintf( esc_html__( 'Total %1$s', 'dtlms' ), $class_plural_label );
			   		$item_data = wp_count_posts('dtlms_classes')->publish;				
			   } else if($attrs['item-type'] == 'courses') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Courses', 'dtlms');
			   		$item_data = wp_count_posts('dtlms_courses')->publish;		   	
			   } else if($attrs['item-type'] == 'lessons') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Lessons', 'dtlms');
			   		$item_data = wp_count_posts('dtlms_lessons')->publish;
			   } else if($attrs['item-type'] == 'quizzes') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Quizzes', 'dtlms');
			   		$item_data = wp_count_posts('dtlms_quizzes')->publish;
			   } else if($attrs['item-type'] == 'questions') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Questions', 'dtlms');
			   		$item_data = wp_count_posts('dtlms_questions')->publish;
			   } else if($attrs['item-type'] == 'assignments') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Assignments', 'dtlms');
			   		$item_data = wp_count_posts('dtlms_assignments')->publish;
			   } else if($attrs['item-type'] == 'packages') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Packages', 'dtlms');
			   		$item_data = wp_count_posts('dtlms_packages')->publish;
			   }		   
			} else if ( in_array( 'instructor', (array) $current_user->roles ) ) {
			   if($attrs['item-type'] == 'classes') {
			   		$class_plural_label = apply_filters( 'class_label', 'plural' );
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : sprintf( esc_html__( 'Total %1$s Created', 'dtlms' ), $class_plural_label );
			   		$item_data = count_user_posts($user_id , 'dtlms_classes');			
			   } else if($attrs['item-type'] == 'courses') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Courses Created', 'dtlms');
			   		$item_data = count_user_posts($user_id , 'dtlms_courses');		   	
			   } else if($attrs['item-type'] == 'lessons') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Lessons Created', 'dtlms');
			   		$item_data = count_user_posts($user_id , 'dtlms_lessons');
			   } else if($attrs['item-type'] == 'quizzes') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Quizzes Created', 'dtlms');
			   		$item_data = count_user_posts($user_id , 'dtlms_quizzes');
			   } else if($attrs['item-type'] == 'questions') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Questions Created', 'dtlms');
			   		$item_data = count_user_posts($user_id , 'dtlms_questions');
			   } else if($attrs['item-type'] == 'assignments') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Assignments Created', 'dtlms');
			   		$item_data = count_user_posts($user_id , 'dtlms_assignments');
			   } else if($attrs['item-type'] == 'packages') {
			   		$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Total Packages Created', 'dtlms');
			   		$item_data = count_user_posts($user_id , 'dtlms_packages');		   		
			   }
			}

			if($item_title != '' || $item_data != '') {
				$output .= '<div class="dtlms-total-items">
								<div class="dtlms-total-item-title">'.$item_title.'</div>
								<span>'.$item_data.'</span>
							</div>';
			} else {
				$output .= esc_html__('No datas to display', 'dtlms');
			}

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_total_items_chart( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
					
					'chart-title' => '',
					'chart-type' => 'pie',
					'set-unique-colors' => '',
					'first-color' => '',
					'second-color' => '',
					'third-color' => '',
					'fourth-color' => '',
					'fifth-color' => '',
					'sixth-color' => '',
					'seventh-color' => '',
					'content-type' => 'all-items',
					'class' => '',

				), $attrs, 'dtlms_total_items_chart' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'administrator', (array) $current_user->roles ) || in_array( 'instructor', (array) $current_user->roles ) ) {

			$class_plural_label = apply_filters( 'class_label', 'plural' );

			$total_items_chart_label_str = '["'.sprintf( esc_html__( 'Total %1$s', 'dtlms' ), $class_plural_label ).'","'.esc_html__('Total Courses', 'dtlms').'","'.esc_html__('Total Lessons', 'dtlms').'","'.esc_html__('Total Quizzes', 'dtlms').'","'.esc_html__('Total Questions', 'dtlms').'","'.esc_html__('Total Assignments', 'dtlms').'","'.esc_html__('Total Packages', 'dtlms').'"]';

			$total_items_chart_data_str = '';

			if ( in_array( 'administrator', (array) $current_user->roles ) && $attrs['content-type'] == 'all-items' ) {
				$total_items_chart_data_str .= '['.wp_count_posts('dtlms_classes')->publish.','.wp_count_posts('dtlms_courses')->publish.','.wp_count_posts('dtlms_lessons')->publish.','.wp_count_posts('dtlms_quizzes')->publish.','.wp_count_posts('dtlms_questions')->publish.','.wp_count_posts('dtlms_assignments')->publish.','.wp_count_posts('dtlms_packages')->publish.']';
			} else if ( in_array( 'instructor', (array) $current_user->roles ) ) {
				$total_items_chart_data_str .= '['.count_user_posts($user_id , 'dtlms_classes').','.count_user_posts($user_id , 'dtlms_courses').','.count_user_posts($user_id , 'dtlms_lessons').','.count_user_posts($user_id , 'dtlms_quizzes').','.count_user_posts($user_id , 'dtlms_questions').','.count_user_posts($user_id , 'dtlms_assignments').','.count_user_posts($user_id , 'dtlms_packages').']';
			}

			if($attrs['chart-title'] != '') {
				$chart_title = $attrs['chart-title'];
			} else {
				$chart_title = esc_html__('Total items added so far', 'dtlms');	
			}

			$chart_label = '';
			$char_bgcolor = '""';
			if($attrs['chart-type'] == 'bar') {
				$chart_label = esc_html__('Number of items', 'dtlms');
			}

			$legend_position = dtlms_option('chart', 'legend-position');
			$legend_position = ($legend_position != '') ? $legend_position : 'right';

			if($attrs['set-unique-colors'] == 'true') {

				if($attrs['chart-type'] == 'pie') {
					$char_bgcolor = '[
					                    "'.$attrs['first-color'].'",
					                    "'.$attrs['second-color'].'",
					                    "'.$attrs['third-color'].'",
					                    "'.$attrs['fourth-color'].'",
					                    "'.$attrs['fifth-color'].'",
					                    "'.$attrs['sixth-color'].'",
					                    "'.$attrs['seventh-color'].'",
				                	]';
	            } else {
	 				$char_bgcolor = '"'.$attrs['first-color'].'"';           	
	            }

			} else {

				$chart_colors = dtlms_option('chart', 'chart-colors');
				if(is_array($chart_colors) && !empty($chart_colors)) {
					if(dtlms_option('chart', 'shuffle-colors') == 'true') {
						shuffle($chart_colors);
					}
					if($attrs['chart-type'] == 'pie') {
						$char_bgcolor = array_slice($chart_colors, 0, 7);
						$char_bgcolor = implode('","', $char_bgcolor);
						$char_bgcolor = '["'.$char_bgcolor.'"]';
					} else{
						if(isset($chart_colors[0])) {
							$char_bgcolor = '"'.$chart_colors[0].'"';
						}
					}
				}

			}

			$chart_id = dtlms_generate_random_number();

			$output .= '<div class="dtlms-chart-holder '.$attrs['class'].'">';

				$output .= '<canvas id="dtlmsTotalItemsChart-'.$chart_id.'"></canvas>';
				$output .= '<script>

								jQuery(document).ready(function() {

							        var dtlmsTotalItemsChartData = {
							            labels: '.$total_items_chart_label_str.',
							            datasets: [{
							                label: "'.$chart_label.'",
							                backgroundColor: '.$char_bgcolor.',
							                data: '.$total_items_chart_data_str.',
							            }]
							        };

								    var ctx = document.getElementById("dtlmsTotalItemsChart-'.$chart_id.'").getContext("2d");
								    window.dtlmsTotalItemsChart = new Chart(ctx, {
						                type: "'.$attrs['chart-type'].'",
						                data: dtlmsTotalItemsChartData,
						                options: {
						                    responsive: true,
						                    legend: {
						                        position: "'.$legend_position.'",
						                    },
						                    title: {
						                        display: true,
						                        text: "'.$chart_title.'"
						                    }                
						                }
						            });

						        });

			    			</script>';

			$output .= '</div>';    			

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}
			
		return $output;

	}

	function dtlms_purchases_overview_chart( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
					
					'chart-title' => '',
					'include-class-purchases' => '',
					'include-course-purchases' => '',
					'include-package-purchases' => '',
					'include-data' => '',
					'set-unique-colors' => '',
					'first-color' => '',
					'second-color' => '',
					'third-color' => '',
					'enable-instructor-filter' => 'false',
					'class' => '',

				), $attrs, 'dtlms_purchases_overview_chart' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'instructor', (array) $current_user->roles ) || in_array( 'administrator', (array) $current_user->roles )) {

			$output .= '<div class="dtlms-chart-container '.$attrs['class'].'">';

				$id_attribute = '';
				if ( in_array( 'instructor', (array) $current_user->roles ) ) {
					$id_attribute = 'data-instructorid="'.$user_id.'"';
				}

				if ( in_array( 'administrator', (array) $current_user->roles ) && $attrs['enable-instructor-filter'] == 'true' ) {

					$instructor_label = apply_filters( 'instructor_label', 'singular' );
				    $output .= '<select class="dtlms-purchases-overview-instructor-filter" name="dtlms-purchases-overview-instructor-filter" data-placeholder="'.sprintf( esc_html__('Choose %1$s ...', 'dtlms'), $instructor_label ).'" class="dtlms-chosen-select">';

						$output .= '<option value="-1">'.esc_html__('All', 'dtlms').'</option>';

						$instructors = get_users ( array ('role' => 'instructor') );
				        if ( count( $instructors ) > 0 ) {
				            foreach ($instructors as $instructor) {
								$instructor_id = $instructor->data->ID;
				                $output .= '<option value="' . esc_attr( $instructor_id ) . '">' . esc_html( $instructor->data->display_name ) . '</option>';
				            }
				        }

				    $output .= '</select>';

				    $id_attribute = 'data-instructorid="-1"';

				}

				$first_color = $second_color = $third_color = '';
				if($attrs['set-unique-colors'] == 'true') {

					$first_color = $attrs['first-color'];
					$second_color = $attrs['second-color'];
					$third_color = $attrs['third-color'];

				} else {

					$chart_colors = dtlms_option('chart', 'chart-colors');
					if(is_array($chart_colors) && !empty($chart_colors)) {
						if(dtlms_option('chart', 'shuffle-colors') == 'true') {
							shuffle($chart_colors);
						}

						$first_color = $chart_colors[0];
						$second_color = $chart_colors[1];
						$third_color = $chart_colors[2];
					}

				}

				$output .= '<div class="dtlms-chart-holder">';

					$output .= '<ul class="dtlms-purchases-overview-chart-options" data-includeclasspurchases="'.$attrs['include-class-purchases'].'" data-includecoursepurchases="'.$attrs['include-course-purchases'].'" data-includepackagepurchases="'.$attrs['include-package-purchases'].'" data-includedata="'.$attrs['include-data'].'" data-charttitle="'.$attrs['chart-title'].'" data-firstcolor="'.$first_color.'" data-secondcolor="'.$second_color.'" data-thirdcolor="'.$third_color.'">';
						$output .= '<li><a href="#" onclick="return false;" data-overviewchartoption="daily" class="active" '.$id_attribute.'>'.esc_html__('Daily', 'dtlms').'</a></li>';
						$output .= '<li><a href="#" onclick="return false;" data-overviewchartoption="monthly" '.$id_attribute.'>'.esc_html__('Monthly', 'dtlms').'</a></li>';
						$output .= '<li><a href="#" onclick="return false;" data-overviewchartoption="alltime" '.$id_attribute.'>'.esc_html__('All Time', 'dtlms').'</a></li>';
					$output .= '</ul>';

					$output .= dtlms_generate_loader_html(true);
					
					$output .= '<div class="dtlms-overview-chart-container"></div>';

				$output .= '</div>';

			$output .= '</div>';

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}			

		return $output;

	}

	function dtlms_instructor_commission_earnings( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
					
					'chart-title' => '',
					'enable-instructor-filter' => 'false',
					'instructor-earnings' => 'over-period',
					'content-filter' => 'both',
					'chart-type' => 'bar',
					'timeline-filter' => 'all',
					'include-course-commission' => 'true',
					'include-class-commission' => '',
					'include-other-commission' => '',
					'include-total-commission' => '',
					'class' => '',

				), $attrs, 'dtlms_instructor_commission_earnings' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		$id_attribute = '';
		if ( in_array( 'instructor', (array) $current_user->roles ) || in_array( 'administrator', (array) $current_user->roles )) {
			
			$output .= '<div class="dtlms-chart-holder '.$attrs['class'].'">';

				$instructor_id = $user_id;

				if ( in_array( 'administrator', (array) $current_user->roles ) && $attrs['enable-instructor-filter'] == 'true' ) {

					$instructor_label = apply_filters( 'instructor_label', 'singular' );
				    $output .= '<select class="dtlms-commissions-overview-instructor-filter" name="dtlms-commissions-overview-instructor-filter" data-placeholder="'.sprintf( esc_html__('Choose %1$s ...', 'dtlms'), $instructor_label ).'" class="dtlms-chosen-select">';

						$output .= '<option value="-1">'.esc_html__('None', 'dtlms').'</option>';

						$instructors = get_users ( array ('role' => 'instructor') );
				        if ( count( $instructors ) > 0 ) {
				            foreach ($instructors as $instructor) {
								$instructor_id = $instructor->data->ID;
				                $output .= '<option value="' . esc_attr( $instructor_id ) . '">' . esc_html( $instructor->data->display_name ) . '</option>';
				            }
				        }

				    $output .= '</select>';

				    $instructor_id = -1;

				}

				if($attrs['instructor-earnings'] == 'over-period' || ($attrs['instructor-earnings'] == 'over-item' && $attrs['timeline-filter'] == 'all')) {
					$output .= '<ul class="dtlms-commissions-overview-chart-options" data-charttitle="'.$attrs['chart-title'].'" data-instructorearnings="'.$attrs['instructor-earnings'].'" data-contentfilter="'.$attrs['content-filter'].'" data-charttype="'.$attrs['chart-type'].'" data-timelinefilter="'.$attrs['timeline-filter'].'" data-includecoursecommission="'.$attrs['include-course-commission'].'" data-includeclasscommission="'.$attrs['include-class-commission'].'" data-includeothercommission="'.$attrs['include-other-commission'].'" data-includetotalcommission="'.$attrs['include-total-commission'].'" data-instructorid="'.$instructor_id.'">';
						$output .= '<li><a href="#" onclick="return false;" data-overviewchartoption="daily" class="active">'.esc_html__('Daily', 'dtlms').'</a></li>';
						$output .= '<li><a href="#" onclick="return false;" data-overviewchartoption="monthly">'.esc_html__('Monthly', 'dtlms').'</a></li>';
						$output .= '<li><a href="#" onclick="return false;" data-overviewchartoption="alltime">'.esc_html__('All Time', 'dtlms').'</a></li>';
					$output .= '</ul>';					
				} else {
					$output .= '<ul class="dtlms-commissions-overview-chart-options hidden" data-charttitle="'.$attrs['chart-title'].'" data-instructorearnings="'.$attrs['instructor-earnings'].'" data-contentfilter="'.$attrs['content-filter'].'" data-charttype="'.$attrs['chart-type'].'" data-timelinefilter="'.$attrs['timeline-filter'].'" data-includecoursecommission="'.$attrs['include-course-commission'].'" data-includeclasscommission="'.$attrs['include-class-commission'].'" data-includeothercommission="'.$attrs['include-other-commission'].'" data-includetotalcommission="'.$attrs['include-total-commission'].'" data-instructorid="'.$instructor_id.'">';
						$output .= '<li><a href="#" onclick="return false;" data-overviewchartoption="'.$attrs['timeline-filter'].'" class="active">'.ucfirst($attrs['timeline-filter']).'</a></li>';
					$output .= '</ul>';					
				}

				$output .= dtlms_generate_loader_html(true);		
				
				$output .= '<div class="dtlms-overview-chart-container"></div>';

			$output .= '</div>';

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}
			
		return $output;

	}	


	function dtlms_instructor_courses( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'enable-instructor-filter' => 'false',

				), $attrs, 'dtlms_instructor_alltime_earnings' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'instructor', (array) $current_user->roles ) || in_array( 'administrator', (array) $current_user->roles ) ) {
			
			$output .= '<div class="dtlms-statistics-container">';

				if ( in_array( 'administrator', (array) $current_user->roles ) && $attrs['enable-instructor-filter'] == 'true' ) {

					$instructor_label = apply_filters( 'instructor_label', 'singular' );
				    $output .= '<select class="dtlms-statistics-courses-instructor" name="dtlms-statistics-courses-instructor" data-placeholder="'.sprintf( esc_html__('Choose %1$s ...', 'dtlms'), $instructor_label ).'" class="dtlms-chosen-select">';

						$output .= '<option value="-1">'.esc_html__('All', 'dtlms').'</option>';

						$instructors = get_users ( array ('role' => 'instructor') );
				        if ( count( $instructors ) > 0 ) {
				            foreach ($instructors as $instructor) {
								$instructor_id = $instructor->data->ID;
				                $output .= '<option value="' . esc_attr( $instructor_id ) . '">' . esc_html( $instructor->data->display_name ) . '</option>';
				            }
				        }

				    $output .= '</select>';

					$output .= '<div class="dtlms-hr-invisible"></div>';

					$output .= dtlms_generate_loader_html(true);

				    $output .= '<div class="dtlms-instructor-courses-container">'.dtlms_load_instructorwise_courses(-1, 'dtlms_load_instructorwise_courses', 'dtlms-instructor-courses-container').'</div>';

				} else {

					$output .= '<div class="dtlms-instructor-courses-container">'.dtlms_load_instructorwise_courses($user_id, 'dtlms_load_instructorwise_courses', 'dtlms-instructor-courses-container').'</div>';

				}

			$output .= '</div>';

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

			
		return $output;

	}

	function dtlms_instructor_commissions( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'enable-instructor-filter' => 'false',
					'commission-content' => 'course',

				), $attrs, 'dtlms_instructor_alltime_earnings' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'instructor', (array) $current_user->roles ) || in_array( 'administrator', (array) $current_user->roles ) ) {

			$output .= '<div class="dtlms-statistics-container">';

				if ( in_array( 'administrator', (array) $current_user->roles ) && $attrs['enable-instructor-filter'] == 'true' ) {

					$instructor_label = apply_filters( 'instructor_label', 'singular' );
				    $output .= '<select class="dtlms-statistics-commission-instructor" name="dtlms-statistics-commission-instructor" style="width:50%;" data-placeholder="'.sprintf( esc_html__('Choose %1$s ...', 'dtlms'), $instructor_label ).'" class="dtlms-chosen-select" data-commissioncontent="'.$attrs['commission-content'].'">';

						$output .= '<option value="-1">'.esc_html__('None', 'dtlms').'</option>';

						$instructors = get_users ( array ('role' => 'instructor') );
				        if ( count( $instructors ) > 0 ) {
				            foreach ($instructors as $instructor) {
								$instructor_id = $instructor->data->ID;
				                $output .= '<option value="' . esc_attr( $instructor_id ) . '">' . esc_html( $instructor->data->display_name ) . '</option>';
				            }
				        }

				    $output .= '</select>';

					$output .= '<div class="dtlms-hr-invisible"></div>';

					$output .= dtlms_generate_loader_html(true);

					$output .= '<div class="dtlms-instructor-commissions-container">'.dtlms_load_instructorwise_commissions(-1, 'dtlms_load_instructorwise_commissions', 'dtlms-instructor-commissions-container', $attrs['commission-content']).'</div>';

				} else {

					$output .= '<div class="dtlms-instructor-commissions-container">'.dtlms_load_instructorwise_commissions($user_id, 'dtlms_load_instructorwise_commissions', 'dtlms-instructor-commissions-container', $attrs['commission-content']).'</div>';

				}

			$output .= '</div>';

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

			
		return $output;

	}	

	function dtlms_student_courses( $attrs, $content = null ) {

		$output = '';

		$current_user = wp_get_current_user();

		if ( in_array( 'administrator', (array) $current_user->roles ) ) {
			
			$output .= '<div class="dtlms-statistics-container">';
				$output .= '<div class="dtlms-students-statistics-container">';
					$output .= dtlms_load_statistics_students_content();
				$output .= '</div>';
			$output .= '</div>';

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_instructor_added_courses( $attrs, $content = null ) {

		$output = '';

		$current_user = wp_get_current_user();

		if ( in_array( 'administrator', (array) $current_user->roles ) ) {
			
			$output .= '<div class="dtlms-statistics-container">';
				$output .= '<div class="dtlms-instructor-statistics-container">';
					$output .= dtlms_load_statistics_instructor_content();
				$output .= '</div>';
			$output .= '</div>';

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_package_details( $attrs, $content = null ) {

		$output = '';

		$current_user = wp_get_current_user();

		if ( in_array( 'administrator', (array) $current_user->roles ) ) {
			
			$output .= '<div class="dtlms-statistics-container">';
				$output .= '<div class="dtlms-package-statistics-container">';
					$output .= dtlms_load_statistics_packages_content();
				$output .= '</div>';
			$output .= '</div>';

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_class_details( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'enable-instructor-filter' => 'false',

				), $attrs, 'dtlms_class_details' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'instructor', (array) $current_user->roles ) || in_array( 'administrator', (array) $current_user->roles ) ) {
			
			$output .= '<div class="dtlms-statistics-container">';

				if ( in_array( 'administrator', (array) $current_user->roles ) && $attrs['enable-instructor-filter'] == 'true' ) {

					$instructor_label = apply_filters( 'instructor_label', 'singular' );
				    $output .= '<select class="dtlms-statistics-classes-instructor" name="dtlms-statistics-classes-instructor" data-placeholder="'.sprintf( esc_html__('Choose %1$s ...', 'dtlms'), $instructor_label ).'" class="dtlms-chosen-select">';

						$output .= '<option value="-1">'.esc_html__('All', 'dtlms').'</option>';

						$instructors = get_users ( array ('role' => 'instructor') );
				        if ( count( $instructors ) > 0 ) {
				            foreach ($instructors as $instructor) {
								$instructor_id = $instructor->data->ID;
				                $output .= '<option value="' . esc_attr( $instructor_id ) . '">' . esc_html( $instructor->data->display_name ) . '</option>';
				            }
				        }

				    $output .= '</select>';

					$output .= '<div class="dtlms-hr-invisible"></div>';

					$output .= dtlms_generate_loader_html(true);

				    $output .= '<div class="dtlms-instructor-classes-container">'.dtlms_load_instructorwise_classes(-1, 'dtlms_load_instructorwise_classes', 'dtlms-instructor-classes-container').'</div>';

				} else {

					$output .= '<div class="dtlms-instructor-classes-container">'.dtlms_load_instructorwise_classes($user_id, 'dtlms_load_instructorwise_classes', 'dtlms-instructor-classes-container').'</div>';

				}

			$output .= '</div>';

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}		

	function dtlms_student_purchased_items( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-title' => '',
					'item-type' => '',

				), $attrs, 'dtlms_student_purchased_items' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			$purchased_items = array ();
			
			if($attrs['item-type'] == 'class') {
				$purchased_items = get_user_meta($user_id, 'purchased_classes', true);
				$purchased_items = (is_array($purchased_items) && !empty($purchased_items)) ? array_filter($purchased_items) : array ();
			} else if($attrs['item-type'] == 'course') {
				$purchased_items = get_user_meta($user_id, 'purchased_courses', true);
				$purchased_items = (is_array($purchased_items) && !empty($purchased_items)) ? array_filter($purchased_items) : array ();
			} else if($attrs['item-type'] == 'package') {
				$purchased_items = get_user_meta($user_id, 'purchased_packages', true);
				$purchased_items = (is_array($purchased_items) && !empty($purchased_items)) ? $purchased_items : array ();
				$purchased_items = array_keys($purchased_items);
			}

			$purchased_items_cnt = count($purchased_items);

			$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Purchased Items', 'dtlms');

			$output .= '<div class="dtlms-total-items">
							<div class="dtlms-total-item-title">'.$item_title.'</div>
							<span>'.$purchased_items_cnt.'</span>
						</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_student_assigned_items( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-title' => '',
					'item-type' => '',

				), $attrs, 'dtlms_student_assigned_items' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			if($attrs['item-type'] == 'class') {
				$assigned_items = get_user_meta($user_id, 'assigned_classes', true);
				$assigned_items = (is_array($assigned_items) && !empty($assigned_items)) ? array_filter($assigned_items) : array ();
			} else if($attrs['item-type'] == 'course') {
				$assigned_items = get_user_meta($user_id, 'assigned_courses', true);
				$assigned_items = (is_array($assigned_items) && !empty($assigned_items)) ? array_filter($assigned_items) : array ();
			}

			$assigned_items_cnt = count($assigned_items);

			$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Assigned Items', 'dtlms');

			$output .= '<div class="dtlms-total-items">
							<div class="dtlms-total-item-title">'.$item_title.'</div>
							<span>'.$assigned_items_cnt.'</span>
						</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}	

	function dtlms_student_undergoing_items( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-title' => '',
					'item-type' => '',

				), $attrs, 'dtlms_student_undergoing_items' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			if($attrs['item-type'] == 'class') {

				$started_classes = get_user_meta($user_id, 'started_classes', true);
				$started_classes = (is_array($started_classes) && !empty($started_classes)) ? array_filter($started_classes) : array ();

				$submitted_classes = get_user_meta($user_id, 'submitted_classes', true);
				$submitted_classes = (is_array($submitted_classes) && !empty($submitted_classes)) ? array_filter($submitted_classes) : array ();	

				$items_undergoing = array_diff($started_classes, $submitted_classes);
				$items_undergoing_cnt = count($items_undergoing);

			} else if($attrs['item-type'] == 'course') {

				$started_courses = get_user_meta($user_id, 'started_courses', true);
				$started_courses = (is_array($started_courses) && !empty($started_courses)) ? array_filter($started_courses) : array ();

				$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
				$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? array_filter($submitted_courses) : array ();

				$items_undergoing = array_diff($started_courses, $submitted_courses);
				$items_undergoing_cnt = count($items_undergoing);

			}		

			$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Undergoing Items', 'dtlms');

			$output .= '<div class="dtlms-total-items">
							<div class="dtlms-total-item-title">'.$item_title.'</div>
							<span>'.$items_undergoing_cnt.'</span>
						</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');
		
		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_student_underevaluation_items( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-title' => '',
					'item-type' => '',

				), $attrs, 'dtlms_student_underevaluation_items' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			if($attrs['item-type'] == 'class') {

				$submitted_classes = get_user_meta($user_id, 'submitted_classes', true);
				$submitted_classes = (is_array($submitted_classes) && !empty($submitted_classes)) ? array_filter($submitted_classes) : array ();

				$completed_classes = get_user_meta($user_id, 'completed_classes', true);
				$completed_classes = (is_array($completed_classes) && !empty($completed_classes)) ? array_filter($completed_classes) : array ();

				$items_underevaluation = array_diff($submitted_classes, $completed_classes);
				$items_underevaluation_cnt = count($items_underevaluation);

			} else if($attrs['item-type'] == 'course') {

				$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
				$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? array_filter($submitted_courses) : array ();

				$completed_courses = get_user_meta($user_id, 'completed_courses', true);
				$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? array_filter($completed_courses) : array ();

				$items_underevaluation = array_diff($submitted_courses, $completed_courses);
				$items_underevaluation_cnt = count($items_underevaluation);

			}

			$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Underevaluation Items', 'dtlms');

			$output .= '<div class="dtlms-total-items">
							<div class="dtlms-total-item-title">'.$item_title.'</div>
							<span>'.$items_underevaluation_cnt.'</span>
						</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');
		
		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_student_completed_items( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-title' => '',
					'item-type' => '',

				), $attrs, 'dtlms_student_completed_items' );


		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			if($attrs['item-type'] == 'class') {
				$completed_items = get_user_meta($user_id, 'completed_classes', true);
				$completed_items = (is_array($completed_items) && !empty($completed_items)) ? array_filter($completed_items) : array ();
			} else if($attrs['item-type'] == 'course') {
				$completed_items = get_user_meta($user_id, 'completed_courses', true);
				$completed_items = (is_array($completed_items) && !empty($completed_items)) ? array_filter($completed_items) : array ();
			}

			$completed_items_cnt = count($completed_items);

			$item_title = ($attrs['item-title'] != '') ? $attrs['item-title'] : esc_html__('Completed Items', 'dtlms');

			$output .= '<div class="dtlms-total-items">
							<div class="dtlms-total-item-title">'.$item_title.'</div>
							<span>'.$completed_items_cnt.'</span>
						</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');
		
		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_student_badges( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-type' => 'course',
					'include-registration-class' => '',

				), $attrs, 'dtlms_student_badges' );

		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			$registered_classes = $completed_items = array ();

			if($attrs['item-type'] == 'class' || $attrs['item-type'] == 'all') {

				$completed_classes = get_user_meta($user_id, 'completed_classes', true);
				$completed_classes = (is_array($completed_classes) && !empty($completed_classes)) ? array_filter($completed_classes) : array ();

				$completed_items = array_merge_recursive($completed_items, $completed_classes);		
		
			} 

			if($attrs['item-type'] == 'course' || $attrs['item-type'] == 'all') {

				$completed_courses = get_user_meta($user_id, 'completed_courses', true);
				$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? array_filter($completed_courses) : array ();

				$completed_items = array_merge_recursive($completed_items, $completed_courses);

			}

			if(($attrs['item-type'] == 'class' && $attrs['include-registration-class'] == 'true') || $attrs['item-type'] == 'all') {
				$registered_classes = get_user_meta($user_id, 'registered_classes', true);
				$registered_classes = (is_array($registered_classes) && !empty($registered_classes)) ? $registered_classes : array ();
			}		

			$output .= '<div class="dtlms-student-badges-holder">';

				if(is_array($completed_items) && !empty($completed_items)) {
					foreach($completed_items as $completed_item) {
						$curriculum_details = get_user_meta($user_id, $completed_item, true);
						$completed_item_grade_id = $curriculum_details['grade-post-id'];

			            $badge_achieved = get_post_meta($completed_item_grade_id, 'badge-achieved', true);
			            if($badge_achieved == 'true') {
							$badge_image_url = get_post_meta ( $completed_item, 'badge-image-url', true );
							$output .= '<img src="'.$badge_image_url.'" alt="'.esc_html__('LMS Badge', 'dtlms').'" title="'.esc_html__('LMS Badge', 'dtlms').'" />';
			            }				
					}
				}

				if(is_array($registered_classes) && !empty($registered_classes)) {
					foreach($registered_classes as $class_id) {
						$registered_users = get_post_meta($class_id, 'registered_users', true);
						$registered_users = (is_array($registered_users) && !empty($registered_users)) ? $registered_users : array ();

						if(isset($registered_users[$user_id]['badge']) && $registered_users[$user_id]['badge'] == 'approved') {
							$badge_image_url = get_post_meta($class_id, 'badge-image-url', true);
							$output .= '<img src="'.$badge_image_url.'" alt="'.esc_html__('LMS Badge', 'dtlms').'" title="'.esc_html__('LMS Badge', 'dtlms').'" />';								
						}
					}
				}	

			$output .= '</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');
		
		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_student_certificates( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-type' => '',
					'include-registration-class' => '',

				), $attrs, 'dtlms_student_certificates' );

		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			$registered_classes = $completed_items = array ();

			if($attrs['item-type'] == 'class' || $attrs['item-type'] == 'all') {

				$completed_classes = get_user_meta($user_id, 'completed_classes', true);
				$completed_classes = (is_array($completed_classes) && !empty($completed_classes)) ? array_filter($completed_classes) : array ();

				$completed_items = array_merge_recursive($completed_items, $completed_classes);

			} 
			if($attrs['item-type'] == 'course' || $attrs['item-type'] == 'all') {

				$completed_courses = get_user_meta($user_id, 'completed_courses', true);
				$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? array_filter($completed_courses) : array ();

				$completed_items = array_merge_recursive($completed_items, $completed_courses);

			}

			if(($attrs['item-type'] == 'class' && $attrs['include-registration-class'] == 'true') || $attrs['item-type'] == 'all') {
				$registered_classes = get_user_meta($user_id, 'registered_classes', true);
				$registered_classes = (is_array($registered_classes) && !empty($registered_classes)) ? $registered_classes : array ();
			}


			// Create completed courses array with grade id
			$certificate_achieved_items = array ();
			if(!empty($completed_items)) {
				foreach($completed_items as $completed_item) {
					$curriculum_details = get_user_meta($user_id, $completed_item, true);
					$completed_item_grade_id = $curriculum_details['grade-post-id'];

					$certificate_achieved = get_post_meta($completed_item_grade_id, 'certificate-achieved', true);
					if($certificate_achieved == 'true') {
						$certificate_achieved_items[$completed_item] = $completed_item_grade_id;
					}					
				}
			}

			if(is_array($registered_classes) && !empty($registered_classes)) {
				foreach($registered_classes as $class_id) {
					$registered_users = get_post_meta($class_id, 'registered_users', true);
					$registered_users = (is_array($registered_users) && !empty($registered_users)) ? $registered_users : array ();

					if(isset($registered_users[$user_id]['certificate']) && $registered_users[$user_id]['certificate'] == 'approved') {
						$certificate_achieved_items[$class_id] = -1;							
					}
				}
			}		

			$output .= '<div class="dtlms-student-certificate-holder">';

				if(!empty($certificate_achieved_items)) {

					$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
					              <tr>
					                <th scope="col">'.esc_html__('#', 'dtlms').'</th>
					                <th scope="col">'.esc_html__('Item Name', 'dtlms').'</th>
					                <th scope="col">'.esc_html__('Percentage', 'dtlms').'</th>
					                <th scope="col">'.esc_html__('Certificate', 'dtlms').'</th>
					                <th scope="col">'.esc_html__('Options', 'dtlms').'</th>
					              </tr>';

								$i = 1;
								foreach($certificate_achieved_items as $certificate_achieved_item => $item_grade_id) {

									$user_percentage = '';
									if($item_grade_id > 0) {
										$user_percentage = get_post_meta($item_grade_id, 'user-percentage', true);
										if($user_percentage != '') {
											$user_percentage = $user_percentage.'%';
										}
									}

									$certificate_template = get_post_meta($certificate_achieved_item, 'certificate-template', true);

									$output .= '<tr>
												<td>'.$i.'</td>
												<td><a href="'.get_permalink($certificate_achieved_item).'">'.get_the_title($certificate_achieved_item).'</a></td>
												<td>'.$user_percentage.'</td>';

												if($certificate_template != '') {
													$output .= '<td>'.get_the_title($certificate_template).'</td>
																<td>
																	<a href="#" class="dtlms-generate-certificate-content" data-certificateid="'.$certificate_template.'"  data-itemid="'.$certificate_achieved_item.'" data-gradeid="'.$item_grade_id.'" data-userid="'.$user_id.'" onclick="return false;">'.esc_html__('Download', 'dtlms').'</a>
																</td>';
												} else {
													$output .= '<td></td>
																<td></td>';															
												}							

									$output .= '</tr>';

									$i++; 

								}

				    $output .= '</table>';

				} else {
					$output .= '<p class="dtlms-note">'.esc_html__('No certificates found!', 'dtlms').'</p>';
				}

				$output .= dtlms_generate_loader_html(false);

			$output .= '</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_student_purchased_items_list( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-type' => '',

				), $attrs, 'dtlms_student_purchased_items_list' );

		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			$item_name = '';
			if($attrs['item-type'] == 'class') {

				$purchased_items = get_user_meta($user_id, 'purchased_classes', true);
				$purchased_items = (is_array($purchased_items) && !empty($purchased_items)) ? array_filter($purchased_items) : array ();
				$class_plural_label = apply_filters( 'class_label', 'plural' );
				$item_name = sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label );

			} else if($attrs['item-type'] == 'course') {

				$purchased_items = get_user_meta($user_id, 'purchased_courses', true);
				$purchased_items = (is_array($purchased_items) && !empty($purchased_items)) ? array_filter($purchased_items) : array ();
				$item_name = esc_html__('Courses', 'dtlms');

			} else if($attrs['item-type'] == 'package') {

				$purchased_items = get_user_meta($user_id, 'purchased_packages', true);
				$purchased_items = (is_array($purchased_items) && !empty($purchased_items)) ? $purchased_items : array ();
				$purchased_items = array_keys($purchased_items);
				$item_name = esc_html__('Packages', 'dtlms');

			}

			$output .= '<div class="dtlms-custom-table-wrapper">';

				$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
				              <tr>
				                <th scope="col">'.esc_html__('#', 'dtlms').'</th>
				                <th scope="col">'.$item_name.'</th>
				              </tr>';		

					if(!empty($purchased_items)) {

						$i = 1;         
						foreach ($purchased_items as $purchased_item) {

							$output .= '<tr>
											<td>'.$i.'</td>
											<td><a href="'.get_permalink($purchased_item).'">'.get_the_title($purchased_item).'</a></td>
										</tr>';
							$i++; 

						}

					} else {

						$output .= '<tr><td colspan="2">'.esc_html__('No records found!', 'dtlms').'</td></tr>';

					}

				$output .= '</table>';

			$output .= '</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_student_assigned_items_list( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-type' => '',

				), $attrs, 'dtlms_student_assigned_items_list' );

		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			$item_name = '';
			if($attrs['item-type'] == 'class') {
				$assigned_items = get_user_meta($user_id, 'assigned_classes', true);
				$assigned_items = (is_array($assigned_items) && !empty($assigned_items)) ? array_filter($assigned_items) : array ();
				$class_plural_label = apply_filters( 'class_label', 'plural' );
				$item_name = sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label );				
			} else if($attrs['item-type'] == 'course') {
				$assigned_items = get_user_meta($user_id, 'assigned_courses', true);
				$assigned_items = (is_array($assigned_items) && !empty($assigned_items)) ? array_filter($assigned_items) : array ();
				$item_name = esc_html__('Courses', 'dtlms');
			}

			$output .= '<div class="dtlms-custom-table-wrapper">';

				$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
				              <tr>
				                <th scope="col">'.esc_html__('#', 'dtlms').'</th>
				                <th scope="col">'.$item_name.'</th>
				              </tr>';

					if(!empty($assigned_items)) {

						$i = 1;         
						foreach ($assigned_items as $assigned_item) {

							$output .= '<tr>
											<td>'.$i.'</td>
											<td><a href="'.get_permalink($assigned_item).'">'.get_the_title($assigned_item).'</a></td>
										</tr>';
							$i++; 

						}			

					} else {

						$output .= '<tr><td colspan="2">'.esc_html__('No records found!', 'dtlms').'</td></tr>';

					}

				$output .= '</table>';

			$output .= '</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}			

		return $output;

	}	
		
	function dtlms_student_undergoing_items_list( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-type' => '',

				), $attrs, 'dtlms_student_undergoing_items_list' );

		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;


		if ( in_array( 'student', (array) $current_user->roles ) ) {

			$item_name = '';
			if($attrs['item-type'] == 'class') {

				$started_classes = get_user_meta($user_id, 'started_classes', true);
				$started_classes = (is_array($started_classes) && !empty($started_classes)) ? array_filter($started_classes) : array ();

				$submitted_classes = get_user_meta($user_id, 'submitted_classes', true);
				$submitted_classes = (is_array($submitted_classes) && !empty($submitted_classes)) ? array_filter($submitted_classes) : array ();

				$items_undergoing = array_diff($started_classes, $submitted_classes);

				$class_plural_label = apply_filters( 'class_label', 'plural' );
				$item_name = sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label );				

			} else if($attrs['item-type'] == 'course') {

				$started_courses = get_user_meta($user_id, 'started_courses', true);
				$started_courses = (is_array($started_courses) && !empty($started_courses)) ? array_filter($started_courses) : array ();

				$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
				$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? array_filter($submitted_courses) : array ();

				$items_undergoing = array_diff($started_courses, $submitted_courses);

				$item_name = esc_html__('Courses', 'dtlms');

			}

			$output .= '<div class="dtlms-custom-table-wrapper">';

				$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
				              <tr>
				                <th scope="col">'.esc_html__('#', 'dtlms').'</th>
				                <th scope="col">'.$item_name.'</th>
				                <th scope="col">'.esc_html__('Progress', 'dtlms').'</th>
				              </tr>';

					if(!empty($items_undergoing)) {

						if($attrs['item-type'] == 'class') {

							$i = 1;         
							foreach ($items_undergoing as $item_undergoing) {

								$total_curriculum_count = $submitted_items_count = 0;

								$class_courses = get_post_meta($item_undergoing, 'dtlms-class-courses', true);
								if(is_array($class_courses) && !empty($class_courses)) {
									$total_curriculum_count = count($class_courses);
									foreach($class_courses as $course_id) {

										// Submitted Items
										$submitted_args = array ( 
															'post_type' => 'dtlms_gradings',
															'meta_query'=>array(), 
														);	
										$submitted_args['meta_query'][] = array (
																				'key'     => 'dtlms-course-id',
																				'value'   => $course_id,
																				'compare' => '=='
																			);
										$submitted_args['meta_query'][] = array (
																				'key'     => 'dtlms-user-id',
																				'value'   => $user_id,
																				'compare' => '=='
																			);																			
										$submitted_args['meta_query'][] = array (
																				'key'     => 'grade-type',
																				'value'   => 'course',
																				'compare' => '=='
																			);																	
										$submitted_args['meta_query'][] = array (
																				'key'     => 'submitted',
																				'value'   => '1',
																				'compare' => '=='
																			);					
										$submitted_gradings = new WP_Query( $submitted_args );
										$submitted_gradings_count = $submitted_gradings->found_posts; 
										wp_reset_postdata();

										$submitted_items_count = $submitted_items_count + $submitted_gradings_count;

									}
								}

								$submitted_percentage = 0;
								if($total_curriculum_count > 0) {

									if($submitted_items_count > 0) {
										$submitted_percentage = round((($submitted_items_count/$total_curriculum_count)*100), 2);
									} else {
										$submitted_percentage = 0;
									}

								}

								$output .= '<tr>
												<td>'.$i.'</td>
												<td><a href="'.get_permalink($item_undergoing).'">'.get_the_title($item_undergoing).'</a></td>
												<td>'.dtlms_generate_progressbar($submitted_percentage).'<span class="dtlms-item-percentage">'.$submitted_percentage.'%</span></td>
											</tr>';

								$i++; 						

							}


						} else if($attrs['item-type'] == 'course') {

							$i = 1;         
							foreach ($items_undergoing as $item_undergoing) {

								$total_curriculum_count = dtlms_course_curriculum_counts($item_undergoing, true);
								$curriculum_details = get_user_meta($user_id, $item_undergoing, true);
								$submitted_items_count = dtlms_parse_array_and_count_particular_key($curriculum_details['curriculum'], 'grade-post-id', 0);

								if($total_curriculum_count > 0) {
									$percentage = round((($submitted_items_count/$total_curriculum_count)*100), 2);	
								} else {
									$percentage = '';
								}

								$output .= '<tr>
												<td>'.$i.'</td>
												<td><a href="'.get_permalink($item_undergoing).'">'.get_the_title($item_undergoing).'</a></td>
												<td>'.dtlms_generate_progressbar($percentage).'<span class="dtlms-item-percentage">'.$percentage.'%</span></td>
											</tr>';

								$i++; 

							}

						}		

					} else {

						$output .= '<tr><td colspan="3">'.esc_html__('No records found!', 'dtlms').'</td></tr>';

					}

				$output .= '</table>';

			$output .= '</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}	

		return $output;

	}

	function dtlms_student_underevaluation_items_list( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-type' => '',

				), $attrs, 'dtlms_student_underevaluation_items_list' );

		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			$item_name = '';
			if($attrs['item-type'] == 'class') {

				$submitted_classes = get_user_meta($user_id, 'submitted_classes', true);
				$submitted_classes = (is_array($submitted_classes) && !empty($submitted_classes)) ? array_filter($submitted_classes) : array ();

				$completed_classes = get_user_meta($user_id, 'completed_classes', true);
				$completed_classes = (is_array($completed_classes) && !empty($completed_classes)) ? array_filter($completed_classes) : array ();

				$items_underevaluation = array_diff($submitted_classes, $completed_classes);

				$class_plural_label = apply_filters( 'class_label', 'plural' );
				$item_name = sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label );

			} else if($attrs['item-type'] == 'course') {

				$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
				$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? array_filter($submitted_courses) : array ();

				$completed_courses = get_user_meta($user_id, 'completed_courses', true);
				$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? array_filter($completed_courses) : array ();

				$items_underevaluation = array_diff($submitted_courses, $completed_courses);

				$item_name = esc_html__('Courses', 'dtlms');

			}

			$output .= '<div class="dtlms-custom-table-wrapper">';

				$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
				              <tr>
				                <th scope="col">'.esc_html__('#', 'dtlms').'</th>
				                <th scope="col">'.$item_name.'</th>
				                <th scope="col">'.esc_html__('Progress', 'dtlms').'</th>
				              </tr>';		

					if(!empty($items_underevaluation)) {

						if($attrs['item-type'] == 'class') {

							$i = 1;         
							foreach ($items_underevaluation as $item_underevaluation) {

								$total_curriculum_count = $completed_items_count = 0;

								$class_courses = get_post_meta($item_underevaluation, 'dtlms-class-courses', true);
								if(is_array($class_courses) && !empty($class_courses)) {
									$total_curriculum_count = count($class_courses);
									foreach($class_courses as $course_id) {

										// Completed Items
										$completed_args = array ( 
															'post_type' => 'dtlms_gradings',
															'meta_query'=>array(), 
														);	
										$completed_args['meta_query'][] = array (
																				'key'     => 'dtlms-course-id',
																				'value'   => $course_id,
																				'compare' => '=='
																			);
										$completed_args['meta_query'][] = array (
																				'key'     => 'dtlms-user-id',
																				'value'   => $user_id,
																				'compare' => '=='
																			);																			
										$completed_args['meta_query'][] = array (
																				'key'     => 'grade-type',
																				'value'   => 'course',
																				'compare' => '=='
																			);																	
										$completed_args['meta_query'][] = array (
																				'key'     => 'graded',
																				'value'   => 'true',
																				'compare' => '=='
																			);				
										$completed_gradings = new WP_Query( $completed_args );
										$completed_gradings_count = $completed_gradings->found_posts; 
										wp_reset_postdata();

										$completed_items_count = $completed_items_count + $completed_gradings_count;	

									}
								}

								$completed_percentage = 0;
								if($total_curriculum_count > 0) {

									if($completed_items_count > 0) {
										$completed_percentage = round((($completed_items_count/$total_curriculum_count)*100), 2);
									} else {
										$completed_percentage = 0;
									}	

								}

								$output .= '<tr>
												<td>'.$i.'</td>
												<td><a href="'.get_permalink($item_underevaluation).'">'.get_the_title($item_underevaluation).'</a></td>
												<td>'.dtlms_generate_progressbar($completed_percentage).'<span class="dtlms-item-percentage">'.$completed_percentage.'%</span></td>
											</tr>';

								$i++; 						

							}


						} else if($attrs['item-type'] == 'course') {

							$i = 1;         
							foreach ($items_underevaluation as $item_underevaluation) {

								$total_curriculum_count = dtlms_course_curriculum_counts($item_underevaluation, true);
								$curriculum_details = get_user_meta($user_id, $item_underevaluation, true);
								$completed_items_count = dtlms_parse_array_and_count_particular_key($curriculum_details['curriculum'], 'completed', 0);

								$percentage = 0;
								if($total_curriculum_count > 0) {
									$percentage = round((($completed_items_count/$total_curriculum_count)*100), 2);
								}

								$output .= '<tr>
												<td>'.$i.'</td>
												<td><a href="'.get_permalink($item_underevaluation).'">'.get_the_title($item_underevaluation).'</a></td>
												<td>'.dtlms_generate_progressbar($percentage).'<span class="dtlms-item-percentage">'.$percentage.'%</span></td>
											</tr>';

								$i++; 

							}

						}

					} else {

						$output .= '<tr><td colspan="3">'.esc_html__('No records found!', 'dtlms').'</td></tr>';

					}

				$output .= '</table>';

			$output .= '</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}	

		return $output;

	}

	function dtlms_student_completed_items_list( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'item-type' => '',

				), $attrs, 'dtlms_student_completed_items_list' );

		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			$item_name = '';
			if($attrs['item-type'] == 'class') {

				$completed_items = get_user_meta($user_id, 'completed_classes', true);
				$completed_items = (is_array($completed_items) && !empty($completed_items)) ? array_filter($completed_items) : array ();	
				$class_plural_label = apply_filters( 'class_label', 'plural' );
				$item_name = sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label );

			} else if($attrs['item-type'] == 'course') {

				$completed_items = get_user_meta($user_id, 'completed_courses', true);
				$completed_items = (is_array($completed_items) && !empty($completed_items)) ? array_filter($completed_items) : array ();
				$item_name = esc_html__('Courses', 'dtlms');

			}

			$output .= '<div class="dtlms-custom-table-wrapper">';

				$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
								<tr>
									<th scope="col">'.esc_html__('#', 'dtlms').'</th>
									<th scope="col">'.$item_name.'</th>
								</tr>';

					if(!empty($completed_items)) {

							$i = 1;         
							foreach ($completed_items as $completed_item) {

								$output .= '<tr>
												<td>'.$i.'</td>
												<td><a href="'.get_permalink($completed_item).'">'.get_the_title($completed_item).'</a></td>
											</tr>';
								$i++; 

							}

					} else {

						$output .= '<tr><td colspan="2">'.esc_html__('No records found!', 'dtlms').'</td></tr>';

					}

				$output .= '</table>';

			$output .= '</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}	

		return $output;

	}

	function dtlms_student_course_curriculum_details( $attrs, $content = null ) {

		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			$completed_courses = get_user_meta($user_id, 'completed_courses', true);
			$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? $completed_courses : array ();

			$courses_list = $completed_courses;

			$output .= '<div class="dtlms-custom-table-wrapper">';

				$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
				              <tr>
				                <th scope="col">'.esc_html__('#', 'dtlms').'</th>
				                <th scope="col">'.esc_html__('Course', 'dtlms').'</th>
				                <th scope="col">'.esc_html__('Option', 'dtlms').'</th>
				              </tr>';

					if(!empty($courses_list)) {

						$i = 1;         
						foreach ($courses_list as $course_id) {

							$output .= '<tr>
											<td>'.$i.'</td>
											<td><a href="'.get_permalink($course_id).'">'.get_the_title($course_id).'</a></td>
											<td><a href="#" class="dtlms-button dtlms-view-course-result dtlms-dashboard filled small"  data-courseid="'.$course_id.'" data-userid="'.$user_id.'">'.esc_html__('View Results', 'dtlms').'</a></td>
										</tr>';

							$i++; 

						}		

					} else {

						$output .= '<tr><td colspan="3">'.esc_html__('No records found!', 'dtlms').'</td></tr>';

					}

				$output .= '</table>';

			$output .= '</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');

		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}

		return $output;

	}

	function dtlms_student_course_events( $attrs, $content = null ) {

		$output = '';

		if(class_exists('Tribe__Events__Pro__Main')) {

			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;

			if ( in_array( 'student', (array) $current_user->roles ) ) {

				$started_courses = get_user_meta($user_id, 'started_courses', true);
				$started_courses = (is_array($started_courses) && !empty($started_courses)) ? $started_courses : array ();

				$filter_str = '';
				foreach($started_courses as $course_id) {
					$dt_course_event_catids =  get_post_meta( $course_id, 'dtlms-course-event-catid', true );
					if(isset($dt_course_event_catids) && !empty($dt_course_event_catids)) {
						foreach($dt_course_event_catids as $dt_course_event_catid) {
							$filter_str .= '{"tribe_events_cat":["'.$dt_course_event_catid.'"]},';
						}		
					}
				}

				$filter_str = rtrim($filter_str, ',');

				$output .= '<h4>'.esc_html__('Course Events', 'dtlms').'</h4>';

				if($filter_str != '') {

					$instance = array();
					$instance['title'] = '';
					$instance['count'] = 10;
					$instance['filters'] = $filter_str;
					$instance['operand'] = 'OR';

					ob_start();
					the_widget('Tribe__Events__Pro__Mini_Calendar_Widget', $instance);
					$events_output = ob_get_contents();
					ob_end_clean();

					$output .= $events_output;	

				} else {

					$output .= '<p class="dtlms-note">'.esc_html__('No events found!', 'dtlms').'</p>';

				}	

			} else if(in_array( 'administrator', (array) $current_user->roles )) {

				$output .= esc_html__('No records found.', 'dtlms');

			} else {

				$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

			}

		} else {

			$output .= '<p class="dtlms-note">'.esc_html__('Please make sure Events Calendar Pro plugin is activated.', 'dtlms').'</p>';

		}

		return $output;

	}

	function dtlms_student_class_curriculum_details( $attrs, $content = null ) {

		$output = '';

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		if ( in_array( 'student', (array) $current_user->roles ) ) {

			$started_classes = get_user_meta($user_id, 'started_classes', true);
			$started_classes = (is_array($started_classes) && !empty($started_classes)) ? $started_classes : array ();

			$submitted_classes = get_user_meta($user_id, 'submitted_classes', true);
			$submitted_classes = (is_array($submitted_classes) && !empty($submitted_classes)) ? $submitted_classes : array ();

			$completed_classes = get_user_meta($user_id, 'completed_classes', true);
			$completed_classes = (is_array($completed_classes) && !empty($completed_classes)) ? $completed_classes : array ();

			$classes_undergoing = array_diff($started_classes, $submitted_classes);
			$classes_underevaluation = array_diff($submitted_classes, $completed_classes);

			$classes_list = array_merge($completed_classes, $classes_underevaluation, $classes_undergoing);
			$classes_list = array_filter($classes_list);

			$class_singular_label = apply_filters( 'class_label', 'singular' );

			$output .= '<div class="dtlms-custom-table-wrapper">';
			
				$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
				              <tr>
				                <th scope="col">'.esc_html__('#', 'dtlms').'</th>
				                <th scope="col">'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ).'</th>
				                <th scope="col">'.esc_html__('Option', 'dtlms').'</th>
				              </tr>';

					if(!empty($classes_list)) {              

						$i = 1;         
						foreach ($classes_list as $class_id) {

							$output .= '<tr>
											<td>'.$i.'</td>
											<td><a href="'.get_permalink($class_id).'">'.get_the_title($class_id).'</a></td>
											<td><a href="#" class="dtlms-button dtlms-view-class-result dtlms-dashboard filled small"  data-classid="'.$class_id.'" data-userid="'.$user_id.'">'.esc_html__('View Results', 'dtlms').'</a></td>
										</tr>';

							$i++; 

						}				

					} else {

						$output .= '<tr><td colspan="3">'.esc_html__('No records found!', 'dtlms').'</td></tr>';

					}

				$output .= '</table>';

			$output .= '</div>';

		} else if(in_array( 'administrator', (array) $current_user->roles )) {

			$output .= esc_html__('No records found.', 'dtlms');
			
		} else {

			$output .= esc_html__('You are not authorized to view these datas.', 'dtlms');

		}
		
		return $output;

	}

}

?>