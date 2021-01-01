<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= __('member'); ?></h3>

                <div class="box-tools pull-right">
                    <?php
                        if(isset($permissions[$model]['edit']) && $permissions[$model]['edit']){
                            echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '. __d('member', 'edit_item'), array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-primary', 'escape' => false));
                        } 
                    ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role=<?= $model ?> class="active">
                            <a href="#info-tab" aria-controls="tab" role="tab" data-toggle="tab">
                                <?= __('member') ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="info-tab">
                        <?php if($dbdata[$model]){ ?>
                            <table id="TicketType" class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <td><strong><?= __('id'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['id']); ?>
                                        </td>
                                    </tr>       
                                    <tr>
                                        <td><strong><?= __('name'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['title']) . ' ' . h($dbdata[$model]['name']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __d('member', 'month_of_birth'); ?></strong></td>
                                        <td>
                                            <?=                                      
                                            isset($dobMonths[$dbdata[$model]['birth_month']]) ? $dobMonths[$dbdata[$model]['birth_month']] : ''; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('age_group'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata['AgeGroupLanguage']['name']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('district'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata['DistrictLanguage']['name']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __d('member', 'country_code'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['country_code']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('phone'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['phone']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __( 'qrcode_path'); ?></strong></td>
                                        <td>
                                            <?php if (isset($dbdata[$model]['qrcode_path']) && !empty($dbdata[$model]['qrcode_path'])) { ?>
                                                <img height="240" src="<?= $this->webroot . 'img/' . $dbdata[$model]['qrcode_path'];?>" />
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __d('member', 'phone_verification'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['phone_verification']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __d('member', 'phone_verified'); ?></strong></td>
                                        <td>
                                            <?= $this->element('view_verify_stick_color_icon',array('check' => $dbdata[$model]['is_phone_verified'])); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('email'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['email']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __d('member', 'email_verification'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['email_verification']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __d('member', 'email_verified'); ?></strong></td>
                                        <td>
                                            <?= $this->element('view_verify_stick_color_icon',array('check' => $dbdata[$model]['is_email_verified'])) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __d('member', 'renewal_status'); ?></strong></td>
                                        <td>
                                            <?= $this->element('view_verify_stick_color_icon',array('check' => $dbdata[$model]['is_renewal'])); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __d('member', 'expired_date'); ?></strong></td>
                                        <td>
                                            <?php
//                                                $expired_date = '';
//
//                                                if (!empty($dbdata['MemberRenewal'])) {
//                                                    $expired_date = $dbdata['MemberRenewal'][0]['expired_date'];
//                                                    $expired_date = date('Y-m-d', strtotime($expired_date));
//                                                };
//                                                echo $expired_date;
                                                echo $dbdata['MemberRenewal']['expired_date']
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('updated'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['updated']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('updated_by'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata['UpdatedBy']['email']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('created'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['created']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('created_by'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata['CreatedBy']['email']); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        <?php } ?>
                    </div> <!-- close tabpanel -->
                </div> <!-- close tab-content -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
    });
</script>   
    