<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disability Day 2026 – Stall Allotment Confirmation</title>
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
        .allotment-banner {
            background-color: #ecfdf5;
            border: 2px solid #10b981;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 22px 0;
        }
        .allotment-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #065f46;
            margin-bottom: 8px;
        }
        .stall-badges-container {
            margin: 10px 0;
        }
        .stall-badge {
            display: inline-block;
            background: #ffffff;
            color: #047857;
            border: 2px solid #10b981;
            border-radius: 6px;
            font-family: "Courier New", Courier, monospace;
            font-size: 20px;
            font-weight: 800;
            padding: 6px 16px;
            margin: 4px;
            letter-spacing: 0.5px;
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
            width: 40%;
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
            background-color: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 14px 16px;
            font-size: 13px;
            color: #0369a1;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
            line-height: 1.5;
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
            <h2 style="font-size: 18px; margin-top: 0; color: #0b3954;">Stall Allotment Confirmation</h2>
            <p style="font-size: 14px; margin-bottom: 15px;">
                Dear <strong>{{ $registration->contact_person }}</strong>,
            </p>
            <p style="font-size: 14px;">
                We are pleased to inform you that your organization <strong>{{ $registration->organization_name }}</strong> has been officially allotted exhibition stall space for the upcoming <strong>{{ $eventName }}</strong>.
            </p>

            <div class="allotment-banner">
                <div class="allotment-label"><i class="fas fa-check-circle"></i> Official Stall Allotment</div>
                <div class="stall-badges-container">
                    @forelse($allottedStalls as $stallNum)
                        <span class="stall-badge">Stall #{{ $stallNum }}</span>
                    @empty
                        <span class="stall-badge">{{ $registration->allotted_stall ?: 'Assigned' }}</span>
                    @endforelse
                </div>
                <div style="font-size: 12px; color: #065f46; margin-top: 6px;">
                    Total Stalls Allotted: <strong>{{ count($allottedStalls) }}</strong>
                </div>
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
                    <td class="label">Venue / Location</td>
                    <td class="value"><strong>{{ $venue }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Category</td>
                    <td class="value">Exhibition &amp; Service Stall</td>
                </tr>
                <tr>
                    <td class="label">Organization Name</td>
                    <td class="value"><strong>{{ $registration->organization_name }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Registration Number</td>
                    <td class="value" style="font-family: monospace; font-weight: 700; color: #0b3954;">
                        {{ $registration->registration_no }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Contact Person</td>
                    <td class="value">{{ $registration->contact_person }}</td>
                </tr>
                <tr>
                    <td class="label">Registered Email ID</td>
                    <td class="value">{{ $registration->email }}</td>
                </tr>
                <tr>
                    <td class="label">Registered Phone</td>
                    <td class="value">+91 {{ $registration->phone }}</td>
                </tr>
                <tr>
                    <td class="label">District / State</td>
                    <td class="value">{{ $registration->district ? $registration->district->district_name : ($registration->district_name ?? '—') }}, {{ $registration->state ? $registration->state->state_name : ($registration->state_name ?? 'Odisha') }}</td>
                </tr>
                @if($registration->financial_year)
                <tr>
                    <td class="label">Financial Year</td>
                    <td class="value">{{ $registration->financial_year }}</td>
                </tr>
                @endif
            </table>

            <div class="notice-box">
                <strong>Official Department Confirmation:</strong><br>
                Please report at the venue (S'O'A University) by 08:30 AM on 01 December 2026 for setup and stall handover. Kindly carry an authorized photo ID along with a copy of this allotment confirmation email and your Registration Number (<strong>{{ $registration->registration_no }}</strong>).
            </div>

            <div class="btn-wrapper">
                <a href="{{ $trackingUrl }}" class="btn" target="_blank">Track Your Application Online</a>
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0 0 5px;">This is an official communication from the Social Security &amp; Empowerment of Persons with Disabilities Department, Government of Odisha.</p>
            <p style="margin: 0;">Please do not reply directly to this automated email.</p>
        </div>
    </div>
</body>
</html>
