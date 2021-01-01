<p>Dear Manager</p>

<p>
<table>
    <tr>
        <td align="center">There are new contact request</td>
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
            <?= __('message') ?>
        </td>
        <td>
            <?php
            if(isset($message) && !empty($message))  {
                echo $message;
            }
            ?>
        </td>
    </tr>
</table>

</p>

<p>Thank you</p>