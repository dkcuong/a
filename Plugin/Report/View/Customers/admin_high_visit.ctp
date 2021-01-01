<div class="row">
    <!-- Start Gender Member Section -->
    <div class="col-md-12">
        <div class="box-body">
            <h2><?php echo __d('dashboard', 'most_visited'); ?></h2>
            <table id="most_visited" class="table table-bordered table-striped tbl-all-conten-center">
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
                        <?php foreach($members as $key => $item){ 
                            $member = $item['Member']; 
                                ?>
                            <tr>
                                <td class="text-center"><?= ($key + 1) ?></td>
                                <th class="text-center"><?= $member['country_code'] ?></th>
                                <th class="text-center"><?= $member['phone'] ?></th>
                                <th class="text-center"><?= $member['first_name'] ?></th>
                                <th class="text-center"><?= $member['last_name'] ?></th>
                                <th class="text-center"><?= $member['visit_count'] ?></th>
                            </tr>
                        <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- End Gender Member Section -->
</div>