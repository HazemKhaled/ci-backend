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

	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('base_url') ?>assets/bloganje/css/ie-sucks.css" />
	<![endif]-->
	<style type="text/css">
	body{
		background-position: center -50px;
	}
	</style>
</head>
<body>

	<div id="container" style="width:auto">
		<div id="header" style="width:auto">
			<h2>رفع ملف بحقل <?php echo $this->backend->source[$action]->fields->$field->title ?></h2>
		</div>
	</div>

	<div id="wrapper">
		<div id="content" style="width: 90%; float: none;">
			<div id="box">
			<?php if ( !empty($upload) ) : ?>
			<?php echo lang('upload_done') ?>
			<?php $file_link = /*$this->config->item('base_url').*/"uploads/".$upload['file_name'] ?>
			<script type="text/javascript">
				opener.$('#<?php echo $field ?>').val('<?php echo $file_link ?>');
				setTimeout("window.close()", 2000);
			</script>

			<?php else: ?>

			<?php if ( !empty($upload_error) ) : ?>
			<?php echo $upload_error ?>
			<?php endif; ?>

			<?php echo form_open_multipart('admin/upload/' . $action . '/' . $field ) ?>
				<?php echo form_upload('userfile', '', 'id="' . $field . '"') ?><br />
				<?php echo form_submit('', lang('upload')) ?>
				<br /><small><?php echo lang('allowed_types') . $config['allowed_types'] ?></small>
				<br /><small><?php echo lang('max_size') . ($config['max_size'] == 0 ? lang('upload_nolimit') : $config['max_size'] . ' KB' ) ?></small>
			<?php echo form_close() ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
</body>
</html>