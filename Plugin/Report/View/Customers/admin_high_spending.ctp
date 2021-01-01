<div class="row">
    <!-- Start Gender Member Section -->
    <div class="col-md-12">
        
    <div class="box box-all-info">
        <div class="box-body">
            <h2><?php echo __d('dashboard', 'high_spending'); ?></h2>
            <table id="high_spending" class="table table-bordered table-striped tbl-all-conten-center">
                <thead>
                    <tr>
                        <th class="text-center"><?= __('no.'); ?></th>
                        <th class="text-center"><?= __d('member', 'country_code'); ?></th>
                        <th class="text-center"><?= __('phone'); ?></th>
                        <th class="text-center"><?= __('first_name'); ?></th>
                        <th class="text-center"><?= __('last_name'); ?></th>
                        <th class="text-center"><?= __d('member', 'spending'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach($orders as $key => $item){ 
                            if(!$members[$item['Order']['member_id']]){
                                continue;
                            }
                            $member = $members[$item['Order']['member_id']];
                            ?>
                        <tr>
                            <td class="text-center"><?= ($key + 1) ?></td>
                            <th class="text-center"><?= $member['country_code'] ?></th>
                            <th class="text-center"><?= $member['phone'] ?></th>
                            <th class="text-center"><?= $member['first_name'] ?></th>
                            <th class="text-center"><?= $member['last_name'] ?></th>
                            <th class="text-center"><?= $item['0']['total_spending'] ?></th>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- End Gender Member Section -->
</div>