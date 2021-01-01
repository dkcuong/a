<aside class="left-side sidebar-offcanvas">
	<section class="sidebar">
        <ul class="sidebar-menu">
            <?php if(isset($permissions) && $permissions){  ?>
                <li class="active">
                    <?= $this->Html->link('<i class="fa fa-dashboard"></i> <span>' . __('home') . '</span>', 
                        array( 'plugin' => 'dashboard', 'controller' => 'dashboard', 'action' => 'index', 'admin' => true ),
                        array('escape' => false)); ?>
                </li>
                <!-- Start Plugin::Administration  -->
                <?php if(   (isset($permissions['Administrator']['view']) && ($permissions['Administrator']['view'] == true)) ||
                            (isset($permissions['Permission']['view']) && ($permissions['Permission']['view'] == true)) || 
                            (isset($permissions['Role']['view']) && ($permissions['Role']['view'] == true))) { ?>
                    <li class="treeview <?= ($this->params['plugin'] == 'administration'?' active':'');?>">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span><?= __('administrator'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if( isset($permissions['Administrator']['view']) && ($permissions['Administrator']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'administrators'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('administrators'),
                                            array( 'plugin' => 'administration', 'controller' => 'administrators', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['Permission']['view']) && ($permissions['Permission']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'permissions'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('permissions'),
                                            array( 'plugin' => 'administration', 'controller' => 'permissions', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['Role']['view']) && ($permissions['Role']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'roles'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('roles'),
                                            array( 'plugin' => 'administration', 'controller' => 'roles', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <!-- End Plugin::Administration  -->
                <!-- Start Plugin::Cinema Management  -->
                <?php if(    
                            (isset($permissions['Cinema']['view']) && ($permissions['Cinema']['view'] == true) )  ||
                            (isset($permissions['Hall']['view'])   && ($permissions['Hall']['view'] == true)   )  ||
                            (isset($permissions['Staff']['view'])  && ($permissions['Staff']['view'] == true)  )  ||
                            (isset($permissions['StaffLog']['view'])  && ($permissions['StaffLog']['view'] == true)) ||
                            (isset($permissions['BookingEnquiry']['view'])  && ($permissions['BookingEnquiry']['view'] == true))
                ){ ?>
                    <li class="treeview<?= ($this->params['plugin'] == 'cinema'?' active':'');?>">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span><?= __d('place', 'cinema_title'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if( isset($permissions['Cinema']['view']) && ($permissions['Cinema']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'cinemas'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('place', 'cinema_title'),
                                            array( 'plugin' => 'cinema', 'controller' => 'cinemas', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['Hall']['view']) && ($permissions['Hall']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'halls'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('place', 'hall_title'),
                                            array( 'plugin' => 'cinema', 'controller' => 'halls', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['Staff']['view']) && ($permissions['Staff']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'staffs'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('staff', 'staff_title'),
                                            array( 'plugin' => 'cinema', 'controller' => 'staffs', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['StaffLog']['view']) && ($permissions['StaffLog']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'staff_logs'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('place', 'staff_log_title'),
                                            array( 'plugin' => 'cinema', 'controller' => 'staff_logs', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['BookingEnquiry']['view']) && ($permissions['BookingEnquiry']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'booking_enquiries'?' active':'');?>">
                                    <?php
                                    echo $this->Html->link(
                                        '<i class="fa fa-angle-double-right"></i>' . __d('place', 'booking_enquiry_title'),
                                        array( 'plugin' => 'cinema', 'controller' => 'booking_enquiries', 'action' => 'index', 'admin' => true ),
                                        array('escape' => false)
                                    );
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <!-- End Plugin::Cinema Management  -->
                <!-- Start Plugin::Movie Management  -->
                <?php if(
                    (isset($permissions['Star']['view']) && ($permissions['Star']['view'] == true) )  ||
                    (isset($permissions['MovieType']['view']) && ($permissions['MovieType']['view'] == true) )  ||
                    (isset($permissions['Movie']['view'])     && ($permissions['Movie']['view'] == true)     )  ||
                    (isset($permissions['Schedule']['view'])  && ($permissions['Schedule']['view'] == true)  )                  
                ){ ?>
                    <li class="treeview<?= ($this->params['plugin'] == 'movie'?' active':'');?>">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span><?= __d('movie', 'item_title'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if( isset($permissions['Star']['view']) && ($permissions['Star']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'stars'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('movie', 'star'),
                                            array( 'plugin' => 'movie', 'controller' => 'stars', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['MovieType']['view']) && ($permissions['MovieType']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'movie_types'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('movie', 'movie_type'),
                                            array( 'plugin' => 'movie', 'controller' => 'movie_types', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['Movie']['view']) && ($permissions['Movie']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'movies'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('movie', 'item_title'),
                                            array( 'plugin' => 'movie', 'controller' => 'movies', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['Schedule']['view']) && ($permissions['Schedule']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'schedules'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('schedule', 'item_title'),
                                            array( 'plugin' => 'movie', 'controller' => 'schedules', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['Schedule']['view']) && ($permissions['Schedule']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'schedules'?' active':'');?>">
                                    <?php
                                    echo $this->Html->link(
                                        '<i class="fa fa-angle-double-right"></i>' . __d('schedule', 'past_item_title'),
                                        array( 'plugin' => 'movie', 'controller' => 'schedules', 'action' => 'past_index', 'admin' => true ),
                                        array('escape' => false)
                                    );
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <!-- End Plugin::Movie Management  -->
                <!-- Start Plugin::POS Management  -->
                <?php if(
                    (isset($permissions['Coupon']['view'])             && ($permissions['Coupon']['view'] == true)            )  ||
                    (isset($permissions['ItemGroup']['view'])     && ($permissions['ItemGroup']['view'] == true)     )  ||
                    (isset($permissions['Item']['view'])         && ($permissions['Item']['view'] == true)         )  ||
                    (isset($permissions['PaymentMethod']['view'])  && ($permissions['PaymentMethod']['view'] == true)  )  ||               
                    (isset($permissions['Order']['view'])  && ($permissions['Order']['view'] == true)  )  ||               
                    (isset($permissions['TicketType']['view'])   && ($permissions['TicketType']['view'] == true)   )                  
                ){ ?>
                    <li class="treeview<?= ($this->params['plugin'] == 'pos'?' active':'');?>">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span><?= __('pos'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if( isset($permissions['Coupon']['view']) && ($permissions['Coupon']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'coupons'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('coupon', 'item_title'),
                                            array( 'plugin' => 'pos', 'controller' => 'coupons', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['ItemGroup']['view']) && ($permissions['ItemGroup']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'item_groups'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('item_group', 'item_title'),
                                            array( 'plugin' => 'pos', 'controller' => 'item_groups', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['Item']['view']) && ($permissions['Item']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'items'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('item', 'item_title'),
                                            array( 'plugin' => 'pos', 'controller' => 'items', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['PaymentMethod']['view']) && ($permissions['PaymentMethod']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'payment_methods'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('payment', 'method'),
                                            array( 'plugin' => 'pos', 'controller' => 'payment_methods', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['TicketType']['view']) && ($permissions['TicketType']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'ticket_types'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('ticket_type', 'item_title'),
                                            array( 'plugin' => 'pos', 'controller' => 'ticket_types', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['Order']['view']) && ($permissions['Order']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'orders'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('order', 'item_title'),
                                            array( 'plugin' => 'pos', 'controller' => 'orders', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php }?>
                            <?php if( isset($permissions['Purchase']['view']) && ($permissions['Purchase']['view'] == true) ){ ?>
                            <li class="<?= ($this->params['controller'] == 'orders'?' active':'');?>">
                                <?php
                                echo $this->Html->link(
                                    '<i class="fa fa-angle-double-right"></i>' . __d('purchase', 'item_title'),
                                    array( 'plugin' => 'pos', 'controller' => 'purchases', 'action' => 'index', 'admin' => true ),
                                    array('escape' => false)
                                );
                                ?>
                            </li>
                            <?php }?>
                            <?php if( isset($permissions['Order']['view']) && ($permissions['Order']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'orders'?' active':'');?>">
                                    <?php
                                    echo $this->Html->link(
                                        '<i class="fa fa-angle-double-right"></i>' . __d('report', 'item_title'),
                                        array( 'plugin' => 'pos', 'controller' => 'orders', 'action' => 'report', 'admin' => true ),
                                        array('escape' => false)
                                    );
                                    ?>
                                </li>
                            <?php }?>
                        </ul>
                    </li>
                <?php } ?>
                <!-- End Plugin::POS Management  -->
                <!-- Start Plugin::Member Management  -->
                <?php if(
                    (isset($permissions['MemberCoupon']['view'])       && ($permissions['MemberCoupon']['view'] == true)      )  ||
                    (isset($permissions['Member']['view'])             && ($permissions['Member']['view'] == true)            )  ||               
                    (isset($permissions['MemberNotification']['view']) && ($permissions['MemberNotification']['view'] == true))  ||
                    ( isset($permissions['MemberRenewal']['view']) && ($permissions['MemberRenewal']['view'] == true) )          ||
                    ( isset($permissions['ContactRequest']['view']) && ($permissions['ContactRequest']['view'] == true) )
                ){ ?>
                    <li class="treeview<?= ($this->params['plugin'] == 'member'?' active':'');?>">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span><?= __('members'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if( isset($permissions['MemberCoupon']['view']) && ($permissions['MemberCoupon']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'member_coupons'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('member_coupon', 'item_title'),
                                            array( 'plugin' => 'member', 'controller' => 'member_coupons', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['Member']['view']) && ($permissions['Member']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'members'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('member'),
                                            array( 'plugin' => 'member', 'controller' => 'members', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['MemberNotification']['view']) && ($permissions['MemberNotification']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'member_notifications'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('member', 'notice_item'),
                                            array( 'plugin' => 'member', 'controller' => 'member_notifications', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['MemberRenewal']['view']) && ($permissions['MemberRenewal']['view'] == true) ){ ?>
                                <!--<li class="<?/*= ($this->params['controller'] == 'member_renewals'?' active':'');*/?>">
                                    <?php
/*                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __d('member', 'renewal_item'),
                                            array( 'plugin' => 'member', 'controller' => 'member_renewals', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    */?>
                                </li>-->
                            <?php } ?>
                            <?php if( isset($permissions['ContactRequest']['view']) && ($permissions['ContactRequest']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'contact_requests'?' active':'');?>">
                                    <?php
                                    echo $this->Html->link(
                                        '<i class="fa fa-angle-double-right"></i>' . __d('member', 'contact_request_item'),
                                        array( 'plugin' => 'member', 'controller' => 'contact_requests', 'action' => 'index', 'admin' => true ),
                                        array('escape' => false)
                                    );
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <!-- End Plugin::Member Management  -->
                <!-- Start Plugin::Content Management  -->
                <?php if(
                    (isset($permissions['PromotionAd']['view']) && ($permissions['PromotionAd']['view'] == true)) ||
                    (isset($permissions['MobileMenu']['view']) && ($permissions['MobileMenu']['view'] == true))
                ){ ?>
                    <li class="treeview<?= ($this->params['plugin'] == 'content'?' active':'');?>">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span><?= __d('content', 'item_title'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if( isset($permissions['PromotionAd']['view']) && ($permissions['PromotionAd']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'promotion_ads'?' active':'');?>">
                                    <?php
                                    echo $this->Html->link(
                                        '<i class="fa fa-angle-double-right"></i>' . __d('content', 'promotion_ads'),
                                        array( 'plugin' => 'content', 'controller' => 'promotion_ads', 'action' => 'index', 'admin' => true ),
                                        array('escape' => false)
                                    );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['MobileMenu']['view']) && ($permissions['MobileMenu']['view'] == true) ){?>
                                <li class="<?= ($this->params['controller'] == 'mobile_menus'?' active':'');?>">
                                    <?php
                                    echo $this->Html->link(
                                        '<i class="fa fa-angle-double-right"></i>' . __d('content', 'mobile_menus'),
                                        array( 'plugin' => 'content', 'controller' => 'mobile_menus', 'action' => 'index', 'admin' => true ),
                                        array('escape' => false)
                                    );
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <!-- End Plugin::Member Management  -->
                <!-- Start Plugin::Facts Management  -->
                <?php if(
                    (isset($permissions['Promotion']['view'])  && ($permissions['Promotion']['view'] == true)  )  ||
                    (isset($permissions['AgeGroup']['view'])   && ($permissions['AgeGroup']['view'] == true)   )  
                ){ ?>
                    <li class="treeview<?= ($this->params['plugin'] == 'fact'?' active':'');?>">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span><?= __('facts'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if( isset($permissions['Promotion']['view']) && ($permissions['Promotion']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'promotions'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('promotion'),
                                            array( 'plugin' => 'fact', 'controller' => 'promotions', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['AgeGroup']['view']) && ($permissions['AgeGroup']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'age_groups'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('age_group'),
                                            array( 'plugin' => 'fact', 'controller' => 'age_groups', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <!-- End Plugin::Facts Management  -->
                <!-- Start Plugin::Setting Management  -->
                <?php if(
                    (isset($permissions['Setting']['view'])       && ($permissions['Setting']['view'] == true)     ) ||
                    (isset($permissions['FormInquiry']['view'])   && ($permissions['FormInquiry']['view'] == true) ) ||  
                    (isset($permissions['HouseBooking']['view'])  && ($permissions['HouseBooking']['view'] == true ) )
                ){ ?>
                    <li class="treeview<?= ($this->params['plugin'] == 'setting'?' active':'');?>">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span><?= __('settings'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if( isset($permissions['Setting']['view']) && ($permissions['Setting']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'settings'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('setting'),
                                            array( 'plugin' => 'setting', 'controller' => 'settings', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['HouseBooking']['view']) && ($permissions['HouseBooking']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'house_bookings'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('house_booking'),
                                            array( 'plugin' => 'setting', 'controller' => 'house_bookings', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <!-- End Plugin::Setting Management  -->
                <!-- Start Plugin::Report Management  -->
                <?php if(
                    (isset($permissions['Sales Analytic']['view'])          && ($permissions['Sales Analytic']['view'] == true)          ) ||
                    (isset($permissions['FilmPerformanceReport']['view'])   && ($permissions['Film Performance Report']['view'] == true) ) ||  
                    (isset($permissions['MembershipReport']['view'])        && ($permissions['MembershipReport']['view'] == true)        ) ||
                    (isset($permissions['PromotionReport']['view'])         && ($permissions['PromotionReport']['view'] == true)         ) ||
                    (isset($permissions['TicketSellingReport']['view'])     && ($permissions['TicketSellingReport']['view'] == true)     )
                ){ ?>
                    <li class="treeview<?= ($this->params['plugin'] == 'report'?' active':'');?>">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span><?= __('reports'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if( isset($permissions['SalesAnalytic']['view']) && ($permissions['SalesAnalytic']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'sales_analytics'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('sales_analytic'),
                                            array( 'plugin' => 'reports', 'controller' => 'sales_analytics', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['FilmPerformanceReport']['view']) && ($permissions['FilmPerformanceReport']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'film_performance_reports'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('film_performance_report'),
                                            array( 'plugin' => 'reports', 'controller' => 'film_performance_reports', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['MembershipReport']['view']) && ($permissions['MembershipReport']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'membership_reports'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('membership_report'),
                                            array( 'plugin' => 'reports', 'controller' => 'membership_reports', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['PromotionReport']['view']) && ($permissions['PromotionReport']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'promotion_reports'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('promotion_report'),
                                            array( 'plugin' => 'reports', 'controller' => 'promotion_reports', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                            <?php if( isset($permissions['TicketSellingReport']['view']) && ($permissions['TicketSellingReport']['view'] == true) ){ ?>
                                <li class="<?= ($this->params['controller'] == 'ticket_selling_report'?' active':'');?>">
                                    <?php
                                        echo $this->Html->link(
                                            '<i class="fa fa-angle-double-right"></i>' . __('ticket_selling_report'),
                                            array( 'plugin' => 'reports', 'controller' => 'ticket_selling_reports', 'action' => 'index', 'admin' => true ),
                                            array('escape' => false)
                                        );
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <!-- End Plugin::Report Management  -->

            <?php }else{ ?>
				<li class="text-center active">
					<?php 
						echo $this->Html->link('Sign In First', array(
							'plugin' => 'administration',
							'controller' => 'administrators',
							'action' => 'login',
							'admin' => true
						)); 
					?>
				</li>
            <?php } ?>
        </ul>
	</section>
</aside>
