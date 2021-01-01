<p>Dear our trusted partner,</p>

<p>
    There are some remark from nanny:
    <ul>
        <?php foreach($remarks as $item):?>
            <li>Trip Record <?= $item['trip_record_id'] ?>: <?= $item['remarks'] ?> by <?= $item['nanny'] ?></li>
        <?php endforeach;?>
    </ul>
</p>

<p>Thank you</p>