<!-- language panel -->
<?php if ($models): ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="well">
				<?php foreach ($models as  $model): 
				?>
					<button class="btn 
					<?php 
						if ($model['count']) {
							echo $model['vacancy'] ? 'btn-warning':'btn-success';
						}else{
							echo 'btn-default';
						}
					?>
					" type="button" style="margin: 5px;"
					<?= $model['vacancy']?'':'disabled="disabled"'; ?>
					data-model="<?= $model['model']; ?>"
					data-plugin="<?= $model['plugin']; ?>"
					data-toggle="modal"
				 	data-target="#language-modal"	
					>
				  		<?= $model['model']; ?> 
				  		<span class="badge"> 
				  			<?= $model['count'] - $model['vacancy']; ?> 
				  			/ 
				  			<?= $model['count']?$model['count']:0; ?> 
				  		</span>
					</button>
				<?php endforeach ?>
			</div>
		</div>
	</div>
<?php endif ?>


<!-- Large modal -->
<div class="modal fade" id="language-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
		
    </div>
  </div>
</div>


<script type="text/javascript">
	$('#language-modal').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var model = button.data('model') // Extract info from data-* attributes
	  var plugin = button.data('plugin') // Extract info from data-* attributes
	  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).

	  	var url = cakephp.base + 'dictionary/languages/vacancy_languages';

        COMMON.call_ajax({
			type: "POST",
			url: url,
			dataType: 'html',
			cache: false,
			data: {
				model: model,
				plugin: plugin,
			},
			success: function( result ){
				$('#language-modal .modal-content').html(result);
			},
			error: function( result ){
				// console.log('error :');
				// console.log( result );
			}
		});

	})

</script>