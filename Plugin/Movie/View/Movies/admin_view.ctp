
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php  echo __d('movie', 'item_title'); ?></h3>

                <div class="box-tools pull-right">
                    <?php 
                        if( isset($permissions['Movie']['edit']) && ($permissions['Movie']['edit'] == true) ){
                            echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '.__d('movie', 'edit_item'), array('action' => 'edit', $dbdata['Movie']['id']), array('class' => 'btn btn-primary', 'escape' => false));
                        }
                    ?>
                </div>
            </div>

            <div class="box-body">

                <div class="row">
                    <div class="col-md-12">
                        <?php echo __('name'); ?>
                    </div>
                    <div class="col-md-12">
                        <div class="margin-top-15">
                            <?php echo $this->element('content_view',array(
                                'languages' => $languages,
                                'language_input_fields' => $language_input_fields,
                            )); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo __d('movie', 'storyline'); ?>
                    </div>
                    <div class="col-md-12">
                        <div class="margin-top-15">
                            <?php echo $this->element('content_view',array(
                                'languages' => $languages,
                                'language_input_fields' => array('storyline'),
                            )); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo __d('movie', 'lang_movie'); ?>
                    </div>
                    <div class="col-md-12">
                        <div class="margin-top-15">
                            <?php echo $this->element('content_view',array(
                                'languages' => $languages,
                                'language_input_fields' => array('lang_movie'),
                            )); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo __d('movie', 'lang_info'); ?>
                    </div>
                    <div class="col-md-12">
                        <div class="margin-top-15">
                            <?php echo $this->element('content_view',array(
                                'languages' => $languages,
                                'language_input_fields' => array('lang_info'),
                            )); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo __d('movie', 'director'); ?>
                    </div>
                    <div class="col-md-12">
                        <div class="margin-top-15">
                            <?php echo $this->element('content_view',array(
                                'languages' => $languages,
                                'language_input_fields' => array('director'),
                            )); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo __d('movie', 'genre'); ?>
                    </div>
                    <div class="col-md-12">
                        <div class="margin-top-15">
                            <?php echo $this->element('content_view',array(
                                'languages' => $languages,
                                'language_input_fields' => array('genre'),
                            )); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo __d('movie', 'subtitle'); ?>
                    </div>
                    <div class="col-md-12">
                        <div class="margin-top-15">
                            <?php echo $this->element('content_view',array(
                                'languages' => $languages,
                                'language_input_fields' => array('subtitle'),
                            )); ?>
                        </div>
                    </div>
                </div>

                <table id="Movie" class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <td><strong><?= __('id'); ?></strong></td>
                            <td>
                                <?= h($dbdata['Movie']['id']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __('code'); ?></strong></td>
                            <td>
                                <?= h($dbdata['Movie']['code']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __d('movie', 'slug'); ?></strong></td>
                            <td>
                                <?= h($dbdata['Movie']['slug']); ?>
                                &nbsp;
                            </td>
                        </tr>
<!--                        <tr>-->
<!--                            <td><strong>--><?//= __d('movie', 'genre'); ?><!--</strong></td>-->
<!--                            <td>-->
<!--                                --><?//= h($dbdata['Movie']['genre']); ?>
<!--                                &nbsp;-->
<!--                            </td>-->
<!--                        </tr>     -->
<!--                        <tr>-->
<!--                            <td><strong>--><?//= __d('movie', 'subtitle'); ?><!--</strong></td>-->
<!--                            <td>-->
<!--                                --><?//= h($dbdata['Movie']['subtitle']); ?>
<!--                                &nbsp;-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td><strong>--><?//= __d('movie', 'lang_info'); ?><!--</strong></td>-->
<!--                            <td>-->
<!--                                --><?//= h($dbdata['Movie']['lang_info']); ?>
<!--                                &nbsp;-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td><strong>--><?//= __d('movie', 'director'); ?><!--</strong></td>-->
<!--                            <td>-->
<!--                                --><?//= h($dbdata['Movie']['director']); ?>
<!--                                &nbsp;-->
<!--                            </td>-->
<!--                        </tr>-->
                        <tr>
                            <td><strong><?= __d('movie', 'writer'); ?></strong></td>
                            <td>
                                <?= h($dbdata['Movie']['writer']); ?>
                                &nbsp;
                            </td>
                        </tr>

<!--                        <tr>-->
<!--                            <td><strong>--><?//= __d('movie', 'language'); ?><!--</strong></td>-->
<!--                            <td>-->
<!--                                --><?//= h($dbdata['Movie']['language']); ?>
<!--                                &nbsp;-->
<!--                            </td>-->
<!--                        </tr>-->
                        <tr>
                            <td><strong><?= __d('movie', 'rating'); ?></strong></td>
                            <td>
                                <?= h($dbdata['Movie']['rating']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __d('movie', 'duration'); ?></strong></td>
                            <td>
                                <?= h($dbdata['Movie']['duration']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __d('movie', 'poster'); ?></strong></td>
                            <td>
                                <?php if (isset($dbdata['Movie']['poster']) && !empty($dbdata['Movie']['poster'])) { ?>
                                        <img height="240" src="<?= $this->webroot . 'img/' . $dbdata['Movie']['poster'];?>" />
                                <?php } ?>
                                &nbsp;
                            </td>
                        </tr>

                        <?php 
                       
                        foreach ($dbdata['MovieTrailer'] as $key => $movie_trailer) { ?>
                                <tr>
                                    <td><strong><?= __d('movie', 'video'); ?></strong></td>
                                    <td class="text-left">
                                        <?php if (isset($movie_trailer['video_path']) && !empty($movie_trailer['video_path'])) { ?>
                                            <video width="320" height="240" controls>
                                                <source src="<?= $this->webroot . 'img/' . $movie_trailer['video_path'];?>">
                                            </video>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left">
                                        <?php if (isset($movie_trailer['poster_path']) && !empty($movie_trailer['poster_path'])) { ?>
                                            <img height="240" src="<?= $this->webroot . 'img/' . $movie_trailer['poster_path'];?>" />
                                        <?php } ?>
                                    </td>
                                </tr>
                        <?php } ?>

                        <tr>
                            <td><strong><?= __('enabled'); ?></strong></td>
                            <td>
                                <?= $this->element('view_check_ico',array('_check' => $dbdata[$model]['enabled'])) ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __('is_feature'); ?></strong></td>
                            <td>
                                <?= $this->element('view_check_ico',array('_check' => $dbdata[$model]['is_feature'])) ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __('updated_by'); ?></strong></td>
                            <td>
                                <?= h($dbdata['Movie']['updated']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __('updated'); ?></strong></td>
                            <td>
                                <?= h($dbdata['UpdatedBy']['email']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __('created'); ?></strong></td>
                            <td>
                                <?= h($dbdata['Movie']['created']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __('created_by'); ?></strong></td>
                            <td>
                                <?= h($dbdata['CreatedBy']['email']); ?>
                                &nbsp;
                            </td>
                        </tr>
                    </tbody>
                </table><!-- /.table table-striped table-bordered -->
            </div>
        </div><!-- /.view -->

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= __d('movie', 'movie_type'); ?></h3>
            </div>

            <?php if( isset($dbdata['MovieType']) && !empty($dbdata['MovieType']) ){ ?>
                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center"><?= __('id'); ?></th>
                                <th class="text-center"><?= __('name'); ?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($dbdata['MovieType'] as $key => $movie_type) { ?>
                                <tr>
                                    <td class="text-center">
                                        <?php
                                            echo $this->Html->link($movie_type['id'], array(
                                                'plugin' => 'movie', 'controller' => 'movie_types',
                                                'action' => 'view', 'admin' => true, 'prefix' => 'admin', $movie_type['id']
                                            ));
                                        ?>
                                    </td>
                                    <td class="text-center"><?= $movie_type['name']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div><!-- /.related -->

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= __d('movie', 'star'); ?></h3>
            </div>

            <?php if( isset($dbdata['Star']) && !empty($dbdata['Star']) ){ ?>
                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center"><?= __('id'); ?></th>
                                <th class="text-center"><?= __('name'); ?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($dbdata['Star'] as $key => $star) { ?>
                                <tr>
                                    <td class="text-center">
                                        <?php
                                            echo $this->Html->link($star['id'], array(
                                                'plugin' => 'movie', 'controller' => 'stars',
                                                'action' => 'view', 'admin' => true, 'prefix' => 'admin', $star['id']
                                            ));
                                        ?>
                                    </td>
                                    <td class="text-center"><?= $star['name']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div><!-- /.related -->
    </div><!-- /#page-content .span9 -->
</div><!-- /#page-container .row-fluid -->
