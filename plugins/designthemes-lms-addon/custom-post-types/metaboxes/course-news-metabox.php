<?php
global $post;

$course_news_id = get_post_meta( $post->ID, 'course-news', true );
$course_news_id = (is_array($course_news_id) && !empty($course_news_id)) ? $course_news_id : array ();

$args = array (
	            'post_type'=> 'post',
	            'numberposts'=> -1,
	            'suppress_filters'  => FALSE
            );
    
$blog_posts = get_posts($args);

?>

<p><?php esc_html_e( 'Choose news items for this course.', 'dtlms' ); ?></p>
<select name="course-news[]" id="course-news" class="dtlms-chosen-select" multiple="multiple">
	<option value=""><?php esc_html_e( 'Select', 'dtlms' ); ?></option>
	<?php
	foreach($blog_posts as $blog_post) {
		$sel_str = '';
		if(!empty($course_news_id) && in_array($blog_post->ID, $course_news_id)) {
			$sel_str = 'selected="selected"'; 
		}		
		?>
        <option value="<?php echo $blog_post->ID; ?>" <?php echo $sel_str; ?>><?php echo $blog_post->post_title; ?></option>
		<?php
	}
	?>
</select>

<?php
wp_reset_postdata();
?>