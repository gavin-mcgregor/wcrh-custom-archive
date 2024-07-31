<?php

/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Post Type
if (!empty($attributes['posttype'])) {
	if ($attributes['posttype'] === 'posts') {
		$post_type = 'post';
	} else {
		$post_type = $attributes['posttype'];
	}
} else {
	$post_type = 'post';
}

// Posts (per page)
if (!empty($attributes['numPosts'])) {
	$posts_num = is_numeric($attributes['numPosts']) ? $attributes['numPosts'] : 4;
}

// Arguments
$args = array(
	'post_type' => $post_type,
	'posts_per_page' => $posts_num,
	'paged' => $paged
);

// Query
$query = new WP_Query($args);
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php if ($query->have_posts()) : ?>
		<?php while ($query->have_posts()) : $query->the_post(); ?>
			<a href="<?php echo the_permalink(); ?>" target="_self">
				<article class="blogpost">
					<div class="image-container">
						<?php the_post_thumbnail() ?>
					</div>
					<div class="text-container">
						<p><?php echo get_the_title(); ?></p>
					</div>
				</article>
			</a>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>
</div>

<!-- TODO - add pagination and more style? maybe posted date or category? and mobile styles? -->