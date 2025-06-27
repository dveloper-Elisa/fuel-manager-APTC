<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Service Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print, button, aside {
                display: none !important;
            }
            body {
                padding: 0;
                margin: 0;
                font-size: 11pt;
            }
            table {
                width: 100%;
                font-size: 10pt;
            }
           #heading{
            text-align: center;
            align-items: center;
           }
           #logo{
            display: block;
            margin: 0 auto;
            width: 500px; 
            height: auto; 
           }

        }
    </style>
</head>
<body class="bg-gray-50 ">

    <div class="bg-white h-screen rounded-lg flex flex-row">
    <?php
    session_start();
    include "./side.php"
    ?>


    <div class="max-w-7xl mx-auto shadow-md p-6 ">
        <img src="../../img/image.png" alt="Logo Image" srcset="" id="logo" class="hidden mx-auto mb-4 items-center">
        <div class="flex justify-between items-center mb-6">
            <h1 id='heading' class="text-3xl font-bold text-gray-800">Garage Service Report</h1>
            <button onclick="window.print()" class="no-print bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 ease-in-out">
                Print Report
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">License Plate</th>
                        <!-- <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Make</th> -->
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Service Type</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Part Name</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Quantity</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Unit Price</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Parts Cost</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Total</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">Date</th>
                    </tr>
                </thead>
                <tbody id="report-data" class="bg-white divide-y divide-gray-200">
                    <!-- Data will be inserted here by JavaScript -->
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../api/get_garage_report.php')
                .then(response => {
                    if (!response.ok) {
                        console.log(response)
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const tableBody = document.getElementById('report-data');
                    
                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="13" class="px-3 py-2 text-center text-gray-500">No records found</td></tr>';
                        return;
                    }
                    
                    data.forEach(record => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-zinc-300';
                        row.innerHTML = `
                            <td class="px-3 py-2 whitespace-nowrap border border-gray-200">${record.license_plate}</td>
                            <td class="px-3 py-2 whitespace-nowrap border border-gray-200 capitalize">${record.service_type}</td>
                            <td class="px-3 py-2 whitespace-nowrap border border-gray-200">${record.part_name}</td>
                            <td class="px-3 py-2 whitespace-nowrap border border-gray-200">${record.quantity}</td>
                            <td class="px-3 py-2 whitespace-nowrap border border-gray-200">${record.unit_price} RWF</td>
                            <td class="px-3 py-2 whitespace-nowrap border border-gray-200">${record.total_parts_cost} RWF</td>
                            <td class="px-3 py-2 whitespace-nowrap border border-gray-200 font-medium">${record.grand_total} RWF</td>
                            <td class="px-3 py-2 whitespace-nowrap border border-gray-200">${new Date(record.service_date).toLocaleDateString()}</td>
                        `;
                        tableBody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    document.getElementById('report-data').innerHTML = 
                        '<tr><td colspan="13" class="px-6 py-4 text-center text-red-500">Error loading data. Please try again later.</td></tr>';
                });
        });
    </script>
</body>
</html>