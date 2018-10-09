<!DOCTYPE html>
<!--[if (gte IE 9)|!(IE)]><!--> <html class="not-ie no-js" <?php language_attributes(); ?>>  <!--<![endif]-->
<head>

	<!-- Basic Page Needs
    ==================================================== -->
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<!-- Mobile Specific Metas
	==================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php wp_head(); ?>

</head>

<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '2038385142840895');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=2038385142840895&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->
<!-- Global site tag (gtag.js) - Google Ads: 998910174 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-998910174"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-998910174');
</script>

<!-- Event snippet for Ajout panier conversion page -->
<script>
  gtag('event', 'conversion', {
      'send_to': 'AW-998910174/kXKPCIKOn4YBEN7RqNwD',
      'transaction_id': ''
  });
</script>

<?php
global $knowhere_config;
$header_classes = $knowhere_config['header_classes'];
$header_type = $knowhere_config['header_type'];
$page_content_classes = $knowhere_config['page_content_classes'];
$style_page_header = $knowhere_config['job-single-style']; ?>

<body <?php body_class(); ?>>

<?php do_action('knowhere_body_append', get_the_ID()); ?>

<div class="kw-wide-layout-type">

	<?php do_action('knowhere_header_prepend') ?>

	<!-- - - - - - - - - - - - - - Header - - - - - - - - - - - - - - - - -->

	<header id="header" class="kw-header <?php echo esc_attr($header_classes); ?>">
		<div class="logo_mobile_2" style="display:none">
			<a  href="https://www.mylittlewe.com/" title="mylittlewe - Activités et loisirs organisés par les particuliers" rel="home">
					<img  src="//www.mylittlewe.com/wp-content/uploads/2018/02/logo2-2.png" alt="mylittlewe">				
			</a>
		</div>
		<?php do_action( 'knowhere_header_layout', $header_type ); ?>
	</header><!--/ #header -->

	<!-- - - - - - - - - - - - - - / Header - - - - - - - - - - - - - - -->

	<?php
		/**
		 * knowhere_header_after hook
		 *
		 * @hooked page_title_and_breadcrumbs
		 */

		do_action( 'knowhere_header_after', $style_page_header );
	?>

	<div class="<?php echo sprintf('%s', $page_content_classes) ?>">

		<?php if ( is_page_template() ): ?>

		<?php else: ?>

			<?php do_action( 'knowhere_page_content_prepend' ); ?>

			<div class="kw-entry-content">

				<div class="container">

					<div class="row">

						<main class="kw-site-main">

		<?php endif; ?>
