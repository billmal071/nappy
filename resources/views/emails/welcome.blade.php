<?php $settings = App\Models\AdminSettings::first();?>
<!DOCTYPE html>
<html>
    <head>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <style media="all" rel="stylesheet" type="text/css">
            /* Media Queries */
        @media  only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
        </style>
    </head>
    <body style="margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="center" style="width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <!-- Logo -->
                        <tr>
                            <td style="padding: 25px 0; text-align: center;">
                                <a href="{{url('/')}}" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 16px; font-weight: bold; color: #2F3133; text-decoration: none; text-shadow: 0 1px 0 white;" target="_blank">
                                    {{$settings->title}}
                                </a>
                            </td>
                        </tr>
                        <!-- Email Body -->
                        <tr>
                            <td style="width: 100%; margin: 0; padding: 0; border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; background-color: #FFF;" width="100%">
                                <table align="center" cellpadding="0" cellspacing="0" style="width: auto; max-width: 570px; margin: 0 auto; padding: 0;" width="570">
                                    <tr>
                                        <td style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; padding: 35px;">
                                            <!-- Greeting -->
                                            <h1 style="margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;">
                                                Welcome to {{$settings->title}}, {{$user->username}}!
                                            </h1>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
<!-- Salutation -->
<p style="margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;">
    Regards,
    <br>
        {{$settings->title}}
    </br>
</p>
<!-- Footer -->
<tr>
    <td>
        <table align="center" cellpadding="0" cellspacing="0" style="width: auto; max-width: 570px; margin: 0 auto; padding: 0; text-align: center;" width="570">
            <tr>
                <td style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #AEAEAE; padding: 35px; text-align: center;">
                    <p style="margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;">
                        ©
                        <?php echo date('Y'); ?>
                        {{$settings->title}}
                                            All rights reserved.
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>

