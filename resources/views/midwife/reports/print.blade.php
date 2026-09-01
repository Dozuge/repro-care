<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReproCare Reports - Print</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media print {
            body {
                font-size: 12pt;
                line-height: 1.4;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
            .container-fluid {
                padding: 0;
            }
            table {
                font-size: 10pt;
            }
            .card {
                border: 1px solid #dee2e6 !important;
                break-inside: avoid;
            }
        }

        @media screen {
            body {
                background-color: #f8f9fa;
                padding: 20px;
            }
            .print-container {
                max-width: 1200px;
                margin: 0 auto;
                background: white;
                padding: 40px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }
        }

        .report-header {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .org-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .table th {
            background-color: #e9ecef !important;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Print Controls (Screen only) -->
        <div class="no-print mb-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Print Preview</h5>
            <div class="btn-group">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print Now
                </button>
                <a href="{{ route('midwife.reports.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Reports
                </a>
            </div>
        </div>

        <!-- Report Header -->
        <div class="report-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="org-logo">RC</div>
                </div>
                <div class="col">
                    <h3 class="mb-1">ReproCare</h3>
                    <p class="text-muted mb-0">Reproductive Healthcare Management System</p>
                    <p class="text-muted mb-0">Patient Reports - Admin View</p>
                </div>
                <div class="col-auto text-end">
                    <div class="text-muted small">Report Generated</div>
                    <div class="fw-bold">{{ now()->format('F d, Y \a\\t g:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- Report Title -->
        <div class="text-center mb-4">
            <h2 class="mb-2">Patient Reports Summary</h2>
            <p class="lead text-muted">{{ now()->format('F d, Y') }}</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-number text-primary">{{ $totalPatients }}</div>
                <div class="stat-label">Total Patients</div>
            </div>
            <div class="stat-box">
                <div class="stat-number text-info">{{ $pregnantPatients }}</div>
                <div class="stat-label">Pregnant Patients</div>
            </div>
            <div class="stat-box">
                <div class="stat-number text-success">{{ $completedCheckups }}</div>
                <div class="stat-label">Completed Checkups</div>
            </div>
            <div class="stat-box">
                <div class="stat-number text-warning">{{ $upcomingAppointments }}</div>
                <div class="stat-label">Upcoming Appointments</div>
            </div>
        </div>

        <!-- Patient Table -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-table"></i> Patient Data</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Patient Name</th>
                                <th>Age</th>
                                <th>Status</th>
                                <th>Last Check-up</th>
                                <th>Next Appointment</th>
                                <th>Assigned BHW</th>
                                <th>Email</th>
                                <th>Barangay</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $index => $woman)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-medium">{{ $woman->name }}</td>
                                    <td>{{ $woman->age }}</td>
                                    <td>{{ $woman->pregnancy_status }}</td>
                                    <td>{{ $woman->last_checkup ? $woman->last_checkup->scheduled_date->format('M d, Y') : 'None' }}</td>
                                    <td>{{ $woman->next_appointment ? $woman->next_appointment->scheduled_date->format('M d, Y') : 'None' }}</td>
                                    <td>{{ $woman->assigned_bhw ? $woman->assigned_bhw->name : 'None' }}</td>
                                    <td>{{ $woman->email }}</td>
                                    <td>{{ $woman->barangay ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-5 pt-3 border-top text-center text-muted small">
            <p class="mb-0">This report was generated electronically by the ReproCare Health Management System.</p>
            <p class="mb-0">For verification, please contact your assigned midwife or health center.</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Uncomment below to auto-print on load
            // window.print();
        };
    </script>
</body>
</html>
