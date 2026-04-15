<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
require '../connection.php';

// Sync database variable
if (!isset($db) && isset($pdo)) { $db = $pdo; }

// Handle Date Filters (Default to current month)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

$requests = [];
$fuelSummary = [];
$totalSpent = 0;
$totalQty = 0;

// SQL: Fetch every approved transaction individually
$sql = "SELECT * FROM fuel_request 
        WHERE status = 'approved' 
        AND requested_date BETWEEN ? AND ? 
        ORDER BY requested_date DESC";

$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
    
    // Calculate Summary by Fuel Type
    $fType = $row['fuel_type'];
    if (!isset($fuelSummary[$fType])) {
        $fuelSummary[$fType] = ['qty' => 0, 'cost' => 0];
    }
    $fuelSummary[$fType]['qty'] += $row['received_qty'];
    $fuelSummary[$fType]['cost'] += ($row['received_qty'] * $row['price']);
    
    // Global Totals
    $totalQty += $row['received_qty'];
    $totalSpent += ($row['received_qty'] * $row['price']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Request Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="max-w-7xl mx-auto p-4 md:p-8">
        
        <div class="bg-white p-6 rounded-t-xl shadow-sm border-b no-print">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <a href="../dashboard.php" class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                        <span>←</span> Back
                    </a>
                    <h1 class="text-2xl font-bold text-gray-800">Montly Fuel Usage</h1>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow transition flex items-center gap-2">
                        Print Report
                    </button>
                </div>
            </div>

            <form method="GET" class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">From Date</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="w-full border rounded-md p-2">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">To Date</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="w-full border rounded-md p-2">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md font-semibold hover:bg-blue-700">Filter Records</button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-xl overflow-hidden">
            <div class="p-8 text-center hidden show-on-print block">
                <h2 class="text-3xl font-bold uppercase tracking-widest">Operational Fuel Usage Report</h2>
                <p class="text-gray-500">Period: <?php echo date('d M Y', strtotime($start_date)); ?> to <?php echo date('d M Y', strtotime($end_date)); ?></p>
            </div>

            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 border-y">
                    <tr>
                        <th class="px-4 py-3 font-bold uppercase">Date</th>
                        <th class="px-4 py-3 font-bold uppercase">Vehicle/Plate</th>
                        <th class="px-4 py-3 font-bold uppercase">Driver</th>
                        <th class="px-4 py-3 font-bold uppercase text-center">Fuel Type</th>
                        <th class="px-4 py-3 font-bold uppercase text-right">Qty (L)</th>
                        <th class="px-4 py-3 font-bold uppercase text-right">Unit Price</th>
                        <th class="px-4 py-3 font-bold uppercase text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (count($requests) > 0): ?>
                        <?php foreach ($requests as $req): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap"><?php echo $req['requested_date']; ?></td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-gray-800"><?php echo htmlspecialchars($req['plate_number']); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($req['vehicle_type']); ?></div>
                                </td>
                                <td class="px-4 py-4"><?php echo htmlspecialchars($req['driver_name']); ?></td>
                                <td class="px-4 py-4 text-center">
                                    <span class="bg-gray-100 px-2 py-1 rounded text-xs font-mono uppercase"><?php echo $req['fuel_type']; ?></span>
                                </td>
                                <td class="px-4 py-4 text-right font-medium"><?php echo number_format($req['received_qty'], 2); ?></td>
                                <td class="px-4 py-4 text-right text-gray-500"><?php echo number_format($req['price'], 2); ?></td>
                                <td class="px-4 py-4 text-right font-bold"><?php echo number_format($req['received_qty'] * $req['price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-4 py-20 text-center text-gray-400">No approved fuel requests found for this period.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

                <?php if (count($requests) > 0): ?>
                <tbody class="bg-blue-50/50 border-t-2 border-blue-200">
                    <tr>
                        <td colspan="7" class="px-4 py-2 text-[10px] font-black uppercase text-blue-400 tracking-tighter">Summary by Fuel Type</td>
                    </tr>
                    <?php foreach ($fuelSummary as $type => $data): ?>
                    <tr>
                        <td colspan="4" class="px-4 py-1 text-right font-bold text-gray-600 italic uppercase">Total <?php echo $type; ?>:</td>
                        <td class="px-4 py-1 text-right font-bold"><?php echo number_format($data['qty'], 2); ?> L</td>
                        <td class="px-4 py-1"></td>
                        <td class="px-4 py-1 text-right font-bold"><?php echo number_format($data['cost'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot class="bg-gray-900 text-white">
                    <tr class="text-base">
                        <td colspan="2" class="px-4 py-4 font-bold uppercase">Grand Totals</td>
                        <td colspan="2" class="px-4 py-4 text-right text-gray-400 italic">Total Transactions: <?php echo count($requests); ?></td>
                        <td class="px-4 py-4 text-right font-bold border-l border-gray-700"><?php echo number_format($totalQty, 2); ?> L</td>
                        <td class="px-4 py-4"></td>
                        <td class="px-4 py-4 text-right font-bold bg-blue-900"><?php echo number_format($totalSpent, 2); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>

        <div class="mt-12 grid grid-cols-2 gap-20">
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-16 italic">Prepared By:</p>
                <div class="border-t border-black pt-2 font-bold uppercase text-xs">Logistics / Head Of Logistics</div>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-16 italic">Approved By:</p>
                <div class="border-t border-black pt-2 font-bold uppercase text-xs">CEO / DCEO</div>
            </div>
        </div>

        <p class="mt-8 text-center text-[10px] text-gray-400 uppercase tracking-widest no-print">End of Generated Report</p>
    </div>

</body>
</html>