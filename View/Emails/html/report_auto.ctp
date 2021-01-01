<p>Dear All,</p>

<p>There is new [<?= $report_name ?>] Report <?php if($is_from_to): ?>from "<?= date('Y-m-d H:i:s', strtotime($from)) ?>" to "<?= date('Y-m-d H:i:s', strtotime($to)) ?>" <?php endif; ?></p> 

<p>Please click <a href="<?= $link; ?>">here</a> to see more detail.</p>

<p>Thank you</p>