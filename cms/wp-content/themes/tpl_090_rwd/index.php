<?php get_header(); ?>
<section>
	<header class="header">
	<?php if (is_category()) : ?>
		<h1 class="title"><span><?php single_cat_title(); ?></span></h1>
	<?php elseif (is_tag()) : ?>
	<h1 class="title"><span><?php single_tag_title(); ?></span></h1>
		<?php elseif (is_day()) : ?>
		<h1 class="title"><span><?php the_time('Y/m/d'); ?></span></h1>
		<?php elseif (is_month()) : ?>
		<h1 class="title"><span><?php the_time('Y/m'); ?></span></h1>
		<?php elseif (is_year()) : ?>
		<h1 class="title"><span><?php the_time('Y'); ?></span></h1>
	<?php elseif(is_search()) : ?>
	<h1 class="title"><span>『<?php the_search_query(); ?>』の検索結果<?php if (get_query_var('paged')) echo ' | '. get_query_var('paged') .'ページ目'; ?></span></h1>
		<?php elseif (isset($_GET['paged']) && !empty($_GET['paged'])) : ?>
		<h1 class="title"><span>Blog Archives</span></h1>
		<?php endif; ?>
	</header>

	<?php get_template_part('module_loop'); ?>

	<?php
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => 10,
	);

	$additional_loop = new WP_Query($args);

	if (function_exists("kriesi_pagination")){
	kriesi_pagination($additional_loop->max_num_pages);
	}?>
</section>
<?php get_footer(); ?>