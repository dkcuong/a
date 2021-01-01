<p>Dear Customer</p>

<p>
    <table>
        <tr>
            <td align="center" colspan="2">
                <img src="<?= Environment::read('web.url_img') . 'general/logo_black.png' ; ?>">
            </td>
        </tr>
    <tr>
        <td> - <?= __d('movie', 'item_title').': '?></td>
        <td style="font-weight: bold;"> <?= $movie_display?></td>
    </tr>
    <tr>
        <td> - <?= __('date').': '?></td>
        <td style="font-weight: bold;"> <?= $date_display ?></td>
    </tr>
    <tr>
        <td> - <?= __('time').': ' ?></td>
        <td style="font-weight: bold;"> <?= $time_display ?></td>
    </tr>
    <tr>
        <td> - <?= __d('place', 'hall_title').': ' ?></td>
        <td style="font-weight: bold;"> <?= $hall_display ?></td>
    </tr>
    <tr>
        <td> - <?= __('number_of_seats').': '?></td>
        <td style="font-weight: bold;"> <?= $number_of_seats ?></td>
    </tr>
    <tr>
        <td> - <?= __('seats').': '?></td>
        <td style="font-weight: bold;"> <?= $seats ?></td>
    </tr>
    <tr>
        <td valign="top"> - <?= __('qrcode_path').': ' ?></td>
        <td>
            <img src="<?= $qrcode_path ?>" width="150" height="150">
        </td>
    </tr>
</table>

</p>

<p>Thank you</p>