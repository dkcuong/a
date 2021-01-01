<p>Dear Manager</p>

<p>
    <table>
        <tr>
            <td align="center">There are new booking enquiry</td>
        </tr>

        <tr>
        </tr>


        <tr>
            <td>
                <?= __('title') ?>
            </td>
            <td>
                <?php
                    if(isset($title) && !empty($title))  {
                        echo __($title);
                    }
                 ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __('name') ?>
            </td>
            <td>
                <?php
                if(isset($name) && !empty($name))  {
                    echo $name;
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __('email') ?>
            </td>
            <td>
                <?php
                if(isset($email) && !empty($email))  {
                    echo $email;
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __('country_code') ?>
            </td>
            <td>
                <?php
                if(isset($country_code) && !empty($country_code))  {
                    echo $country_code;
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __('phone') ?>
            </td>
            <td>
                <?php
                if(isset($phone) && !empty($phone))  {
                    echo __($phone);
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __('date') ?>
            </td>
            <td>
                <?php
                if(isset($date) && !empty($date))  {
                    echo $date;
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __d('booking_enquiry', 'time_from') ?>
            </td>
            <td>
                <?php
                if(isset($time_from) && !empty($time_from))  {
                    echo $time_from;
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __d('booking_enquiry', 'time_to') ?>
            </td>
            <td>
                <?php
                if(isset($time_to) && !empty($time_to))  {
                    echo $time_to;
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __d('booking_enquiry', 'event_purpose') ?>
            </td>
            <td>
                <?php
                if(isset($event_purpose) && !empty($event_purpose))  {
                    echo $event_purpose;
                }
                ?>
            </td>
        </tr>


        <tr>
            <td>
                <?= __d('booking_enquiry', 'movie_name') ?>
            </td>
            <td>
                <?php
                if(isset($movie_name) && !empty($movie_name))  {
                    echo $movie_name;
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __d('booking_enquiry', 'no_of_attendee') ?>
            </td>
            <td>
                <?php
                if(isset($no_of_attendee) && !empty($no_of_attendee))  {
                    echo $no_of_attendee;
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __d('place', 'hall_title') ?>
            </td>
            <td>
                <?php
                if(isset($hall_display) && !empty($hall_display))  {
                    echo $hall_display;
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __d('booking_enquiry', 'special_request') ?>
            </td>
            <td>
                <?php
                if(isset($special_request) && !empty($special_request))  {
                    echo $special_request;
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __d('booking_enquiry', 'equipment') ?>
            </td>
            <td>
                <?php
                if(isset($equipment_display) && !empty($equipment_display))  {
                    echo $equipment_display;
                }
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= __d('booking_enquiry', 'item') ?>
            </td>
            <td>
                <?php
                if(isset($item_display) && !empty($item_display))  {
                    echo $item_display;
                }
                ?>
            </td>
        </tr>
</table>

</p>

<p>Thank you</p>