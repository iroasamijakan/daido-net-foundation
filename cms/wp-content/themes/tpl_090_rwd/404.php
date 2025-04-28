<?php get_header(); ?>
<section id="post-<?php the_ID(); ?>" class="inner">
	<header>  	
  	<h1><?php _e('Page not found'); ?></h1>
  </header>
  <div class="post">
		<p><?php _e("No posts found."); ?></p>
  </div>
</section>        
<?php get_footer(); ?>