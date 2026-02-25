<?php
global $post;

$course_forum_id = get_post_meta( $post->ID, 'dtlms-course-forum-id', true );

$args = array (
	            'post_type'=> 'forum',
	            'numberposts'=> -1,
	            'suppress_filters'  => FALSE
            );
$forum_posts = get_posts($args);

?>

<p><?php esc_html_e( 'Choose bbpress forum for this course.', 'dtlms' ); ?></p>
<select name="dtlms-course-forum" id="dtlms-course-forum" class="dtlms-chosen-select">
	<option value=""><?php esc_html_e( 'Select', 'dtlms' ); ?></option>
	<?php
	foreach($forum_posts as $forum_post) {		
		?>
        <option value="<?php echo $forum_post->ID; ?>" <?php echo (( $course_forum_id == $forum_post->ID )) ? 'selected' : ''; ?>><?php echo $forum_post->post_title; ?></option>
		<?php
	}
	?>
</select>

<?php
wp_reset_postdata();
?>