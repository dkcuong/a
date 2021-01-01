<p>Dear Customer</p>

<p>
<table>
    <tr>
        <td align="center" colspan="2">
            <img src="<?= Environment::read('web.url_img') . 'general/logo.png' ; ?>">
        </td>
    </tr>
    <tr>
        <td> - <?= __d('coupon', 'item_title').': '?></td>
        <td style="font-weight: bold;"> <?= $name ?></td>
    </tr>
    <tr>
        <td> - <?= __('description').': '?></td>
        <td style="font-weight: bold;"> <?= $des ?></td>
    </tr>
    <tr>
        <td valign="top"> - <?= __('qrcode_path').': ' ?></td>
        <td><a target="_blank" href="<?= $welcome_coupon_code ?>"><img src="<?= $welcome_coupon_code ?>" width="150" height="150"></td>
    </tr>
    <tr>
        <td> - <?= __d('coupon', 'expiry_date').': '?></td>
        <td style="font-weight: bold;"> <?= $expired_date ?></td>
    </tr>
    <tr>
        <td valign="top"> - <?= __d('coupon', 'terms').': '?></td>
        <td valign="top"> <?= $terms ?></td>
    </tr>
</table>

</p>

<p>Thank you</p>