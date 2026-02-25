<?php
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">
	<?php 
	if ( have_comments() ) : 
		?>
		<h3><?php comments_number(esc_html__('No Comments', 'dtlms'), esc_html__('Comment ( 1 )', 'dtlms'), esc_html__('Comments ( % )', 'dtlms') );?></h3>
		<?php the_comments_navigation(); ?>
		<ul class="commentlist">
			<?php wp_list_comments(array ( 'avatar_size' => 50 )); ?>
		</ul>
		<?php the_comments_navigation(); ?>
    <?php endif; ?>

    <?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
    	<p class="nocomments"><?php esc_html_e( 'Comments are closed.', 'dtlms'); ?></p>
    <?php endif;?>

    <?php
	$author = '<div class="dtlms-column dtlms-one-half first"><p>
				<label for="title">'.esc_html__( 'Name', 'dtlms' ).'</label>
				<input id="author" name="author" type="text" required />
			</p></div>';
	$email = '<div class="dtlms-column dtlms-one-half"><p>
				<label for="title">'.esc_html__( 'Email', 'dtlms' ).'</label>
				<input id="email" name="email" type="text" required />
			</p></div>';
	
	$comments_args = array (
						'title_reply' 			=> 	esc_html__( 'Leave a Comment', 'dtlms' ),
						'fields'				=> 	array ('author' => $author, 'email' => $email),
						'comment_notes_before'	=>	'',
						'comment_notes_after'	=>	'',
						'label_submit'			=>	esc_html__('Comment', 'dtlms')
					);

	comment_form($comments_args);
	?>
</div>