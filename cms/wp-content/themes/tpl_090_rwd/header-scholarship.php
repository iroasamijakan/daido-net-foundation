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
<?php
$uploads_baseurl = wp_upload_dir()['baseurl'];
$uploads_baseurl = str_replace('http://', 'https://', $uploads_baseurl);
?>
<header id="header" itemscope="itemscope" itemtype="http://schema.org/WPHeader">
  <div id="headerWrap">
  	<p id="logo">
    <?php if (function_exists('the_custom_logo')):?>
    	<?php /* the_custom_logo(); */?>
		<a href="<?php echo home_url('/scholarship/'); ?>" class="custom-logo-link" rel="home">
			<img width="1757" height="402" src="<?php echo $uploads_baseurl; ?>/2025/03/logo.png" class="custom-logo" alt="山田貞夫音楽財団" decoding="async" fetchpriority="high" srcset="<?php echo $uploads_baseurl; ?>/2025/03/logo.png 1757w, <?php echo $uploads_baseurl; ?>/2025/03/logo-300x69.png 300w, <?php echo $uploads_baseurl; ?>/2025/03/logo-1024x234.png 1024w, <?php echo $uploads_baseurl; ?>/2025/03/logo-768x176.png 768w, <?php echo $uploads_baseurl; ?>/2025/03/logo-1536x351.png 1536w" sizes="(max-width: 1757px) 100vw, 1757px">
		</a>
    <?php else:?>
  		<a href="<?php echo esc_url( home_url( '/scholarship/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo('name', 'display')); ?>" rel="home"><?php echo esc_attr( get_bloginfo('name', 'display'))?></a>
    <?php endif;?>
    </p>
  	<nav id="mainnav">
  		<p id="menuWrap"><a id="menu"><span id="menuBtn"></span></a></p>
			<div class="panel">
				<div>
					<ul class="menu">
						<li class="menu-item"><a href="<?php echo home_url('/scholarship/'); ?>">トップページ</a></li>
						<li class="menu-item"><a href="<?php echo home_url('/scholarship/list'); ?>">奨学金応募者一覧</a></li>
						<li class="menu-item"><a href="<?php echo home_url('/scholarship/evaluations'); ?>">奨学金評価一覧</a></li>
					</ul>
				</div>
			</div>
		</nav>
	</div>
</header>