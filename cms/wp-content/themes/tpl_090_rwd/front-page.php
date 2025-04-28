<?php get_header();
if (have_posts()) : while (have_posts()) : the_post(); ?>
  <section id="post-<?php the_ID(); ?>" class="innerS">
    <?php if(has_post_thumbnail()):?>
    	<div id="singleImg">
				<?php the_post_thumbnail('full');?>
      </div>
		<?php endif;?>
	  <header>
			<h1><?php the_title(); ?></h1> 
		</header>
 
    <div class="post">
			<?php the_content(); ?>
    </div>
    <?php wp_link_pages( array(
				'before'      => '<p id="pageLinks">ページ :',
				'after'       => '</p>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
		));?>
    <?php edit_post_link(); ?>
  </section>
	<?php endwhile;?>
  <?php endif; ?>
<?php get_footer(); ?>