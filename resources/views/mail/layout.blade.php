<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; line-height: 1.5; background-color: #f3f4f6; color: #374151; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); margin-top: 40px; }
        .header { background-color: #059669; padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; }
        .content { padding: 40px; }
        .footer { padding: 32px 40px; background-color: #f9fafb; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { margin: 0; color: #6b7280; font-size: 14px; }
        .footer a { color: #059669; text-decoration: none; }
        .button { display: inline-block; background-color: #059669; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; margin-top: 24px; }
        h2 { color: #111827; font-size: 20px; font-weight: bold; margin-top: 0; }
        p { margin-top: 0; margin-bottom: 16px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .data-table th, .data-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .data-table th { color: #6b7280; font-weight: normal; width: 40%; }
        .data-table td { color: #111827; font-weight: 500; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main" width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td class="header">
                    <h1>Penginapan Kelapa Sawit</h1>
                </td>
            </tr>
            <tr>
                <td class="content">
                    @yield('content')
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <p>Penginapan Kelapa Sawit<br>Kota Bangun II, Kutai Kartanegara, Kalimantan Timur</p>
                    <p style="margin-top: 8px;">
                        <a href="https://wa.me/6281234567890">Hubungi kami via WhatsApp</a>
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
