<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
</head>
<body>
<table width="690" border="0" align="left" cellpadding="0" cellspacing="0">
    <tr>
        <td style="font-family:calibri, Arial, Sans-serif;font-size:16px;color:#333333;text-align:left;" >
            Click the link below to reset your password: <br />
            {{ route('auth.password_reset', $token) }}
        </td>
    </tr>
    <tr>
        <td style="font-family:calibri, Arial, Sans-serif;font-size:16px;color:#333333;text-align:left;padding-top:10px" >
            Many thanks, <br />
            {{ env('MAIL_SENDER_NAME', 'Nifty CMS') }}
        </td>
    </tr>
</table>
</body>
</html>
