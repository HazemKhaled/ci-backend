<?php
include APPPATH.'modules/admin/views/backend/bloganje/header.php';
?>

<?php
echo $this->session->flashdata('message');
?>

<div id="login_form">
<?php echo form_open('admin/login', 'post') ?>

<ul>
	<li>
		<?php echo form_label( lang('username') ) ?>
		<?php echo form_input('username') ?>
	</li>
	<li>
		<?php echo form_label( lang('password') ) ?>
		<?php echo form_password('password') ?>
	</li>
</ul>

<?php echo form_submit('', lang('login')) ?>

<?php echo form_close() ?>
</div>
<?php
include APPPATH.'modules/admin/views/backend/bloganje/footer.php';
?>