<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo ( empty( $siteTitle ) ? $this->config->item('site_title') : $siteTitle ) . ' (Powerd by CI Back-end)' ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('base_url') ?>assets/bloganje/css/theme.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('base_url') ?>assets/bloganje/css/style.css" />
	<?php if ( $this->config->item('language') == 'arabic' ) : ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('base_url') ?>assets/bloganje/css/style-rtl.css" />
	<?php endif; ?>
	<script type="text/javascript" src="<?php echo $this->config->item('base_url') ?>assets/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $this->config->item('base_url') ?>assets/js/jquery.simpletip.js"></script>
	<script>
		var StyleFile = "theme" + document.cookie.charAt(6) + ".css";
		document.writeln('<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('base_url') ?>assets/bloganje/css/' + StyleFile + '">');
		$().ready( function(){
			$('.help').each(function(i){
				$(this).simpletip({
									content: $(this).attr('title'),
									fixed: true,
									position: 'top'
								});
				$(this).attr('title', '');
			})

			$('a[href=#search]').click( function () {

				$('#search').slideToggle();

			} );
		} );
	</script>
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('base_url') ?>assets/bloganje/css/ie-sucks.css" />
	<![endif]-->
</head>
<body>

	<div id="container">
		<div id="header">
			<h2>لوحة تحكم ايجى كوك</h2>

			<div id="topmenu">
				<ul>
					<li<?php if ($currentTab == lang('mainTab')) : ?> class="current"<?php endif; ?>><?php echo anchor('admin', lang('home')); ?></li>
					<?php foreach ( $tabs as $k => $v ) : ?>
					<?php
					if ( !$this->backend->tab_visable( $k ) )
					{
						continue;
					}
					?>
					<li <?php if ($currentTab == $k) : ?> class="current"<?php endif; ?>>
						<?php echo anchor('admin/tab/' . $k, $k); ?>
					</li>
					<?php endforeach; ?>
					<li class="fixed"><?php echo anchor('admin/logout', lang('logout')); ?></li>
					<li class="fixed"><?php echo anchor('admin/action/backend-modrator-permissions', lang('permissions')); ?></li>
				</ul>
			</div>
		</div>

		<?php if ( !empty($action) && ( $me->option->attributes()->add == 1 || $active_count > 0 || $me->option->attributes()->search == 1 ) ) :?>
		<div id="top-panel">
			<div id="panel">
				<ul>
				<?php if ( $this->backend->have_access( $action, 'add', false ) && $me->option->attributes()->add == 1 ) : ?>
					<li><?php echo anchor('admin/add/' . $action, lang('add_record'), 'class="add"'); ?></li>
				<?php endif; ?>
				<?php if ( $this->backend->have_access( $action, 'active', false ) && $active_count > 0) : ?>
					<li><?php echo anchor('admin/active/' . $action, lang('active_record') . ' (' . $active_count . ')', 'class="report"');//TODO: add records need to active ?></li>
				<?php endif; ?>
				<?php if ( $me->option->attributes()->search == 1) : ?>
					<li><a href="#search" class="search"><?php echo lang('search') ?></a></li>
				<?php endif; ?>
				</ul>
			</div>
		</div>
		<?php endif; ?>

		<div id="wrapper">
                             
			<div id="content">
				<div id="box">



<?php
if ( !empty($action) && $me->option->attributes()->search == 1 )
{
	include APPPATH.'views/backend/bloganje/search_block.php';
}
