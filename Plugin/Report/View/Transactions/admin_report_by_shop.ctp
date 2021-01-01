
<?= $this->element('Report.spending_by_shop_filter', array(
	'data_search' => $data_search
)); ?>
<div class="row">
    <!-- Start Gender Member Section -->
    <div class="col-md-12">
        
    <div class="box box-all-info">
        <div class="box-body">
            <h2><?php echo __('spending_by_shop'); ?></h2>
            <table id="high_spending" class="table table-bordered table-striped tbl-all-conten-center">
                <thead>
                    <tr>
                        <th class="text-center"><?= __('no.'); ?></th>
                        <th class="text-center"><?= __d('company', 'mall'); ?></th>
                        <th class="text-center"><?= __d('member', 'spending'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach($orders as $key => $item){ 
                            ?>
                        <tr>
                            <td class="text-center"><?= ($key + 1) ?></td>
                            <th class="text-center"><?= isset($shops[$item['Order']['shop_id']]) ? $shops[$item['Order']['shop_id']] : '' ?></th>
                            <th class="text-center"><?= $item['0']['total_spending'] ?></th>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- End Gender Member Section -->
</div>