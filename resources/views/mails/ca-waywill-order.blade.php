<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
            <style type="text/css">
                .welcome-scr{background: #fff; margin: 25px 100px;}
                .wrapper.welcome-scr img{width:100%; }
                .welcome-scr tr td p{text-align: center;padding: 15px; line-height: 24px;}
            </style>
        </meta>
    </head>
    <body>
        <div class="" style="background-color:#eff4f9; width: 640px; border: 1px solid #ccc; margin: auto;  ">
            <div class="" style="background-color:#eff4f9; margin: 0 auto;  border: 1px solid #d7ddec; ">
                <table bgcolor="#FFF" border="0" cellpadding="0" cellspacing="0" style="padding: 0 10px;" width="100%">
                    <tr>
                        <td style=" padding: 16px 0 16px 16px;" valign="top">
                            <table align="left" bgcolor="#FFF" border="0" cellpadding="0" cellspacing="0" width="50%">
                                <tr>
                                    <td>
                                        <img alt="" border="0" src="{{ asset('images/mainlogo.png') }}" style="margin:0; padding:0; display:block;" width="120px"/>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <table class="wrapper welcome-scr">
                <tr>
                    <td colspan="4">
                        <img src="{{ asset('images/reversegear-screen.jpg') }}" style="width: 100%">
                        </img>
                    </td>
                    <tr>
                        <td colspan="4" style="padding: 16px;">
                            <p>
                                Dear {{ $user['name'] }},
                            </p>
                            <p>
                                Your return label has been generated. A CN22 Invoice is also attached with the mail.
                            </p>
                            <p>
                                If you notice your Label the package will be shipped to our warehouse in Chicago. As per Custom requirements it is mandatory for CN22 Invoice to be pasted on the package too.
                            </p>
                            <p>
                                {{ $user['message'] }}
                            </p>
                            <p>
                                <b>It is mandatory that the CN22 invoice is pasted on your package along with the Label. Failure to do so can delay your refund.</b>
                            </p>
                            <p>
                                Should you have any questions or require more information about our service, please email us at info@shipcycle.com.
                            </p>
                            <p>
                                Thank you.
                            </p>
                            <p>
                                {{ config('app.name') }} Team
                            </p>
                        </td>
                    </tr>
                    @if(isset($user['url']))
                    <tr>
                        <td align="center" colspan="4">
                            <a href="{{ $user['url'] }}" style="color: #fff; background: #35bd64; border-radius: 40px; padding: 13px 52px; border:none; margin-bottom: 25px; display: inline-block;" target="_blank">Download Label</a>
                        </td>
                    </tr>
                    @endif
                    @if(isset($user['cn_url']) && !empty($user['cn_url']))
                    <tr>
                        <td align="center" colspan="4">
                            <a href="{{ $user['cn_url'] }}" style="color: #fff; background: #35bd64; border-radius: 40px; padding: 13px 52px; border:none; margin-bottom: 25px; display: inline-block;" target="_blank">Download CN 22</a>
                        </td>
                    </tr>
                    @endif
                </tr>
            </table>
            <table align="center" bgcolor="#212429" border="0" cellpadding="4" cellspacing="0" width="100%">
                <tr>
                    <td valign="top">
                        <table align="center" border="0" class="container" width="100%">
                            <tr>
                                <td align="center" class="mobile" style="color: #fff; font-size: 12px; font-weight: 500" valign="top" width="100%">
                                    <p style="margin: 10px 0px;">
                                        Phone no.:  +44(0)7584 164 115 | Mail us: info@shipcycle.com
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" class="mobile" style="color: #fff; font-size: 12px;" valign="top" width="100%">
                                    <p style="margin: 0 0 10px 0">
                                        Copyright © 2023 {{ config('app.name') }}, All rights reserved. Powered By: Nile Technologies
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>