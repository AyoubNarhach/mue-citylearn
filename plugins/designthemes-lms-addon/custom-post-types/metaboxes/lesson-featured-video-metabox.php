<?php
global $post;
$post_id = $post->ID;
?>

<!-- Lesson Video -->
<div class="dtlms-custom-box">
	<div class="dtlms-column dtlms-one-column first">
		<?php $lesson_video = get_post_meta ( $post_id, 'lesson-video', true ); ?>
        <input id="lesson-video" name="lesson-video" class="large" type="text" value="<?php echo $lesson_video;?>" style="width:100%;" />
		<p class="dtlms-note"> <?php esc_html_e('If you wish! You can add featured video here.', 'dtlms');?> </p>
        <div class="dtlms-clear"></div>
	</div>
</div>
<!-- Lesson Video End -->