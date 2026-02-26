<?php
if ( post_password_required() ) {
	return;
}

global $post;

?>

<div id="comments" class="comments-area dtportfolio-comments-area">
	<?php 
	if ( have_comments() ) : 
		?>
		<h3><?php comments_number(esc_html__('No Comments', 'dtportfolio'), esc_html__('Comment ( 1 )', 'dtportfolio'), esc_html__('Comments ( % )', 'dtportfolio') ); ?></h3>
		<?php the_comments_navigation(); ?>
		<ul class="commentlist">
			<?php wp_list_comments(array ( 'callback' => 'dtportfolio_comment_style') ); ?>
		</ul>
		<?php the_comments_navigation(); ?>
    	<?php 
    endif;
    ?>
</div>