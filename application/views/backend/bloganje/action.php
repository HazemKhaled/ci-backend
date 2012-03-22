<?php
include APPPATH.'views/backend/bloganje/header.php';
?>

<h3>
	<?php echo $me->title ?>

	<?php if ( $activePage == true ) : ?>
	<?php echo anchor('admin/action/' . $action, lang('back'), 'class="back"') ?>
	<?php endif; ?>

	<?php if ( !empty($me->help) ) : ?>
		<a href="#" title="<?php echo $me->help ?>" class="help"></a>
	<?php endif; ?>
</h3>

<?php
echo $this->session->flashdata('message');
?>
<form action="" method="post">
	<table width="100%">
		<thead>
			<tr>
				<?php if ( $this->backend->have_access( $action, 'delete', false ) && $me->option->attributes()->delete == 1 ) : ?>
				<th> -- </th>
				<?php endif; ?>

				<?php if ( $this->backend->have_access( $action, 'edit', false ) && $me->option->attributes()->edit == 1 ) : ?>
				<th> -- </th>
				<?php endif; ?>

				<?php if ( $me->option->attributes()->view == 1 ) : ?>
				<th> -- </th>
				<?php endif; ?>
				<?php foreach ( $me->fields->children() as $k => $f) : ?>
				<?php if ( $f->option->attributes()->list == 1 ) : ?>
				<th><?php echo /*anchor('admin/action/sort/' . $action, $f->title)*/$f->title ?></th>
				<?php endif; ?>
				<?php endforeach; ?>
			</tr>
		</thead>
		<?php foreach ( $data as $d ) : ?>
		<?php
		$id = $d[(string) $me->pk];
		?>
		<tr>
			<?php if ( $this->backend->have_access( $action, 'delete', false ) && $me->option->attributes()->delete == 1 ) : ?>
			<td width="25" align="center"><input type="checkbox" name="<?php echo $me->pk ?>[]" value="<?php echo $id ?>" rel="<?php echo $d[(string) $me->main] ?>" /></td>
			<?php endif; ?>

			<?php if ( $this->backend->have_access( $action, 'edit', false ) && $me->option->attributes()->edit == 1 ) : ?>
			<td width="25" align="center"><?php echo anchor('admin/edit/' . $action . '/' . $id, lang('edit')); ?></td>
			<?php endif; ?>

			<?php if ( $me->option->attributes()->view == 1 ) : ?>
			<td width="25" align="center"><?php echo anchor('admin/view/' . $action . '/' . $id, lang('view')); ?></td>
			<?php endif; ?>
			<?php foreach ( $me->fields->children() as $k => $f) : ?>
			<?php if ( $f->option->attributes()->list == 1 ) : ?>
				<td>
				<?php
					$view = new backend_view( $k, $action, $d );
					echo $view->result;
				?>
				</td>
			<?php endif; ?>
			<?php endforeach; ?>
		</tr>
		<?php endforeach; ?>
	</table>
	<div>
		<?php if ( $this->backend->have_access( $action, 'active', false ) && $activePage == true ) : ?>
		<input type="submit" value="<?php echo lang('active') ?>" class="but" name="active" />
		<?php endif; ?>

		<?php if ( $this->backend->have_access( $action, 'delete', false ) && $me->option->attributes()->delete == 1 ) : ?>
		<input type="submit" value="<?php echo lang('delete') ?>" class="but" name="delete" />
		<?php endif; ?>

		<?php echo $this->backend->select_all_num_rows( $data ) ?>
	</div>
</form>
<div class="pager"><?php echo $pager ?></div>
<?php
include APPPATH.'views/backend/bloganje/footer.php';
?>