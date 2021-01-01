<!-- languages -->
<?php if (isset($languages) && $languages !== false): ?>
	<div class="row">
	
	<?php foreach ($languages as $language): ?>
		<div class="col-xs-4">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">
						<?php if (isset($language['language'])) echo h($language['language']);?>
					</h3>
				</div>

				<div class="box-body">
					<?php foreach ($language_input_fields as $language_input_field): ?>
						<?php 
						
                            if(isset($language[$language_input_field]) && $language[$language_input_field] != ""){
                                $ico = '';
                                $style = '';
    
                                if (strpos($language_input_field, 'name') !== false || strpos($language_input_field, 'title') === 0) {
                                    $style = 'font-size: 28px; font-weigth:bold;';
                                }
    
                                if (strpos($language_input_field, 'description') !== false) {
                                    $style = 'font-size: 16px; font-style:italic; color: #ccc;';
                                }
    
                                if (strpos($language_input_field, 'address') !== false) {
                                    $style = 'font-size: 16px; color: #333;';
                                    $ico = '<i class="fa fa-map"></i>';
                                }
    
                                if (strpos($language_input_field, 'content') !== false ||
                                    strpos($language_input_field, 'terms') !== false ||
                                    strpos($language_input_field, 'privacy') !== false ||
                                    strpos($language_input_field, 'about') !== false) {
                                    $style = 'font-size: 14px; color: #666;';
                                    $ico = '<i class="fa fa-paragraph"></i> '.ucfirst($language_input_field);
                                }
    
                                if (
                                    strpos($language_input_field, 'time') !== false ||
                                    strpos($language_input_field, 'open') !== false 
                                    ) {
                                    $style = 'font-size: 16px; color: #333;';
                                    $ico = '<i class="fa fa-clock-o"></i> ';
                                }
    
                                if (
                                    strpos($language_input_field, 'hotline') !== false ||
                                    strpos($language_input_field, 'phone') !== false
                                    ) {
                                    $style = 'font-size: 16px; color: #333;';
                                    $ico = '<i class="fa fa-phone"></i> ';
                                }
						?>
                            <div style="padding: 10px 0; <?= $style; ?>">
                                <?= $ico; ?> 
                                <?= $language[$language_input_field] ?>
                            </div>
                        <?php 
                            }
                        ?>
					<?php endforeach ?>
				</div>

				<div class="box-body">
					<?php if (isset($_data['link_zho'])) echo  $this->Html->link($_data['link_zho'],$_data['link_zho']); ?>
				</div>
			</div>
		</div>
	<?php endforeach ?>
	</div>
<?php endif ?>
<!-- /languages -->


<!-- images -->
<?php $id_cover_images = isset($id_collapse_image) ? $id_collapse_image : 'collapseExample'; ?>
<?php if (isset($images) && !empty($images)): ?>
	<h2>&nbsp;
		<i>Images:</i>
		<button type="button" class="btn btn-box-tool" data-toggle="collapse" data-target="#<?= $id_cover_images ?>" aria-expanded="true" aria-controls="<?= $id_cover_images ?>">
			<i class="glyphicon glyphicon-eye-open"></i>
		</button>
	</h2>
	<div class="col-xs-12 collapse"  id="<?= $id_cover_images ?>">
	<?php foreach ($images as $image): ?>
		<div class="col-xs-4">
		    <span class="thumbnail fancybox" 
		    	href="<?= Router::url('/',true).$image['path']; ?>" 
		    	data-fancybox-group="gallery" 
		    	data-toggled="off">
				<img src="<?= Router::url('/',true).$image['path']; ?>" />
	      		<div class="caption">
			        <h4><center> <?= $image['type'] ?> </center></h4>
			    </div>
		    </span>
		</div>
	<?php endforeach ?>
	</div>
<?php endif ?>
<!-- /images -->


<script type="text/javascript">
	$(function (){
		$('.fancybox').fancybox({
			prevEffect : 'none',
			nextEffect : 'none',
		});
	});
</script>