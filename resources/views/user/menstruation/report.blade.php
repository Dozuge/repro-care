<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Menstrual Cycle Report - {{ $user->name }}</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            margin: 0;
            background: #f5f3ff;
            padding: 28px;
        }
        .page {
            max-width: 980px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(91, 33, 182, 0.12);
            overflow: hidden;
        }
        .header {
            text-align: left;
            padding: 32px;
            background: linear-gradient(135deg, #6d28d9 0%, #9333ea 58%, #ec4899 100%);
            color: #fff;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -0.03em;
        }
        .header p {
            margin: 6px 0 0;
            color: rgba(255, 255, 255, 0.86);
        }
        .content {
            padding: 28px 32px 36px;
        }
        .info-box {
            background: #f8f5ff;
            border: 1px solid #e9ddff;
            padding: 18px 20px;
            border-radius: 18px;
            margin-bottom: 20px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #6d28d9;
            font-size: 16px;
        }
        .stats-grid {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 30px;
        }
        .stat-item {
            text-align: center;
            padding: 18px 16px;
            background: linear-gradient(180deg, #faf7ff 0%, #f4edff 100%);
            border: 1px solid #eadcff;
            border-radius: 18px;
            flex: 1;
        }
        .stat-value {
            font-size: 30px;
            font-weight: 700;
            color: #6d28d9;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            overflow: hidden;
            border-radius: 16px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #6d28d9;
            color: white;
            font-weight: bold;
        }
        .normal {
            color: #16a34a;
            font-weight: bold;
        }
        .abnormal {
            color: #dc2626;
            font-weight: bold;
        }
        .section-title {
            background: linear-gradient(135deg, #6d28d9 0%, #9333ea 100%);
            color: white;
            padding: 12px 16px;
            margin: 30px 0 15px 0;
            font-size: 16px;
            font-weight: bold;
            border-radius: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #666;
        }
        .note {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-left: 4px solid #f59e0b;
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="page">
    <div class="header">
        <h1>Menstrual Cycle Medical Report</h1>
        <p><strong>ReproCare Health System</strong></p>
        <p>Generated on: {{ now()->format('F j, Y') }}</p>
    </div>
    <div class="content">

    <div class="info-box">
        <h3>Patient Information</h3>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Age:</strong> {{ $user->birth_date ? $user->birth_date->age . ' years' : 'Not provided' }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-value">{{ $averageCycle ?? 'N/A' }}</div>
            <div class="stat-label">Avg Cycle Length</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $averagePeriod ?? 'N/A' }}</div>
            <div class="stat-label">Avg Period Duration</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $records->count() }}</div>
            <div class="stat-label">Total Periods Recorded</div>
        </div>
    </div>

    <div class="section-title">Cycle History (Last 6 Periods)</div>
    <table>
        <thead>
            <tr>
                <th>Period #</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Duration</th>
                <th>Cycle Length</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $prevDate = null; @endphp
            @foreach($records as $index => $record)
                @php
                    $cycleLength = $prevDate ? $prevDate->diffInDays($record->period_start_date, false) : null;
                    $isNormal = $cycleLength ? ($cycleLength >= 21 && $cycleLength <= 35) : null;
                    $prevDate = $record->period_start_date;
                @endphp
                <tr>
                    <td>{{ $records->count() - $index }}</td>
                    <td>{{ $record->period_start_date->format('M d, Y') }}</td>
                    <td>{{ $record->period_end_date ? $record->period_end_date->format('M d, Y') : 'Ongoing' }}</td>
                    <td>{{ $record->duration }} days</td>
                    <td>{{ $cycleLength ? $cycleLength . ' days' : '-' }}</td>
                    <td>
                        @if($isNormal === null)
                            -
                        @elseif($isNormal)
                            <span class="normal">Normal</span>
                        @else
                            <span class="abnormal">Abnormal</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="note">
        <strong>Note:</strong> Normal menstrual cycle length ranges from 21 to 35 days. Cycles outside this range may indicate hormonal irregularities or other health conditions requiring medical attention.
    </div>

    <div class="section-title">Daily Tracking Summary (Last 3 Months)</div>
    @if($dailyData->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Period</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyData->take(20) as $day)
                    <tr>
                        <td>{{ $day->date->format('M d, Y') }}</td>
                        <td>{{ $day->is_period ? 'Yes' : 'No' }}</td>
                        <td>{{ $day->notes ? \Illuminate\Support\Str::limit($day->notes, 30) : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($dailyData->count() > 20)
            <p style="text-align: center; color: #666; font-style: italic;">... and {{ $dailyData->count() - 20 }} more entries</p>
        @endif
    @else
        <p style="text-align: center; color: #666;">No daily tracking data recorded in the last 3 months.</p>
    @endif

    <div class="section-title">Medical Interpretation</div>
    <div class="info-box">
        <h3>Cycle Pattern Analysis</h3>
        @if($averageCycle)
            <p><strong>Average Cycle Length:</strong> {{ $averageCycle }} days</p>
            <p>
                @if($averageCycle >= 21 && $averageCycle <= 35)
                    <span class="normal">✓ Within normal range (21-35 days)</span>
                @elseif($averageCycle < 21)
                    <span class="abnormal">⚠ Short cycle - May indicate hormonal imbalance or polycystic ovary syndrome (PCOS)</span>
                @else
                    <span class="abnormal">⚠ Long cycle - May indicate anovulation, thyroid issues, or other hormonal conditions</span>
                @endif
            </p>
        @else
            <p>Insufficient data to determine cycle pattern.</p>
        @endif

        @if($averagePeriod)
            <p style="margin-top: 15px;"><strong>Average Period Duration:</strong> {{ $averagePeriod }} days</p>
            <p>
                @if($averagePeriod >= 2 && $averagePeriod <= 7)
                    <span class="normal">✓ Normal period duration (2-7 days)</span>
                @elseif($averagePeriod > 7)
                    <span class="abnormal">⚠ Prolonged bleeding - May indicate fibroids, polyps, or hormonal issues</span>
                @else
                    <span class="abnormal">⚠ Very short period - May indicate hormonal imbalance</span>
                @endif
            </p>
        @endif
    </div>

    <div class="footer">
        <p>This report was generated by ReproCare Health System</p>
        <p>For medical advice, please consult with your healthcare provider</p>
        <p style="margin-top: 10px;"><strong>Report ID:</strong> MCR-{{ $user->id }}-{{ now()->format('Ymd') }}</p>
    </div>
    </div>
    </div>
</body>
</html>
