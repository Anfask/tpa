<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TPA - Performance Report</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #0ea5e9;
            --accent: #10b981;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --bg-app: #f8fafc;

            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            color: var(--text-primary);
            background-color: #ffffff;
            padding: 2rem;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            border-radius: 0;
            overflow: hidden;
        }

        .brand-text h1 {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }

        .brand-text p {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .meta-info {
            text-align: right;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .meta-info h2 {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.25rem;
            background-color: var(--bg-app);
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .card-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .card-value {
            font-family: var(--font-heading);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-sub {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .table-title {
            font-family: var(--font-heading);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        th {
            font-family: var(--font-heading);
            background-color: var(--bg-app);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        td {
            padding: 0.85rem 1rem;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        tr:nth-child(even) {
            background-color: rgba(248, 250, 252, 0.5);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--accent);
        }

        .badge-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning, #f59e0b);
        }

        .badge-danger {
            background-color: rgba(244, 63, 94, 0.1);
            color: #f43f5e;
        }

        .print-btn-container {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
        }

        .btn-secondary {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }

        @media print {
            body {
                padding: 0;
            }

            .print-btn-container {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="report-header">
        <div class="brand">
            <div class="brand-icon">
                <img src="{{ asset('1.png') }}" alt="TPA Logo" style="width:100%; height:100%; object-fit:contain;">
            </div>
            <div class="brand-text">
                <h1>TPA Performance</h1>
                <p>Teacher Performance Association</p>
            </div>
        </div>

        <div class="meta-info">
            <h2>Evaluation Report</h2>
            <div>Generated: {{ now()->format('M d, Y h:i A') }}</div>
            <div>Category: <span style="text-transform: capitalize; font-weight: 600;">{{ $type }}</span></div>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="summary-cards">
        <div class="card">
            <span class="card-label">Total Evaluations</span>
            <span class="card-value">{{ $inspections->count() }}</span>
            <span class="card-sub">Filtered records found</span>
        </div>
        <div class="card">
            <span class="card-label">Average Score</span>
            <span class="card-value">
                {{ $inspections->count() > 0 ? round($inspections->avg('score'), 1) . '%' : 'N/A' }}
            </span>
            <span class="card-sub">Out of 100% standard criteria</span>
        </div>
        <div class="card">
            <span class="card-label">Report Type</span>
            <span class="card-value" style="font-size: 1.35rem; text-transform: capitalize; padding-top: 0.4rem;">
                {{ $type }} Logs
            </span>
            <span class="card-sub">TPA Database Extraction</span>
        </div>
    </div>

    <h3 class="table-title">
        Detailed Inspection Records
    </h3>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date & Time</th>
                <th>Inspector</th>
                <th>Target Entity</th>
                <th>Score (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inspections as $ins)
                @php
                    $target = '';
                    if ($type === 'teacher')
                        $target = $ins->teacher->name ?? 'N/A';
                    elseif ($type === 'admin')
                        $target = $ins->admin->name ?? 'N/A';
                    elseif ($type === 'campus')
                        $target = $ins->campus->name ?? 'N/A';

                    $scoreClass = $ins->score >= 85 ? 'badge-success' : ($ins->score >= 70 ? 'badge-warning' : 'badge-danger');
                @endphp
                <tr>
                    <td style="font-weight: 600; color: var(--primary);">#{{ $ins->id }}</td>
                    <td>{{ $ins->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $ins->inspector->name ?? 'System' }}</td>
                    <td><strong>{{ $target }}</strong></td>
                    <td>
                        <span class="badge {{ $scoreClass }}">
                            {{ $ins->score }}%
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                        No inspection logs matching the selected filters were found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="print-btn-container">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fa-solid fa-print"></i> Print / Save as PDF
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            Close Tab
        </button>
    </div>

    <script>
        // Auto trigger print dialogue when page loads
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>

</html>