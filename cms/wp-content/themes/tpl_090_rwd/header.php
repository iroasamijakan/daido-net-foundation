<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
<meta name="description" content="<?php echo trim(wp_title('', false)); if(wp_title('', false)) { echo ' - '; } bloginfo('description'); ?>">
<title><?php global $page, $paged;
  wp_title( '|', true, 'right' );
	bloginfo( 'name' );
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'tpl_090_rwd' ), max( $paged, $page ) );
	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link href="https://use.fontawesome.com/releases/v6.2.0/css/all.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
<?php $user = wp_get_current_user();
if (current_user_can('subscriber')) {
	add_filter( 'show_admin_bar', '__return_false' );
} ?>
</head>
<body>
<header id="header" itemscope="itemscope" itemtype="http://schema.org/WPHeader">
  <div id="headerWrap">
  	<p id="logo">
    <?php if (function_exists('the_custom_logo')):?>
    	<?php the_custom_logo();?>
    <?php else:?>
  		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo('name', 'display')); ?>" rel="home"><?php echo esc_attr( get_bloginfo('name', 'display'))?></a>
    <?php endif;?>
    </p>
  	<nav id="mainnav">
  		<p id="menuWrap"><a id="menu"><span id="menuBtn"></span></a></p>
			<div class="panel">
    		<?php wp_nav_menu(array('theme_location' => 'primary'));?>
			</div>
		</nav>
	</div>
</header>