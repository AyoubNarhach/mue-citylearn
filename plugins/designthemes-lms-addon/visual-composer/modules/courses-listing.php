<?php 
add_action( 'vc_before_init', 'dtlms_courses_listing_vc_map' );

function dtlms_courses_listing_vc_map() {

	$instructor_label = apply_filters( 'instructor_label', 'singular' );

	$dtlms_pages_list = array ();
	$dtlms_pages_list[esc_html__('Default - Ajax Output', 'dtlms')] = '';
	$pages = get_pages(); 
	foreach ( $pages as $page ) {
		$dtlms_pages_list[$page->post_title] = $page->ID;
	}

	vc_map( array(
		"name" => esc_html__( 'Courses Listing', 'dtlms' ),
		"base" => "dtlms_courses_listing",
		"icon" => "dtlms_courses_listing",
		"category" => DTLMSADDON_TITLE,
		"params" => array(

			// Disable All Filter Options
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Disable All Filter Options','dtlms'),
				'param_name' => 'disable-all-filters',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can disable all filter options and only course content will be displayed.', 'dtlms' ),
				'std' => ''				
			),

			// Enable Search Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Search Filter','dtlms'),
				'param_name' => 'enable-search-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable search filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Enable Display Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Display Filter','dtlms'),
				'param_name' => 'enable-display-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable display filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Enable Order By Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Order By Filter','dtlms'),
				'param_name' => 'enable-orderby-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable orderby filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Enable Category Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Category Filter','dtlms'),
				'param_name' => 'enable-category-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable category filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Enable Instructor Filter
			array(
				'type' => 'dropdown',
				'heading' => sprintf(esc_html__('Enable %s Filter', 'dtlms'), $instructor_label),
				'param_name' => 'enable-instructor-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable instructor filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),	

			// Enable Cost Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Cost Filter','dtlms'),
				'param_name' => 'enable-cost-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable cost filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Enable Date Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Date Filter','dtlms'),
				'param_name' => 'enable-date-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable date filter option.', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'false'),	
				'std' => ''				
			),


			// Listing Output Page
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Listing Output Page','dtlms'),
				'param_name' => 'listing-output-page',
				'value' => $dtlms_pages_list,			
				'description' => esc_html__( 'If you choose a page here course search result will be outputed in that page. For that you have to add this course listing shortcode again in that page.', 'dtlms' ),
				'std' => ''				
			),


			// Default Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Default Filter','dtlms'),
				'param_name' => 'default-filter',
				'value' => array(
					esc_html__('None', 'dtlms') => '',
					esc_html__('Upcoming Courses', 'dtlms') => 'upcoming-courses',
					esc_html__('Recent Courses', 'dtlms') => 'recent-courses',
					esc_html__('Highest Rated Courses', 'dtlms') => 'highest-rated-courses',
					esc_html__('Most Membered Courses', 'dtlms') => 'most-membered-courses',
					esc_html__('Paid Courses', 'dtlms') => 'paid-courses',
					esc_html__('Free Courses', 'dtlms') => 'free-courses',
				),				
				'description' => esc_html__( 'Choose default filter you like to apply in courses listing. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'true'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Course Item Ids
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Course Item Ids','dtlms'),
				'param_name' => 'course-item-ids',
				'value' => '',
				'description' => esc_html__( 'Enter course item ids separated by comma to display from. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'true'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Course Category Ids
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Course Category Ids','dtlms'),
				'param_name' => 'course-category-ids',
				'value' => '',
				'description' => esc_html__( 'Enter course category separated by comma to display from. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'true'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Instructor Ids
			array(
				'type' => 'textfield',
				'heading' => sprintf(esc_html__('%s Ids', 'dtlms'), $instructor_label),
				'param_name' => 'instructor-ids',
				'value' => '',
				'description' => sprintf(esc_html__('Enter %s ids separated by comma to display from. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms'), $instructor_label),
				'dependency' => array( 'element' => 'disable-all-filters', 'value' => 'true'),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),			


			// Apply Isotope
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Apply Isotope','dtlms'),
				'param_name' => 'apply-isotope',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you like to apply isotope for your courses listing, choose "True". This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page". "Apply Isotope" won\'t work along with "Carousel".', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Enable Category Isotope Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Category Isotope Filter','dtlms'),
				'param_name' => 'enable-category-isotope-filter',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'You can enable category isotope filter for your course listing. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Show Author Details
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Show Author Details','dtlms'),
				'param_name' => 'show-author-details',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you like to show author details along with course. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => ''				
			),

			// Display Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Display Type','dtlms'),
				'param_name' => 'default-display-type',
				'value' => array(
					esc_html__('Grid', 'dtlms') => 'grid',
					esc_html__('List', 'dtlms') => 'list',
				),				
				'description' => esc_html__( 'Choose display type for your courses listing. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => 'grid'				
			),	

			// Post Per Page
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Post Per Page', 'dtlms' ),
				'param_name' => 'post-per-page',
				'description' => esc_html__( 'Number of posts to show. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Columns
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Columns', 'dtlms'),
				'param_name' => 'columns',
				'value' => array( 
							esc_html__('I Column', 'dtlms') => 1 ,
							esc_html__('II Columns', 'dtlms') => 2 ,
							esc_html__('III Columns', 'dtlms') => 3,
						),
				'description' => esc_html__( 'Number of columns you like to display your courses. III Columns option will work only if "Enable Fullwidth" is set to "True". Also III Columns option is applicable for "Grid View" only when all filters are disabled. In list view, II Columns option is applicable only for "type6". This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'std' => 1,
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Enable Fullwidth
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Fullwidth','dtlms'),
				'param_name' => 'enable-fullwidth',
				'value' => array( 
							esc_html__('False','dtlms') => '', 
							esc_html__('True','dtlms') => 'true', 
						),
				'description' => esc_html__( 'If you wish you can enable fullwidth for your course listings. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page".', 'dtlms' ),
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),			

			// Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Type','dtlms'),
				'param_name' => 'type',
				'value' => array(
					esc_html__('Type 1', 'dtlms') => 'type1',
					esc_html__('Type 2', 'dtlms') => 'type2',
					esc_html__('Type 3', 'dtlms') => 'type3',
					esc_html__('Type 4', 'dtlms') => 'type4',
					esc_html__('Type 5', 'dtlms') => 'type5',
					esc_html__('Type 6', 'dtlms') => 'type6',
					esc_html__('Type 7', 'dtlms') => 'type7',
					esc_html__('Type 8', 'dtlms') => 'type8',
					esc_html__('Type 9', 'dtlms') => 'type9',
					esc_html__('Type 10', 'dtlms') => 'type10',
				),
				'description' => esc_html__( 'Choose any of the available design types.', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => 'type1'				
			),

			// Show Description
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Show Description','dtlms'),
				'param_name' => 'show-description',
				'value' => array( 
							esc_html__('False','dtlms') => '', 
							esc_html__('True','dtlms') => 'true', 
						),
				'description' => esc_html__( 'If you like to show description along with the post.', 'dtlms' ),
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Hide Labels
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Hide Labels','dtlms'),
				'param_name' => 'hide-labels',
				'value' => array( 
							esc_html__('False','dtlms') => '', 
							esc_html__('True','dtlms') => 'true', 
						),
				'description' => esc_html__( 'If you wish you can hide labels ( certificate and badge ) for your course listings.', 'dtlms' ),
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Hide Category
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Hide Category','dtlms'),
				'param_name' => 'hide-category',
				'value' => array( 
							esc_html__('False','dtlms') => '', 
							esc_html__('True','dtlms') => 'true', 
						),
				'description' => esc_html__( 'If you wish you can hide category for your course listings.', 'dtlms' ),
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Hide Add to Cart Button
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Hide Add to Cart Button','dtlms'),
				'param_name' => 'hide-addtocartbutton',
				'value' => array( 
							esc_html__('False','dtlms') => '', 
							esc_html__('True','dtlms') => 'true', 
						),
				'description' => esc_html__( 'If you wish you can hide add to cart button for your course listings.', 'dtlms' ),
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Class
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Class', 'dtlms' ),
				'param_name' => 'class',
				'description' => esc_html__( 'If you wish you can add additional class name here.', 'dtlms' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),


			// Carousel Options

			// Enable Carousel
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Carousel','dtlms'),
				'param_name' => 'enable-carousel',
				'value' => array( 
							esc_html__('False','dtlms') => '', 
							esc_html__('True','dtlms') => 'true', 
						),
				'description' => esc_html__( 'If you wish you can enable carousel for course listings. This option is not applicable if "Default - Ajax Output" is not chosen in "Listing Output Page". "Carousel" won\'t work along with "Apply Isotope".', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'apply-isotope', 'value' => 'false'),	
				'std' => ''
			),

			/*// Effect
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Effect', 'dtlms'),
				'param_name' => 'carousel-effect',
				'value' => array( 
							esc_html__('Default', 'dtlms') => '', 
							esc_html__('Fade', 'dtlms') => 'fade', 
						),
				'description' => esc_html__( 'Choose effect for your carousel. Slides Per View has to be 1 for Fade effect.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''
			),*/

			// Auto Play
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Auto Play', 'dtlms'),
				'param_name' => 'carousel-autoplay',
				'description' => esc_html__( 'Delay between transitions ( in ms, ex. 1000 ). Leave empty if you don\'t want to auto play.', 'dtlms' ),	
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
			),

			// Slides Per View
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Slides Per View','dtlms'),
				'param_name' => 'carousel-slidesperview',
				'value' => array( 
							1 => 1, 
							2 => 2, 
							3 => 3, 
						),
				'description' => esc_html__( 'Number slides of to show in view port. If display type is "List", 2 & 3 option in "Slides Per View" won\'t work.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => 2
			),	

			// Enable loop mode
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Loop Mode','dtlms'),
				'param_name' => 'carousel-loopmode',
				'value' => array(
					esc_html__('False','dtlms') => 'false',
					esc_html__('True','dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable continous loop mode for your carousel.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),	

			// Enable mousewheel control
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Mousewheel Control', 'dtlms'),
				'param_name' => 'carousel-mousewheelcontrol',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable mouse wheel control for your carousel.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),	

			// Enable Bullet Pagination
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Bullet Pagination', 'dtlms'),
				'param_name' => 'carousel-bulletpagination',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'To enable bullet pagination.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),

			// Enable Arrow Pagination
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Arrow Pagination', 'dtlms'),
				'param_name' => 'carousel-arrowpagination',
				'value' => array(
					esc_html__('False', 'dtlms') => 'false',
					esc_html__('True', 'dtlms') => 'true',
				),
				'description' => esc_html__( 'To enable arrow pagination.', 'dtlms' ),
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
				'std' => ''				
			),

			// Space Between Sliders
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Space Between Sliders','dtlms'),
				'param_name' => 'carousel-spacebetween',
				'description' => esc_html__( 'Space between sliders can be given here.', 'dtlms' ),	
				'group' => 'Carousel',
				'dependency' => array( 'element' => 'enable-carousel', 'value' => 'true'),
			),								

		)
	) );
}
?>