<?php
include APPPATH.'modules/admin/views/backend/bloganje/header.php';
?>

<h3>
	<?php echo lang('add_record') . ' ' . $me->title ?>

	<?php if ( !empty($me->help) ) : ?>
		<a href="#" title="<?php echo $me->help ?>" class="help"></a>
	<?php endif; ?>

	<?php echo anchor('admin/action/' . $action, lang('back'), 'class="back"') ?>
</h3>


<form action="" method="post" class="form">
	<fieldset id="personal">
		<legend><?php echo lang('add_record') ?></legend>
		<?php foreach ( $me->fields->children() as $k => $f) : ?>
		<?php if ( $f->option->attributes()->add == 1 ) : ?>
		<label for="<?php echo $k ?>"><?php echo $f->title ?>:</label>  
		<?php
			$input = new backend_input( $k, $action );
			echo $input->result;
		?>

		<?php if ( !empty($f->help) ) : ?>
			<a href="#" title="<?php echo $f->help ?>" class="help"></a>
		<?php endif; ?>
		<br />
		<?php endif; ?>
		<?php endforeach; ?>
	</fieldset>
	<div align="center">
		<input type="submit" value="<?php echo lang('add') ?>" class="but" />
	</div>
</form>


<?php
include APPPATH.'modules/admin/views/backend/bloganje/footer.php';
?>