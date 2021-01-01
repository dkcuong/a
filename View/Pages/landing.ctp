<h1>
	This is the landing page.
</h1>

<?php 
	if( isset($services) && !empty($services) ){
?>
		<ul>
<?php
		foreach ($services as $key => $service) {
?>
			<li style="display: inline-block; width: 200px; height: 200px; background-color: #ffa2a2; margin-right: 20px; line-height: 200px; text-align: center; font-size: 2em;">
				<?php 
					$link = "#";

					switch ( $key ) {
						case 'tour':
							// $link = array('plugin' => 'tour', 'controller' => 'tours', 'action' => 'index');
							$link = '/tours/list';
							break;
						case 'aboard':
							// $link = array('plugin' => 'aboard', 'controller' => 'aboards', 'action' => 'index');
							$link = '/aboards/list';
							break;
						case 'competition':
							$link = array('plugin' => 'competition', 'controller' => 'competitions', 'action' => 'index');
							// $link = '/competitions/list';
							break;
					}

					print $this->Html->link($service, $link);
				?>
			</li>
<?php 
		}
?>
		</ul>
<?php
	}
?>