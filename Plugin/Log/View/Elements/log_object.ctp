<ul>
    <?php foreach($data_render as $key => $item){ ?>
        <li>
            <b><?= $key ?>: </b>
            <?php if(is_array($item)){ ?>
                <?php
                    echo $this->element('Log.log_array', array(
                        'data_render' => $item,
                    )); 
                ?>
            <?php }else if(is_object($item)){ ?>
                <?php
                    echo $this->element('Log.log_object', array(
                        'data_render' => $item,
                    )); 
                ?>
            <?php }else { ?>
                <?= $item; ?>
            <?php } ?>
        </li>
    <?php } ?>
</ul>