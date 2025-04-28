<?php get_header();
$header_image = get_header_image();
  if ($header_image):?>
  <div id="mainImg"><img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="<?php bloginfo( 'description' ); ?>"></div>
  <?php endif;?>

  <?php if ( have_posts()) : the_post(); ?>
    <section id="toppage">
      <header class="header">
        <h1 class="title"><span><?php the_title(); ?></span></h1>
      </header>
      <div class="post innerS">
        <?php the_content();?>
      </div>
    </section>
  <?php endif; ?>

  <?php query_posts('post_type=post'); ?>
  <?php get_template_part('module_loop'); ?>

  <?php wp_reset_query(); ?>
<?php get_footer(); ?>