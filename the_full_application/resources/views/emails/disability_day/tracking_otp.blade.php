<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Tracking OTP</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #333333;
            line-height: 1.6;
        }
        .container {
            max-width: 580px;
            margin: 25px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e1e6ed;
        }
        .header {
            background: linear-gradient(135deg, #0b3954 0%, #087e8b 100%);
            color: #ffffff;
            padding: 24px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 4px;
            font-size: 19px;
            font-weight: 700;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 30px 25px;
            text-align: center;
        }
        .otp-box {
            background-color: #eef7f9;
            border: 2px solid #087e8b;
            border-radius: 8px;
            padding: 20px;
            display: inline-block;
            margin: 20px 0;
            min-width: 240px;
        }
        .otp-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #0b3954;
            margin-bottom: 6px;
        }
        .otp-code {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 6px;
            color: #087e8b;
            font-family: "Courier New", Courier, monospace;
        }
        .meta-info {
            font-size: 13px;
            color: #555555;
            margin: 15px 0;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 12px 15px;
            border-radius: 4px;
            font-size: 12px;
            margin: 20px 0 10px;
            text-align: left;
        }
        .footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 16px 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Government of Odisha</h1>
            <p>SSEPD Department — International Day of Persons With Disabilities 2026</p>
        </div>

        <div class="content">
            <h2 style="font-size: 17px; margin-top: 0; color: #0b3954;">One-Time Verification Code (OTP)</h2>
            <p style="font-size: 14px; text-align: left;">
                Dear Applicant,
            </p>
            <p style="font-size: 14px; text-align: left;">
                A request was received to access application details for Registration Number: <strong style="font-family: monospace; color: #0b3954;">{{ $registration->registration_no }}</strong>.
            </p>
            <p style="font-size: 14px; text-align: left;">
                Use the following 6-digit One-Time Password to complete your verification:
            </p>

            <div class="otp-box">
                <div class="otp-label">Verification OTP</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <div class="meta-info">
                This verification code is valid for <strong>{{ $validMinutes }} minutes</strong>.
            </div>

            <div class="warning">
                <strong>Security Reminder:</strong> Never share this OTP or your registration credentials with anyone. Department officials will never ask for your verification code.
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0 0 5px;">This is an automated security communication from the SSEPD Department portal.</p>
            <p style="margin: 0;">If you did not initiate this request, please ignore this email.</p>
        </div>
    </div>
</body>
</html>

