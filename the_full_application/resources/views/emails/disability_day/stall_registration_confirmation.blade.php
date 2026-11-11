<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stall Registration Confirmation</title>
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
            max-width: 620px;
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
            padding: 28px 25px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 6px;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 30px 25px;
        }
        .reg-card {
            background-color: #f0f7fd;
            border: 2px dashed #087e8b;
            border-radius: 8px;
            padding: 18px 20px;
            text-align: center;
            margin: 22px 0;
        }
        .reg-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0b3954;
            margin-bottom: 5px;
        }
        .reg-number {
            font-size: 22px;
            font-weight: 800;
            color: #d13438;
            font-family: "Courier New", Courier, monospace;
            letter-spacing: 1px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        .details-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e9ecef;
        }
        .details-table td.label {
            font-weight: 600;
            color: #555555;
            width: 38%;
            background-color: #fafbfc;
        }
        .details-table td.value {
            color: #111111;
        }
        .btn-wrapper {
            text-align: center;
            margin: 25px 0 15px;
        }
        .btn {
            display: inline-block;
            background-color: #087e8b;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 14px;
        }
        .notice-box {
            background-color: #fff9e6;
            border-left: 4px solid #ffb703;
            padding: 12px 15px;
            font-size: 13px;
            color: #664d03;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 18px 20px;
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
            <p>Social Security & Empowerment of Persons with Disabilities Department</p>
        </div>

        <div class="content">
            <h2 style="font-size: 17px; margin-top: 0; color: #0b3954;">Stall Registration Confirmation</h2>
            <p style="font-size: 14px; margin-bottom: 15px;">
                Dear <strong>{{ $registration->contact_person }}</strong>,
            </p>
            <p style="font-size: 14px;">
                Thank you for applying for a stall at the upcoming <strong>{{ $eventName }}</strong>. Your registration has been successfully received by the department.
            </p>

            <div class="reg-card">
                <div class="reg-label">Your Unique Registration Number</div>
                <div class="reg-number">{{ $registration->registration_no }}</div>
            </div>

            <table class="details-table">
                <tr>
                    <td class="label">Event</td>
                    <td class="value"><strong>{{ $eventName }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Event Dates</td>
                    <td class="value">{{ $eventDates }}</td>
                </tr>
                <tr>
                    <td class="label">Venue</td>
                    <td class="value">{{ $venue }}</td>
                </tr>
                <tr>
                    <td class="label">Organization Name</td>
                    <td class="value">{{ $registration->organization_name }}</td>
                </tr>
                <tr>
                    <td class="label">Contact Person</td>
                    <td class="value">{{ $registration->contact_person }}</td>
                </tr>
                <tr>
                    <td class="label">Registered Email</td>
                    <td class="value">{{ $registration->email }}</td>
                </tr>
                <tr>
                    <td class="label">Mobile Number</td>
                    <td class="value">{{ $registration->phone }}</td>
                </tr>
                <tr>
                    <td class="label">Applied Date & Time</td>
                    <td class="value">{{ optional($registration->applied_at)->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A') }}</td>
                </tr>
            </table>

            <div class="notice-box">
                <strong>Important Notice:</strong>
                Please preserve your <strong>Registration Number</strong> for future communications. Stall allotments are processed by the authorized departmental committee. You can check your allotment status anytime through our official online tracking facility using your Registration Number and Registered Email.
            </div>

            <div class="btn-wrapper">
                <a href="{{ $trackingUrl }}" class="btn" target="_blank">Track Your Application Online</a>
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0 0 5px;">This is an automated communication from the SSEPD Department, Government of Odisha.</p>
            <p style="margin: 0;">Please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>

