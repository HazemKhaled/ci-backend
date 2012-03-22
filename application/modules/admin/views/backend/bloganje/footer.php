				</div>
			</div>

			<div id="sidebar">
  				<ul>
					<?php foreach ( $tabs[ $currentTab ] as $k => $v ) : ?>
					<?php if ( $this->backend->block_visable( $k, $v ) ): ?>
					<li>
						<h3><?php echo $k ?></h3>
						<ul>
							<?php foreach ( $v as $k1 => $v1) : ?>
								<?php if ( $this->backend->link_visable( $k1 ) ): ?>
                                    <?php if ( !empty($this->backend->source[$k1]->link) ) : ?>
                                        <li><?php echo anchor($this->backend->source[$k1]->link, $this->backend->source[$k1]->title); ?></li>
                                    <?php else:  ?>
								        <li><?php echo anchor('admin/action/' . $k1, $v1, (!empty($action) && $action == $k1) ? 'class="current"' : '' ); ?></li>
                                    <?php endif; ?>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</li>
					<?php endif; ?>
                	<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div id="footer">
			<div id="credits"><?php echo lang('credits') ?></div>

			<div id="styleswitcher">
				<ul>
					<li><a href="javascript: document.cookie='theme='; window.location.reload();" title="Default" id="defswitch">d</a></li>
					<li><a href="javascript: document.cookie='theme=1'; window.location.reload();" title="Blue" id="blueswitch">b</a></li>
					<li><a href="javascript: document.cookie='theme=2'; window.location.reload();" title="Green" id="greenswitch">g</a></li>
					<li><a href="javascript: document.cookie='theme=3'; window.location.reload();" title="Brown" id="brownswitch">b</a></li>
					<li><a href="javascript: document.cookie='theme=4'; window.location.reload();" title="Mix" id="mixswitch">m</a></li>
				</ul>
			</div><br />

		</div>
	</div>
</body>
</html>