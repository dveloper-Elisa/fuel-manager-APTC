<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
require '../connection.php';

if (!isset($db) && isset($pdo)) { $db = $pdo; }

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-01-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-12-31');

$reports = [];
$fuelTotals = []; // Array to store totals per fuel type
$totalOverallTransactions = 0;
$grandTotalLiters = 0;

$sql = "SELECT 
            DATE_FORMAT(required_date, '%Y-%m') as report_month,
            fuel,
            SUM(litter) as total_liters,
            COUNT(id) as total_transactions
        FROM operation 
        WHERE status = 'approved' 
        AND required_date BETWEEN ? AND ?
        GROUP BY report_month, fuel
        ORDER BY report_month DESC, fuel ASC";

$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
    
    // Calculate Summary Data
    $fuelName = $row['fuel'];
    if (!isset($fuelTotals[$fuelName])) {
        $fuelTotals[$fuelName] = 0;
    }
    $fuelTotals[$fuelName] += $row['total_liters'];
    $totalOverallTransactions += $row['total_transactions'];
    $grandTotalLiters += $row['total_liters'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operational Fuel Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-6xl mx-auto">
                
                <div class="mb-8 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800">Operational Fuel Monthly Report</h1>
                            <p class="text-gray-600">Breakdown of approved fuel operations and consumption totals.</p>
                        </div>
                        <div class="flex gap-4 no-print">
                            <a class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 transition" href="../dashboard.php">Back</a>
                            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition">Print Report</button>
                        </div>
                    </div>

                    <form method="GET" class="mt-6 flex flex-wrap gap-4 no-print border-t pt-4">
                        <div class="flex flex-col">
                            <label class="text-xs font-bold uppercase text-gray-500 mb-1">From Date</label>
                            <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="border rounded px-3 py-2 text-sm">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs font-bold uppercase text-gray-500 mb-1">To Date</label>
                            <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="border rounded px-3 py-2 text-sm">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded text-sm hover:bg-black transition">Generate Report</button>
                        </div>
                    </form>
                </div>

                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-6 py-4 text-sm font-semibold uppercase">Month</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase">Fuel Type</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase text-center">Transactions</th>
                                <th class="px-6 py-4 text-sm font-semibold uppercase text-right">Total Liters</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php 
                            $currentMonth = "";
                            if (count($reports) > 0): 
                                foreach ($reports as $row): 
                                    $displayMonth = date("F Y", strtotime($row['report_month'] . "-01"));
                                    $isNewMonth = ($currentMonth !== $displayMonth);
                                    $currentMonth = $displayMonth;
                            ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 <?php echo $isNewMonth ? 'border-t-2 border-gray-100' : ''; ?>">
                                        <?php echo $isNewMonth ? $displayMonth : ''; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded border border-blue-100 font-semibold uppercase">
                                            <?php echo htmlspecialchars($row['fuel']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-600 italic">
                                        <?php echo $row['total_transactions']; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-bold text-gray-800">
                                        <?php echo number_format($row['total_liters'], 2); ?> L
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <tr class="bg-gray-50">
                                <td colspan="4" class="px-6 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Summary per Fuel Type</td>
                            </tr>
                            <?php foreach ($fuelTotals as $fuel => $liters): ?>
                            <tr class="bg-gray-50/50 italic text-sm">
                                <td colspan="2" class="px-6 py-2 text-right text-gray-500">Total <?php echo htmlspecialchars($fuel); ?>:</td>
                                <td class="px-6 py-2"></td>
                                <td class="px-6 py-2 text-right font-bold text-gray-700 underline decoration-double">
                                    <?php echo number_format($liters, 2); ?> L
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php else: ?>
                                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">No records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        
                        <?php if (count($reports) > 0): ?>
                        <tfoot class="border-t-4 border-gray-800">
                            <tr class="bg-gray-800 text-white">
                                <td class="px-6 py-4 font-bold">TOTAL PERIOD SUMMARY</td>
                                <td class="px-6 py-4 text-right text-gray-400 italic">Total Transactions:</td>
                                <td class="px-6 py-4 text-center font-bold text-xl"><?php echo $totalOverallTransactions; ?></td>
                                <td class="px-6 py-4 text-right text-xl font-bold bg-gray-900 border-l border-gray-700">
                                    <?php echo number_format($grandTotalLiters, 2); ?> L
                                </td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>

                <div class="mt-12 flex justify-between items-end">
                    <div class="text-sm text-gray-400 italic">
                        Report generated: <?php echo date('Y-m-d H:i'); ?>
                    </div>
                    <div class="text-center">
                        <p class="mb-10">Certified by:</p>
                        <div class="border-t border-gray-400 w-64 pt-2 text-sm text-gray-500">Authorized Signature / Stamp</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>