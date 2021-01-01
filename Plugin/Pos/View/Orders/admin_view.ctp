<?php
    if (isset($is_generate_new_code) && $is_generate_new_code) {
        echo('Generate new QR Code');
        echo('<br/>');
    }
?>
<img src="<?= $qrcode_path ?>">
<br/>
<div><?= $qrcode_value ?></div>

<?php
    pr($data_order);
?>