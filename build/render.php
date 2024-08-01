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

// Background Color
if (!empty($attributes['color'])) {
	$output_col = $attributes['color'];
} else {
	$output_col = "";
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
				<article class="post-card" style="background-color: <?php echo $output_col ?>">
					<div class="image-container">
						<?php the_post_thumbnail() ?>
					</div>
					<div class="text-container">
						<p><?php echo trimStringToLength(get_the_title(), 37); ?></p>
						<p class="has-small-font-size"><?php echo trimStringToLength(get_the_excerpt(), 120); ?></p>
					</div>
				</article>
			</a>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	<?php else : ?>
		<div class="archive-error">
			<p>
				There are no posts to display.
			</p>
		</div>
	<?php endif; ?>

	<div class="pagination has-small-font-size">
		<?php $total_pages = $query->max_num_pages;
		if ($total_pages > 1) {
			$current_page = max(1, get_query_var('paged'));
			echo paginate_links(array(
				'base'    => get_pagenum_link(1) . '%_%',
				'format'  => 'page/%#%',
				'current' => $current_page,
				'total'   => $total_pages,
				'prev_text' => __('Prev'),
				'next_text' => __('Next'),
			));
		} ?>
	</div>

</div>