<?php
global $post;
$post_id = $post->ID;
?>

<!-- Course Video -->
<div style="float: none; display: inline-block; margin: 6px 0 0;">
	<div class="dtlms-column dtlms-one-column first">
		<?php $course_video = get_post_meta ( $post_id, 'course-video', true ); ?>
        <input id="course-video" name="course-video" class="large" type="text" value="<?php echo $course_video;?>" style="width:100%;" />
		<p class="dtlms-note"> <?php esc_html_e('If you wish! You can add featured video here.', 'dtlms');?> </p>
        <div class="dtlms-clear"></div>
	</div>
</div>
<!-- Course Video End -->