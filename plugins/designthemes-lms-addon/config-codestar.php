<?php
if ( ! class_exists( 'DTLMSCodeStar' ) ) {

	class DTLMSCodeStar {

		function __construct() {

			# Required Form Plugin
			if(!defined( 'CS_ACTIVE_SHORTCODE')) {
				define( 'CS_ACTIVE_SHORTCODE', true );
			}

			# Can changeable in theme or other plugin uses Codestar
			define( 'CS_ACTIVE_CUSTOMIZE', false );
			define( 'CS_ACTIVE_LIGHT_THEME', false );


			add_action( 'init', array( $this, 'dtlms_init_cs_framework' ) );

			# Shortcode Options
			add_filter( 'cs_shortcode_options', array( $this, 'dtlms_cs_shortcode_options' ) );

		}

		function dtlms_init_cs_framework() {

			if( ! function_exists( 'cs_framework_init' ) && ! class_exists( 'CSFramework' ) ) {
				require_once plugin_dir_path ( __FILE__ ) . 'cs-framework/cs-framework.php';
			}

		}

		function dtlms_cs_shortcode_options( $shortcodes ) {

			$instructor_singular_label = apply_filters( 'instructor_label', 'singular' );
			$instructor_plural_label = apply_filters( 'instructor_label', 'plural' );

			$class_singular_label = apply_filters( 'class_label', 'singular' );
			$class_plural_label = apply_filters( 'class_label', 'plural' );


			$dtlms_pages_list = array ();
			$dtlms_pages_list[] = esc_html__('Default - Ajax Output', 'dtlms');
			$pages = get_pages(); 
			foreach ( $pages as $page ) {
				$dtlms_pages_list[$page->ID] = $page->post_title;
			}


			$codestar = dtlms_is_theme_has_codestar();
			$shortcodes = ( $codestar ) ? $shortcodes : array();

			$shortcodes[] = array (
				'title'      => esc_html__('DesignThemes LMS Addon Shortcodes', 'dtlms'),
				'shortcodes' => array (

					# Columns
					array(
						'name'   => 'dtlms_columns',
						'title'  => esc_html__( 'Columns', 'dtlms' ),
						'fields' => array (

							# Columns
							array(
								'id'         => 'columns',
								'type'       => 'select',
								'title'      => esc_html__('Columns', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									1 => 1,
									2 => 2,
									3 => 3,
									4 => 4,
								)
							),

							# First Item
							array(
								'id'         => 'first_item',
								'type'       => 'select',
								'title'      => esc_html__('First Item', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'  => esc_html__('True', 'dtlms'),
								)
							),

					        # Class
					        array (
					          'id'    => 'class',
					          'type'  => 'text',
					          'title' => esc_html__( 'Class', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

						)
					),

					# Vertical Tabs
					array(
						'name'   => 'dtlms_vertical_tabs',
						'title'  => esc_html__( 'Vertical Tabs', 'dtlms' ),
						'fields' => array (

					        # Class
					        array (
					          'id'    => 'class',
					          'type'  => 'text',
					          'title' => esc_html__( 'Class', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

						)
					),

					# Vertical Tab
					array(
						'name'   => 'dtlms_vertical_tab',
						'title'  => esc_html__( 'Vertical Tab', 'dtlms' ),
						'fields' => array (

					        # Title
					        array (
					          'id'    => 'title',
					          'type'  => 'text',
					          'title' => esc_html__( 'Title', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

						)
					),					

					# Login / Logout Links
					array(
						'name'   => 'dtlms_login_logout_links',
						'title'  => esc_html__( 'Login / Logout Links', 'dtlms' ),
						'fields' => array (

					        # Class
					        array (
					          'id'    => 'class',
					          'type'  => 'text',
					          'title' => esc_html__( 'Class', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

						)
					),

					# Certificate Details
					array(
						'name'   => 'dtlms_certificate_details',
						'title'  => esc_html__( 'Certificate Details', 'dtlms' ),
						'fields' => array (

							# Item Type
							array(
								'id'         => 'item_type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'student_name' => esc_html__('Student Name','dtlms'),
									'item_name' => sprintf( esc_html__( 'Item Name ( %1$s or Course )', 'dtlms' ), $class_singular_label ),
									'student_percent' => esc_html__('Student Percent','dtlms'),
									'date' => esc_html__('Date','dtlms')
								)
							),

						)
					),

					# Certificate
					array(
						'name'   => 'dtlms_certificate',
						'title'  => esc_html__( 'Certificate', 'dtlms' ),
						'fields' => array (

							# Type
							array(
								'id'         => 'type',
								'type'       => 'select',
								'title'      => esc_html__('Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'type1' => esc_html__('Type 1','dtlms'),
									'type2' => esc_html__('Type 2', 'dtlms' ),
									'type3' => esc_html__('Type 3','dtlms')
								)
							),

							# Logo 1
							array(
								'id'         => 'logo1',
								'type'       => 'image',
								'title'      => esc_html__('Logo 1', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
							),

							# Logo 2
							array(
								'id'         => 'logo2',
								'type'       => 'image',
								'title'      => esc_html__('Logo 2', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
							),															

					        # Heading
					        array (
								'id'    	=> 'heading',
								'type'  	=> 'text',
								'title' 	=> esc_html__( 'Heading', 'dtlms' ),
								'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

					        # Sub Heading
					        array (
								'id'    	=> 'subheading',
								'type'  	=> 'text',
								'title' 	=> esc_html__( 'Sub Heading', 'dtlms' ),
								'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

							# Footer Logo
							array(
								'id'         => 'footer_logo',
								'type'       => 'image',
								'title'      => esc_html__('Footer Logo', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
							),

							# Signature
							array(
								'id'         => 'signature',
								'type'       => 'image',
								'title'      => esc_html__('Signature', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
							),

						)
					),

					# Courses Listing
					array(
						'name'   => 'dtlms_courses_listing',
						'title'  => esc_html__( 'Courses Listing', 'dtlms' ),
						'fields' => array (

							# Disable All Filter Options
							array(
								'id'         => 'disable-all-filters',
								'type'       => 'select',
								'title'      => esc_html__('Disable All Filter Options', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can disable all filter options and only course content will be displayed.', 'dtlms' ),
							),	

							# Enable Search Filter
							array(
								'id'         => 'enable-search-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Search Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable search filter option.', 'dtlms' ),
							),	

							# Enable Display Filter
							array(
								'id'         => 'enable-display-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Display Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable display filter option.', 'dtlms' ),
							),	

							# Enable Order By Filter
							array(
								'id'         => 'enable-orderby-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Order By Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable orderby filter option.', 'dtlms' ),
							),	

							# Enable Category Filter
							array(
								'id'         => 'enable-category-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Category Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable category filter option.', 'dtlms' ),
							),	

							# Enable Instructor Filter
							array(
								'id'         => 'enable-instructor-filter',
								'type'       => 'select',
								'title'      => sprintf(esc_html__('Enable %s Filter', 'dtlms'), $instructor_singular_label),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable instructor filter option.', 'dtlms' ),
							),	

							# Enable Cost Filter
							array(
								'id'         => 'enable-cost-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Cost Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable cost filter option.', 'dtlms' ),
							),

							# Enable Date Filter
							array(
								'id'         => 'enable-date-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Date Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable date filter option.', 'dtlms' ),
							),	

							# Listing Output Page
							array(
								'id'         => 'listing-output-page',
								'type'       => 'select',
								'title'      => esc_html__('Listing Output Page', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => $dtlms_pages_list,
								'help'  	 => esc_html__( 'If you choose a page here course search result will be outputed in that page. For that you have to add this course listing shortcode again in that page.', 'dtlms' ),
							),


							# Default Filter
							array(
								'id'         => 'default-filter',
								'type'       => 'select',
								'title'      => esc_html__('Default Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'upcoming-courses' => esc_html__('Upcoming Courses', 'dtlms') ,
									'recent-courses' => esc_html__('Recent Courses', 'dtlms'),
									'highest-rated-courses' => esc_html__('Highest Rated Courses', 'dtlms'),
									'most-membered-courses' => esc_html__('Most Membered Courses', 'dtlms'),
									'paid-courses' => esc_html__('Paid Courses', 'dtlms'),
									'free-courses' => esc_html__('Free Courses', 'dtlms'),
								),
								'dependency' => array ( 'disable-all-filters', '==', 'true' ),
								'help'  	 => esc_html__( 'Choose default filter you like to apply in courses listing. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
							),

					        # Course Item Ids
					        array (
					          'id'    => 'course-item-ids',
					          'type'  => 'text',
					          'title' => esc_html__( 'Course Item Ids', 'dtlms' ),
					          'help'  => esc_html__( 'Enter course item ids separated by comma to display from. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					          'dependency' => array ( 'disable-all-filters', '==', 'true' ),
					        ),

					        # Course Category Ids
					        array (
					          'id'    => 'course-category-ids',
					          'type'  => 'text',
					          'title' => esc_html__( 'Course Category Ids', 'dtlms' ),
					          'help'  => esc_html__( 'Enter course category separated by comma to display from. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					          'dependency' => array ( 'disable-all-filters', '==', 'true' ),
					        ),

					        # Instructor Ids
					        array (
					          'id'    => 'instructor-ids',
					          'type'  => 'text',
					          'title' => sprintf(esc_html__('%s Ids', 'dtlms'), $instructor_singular_label),
					          'help'  => sprintf(esc_html__('Enter %s ids separated by comma to display from. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms'), $instructor_singular_label),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					          'dependency' => array ( 'disable-all-filters', '==', 'true' ),
					        ),					        

							# Apply Isotope
							array(
								'id'         => 'apply-isotope',
								'type'       => 'select',
								'title'      => esc_html__('Apply Isotope', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you like to apply isotope for your courses listing, choose "True". This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
							),

							# Enable Category Isotope Filter
							array(
								'id'         => 'enable-category-isotope-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Category Isotope Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'You can enable category isotope filter for your course listing. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
							),	
							
							# Show Author Details
							array(
								'id'         => 'show-author-details',
								'type'       => 'select',
								'title'      => esc_html__('Show Author Details', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you like to show author details along with course. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
							),	
							
							# Display Type
							array(
								'id'         => 'default-display-type',
								'type'       => 'select',
								'title'      => esc_html__('Display Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'grid' => esc_html__('Grid', 'dtlms'),
									'list' => esc_html__('List', 'dtlms'),							
								),
								'help'  	 => esc_html__( 'Choose display type for your courses listing. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
								'value' => 'grid'
							),	

					        # Post Per Page
					        array (
					          'id'    => 'post-per-page',
					          'type'  => 'text',
					          'title' => esc_html__( 'Post Per Page', 'dtlms' ),
					          'help'  => esc_html__( 'Number of posts to show. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),																									

							# Columns
							array(
								'id'         => 'columns',
								'type'       => 'select',
								'title'      => esc_html__('Columns', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									1 => esc_html__('I Column', 'dtlms'),
									2 => esc_html__('II Columns', 'dtlms'),
									3 => esc_html__('III Columns', 'dtlms'),							
								),
								'help'  	 => esc_html__( 'Number of columns you like to display your courses. II Columns & III Columns option not applicable for "List View". Also III Columns option is applicable for "Grid View" only when all filters are disabled. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
								'value' => 2
							),	

							# Enable Fullwidth
							array(
								'id'         => 'enable-fullwidth',
								'type'       => 'select',
								'title'      => esc_html__('Enable Fullwidth', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									''    	=> esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can enable fullwidth for your course listings. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
							),								

							# Type
							array(
								'id'         => 'type',
								'type'       => 'select',
								'title'      => esc_html__('Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'type1' => esc_html__('Type 1', 'dtlms'),
									'type2' => esc_html__('Type 2', 'dtlms'),
									'type3' => esc_html__('Type 3', 'dtlms'),
									'type4' => esc_html__('Type 4', 'dtlms'),
									'type5' => esc_html__('Type 5', 'dtlms'),
									'type6' => esc_html__('Type 6', 'dtlms'),
									'type7' => esc_html__('Type 7', 'dtlms'),
									'type8' => esc_html__('Type 8', 'dtlms'),
									'type9' => esc_html__('Type 9', 'dtlms'),
									'type10' => esc_html__('Type 10', 'dtlms'),
									'type11' => esc_html__('Type 11', 'dtlms'),
									'type12' => esc_html__('Type 12', 'dtlms'),
									'type13' => esc_html__('Type 13', 'dtlms'),
									'type14' => esc_html__('Type 14', 'dtlms'),
									'type15' => esc_html__('Type 15', 'dtlms'),								
								),
								'help' => esc_html__( 'Choose any of the available design types.', 'dtlms' ),
								'value' => 'type1'
							),	

							# Show Description
							array(
								'id'         => 'show-description',
								'type'       => 'select',
								'title'      => esc_html__('Show Description', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									''    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you like to show description along with the post.', 'dtlms' ),
							),	

							# Enable Carousel
							array(
								'id'         => 'enable-carousel',
								'type'       => 'select',
								'title'      => esc_html__('Enable Carousel', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									''    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can enable carousel for course listings. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
							),	

							# Effect
							array(
								'id'         => 'carousel-effect',
								'type'       => 'select',
								'title'      => esc_html__('Effect', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('Default','dtlms'),
									'coverflow' => esc_html__('Cover Flow','dtlms'),
									'cube' => esc_html__('Cube','dtlms'),
									'fade' => esc_html__('Fade','dtlms'),
									'flip' => esc_html__('Flip','dtlms'),
								'help'  => esc_html__( 'Choose effect for your carousel. Slides Per View has to be 1 for Cube and Fade effect.', 'dtlms' ),
								)
							),

							# Auto Play
					        array (
					          'id'    => 'carousel-autoplay',
					          'type'  => 'text',
					          'title' => esc_html__( 'Auto Play', 'dtlms' ),
					          'help'  => esc_html__( 'Delay between transitions ( in ms ). Leave empty if you don\'t want to auto play.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

					        # Slides Per View
							array(
								'id'         => 'carousel-slidesperview',
								'type'       => 'select',
								'title'      => esc_html__('Slides Per View', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									1 => 1, 
									2 => 2, 
									3 => 3, 
									4 => 4,
								),
								'help'  => esc_html__( 'Number slides of to show in view port.', 'dtlms' ),
							),

							# Enable loop mode
							array(
								'id'         => 'carousel-loopmode',
								'type'       => 'select',
								'title'      => esc_html__('Enable Loop Mode','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can enable continous loop mode for your carousel.', 'dtlms' ),
							),	

							# Enable mousewheel control
							array(
								'id'         => 'carousel-mousewheelcontrol',
								'type'       => 'select',
								'title'      => esc_html__('Enable Mousewheel Control','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can enable mouse wheel control for your carousel.', 'dtlms' ),
							),

							# Enable Bullet Pagination
							array(
								'id'         => 'carousel-bulletpagination',
								'type'       => 'select',
								'title'      => esc_html__('Enable Bullet Pagination','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'To enable bullet pagination.', 'dtlms' ),
							),	
							
							# Enable Arrow Pagination
							array(
								'id'         => 'carousel-arrowpagination',
								'type'       => 'select',
								'title'      => esc_html__('Enable Arrow Pagination','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'To enable arrow pagination.', 'dtlms' ),
							),													

					        # Space Between Sliders
					        array (
					          'id'    => 'carousel-spacebetween',
					          'type'  => 'text',
					          'title' => esc_html__( 'Space Between Sliders', 'dtlms' ),
					          'help'  => esc_html__( 'Space between sliders can be given here.', 'dtlms' ),	
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),
					        
					        # Class
					        array (
					          'id'    => 'class',
					          'type'  => 'text',
					          'title' => esc_html__( 'Class', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),							
							
						)
					),					

					# Classes Listing
					array(
						'name'   => 'dtlms_classes_listing',
						'title'  => sprintf( esc_html__( '%1$s Listing', 'dtlms' ), $class_plural_label ),
						'fields' => array (

							# Disable All Filter Options
							array(
								'id'         => 'disable-all-filters',
								'type'       => 'select',
								'title'      => esc_html__('Disable All Filter Options', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can disable all filter options and only course content will be displayed.', 'dtlms' ),
							),	

							# Enable Search Filter
							array(
								'id'         => 'enable-search-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Search Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable search filter option.', 'dtlms' ),
							),	

							# Enable Display Filter
							array(
								'id'         => 'enable-display-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Display Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable display filter option.', 'dtlms' ),
							),	

							# Enable Class Type Filter
							array(
								'id'         => 'enable-classtype-filter',
								'type'       => 'select',
								'title'      => sprintf( esc_html__( 'Enable %1$s Type Filter', 'dtlms' ), $class_singular_label ),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable class type filter option.', 'dtlms' ),
							),	

							# Enable Order By Filter
							array(
								'id'         => 'enable-orderby-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Order By Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable orderby filter option.', 'dtlms' ),
							),	

							# Enable Instructor Filter
							array(
								'id'         => 'enable-instructor-filter',
								'type'       => 'select',
								'title'      => sprintf(esc_html__('Enable %s Filter', 'dtlms'), $instructor_singular_label),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable instructor filter option.', 'dtlms' ),
							),	

							# Enable Cost Filter
							array(
								'id'         => 'enable-cost-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Cost Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable cost filter option.', 'dtlms' ),
							),

							# Enable Date Filter
							array(
								'id'         => 'enable-date-filter',
								'type'       => 'select',
								'title'      => esc_html__('Enable Date Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'disable-all-filters', '==', 'false' ),
								'help'  	 => esc_html__( 'If you wish you can enable date filter option.', 'dtlms' ),
							),	

							# Listing Output Page
							array(
								'id'         => 'listing-output-page',
								'type'       => 'select',
								'title'      => esc_html__('Listing Output Page', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => $dtlms_pages_list,
								'help'  	 => esc_html__( 'If you choose a page here class search result will be outputed in that page. For that you have to add this class listing shortcode again in that page.', 'dtlms' ),
							),


							# Default Filter
							array(
								'id'         => 'default-filter',
								'type'       => 'select',
								'title'      => esc_html__('Default Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'upcoming-classes' => sprintf( esc_html__( 'Upcoming %1$s', 'dtlms' ), $class_plural_label ),
									'recent-classes' => sprintf( esc_html__( 'Recent %1$s', 'dtlms' ), $class_plural_label ),
									'highest-rated-classes' => sprintf( esc_html__( 'Highest Rated %1$s', 'dtlms' ), $class_plural_label ),
									'most-membered-classes' => sprintf( esc_html__( 'Most Membered %1$s', 'dtlms' ), $class_plural_label ),
									'paid-classes' => sprintf( esc_html__( 'Paid %1$s', 'dtlms' ), $class_plural_label ),
									'free-classes' => sprintf( esc_html__( 'Free %1$s', 'dtlms' ), $class_plural_label ),
								),
								'dependency' => array ( 'disable-all-filters', '==', 'true' ),
								'help'  	 => sprintf( esc_html__( 'Choose default filter you like to apply in %1$s listing. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ), $class_plural_label ),
							),

					        # Instructor Ids
					        array (
					          'id'    => 'instructor-ids',
					          'type'  => 'text',
					          'title' => sprintf(esc_html__('%s Ids', 'dtlms'), $instructor_singular_label),
					          'help'  => sprintf(esc_html__('Enter %s ids separated by comma to display from. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms'), $instructor_singular_label),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					          'dependency' => array ( 'disable-all-filters', '==', 'true' ),
					        ),					        

							# Apply Isotope
							array(
								'id'         => 'apply-isotope',
								'type'       => 'select',
								'title'      => esc_html__('Apply Isotope', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you like to apply isotope for your classes listing, choose "True". This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
							),
							
							# Display Type
							array(
								'id'         => 'default-display-type',
								'type'       => 'select',
								'title'      => esc_html__('Display Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'grid' => esc_html__('Grid', 'dtlms'),
									'list' => esc_html__('List', 'dtlms'),							
								),
								'help'  	 => esc_html__( 'Choose display type for your classes listing. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
								'value' => 'grid'
							),	

					        # Post Per Page
					        array (
					          'id'    => 'post-per-page',
					          'type'  => 'text',
					          'title' => esc_html__( 'Post Per Page', 'dtlms' ),
					          'help'  => esc_html__( 'Number of posts to show. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),																									

							# Columns
							array(
								'id'         => 'columns',
								'type'       => 'select',
								'title'      => esc_html__('Columns', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									1 => esc_html__('I Column', 'dtlms'),
									2 => esc_html__('II Columns', 'dtlms'),
									3 => esc_html__('III Columns', 'dtlms'),							
								),
								'help'  	 => esc_html__( 'Number of columns you like to display your classes. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
								'value' => 2
							),	

							# Enable Fullwidth
							array(
								'id'         => 'enable-fullwidth',
								'type'       => 'select',
								'title'      => esc_html__('Enable Fullwidth', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									''    	=> esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can enable fullwidth for your class listings. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
							),

							# Enable Carousel
							array(
								'id'         => 'enable-carousel',
								'type'       => 'select',
								'title'      => esc_html__('Enable Carousel', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									''    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can enable carousel for class listings.', 'dtlms' ),
							),	

							# Effect
							array(
								'id'         => 'carousel-effect',
								'type'       => 'select',
								'title'      => esc_html__('Effect', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('Default','dtlms'),
									'coverflow' => esc_html__('Cover Flow','dtlms'),
									'cube' => esc_html__('Cube','dtlms'),
									'fade' => esc_html__('Fade','dtlms'),
									'flip' => esc_html__('Flip','dtlms'),
								'help'  => esc_html__( 'Choose effect for your carousel. Slides Per View has to be 1 for Cube and Fade effect.', 'dtlms' ),
								)
							),

							# Auto Play
					        array (
					          'id'    => 'carousel-autoplay',
					          'type'  => 'text',
					          'title' => esc_html__( 'Auto Play', 'dtlms' ),
					          'help'  => esc_html__( 'Delay between transitions ( in ms ). Leave empty if you don\'t want to auto play.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

					        # Slides Per View
							array(
								'id'         => 'carousel-slidesperview',
								'type'       => 'select',
								'title'      => esc_html__('Slides Per View', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									1 => 1, 
									2 => 2, 
									3 => 3, 
									4 => 4,
								),
								'help'  => esc_html__( 'Number slides of to show in view port.', 'dtlms' ),
							),

							# Enable loop mode
							array(
								'id'         => 'carousel-loopmode',
								'type'       => 'select',
								'title'      => esc_html__('Enable Loop Mode','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can enable continous loop mode for your carousel.', 'dtlms' ),
							),	

							# Enable mousewheel control
							array(
								'id'         => 'carousel-mousewheelcontrol',
								'type'       => 'select',
								'title'      => esc_html__('Enable Mousewheel Control','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can enable mouse wheel control for your carousel.', 'dtlms' ),
							),

							# Enable Bullet Pagination
							array(
								'id'         => 'carousel-bulletpagination',
								'type'       => 'select',
								'title'      => esc_html__('Enable Bullet Pagination','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'To enable bullet pagination.', 'dtlms' ),
							),	
							
							# Enable Arrow Pagination
							array(
								'id'         => 'carousel-arrowpagination',
								'type'       => 'select',
								'title'      => esc_html__('Enable Arrow Pagination','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'To enable arrow pagination.', 'dtlms' ),
							),													

					        # Space Between Sliders
					        array (
					          'id'    => 'carousel-spacebetween',
					          'type'  => 'text',
					          'title' => esc_html__( 'Space Between Sliders', 'dtlms' ),
					          'help'  => esc_html__( 'Space between sliders can be given here.', 'dtlms' ),	
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),
					        
					        # Class
					        array (
					          'id'    => 'class',
					          'type'  => 'text',
					          'title' => esc_html__( 'Class', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),							
							
						)
					),	

					# Packages Listing
					array(
						'name'   => 'dtlms_packages_listing',
						'title'  => esc_html__( 'Packages Listing', 'dtlms' ),
						'fields' => array (

							# Display Type
							array(
								'id'         => 'display-type',
								'type'       => 'select',
								'title'      => esc_html__('Display Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'grid' => esc_html__('Grid', 'dtlms'),
									'list' => esc_html__('List', 'dtlms'),							
								),
								'help'  	 => esc_html__( 'Choose display type for your packages listing.', 'dtlms' ),
								'value' => 'grid'
							),	

					        # Post Per Page
					        array (
					          'id'    => 'post-per-page',
					          'type'  => 'text',
					          'title' => esc_html__( 'Post Per Page', 'dtlms' ),
					          'help'  => esc_html__( 'Number of posts to show.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),	

							# Columns
							array(
								'id'         => 'columns',
								'type'       => 'select',
								'title'      => esc_html__('Columns', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									1 => esc_html__('I Column', 'dtlms'),
									2 => esc_html__('II Columns', 'dtlms'),
									3 => esc_html__('III Columns', 'dtlms'),							
								),
								'help'  	 => esc_html__( 'Number of columns you like to display your packages. II Columns & III Columns option not applicable for "List View"', 'dtlms' ),
								'value' => 2
							),	


							# Apply Isotope
							array(
								'id'         => 'apply-isotope',
								'type'       => 'select',
								'title'      => esc_html__('Apply Isotope', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you like to apply isotope for your packages listing, choose "True".', 'dtlms' ),
							),
							
							# Enable Carousel
							array(
								'id'         => 'enable-carousel',
								'type'       => 'select',
								'title'      => esc_html__('Enable Carousel', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									''    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can enable carousel for class listings.', 'dtlms' ),
							),	

							# Effect
							array(
								'id'         => 'carousel-effect',
								'type'       => 'select',
								'title'      => esc_html__('Effect', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('Default','dtlms'),
									'coverflow' => esc_html__('Cover Flow','dtlms'),
									'cube' => esc_html__('Cube','dtlms'),
									'fade' => esc_html__('Fade','dtlms'),
									'flip' => esc_html__('Flip','dtlms'),
								'help'  => esc_html__( 'Choose effect for your carousel. Slides Per View has to be 1 for Cube and Fade effect.', 'dtlms' ),
								)
							),

							# Auto Play
					        array (
					          'id'    => 'carousel-autoplay',
					          'type'  => 'text',
					          'title' => esc_html__( 'Auto Play', 'dtlms' ),
					          'help'  => esc_html__( 'Delay between transitions ( in ms ). Leave empty if you don\'t want to auto play.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

					        # Slides Per View
							array(
								'id'         => 'carousel-slidesperview',
								'type'       => 'select',
								'title'      => esc_html__('Slides Per View', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									1 => 1, 
									2 => 2, 
									3 => 3, 
									4 => 4,
								),
								'help'  => esc_html__( 'Number slides of to show in view port.', 'dtlms' ),
								'value'  => 2
							),

							# Enable loop mode
							array(
								'id'         => 'carousel-loopmode',
								'type'       => 'select',
								'title'      => esc_html__('Enable Loop Mode','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can enable continous loop mode for your carousel.', 'dtlms' ),
							),	

							# Enable mousewheel control
							array(
								'id'         => 'carousel-mousewheelcontrol',
								'type'       => 'select',
								'title'      => esc_html__('Enable Mousewheel Control','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you wish you can enable mouse wheel control for your carousel.', 'dtlms' ),
							),

							# Enable Bullet Pagination
							array(
								'id'         => 'carousel-bulletpagination',
								'type'       => 'select',
								'title'      => esc_html__('Enable Bullet Pagination','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'To enable bullet pagination.', 'dtlms' ),
							),	
							
							# Enable Arrow Pagination
							array(
								'id'         => 'carousel-arrowpagination',
								'type'       => 'select',
								'title'      => esc_html__('Enable Arrow Pagination','dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'To enable arrow pagination.', 'dtlms' ),
							),													

					        # Space Between Sliders
					        array (
					          'id'    => 'carousel-spacebetween',
					          'type'  => 'text',
					          'title' => esc_html__( 'Space Between Sliders', 'dtlms' ),
					          'help'  => esc_html__( 'Space between sliders can be given here.', 'dtlms' ),	
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),				
							
						)
					),

					# Course Categories
					array(
						'name'   => 'dtlms_course_categories',
						'title'  => esc_html__( 'Course Categories', 'dtlms' ),
						'fields' => array (

							# Type
							array(
								'id'         => 'type',
								'type'       => 'select',
								'title'      => esc_html__('Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'type1' => esc_html__('Type 1', 'dtlms'),
									'type2' => esc_html__('Type 2', 'dtlms'),
									'type3' => esc_html__('Type 3', 'dtlms'),
									'type4' => esc_html__('Type 4', 'dtlms'),
									'type5' => esc_html__('Type 5', 'dtlms'),
									'type6' => esc_html__('Type 6', 'dtlms'),															
								),
								'help'  	 => esc_html__( 'Choose type of course category to display.', 'dtlms' ),
								'value' => ''
							),	

							# Columns
							array(
								'id'         => 'columns',
								'type'       => 'select',
								'title'      => esc_html__('Columns', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									1 => esc_html__('I Column', 'dtlms'),
									2 => esc_html__('II Columns', 'dtlms'),
									3 => esc_html__('III Columns', 'dtlms'),							
								),
								'help'  	 => esc_html__( 'Number of columns you like to display your course categories.', 'dtlms' ),
								'value' => 2
							),	

					        # Include
					        array (
					          'id'    => 'include',
					          'type'  => 'text',
					          'title' => esc_html__( 'Include', 'dtlms' ),
					          'help'  => esc_html__( 'List of category ids separated by commas.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

					        # Class
					        array (
					          'id'    => 'class',
					          'type'  => 'text',
					          'title' => esc_html__( 'Class', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),		
							
						)
					),

					# Instructors List
					array(
						'name'   => 'dtlms_instructor_list',
						'title'  => sprintf(esc_html__('%s List', 'dtlms'), $instructor_plural_label),
						'fields' => array (

							# Type
							array(
								'id'         => 'type',
								'type'       => 'select',
								'title'      => esc_html__('Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'type1' => esc_html__('Type 1', 'dtlms'),
									'type2' => esc_html__('Type 2', 'dtlms'),
									'type3' => esc_html__('Type 3', 'dtlms'),
									'type4' => esc_html__('Type 4', 'dtlms'),
									'type5' => esc_html__('Type 5', 'dtlms'),
								),
								'help'  	 => sprintf(esc_html__('Choose type for your %s list', 'dtlms'), $instructor_plural_label),
								'value' => 'type1'
							),	

							# Image Types
							array(
								'id'         => 'image-types',
								'type'       => 'select',
								'title'      => esc_html__('Image Types', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('Default', 'dtlms'),
									'with-border' => esc_html__('Default With Border', 'dtlms'),
									'rounded' => esc_html__('Rounded', 'dtlms'),
									'rounded-with-border' => esc_html__('Rounded With Border', 'dtlms'),							
								),
								'help'  	 => sprintf(esc_html__('Choose %s image type here.', 'dtlms'), $instructor_plural_label),
							),	

							# Social Icon Types
							array(
								'id'         => 'social-icon-types',
								'type'       => 'select',
								'title'      => esc_html__('Social Icon Types', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('Default', 'dtlms'),
									'vibrant' => esc_html__('Vibrant', 'dtlms'),
									'with-bg' => esc_html__('With Background', 'dtlms'),							
								),
								'help'  	 => esc_html__('Choose social icon types here.', 'dtlms'),
							),

							# Columns
							array(
								'id'         => 'columns',
								'type'       => 'select',
								'title'      => esc_html__('Columns', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									1 => esc_html__('I Column', 'dtlms'),
									2 => esc_html__('II Columns', 'dtlms'),
									3 => esc_html__('III Columns', 'dtlms'),							
								),
								'help'  	 => sprintf(esc_html__('Number of columns you like to display your %s.', 'dtlms'), $instructor_singular_label),
								'value' => 2
							),	

					        # Include
					        array (
					          'id'    => 'include',
					          'type'  => 'text',
					          'title' => esc_html__( 'Include', 'dtlms' ),
					          'help'  => sprintf(esc_html__('List of %s ids separated by comma.', 'dtlms'), $instructor_singular_label),				
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

					        # Number Of Users
					        array (
					          'id'    => 'number',
					          'type'  => 'text',
					          'title' => esc_html__( 'Number Of Users', 'dtlms' ),
					          'help'  => sprintf(esc_html__('Number of %s to display.', 'dtlms'), $instructor_singular_label),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

					        # Class
					        array (
					          'id'    => 'class',
					          'type'  => 'text',
					          'title' => esc_html__( 'Class', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),		
							
						)
					),


					# Dashboard Shortcodes

					# Dashboard - Total Items
					array(
						'name'   => 'dtlms_total_items',
						'title'  => esc_html__( 'Dashboard - Total Items', 'dtlms' ),
						'fields' => array (

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('Default', 'dtlms'), 
									'classes' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label ),
									'courses' => esc_html__('Courses', 'dtlms'), 
									'lessons' => esc_html__('Lessons', 'dtlms'), 
									'quizzes' => esc_html__('Quizzes', 'dtlms'), 
									'questions' => esc_html__('Questions', 'dtlms'), 
									'assignments' => esc_html__('Assignments', 'dtlms'),
									'packages' => esc_html__('Packages', 'dtlms'), 									
								),
								'help'  	 => sprintf( esc_html__( 'Choose item type to display its total items count. For %1$s total items added by them will be displayed by default.', 'dtlms' ), $instructor_singular_label ),
								'value' => 'type1'
							),	

					        # Item Title
					        array (
					          'id'    => 'item-title',
					          'type'  => 'text',
					          'title' => esc_html__( 'Item Title', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can change the default item title here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

							# Content Type
							array(
								'id'         => 'content-type',
								'type'       => 'select',
								'title'      => esc_html__('Content Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'all-items' => esc_html__('All Items', 'dtlms'), 
									'individual-items' => esc_html__('Individual Items', 'dtlms'),							
								),
								'help'  	 => esc_html__( 'If administrator wishes to see the items added by him / her or all items data. This option is applicable only for administrator.', 'dtlms' ),
							),	
							
						)
					),					

					# Dashboard - Total Items Chart
					array(
						'name'   => 'dtlms_total_items_chart',
						'title'  => esc_html__( 'Dashboard - Total Items Chart', 'dtlms' ),
						'fields' => array (

					        # Chart Title
					        array (
								'id'    => 'chart-title',
								'type'  => 'text',
								'title' => esc_html__( 'Chart Title', 'dtlms' ),
								'help'  => esc_html__( 'You can give title for your chart here.', 'dtlms' ),
								'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

							# Chart Type
							array(
								'id'         => 'chart-type',
								'type'       => 'select',
								'title'      => esc_html__('Chart Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
                                      'pie' => esc_html__('Pie', 'dtlms'),
                                      'bar' => esc_html__('Bar', 'dtlms'), 						
								),
								'help'  	 => esc_html__( 'Choose what type of chart to display', 'dtlms' ),
							),	
							
							# Set Unique Colors
							array(
								'id'         => 'set-unique-colors',
								'type'       => 'select',
								'title'      => esc_html__('Set Unique Colors', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									''    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you like to set unique colors for your chart choose "True", else colors from "Chart Settings" will be used.', 'dtlms' ),
							),	

							# First Color
							array(
								'id'         => 'first-color',
								'type'       => 'color_picker',
								'title'      => esc_html__('First Color', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'dependency' => array ( 'set-unique-colors', '==', 'true' ),
								'help'  	 => esc_html__( 'Select first color for your chart', 'dtlms' ),
							),	

							# Second Color
							array(
								'id'         => 'second-color',
								'type'       => 'color_picker',
								'title'      => esc_html__('Second Color', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'dependency' => array ( 'set-unique-colors', '==', 'true' ),
								'help'  	 => esc_html__( 'Select second color for your chart', 'dtlms' ),
							),	

							# Third Color
							array(
								'id'         => 'third-color',
								'type'       => 'color_picker',
								'title'      => esc_html__('Third Color', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'dependency' => array ( 'set-unique-colors', '==', 'true' ),
								'help'  	 => esc_html__( 'Select third color for your chart', 'dtlms' ),
							),								

							# Fourth Color
							array(
								'id'         => 'fourth-color',
								'type'       => 'color_picker',
								'title'      => esc_html__('Fourth Color', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'dependency' => array ( 'set-unique-colors', '==', 'true' ),
								'help'  	 => esc_html__( 'Select fourth color for your chart', 'dtlms' ),
							),	

							# Fifth Color
							array(
								'id'         => 'fifth-color',
								'type'       => 'color_picker',
								'title'      => esc_html__('Fifth Color', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'dependency' => array ( 'set-unique-colors', '==', 'true' ),
								'help'  	 => esc_html__( 'Select fifth color for your chart', 'dtlms' ),
							),	

							# Sixth Color
							array(
								'id'         => 'sixth-color',
								'type'       => 'color_picker',
								'title'      => esc_html__('Sixth Color', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'dependency' => array ( 'set-unique-colors', '==', 'true' ),
								'help'  	 => esc_html__( 'Select sixth color for your chart', 'dtlms' ),
							),

							# Seventh Color
							array(
								'id'         => 'seventh-color',
								'type'       => 'color_picker',
								'title'      => esc_html__('Seventh Color', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'dependency' => array ( 'set-unique-colors', '==', 'true' ),
								'help'  	 => esc_html__( 'Select seventh color for your chart', 'dtlms' ),
							),

							# Content Type
							array(
								'id'         => 'content-type',
								'type'       => 'select',
								'title'      => esc_html__('Content Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
	                                  'all-items' => esc_html__('All Items', 'dtlms'),
	                                  'individual-items' => esc_html__('Individual Items', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If administrator wishes to see the items added by him / her or all items data. This option is applicable only for administrator.', 'dtlms' ),
							),

					        # Class
					        array (
					          'id'    => 'class',
					          'type'  => 'text',
					          'title' => esc_html__( 'Class', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

						)
					),		

					# Dashboard - Purchases Overview Chart
					array(
						'name'   => 'dtlms_purchases_overview_chart',
						'title'  => esc_html__( 'Dashboard - Purchases Overview Chart', 'dtlms' ),
						'fields' => array (

					        # Chart Title
					        array (
								'id'    => 'chart-title',
								'type'  => 'text',
								'title' => esc_html__( 'Chart Title', 'dtlms' ),
								'help'  => esc_html__( 'You can give title for your chart here.', 'dtlms' ),
								'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

							# Include Class Purchases
							array(
								'id'         => 'include-class-purchases',
								'type'       => 'select',
								'title'      => sprintf( esc_html__( 'Include %1$s Purchases', 'dtlms' ), $class_plural_label ),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => sprintf( esc_html__( 'If you wish you can include %1$s purchases in chart.', 'dtlms' ), strtolower($class_singular_label) ),
							),

							# Include Course Purchases
							array(
								'id'         => 'include-course-purchases',
								'type'       => 'select',
								'title'      => esc_html__('Include Course Purchases', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__('If you wish you can include course purchases in chart.', 'dtlms'),
							),

							# Include Package Purchases
							array(
								'id'         => 'include-package-purchases',
								'type'       => 'select',
								'title'      => esc_html__('Include Package Purchases', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__('If you wish you can include package purchases in chart.', 'dtlms'),
							),							

							# Enable Instructor Filter
							array(
								'id'         => 'enable-instructor-filter',
								'type'       => 'select',
								'title'      => sprintf(esc_html__('Enable %s Filter', 'dtlms'), $instructor_singular_label),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => sprintf(esc_html__('If you wish you can enable %s filter option. This option is applicable only for administrator.', 'dtlms'), $instructor_singular_label),
							),

							# Include Data
							array(
								'id'         => 'include-data',
								'type'       => 'select',
								'title'      => esc_html__('Include Data', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__('If you wish you can include data along with this chart.', 'dtlms'),
							),
							
							# Set Unique Colors
							array(
								'id'         => 'set-unique-colors',
								'type'       => 'select',
								'title'      => esc_html__('Set Unique Colors', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									''    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__( 'If you like to set unique colors for your chart choose "True", else colors from "Chart Settings" will be used.', 'dtlms' ),
							),	

							# First Color
							array(
								'id'         => 'first-color',
								'type'       => 'color_picker',
								'title'      => esc_html__('First Color', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'dependency' => array ( 'set-unique-colors', '==', 'true' ),
								'help'  	 => esc_html__( 'Select first color for your chart', 'dtlms' ),
							),	

							# Second Color
							array(
								'id'         => 'second-color',
								'type'       => 'color_picker',
								'title'      => esc_html__('Second Color', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'dependency' => array ( 'set-unique-colors', '==', 'true' ),
								'help'  	 => esc_html__( 'Select second color for your chart', 'dtlms' ),
							),	

							# Third Color
							array(
								'id'         => 'third-color',
								'type'       => 'color_picker',
								'title'      => esc_html__('Third Color', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'dependency' => array ( 'set-unique-colors', '==', 'true' ),
								'help'  	 => esc_html__( 'Select third color for your chart', 'dtlms' ),
							),								

					        # Class
					        array (
					          'id'    => 'class',
					          'type'  => 'text',
					          'title' => esc_html__( 'Class', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),
					        
						)
					),

					# Dashboard - Instructor Commission Earnings
					array(
						'name'   => 'dtlms_instructor_commission_earnings',
						'title'  => sprintf(esc_html__('Dashboard - %s Commission Earnings', 'dtlms'), $instructor_singular_label),
						'fields' => array (

					        # Chart Title
					        array (
								'id'    => 'chart-title',
								'type'  => 'text',
								'title' => esc_html__( 'Chart Title', 'dtlms' ),
								'help'  => esc_html__( 'You can give title for your chart here.', 'dtlms' ),
								'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

							# Enable Instructor Filter
							array(
								'id'         => 'enable-instructor-filter',
								'type'       => 'select',
								'title'      => sprintf(esc_html__('Enable %s Filter', 'dtlms'), $instructor_singular_label),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => sprintf(esc_html__('If you wish you can enable %s filter option. This option is applicable only for administrator.', 'dtlms'), $instructor_singular_label),
							),

							# Instructor Earnings
							array(
								'id'         => 'instructor-earnings',
								'type'       => 'select',
								'title'      => esc_html__('Instructor Earnings', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
			                    	'over-period' => esc_html__('Over Period', 'dtlms'),
			                    	'over-item' => esc_html__('Over Item', 'dtlms'), 								
								),
								'help'  	 => sprintf( esc_html__( 'You can choose between content over period ( daily, monthly, yearly ) and content over item ( Course Commisions, %1$s Commissions, Other Amounts, Total Commissions ).', 'dtlms' ), $class_singular_label ),
								'help'  	 => 'over-period'
							),

							# Content Filter
							array(
								'id'         => 'content-filter',
								'type'       => 'select',
								'title'      => esc_html__('Content Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'both' => esc_html__('Both', 'dtlms'),
									'chart' => esc_html__('Chart', 'dtlms'), 
									'data' => esc_html__('Data', 'dtlms'), 								
								),
								'help'  	 => esc_html__( 'Would you like to show Chart or Data or Both ?', 'dtlms' ),
							),							

							# Chart Type
							array(
								'id'         => 'chart-type',
								'type'       => 'select',
								'title'      => esc_html__('Chart Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'bar' => esc_html__('Bar', 'dtlms'), 
									'line' => esc_html__('Line', 'dtlms'),
									'pie' => esc_html__('Pie', 'dtlms'),
								),
								'help'  	 => sprintf(esc_html__('Choose what type of chart to display. "Pie" chart will work only with "Over Item" - "%s Earnings"', 'dtlms'), $instructor_singular_label),
								'help'  	 => 'bar'
							),	

							# Timeline Filter
							array(
								'id'         => 'timeline-filter',
								'type'       => 'select',
								'title'      => esc_html__('Timeline Filter', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'all' => esc_html__('All - With Filter', 'dtlms'),
									'daily' => esc_html__('Monthly - Without Filter', 'dtlms'), 
									'monthly' => esc_html__('Yearly - Without Filter', 'dtlms'), 
									'alltime' => esc_html__('All Time - Without Filter', 'dtlms'), 							
								),
								'help'  	 => esc_html__( 'Choose timeline filter to use for content over item.', 'dtlms' ),
							),

							# Include Course Commission
							array(
								'id'         => 'include-course-commission',
								'type'       => 'select',
								'title'      => esc_html__('Include Course Commission', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__('If you wish to include course commission amount in the chart.', 'dtlms'),
							),
							
							# Include Class Commission
							array(
								'id'         => 'include-class-commission',
								'type'       => 'select',
								'title'      => sprintf( esc_html__( 'Include %1$s Commission', 'dtlms' ), $class_singular_label ),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__('If you wish to include class commission amount in the chart.', 'dtlms'),
							),
							
							# Include Other Commission
							array(
								'id'         => 'include-other-commission',
								'type'       => 'select',
								'title'      => esc_html__('Include Other Commission', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__('If you wish to include other commission amount in the chart.', 'dtlms'),
							),

							# Include Total Commission
							array(
								'id'         => 'include-total-commission',
								'type'       => 'select',
								'title'      => esc_html__('Include Total Commission', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => esc_html__('If you wish to include total commission amount in the chart.', 'dtlms'),
							),						

					        # Class
					        array (
					          'id'    => 'class',
					          'type'  => 'text',
					          'title' => esc_html__( 'Class', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),
					        
						)
					),

					# Dashboard - Instructor Courses
					array(
						'name'   => 'dtlms_instructor_courses',
						'title'  => sprintf(esc_html__('Dashboard - %s Courses', 'dtlms'), $instructor_singular_label),
						'fields' => array (

							# Enable Instructor Filter
							array(
								'id'         => 'enable-instructor-filter',
								'type'       => 'select',
								'title'      => sprintf(esc_html__('Enable %s Filter', 'dtlms'), $instructor_singular_label),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => sprintf(esc_html__('If you wish you can enable %s filter option. This option is applicable only for administrator.', 'dtlms'), $instructor_singular_label),
							),
					        
						)
					),

					# Dashboard - Instructor Added Courses
					array(
						'name'   => 'dtlms_instructor_added_courses',
						'title'  => sprintf(esc_html__('Dashboard - %s Added Courses', 'dtlms'), $instructor_singular_label),
					),

					# Dashboard - Instructor Commissions
					array(
						'name'   => 'dtlms_instructor_commissions',
						'title'  => sprintf(esc_html__( 'Dashboard - %s Commissions', 'dtlms' ), $instructor_singular_label),
						'fields' => array (

							# Enable Instructor Filter
							array(
								'id'         => 'enable-instructor-filter',
								'type'       => 'select',
								'title'      => sprintf(esc_html__('Enable %s Filter', 'dtlms'), $instructor_singular_label),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => sprintf(esc_html__('If you wish you can enable %s filter option. This option is applicable only for administrator.', 'dtlms'), $instructor_singular_label),
							),

							# Content
							array(
								'id'         => 'commission-content',
								'type'       => 'select',
								'title'      => esc_html__('Content', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'course' => esc_html__('Course', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
								),
								'help'  	 => esc_html__('Choose content you like to display.', 'dtlms'),
							),							
					        
						)
					),

					# Dashboard - Student Courses
					array(
						'name'   => 'dtlms_student_courses',
						'title'  => esc_html__( 'Dashboard - Student Courses', 'dtlms' ),
					),

					# Dashboard - Package Details
					array(
						'name'   => 'dtlms_package_details',
						'title'  => esc_html__( 'Dashboard - Package Details', 'dtlms' ),
					),

					# Dashboard - Class Details
					array(
						'name'   => 'dtlms_class_details',
						'title'  => sprintf( esc_html__( 'Dashboard - %1$s Details', 'dtlms' ), $class_singular_label ),
						'fields' => array (

							# Enable Instructor Filter
							array(
								'id'         => 'enable-instructor-filter',
								'type'       => 'select',
								'title'      => sprintf(esc_html__('Enable %s Filter', 'dtlms'), $instructor_singular_label),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'help'  	 => sprintf(esc_html__('If you wish you can enable %s filter option. This option is applicable only for administrator.', 'dtlms'), $instructor_singular_label),
							),

						)						
					),					

					# Dashboard - Student Purchased Items
					array(
						'name'   => 'dtlms_student_purchased_items',
						'title'  => esc_html__( 'Dashboard - Student Purchased Items', 'dtlms' ),
						'fields' => array (

					        # Item Title
					        array (
					          'id'    => 'item-title',
					          'type'  => 'text',
					          'title' => esc_html__( 'Item Title', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can change the default item title here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),
									'package' => esc_html__('Package', 'dtlms'),						
								),
								'help'  	 => esc_html__( 'Choose item type to display its purchased list.', 'dtlms' ),
							),

						)						
					),	

					# Dashboard - Student Assigned Items
					array(
						'name'   => 'dtlms_student_assigned_items',
						'title'  => esc_html__( 'Dashboard - Student Assigned Items', 'dtlms' ),
						'fields' => array (

					        # Item Title
					        array (
					          'id'    => 'item-title',
					          'type'  => 'text',
					          'title' => esc_html__( 'Item Title', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can change the default item title here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),						
								),
								'help'  	 => esc_html__( 'Choose item type to display its assigned list.', 'dtlms' ),
							),

						)						
					),

					# Dashboard - Student Undergoing Items
					array(
						'name'   => 'dtlms_student_undergoing_items',
						'title'  => esc_html__( 'Dashboard - Student Undergoing Items', 'dtlms' ),
						'fields' => array (

					        # Item Title
					        array (
					          'id'    => 'item-title',
					          'type'  => 'text',
					          'title' => esc_html__( 'Item Title', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can change the default item title here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),						
								),
								'help'  	 => esc_html__( 'Choose item type to display its undergoing list.', 'dtlms' ),
							),

						)						
					),

					# Dashboard - Student Under Evaluation Items
					array(
						'name'   => 'dtlms_student_underevaluation_items',
						'title'  => esc_html__( 'Dashboard - Student Under Evaluation Items', 'dtlms' ),
						'fields' => array (

					        # Item Title
					        array (
					          'id'    => 'item-title',
					          'type'  => 'text',
					          'title' => esc_html__( 'Item Title', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can change the default item title here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),						
								),
								'help'  	 => esc_html__( 'Choose item type to display its under evaluation list.', 'dtlms' ),
							),

						)						
					),

					# Dashboard - Student Completed Items
					array(
						'name'   => 'dtlms_student_completed_items',
						'title'  => esc_html__( 'Dashboard - Student Completed Items', 'dtlms' ),
						'fields' => array (

					        # Item Title
					        array (
					          'id'    => 'item-title',
					          'type'  => 'text',
					          'title' => esc_html__( 'Item Title', 'dtlms' ),
					          'help'  => esc_html__( 'If you wish you can change the default item title here.', 'dtlms' ),
					          'attributes' => array ( 'style'  => 'width:50%' ),
					        ),

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),						
								),
								'help'  	 => esc_html__( 'Choose item type to display its completed list.', 'dtlms' ),
							),

						)						
					),

					# Dashboard - Student Badges
					array(
						'name'   => 'dtlms_student_badges',
						'title'  => esc_html__( 'Dashboard - Student Badges', 'dtlms' ),
						'fields' => array (

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'all' => esc_html__('All', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),						
								),
								'help'  	 => esc_html__( 'Choose item type to display its corresponding student achieved badges.', 'dtlms' ),
								'value'       => 'course',
							),

							# Include Registration Class
							array(
								'id'         => 'include-registration-class',
								'type'       => 'select',
								'title'      => sprintf( esc_html__( 'Include Registration %1$s', 'dtlms' ), $class_singular_label ),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'item-type', '==', 'class' ),
								'help'  	 => sprintf( esc_html__( 'If you wish to include registration %1$s choose "True".', 'dtlms' ), strtolower($class_singular_label) ),
							),

						)						
					),

					# Dashboard - Student Certificates
					array(
						'name'   => 'dtlms_student_certificates',
						'title'  => esc_html__( 'Dashboard - Student Certificates', 'dtlms' ),
						'fields' => array (

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'all' => esc_html__('All', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),						
								),
								'help'  	 => esc_html__( 'Choose item type to display its corresponding student achieved certificates.', 'dtlms' ),
								'value'       => 'course',
							),

							# Include Registration Class
							array(
								'id'         => 'include-registration-class',
								'type'       => 'select',
								'title'      => sprintf( esc_html__( 'Include Registration %1$s', 'dtlms' ), $class_singular_label ),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'false'    => esc_html__('False', 'dtlms'),
									'true'    => esc_html__('True', 'dtlms'),								
								),
								'dependency' => array ( 'item-type', '==', 'class' ),
								'help'  	 => sprintf( esc_html__( 'If you wish to include registration %1$s choose "True".', 'dtlms' ), $class_singular_label ),
							),

						)						
					),

					# Dashboard - Student Purchased Items List
					array(
						'name'   => 'dtlms_student_purchased_items_list',
						'title'  => esc_html__( 'Dashboard - Student Purchased Items List', 'dtlms' ),
						'fields' => array (

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),
									'package' => esc_html__('Package', 'dtlms'),
								),
								'help'  	 => esc_html__( 'Choose item type to display its purchased list.', 'dtlms' ),
							),

						)						
					),

					# Dashboard - Student Assigned Items List
					array(
						'name'   => 'dtlms_student_assigned_items_list',
						'title'  => esc_html__( 'Dashboard - Student Assigned Items List', 'dtlms' ),
						'fields' => array (

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),
								),
								'help'  	 => esc_html__( 'Choose item type to display its assigned list.', 'dtlms' ),
							),

						)						
					),					

					# Dashboard - Student Undergoing Items List
					array(
						'name'   => 'dtlms_student_undergoing_items_list',
						'title'  => esc_html__( 'Dashboard - Student Undergoing Items List', 'dtlms' ),
						'fields' => array (

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),
								),
								'help'  	 => esc_html__( 'Choose item type to display its undergoing list.', 'dtlms' ),
							),

						)						
					),	

					# Dashboard - Student Under Evaluation Items List
					array(
						'name'   => 'dtlms_student_underevaluation_items_list',
						'title'  => esc_html__( 'Dashboard - Student Under Evaluation Items List', 'dtlms' ),
						'fields' => array (

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),
								),
								'help'  	 => esc_html__( 'Choose item type to display its under evaluation list.', 'dtlms' ),
							),

						)						
					),	

					# Dashboard - Student Completed Items List
					array(
						'name'   => 'dtlms_student_completed_items_list',
						'title'  => esc_html__( 'Dashboard - Student Completed Items List', 'dtlms' ),
						'fields' => array (

							# Item Type
							array(
								'id'         => 'item-type',
								'type'       => 'select',
								'title'      => esc_html__('Item Type', 'dtlms'),
								'attributes' => array ( 'style'  => 'width:50%' ),
								'options'    => array (
									'' => esc_html__('None', 'dtlms'),
									'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
									'course' => esc_html__('Course', 'dtlms'),
								),
								'help'  	 => esc_html__( 'Choose item type to display its completed list.', 'dtlms' ),
							),

						)						
					),	

					# Dashboard - Student Course Curriculum Details
					array(
						'name'   => 'dtlms_student_course_curriculum_details',
						'title'  => esc_html__( 'Dashboard - Student Course Curriculum Details', 'dtlms' ),
					),

					# Dashboard - Student Course Events
					array(
						'name'   => 'dtlms_student_course_events',
						'title'  => esc_html__( 'Dashboard - Student Course Events', 'dtlms' ),
					),

					# Dashboard - Student Class Curriculum Details
					array(
						'name'   => 'dtlms_student_class_curriculum_details',
						'title'  => sprintf( esc_html__( 'Dashboard - Student %1$s Curriculum Details', 'dtlms' ), $class_singular_label ),
					),					

				)
			);
			
			return $shortcodes;

		}

	}
}