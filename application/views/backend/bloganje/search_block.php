<script type="text/javascript">
<!--
$(document).ready( function () {
	$('#search_form').submit(function () {

		if ( $('input[name=s]').val().length < 1 )
		{
			alert('<?php echo lang('search_fill_input') ?>');
			return false;
		}

		var url = '/s/' + $('input[name=s]').val() + '/op/' + $('select[name=op]').val() + '/in/' + $('select[name=in]').val() ;
		location = '<?php echo site_url('/admin/action/' . $action) ?>' + url;
		return false;

	});
} )
//-->
</script>

<div id="search">

<h3>
	<?php echo lang('search') ?>
	<a class="help" title="لا تستخدم المعاملات الرياضية مع الحقول الغير رقمية، لا تستخدم أكبر من او أشغر من مع حقل اللأسم" href="#"></a>
</h3>

<form action="" method="post" class="form" id="search_form">
<table>
	<thead>
	<tr>
		<th width="35%"><?php echo lang('search_key') ?></th>
		<th width="15%"><?php echo lang('search_op') ?></th>
		<th width="35%"><?php echo lang('search_in') ?></th>
		<th width="15%"></th>
	</tr>
	</thead>
	<tr align="center">
		<td><input type="text" name="s" /></td>
		<td>
			<select name="op">
			<?php foreach ( $this->backend->op as $op => $v ) : ?>
				<option value="<?php echo $op ?>"><?php echo lang($op) ?></option>
			<?php endforeach; ?>
			</select>
		</td>
		<td>
			<select name="in">
				<?php foreach ( $me->fields->children() as $k => $f) : ?>
				<?php if ( $f->option->attributes()->search == 1 ) : ?>
						<option value="<?php echo $k ?>"><?php echo $f->title ?></option>
				<?php endif; ?>
				<?php endforeach; ?>
			</select>
		</td>
		<td><input type="submit" value="<?php echo lang('search') ?>" /></td>
	</tr>
</table>

</form>

</div>