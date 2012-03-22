<?php
include APPPATH.'modules/admin/views/backend/bloganje/header.php';
?>

<h3>
	<?php echo lang('view_record') . ' ' . lang('in') . ' ' . $me->title ?>

	<?php if ( !empty($me->help) ) : ?>
		<a href="#" title="<?php echo $me->help ?>" class="help"></a>
	<?php endif; ?>

	<?php if ( $me->option->attributes()->edit == 1 ) : ?>
		<?php echo anchor('admin/edit/' . $action . '/' . $data[(string) $me->pk], lang('edit'), 'class="edit"'); ?>
	<?php endif; ?>

	<?php echo anchor('admin/action/' . $action, lang('back'), 'class="back"') ?>
</h3>


<div class="form">
	<fieldset id="personal">
		<legend><?php echo $data[(string) $me->main] ?></legend>
		<?php foreach ( $me->fields->children() as $k => $f) : ?>
		<?php if ( $f->option->attributes()->view == 1 ) : ?>
		<label for="<?php echo $k ?>"><?php echo $f->title ?> :</label>  
		<?php
			$view = new backend_view( $k, $action, $data );
			echo $view->result;
		?>
		<br />
		<?php endif; ?>
		<?php endforeach; ?>
	</fieldset>
</div>


<?php
include APPPATH.'modules/admin/views/backend/bloganje/footer.php';
?>