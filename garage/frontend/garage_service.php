<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Service Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<div class="container flex">
    <?php
    session_start();
    include "./side.php"
    ?>
    <div class="w-full md:w-3/4 p-4">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Garage Service Form</h1>

        <!-- Service Form -->
        <form id="service-form" class="bg-white p-6 rounded-lg shadow-md">
            <!-- Vehicle Information -->
            <fieldset class="mb-6 p-4 border border-gray-200 rounded">
                <legend class="text-lg font-semibold px-2 text-gray-700">Vehicle Information</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">License Plate:</label>
                        <input type="text" name="license_plate" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Make:</label>
                        <input type="text" name="make" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model:</label>
                        <input type="text" name="model" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </fieldset>

            <!-- Services -->
            <fieldset class="mb-6 p-4 border border-gray-200 rounded">
                <legend class="text-lg font-semibold px-2 text-gray-700">Services Performed</legend>
                <button type="button" id="add-service-btn" 
                        class="mb-4 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md shadow-sm">
                    Add Service
                </button>
                <div id="services-container">
                    <div class="service-item mb-4 p-4 bg-gray-50 rounded border border-gray-200">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-medium text-gray-700">Service #1</h4>
                            <button type="button" onclick="removeService(this)" 
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                Remove
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Service Type:</label>
                                <input type="text" name="service_type[]" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
                                <textarea name="service_description[]" required rows="3"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Parts -->
            <fieldset class="mb-6 p-4 border border-gray-200 rounded">
                <legend class="text-lg font-semibold px-2 text-gray-700">Parts & Materials</legend>
                <button type="button" id="add-part-btn" 
                        class="mb-4 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md shadow-sm">
                    Add Part
                </button>
                <div id="parts-container">
                    <div class="part-item mb-4 p-4 bg-gray-50 rounded border border-gray-200">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-medium text-gray-700">Part #1</h4>
                            <button type="button" onclick="removePart(this)" 
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                Remove
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Part Name:</label>
                                <input type="text" name="part_name[]" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity:</label>
                                <input type="number" name="part_quantity[]" min="1" value="1" required onchange="calculatePartTotal(this)"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price ($):</label>
                                <input type="number" name="part_price[]" step="0.01" min="0" required onchange="calculatePartTotal(this)"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total ($):</label>
                                <input type="number" name="part_total[]" step="0.01" readonly
                                       class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md">
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Summary -->
            <fieldset class="mb-6 p-4 border border-gray-200 rounded">
                <legend class="text-lg font-semibold px-2 text-gray-700">Cost Summary</legend>
                <div class="space-y-2">
                    <p class="flex justify-between"><span>Total Parts:</span> <span id="total-parts" class="font-medium">Rwf0.00</span></p>
                    <p class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
                        <span>Grand Total:</span> <span id="grand-total">Rwf0.00</span>
                    </p>
                </div>
            </fieldset>

            <!-- Notes -->
            <fieldset class="mb-6 p-4 border border-gray-200 rounded">
                <legend class="text-lg font-semibold px-2 text-gray-700">Additional Notes</legend>
                <textarea name="notes" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </fieldset>

            <!-- Buttons -->
            <div class="flex flex-wrap gap-4">
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-md shadow-sm font-medium">
                    Save Record
                </button>
            </div>
        </form>

        <!-- Message Area -->
        <div id="response-message" class="hidden rounded-md p-4 my-4"></div>

        <!-- Service Records -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Service Records</h2>
                <button onclick="loadServiceRecords()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-4 rounded-md shadow-sm text-sm">
                    Refresh
                </button>
            </div>
            <div id="service-records-table" class="bg-white rounded-lg shadow overflow-hidden">
                <p class="p-4 text-gray-600">No service records found.</p>
            </div>
        </div>

        <!-- Modal -->
        <div id="service-detail-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Service Record Details</h3>
                        <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                    </div>
                    <div id="service-detail-content" class="space-y-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        // (Keep all the JavaScript code the same as in your original)
        let serviceCounter = 1;
        let partCounter = 1;
        let serviceRecords = [];

        $(document).ready(function() {
            calculateTotals();
            loadServiceRecords();

            $('#add-service-btn').click(addService);
            $('#add-part-btn').click(addPart);
            $('#service-form').submit(function(e) {
                e.preventDefault();
                saveServiceRecord();
            });
        });

        function addService() {
            serviceCounter++;
            const serviceHTML = `
                <div class="service-item mb-4 p-4 bg-gray-50 rounded border border-gray-200">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium text-gray-700">Service #${serviceCounter}</h4>
                        <button type="button" onclick="removeService(this)" 
                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Remove
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Service Type:</label>
                            <input type="text" name="service_type[]" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
                            <textarea name="service_description[]" required rows="3"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </div>
                </div>
            `;
            $('#services-container').append(serviceHTML);
        }

        function removeService(button) {
            if ($('.service-item').length > 1) {
                $(button).closest('.service-item').remove();
                calculateTotals();
            } else {
                alert('At least one service is required.');
            }
        }

        function addPart() {
            partCounter++;
            const partHTML = `
                <div class="part-item mb-4 p-4 bg-gray-50 rounded border border-gray-200">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium text-gray-700">Part #${partCounter}</h4>
                        <button type="button" onclick="removePart(this)" 
                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Remove
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Part Name:</label>
                            <input type="text" name="part_name[]" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity:</label>
                            <input type="number" name="part_quantity[]" min="1" value="1" required onchange="calculatePartTotal(this)"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price ($):</label>
                            <input type="number" name="part_price[]" step="0.01" min="0" required onchange="calculatePartTotal(this)"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total ($):</label>
                            <input type="number" name="part_total[]" step="0.01" readonly
                                   class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md">
                        </div>
                    </div>
                </div>
            `;
            $('#parts-container').append(partHTML);
        }

        function removePart(button) {
            $(button).closest('.part-item').remove();
            calculateTotals();
        }

        function calculatePartTotal(input) {
            const partItem = $(input).closest('.part-item');
            const quantity = parseFloat(partItem.find('input[name="part_quantity[]"]').val()) || 0;
            const price = parseFloat(partItem.find('input[name="part_price[]"]').val()) || 0;
            const total = quantity * price;

            partItem.find('input[name="part_total[]"]').val(total.toFixed(2));
            calculateTotals();
        }

        function calculateTotals() {
            let totalParts = 0;


            $('.part-item').each(function() {
                const quantity = parseFloat($(this).find('input[name="part_quantity[]"]').val()) || 0;
                const price = parseFloat($(this).find('input[name="part_price[]"]').val()) || 0;
                const partTotal = quantity * price;
                $(this).find('input[name="part_total[]"]').val(partTotal.toFixed(2));
                totalParts += partTotal;
            });

            const subtotal = totalParts;
            const grandTotal = subtotal;

            $('#total-parts').text('$' + totalParts.toFixed(2));
            $('#grand-total').text('$' + grandTotal.toFixed(2));
        }

        // function saveServiceRecord() {
        //     showMessage('Saving service record...', 'info');

        //     const formData = collectFormData();

        //     if (!validateFormData(formData)) {
        //         showMessage('Please fill in all required fields.', 'error');
        //         return;
        //     }

        //     formData.id = Date.now();
        //     formData.service_date = new Date().toISOString();
        //     serviceRecords.push(formData);

        //     showMessage('Service record saved successfully!', 'success');
        //     resetForm();
        //     loadServiceRecords();
        // }


        /**
         * Save service record to the database
         */

         function saveServiceRecord() {
    showMessage('Saving service record...', 'info');

    const formData = collectFormData();

    if (!validateFormData(formData)) {
        showMessage('Please fill in all required fields.', 'error');
        return;
    }

    formData.service_date = new Date().toISOString(); // Ensure date format
    // Send data to PHP backend
    fetch('../api/save_service.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showMessage('Service record saved successfully!', 'success');
            resetForm();
            loadServiceRecords();
        } else {
            showMessage(data.message || 'Failed to save record.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while saving the record.', 'error');
    });
}




        function collectFormData() {
            const data = {
                license_plate: $('input[name="license_plate"]').val().trim(),
                make: $('input[name="make"]').val().trim(),
                model: $('input[name="model"]').val().trim(),
                notes: $('textarea[name="notes"]').val().trim(),
                services: [],
                parts: [],
                total_parts_cost: 0,
                grand_total: 0
            };

            $('.service-item').each(function() {
                const service = {
                    service_type: $(this).find('input[name="service_type[]"]').val().trim(),
                    service_description: $(this).find('textarea[name="service_description[]"]').val().trim(),
                    
                };

                if (service.service_type && service.service_description) {
                    data.services.push(service);
                }
            });

            $('.part-item').each(function() {
                const part = {
                    part_name: $(this).find('input[name="part_name[]"]').val().trim(),
                    quantity: parseInt($(this).find('input[name="part_quantity[]"]').val()) || 0,
                    unit_price: parseFloat($(this).find('input[name="part_price[]"]').val()) || 0
                };

                if (part.part_name && part.quantity > 0) {
                    data.parts.push(part);
                    data.total_parts_cost += part.quantity * part.unit_price;
                }
            });

            const subtotal = data.total_parts_cost;
            data.grand_total = subtotal;

            return data;
        }

                function resetForm() {
            $('#service-form')[0].reset();
            $('#services-container').html(`
                <div class="service-item">
                    <h4>Service #1</h4>
                    <button type="button" onclick="removeService(this)">Remove</button><br>
                    <label>Service Type: <input type="text" name="service_type[]" required></label><br>
                    <label>Labor Hours: <input type="number" name="labor_hours[]" step="0.5" min="0" required onchange="calculateTotals()"></label><br>
                    <label>Labor Rate ($/hr): <input type="number" name="labor_rate[]" step="0.01" min="0" value="85.00" required onchange="calculateTotals()"></label><br>
                    <label>Description: <textarea name="service_description[]" required></textarea></label><br>
                </div>
            `);
            $('#parts-container').html(`
                <div class="part-item">
                    <h4>Part #1</h4>
                    <button type="button" onclick="removePart(this)">Remove</button><br>
                    <label>Part Name: <input type="text" name="part_name[]" required></label><br>
                    <label>Quantity: <input type="number" name="part_quantity[]" min="1" value="1" required onchange="calculatePartTotal(this)"></label><br>
                    <label>Unit Price ($): <input type="number" name="part_price[]" step="0.01" min="0" required onchange="calculatePartTotal(this)"></label><br>
                    <label>Total ($): <input type="number" name="part_total[]" step="0.01" readonly></label><br>
                </div>
            `);
            serviceCounter = 1;
            partCounter = 1;
            calculateTotals();
            showMessage('Form reset successfully!', 'success');
        }
        

        function validateFormData(data) {
            return data.license_plate && data.make && data.model && data.services.length > 0;
        }

        function loadServiceRecords() {
            displayServiceRecords(serviceRecords);
        }

        function displayServiceRecords(records) {
            if (!records || records.length === 0) {
                $('#service-records-table').html('<p class="p-4 text-gray-600">No service records found.</p>');
                return;
            }

            let tableHTML = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Services</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
            `;

            records.forEach(function(record) {
                const serviceDate = new Date(record.service_date).toLocaleDateString();
                const servicesList = record.services.map(s => s.service_type).join(', ');

                tableHTML += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${serviceDate}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${record.license_plate}</div>
                            <div class="text-sm text-gray-500">${record.make} ${record.model}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">${servicesList}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">$${parseFloat(record.grand_total).toFixed(2)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewServiceDetails(${record.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
                        </td>
                    </tr>
                `;
            });

            tableHTML += `
                        </tbody>
                    </table>
                </div>
            `;
            $('#service-records-table').html(tableHTML);
        }

        function viewServiceDetails(serviceRecordId) {
            const record = serviceRecords.find(r => r.id === serviceRecordId);
            if (!record) return;

            let content = `
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Date</p>
                            <p class="font-medium">${new Date(record.service_date).toLocaleDateString()}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Vehicle</p>
                            <p class="font-medium">${record.license_plate} - ${record.make} ${record.model}</p>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Services:</h4>
                        <div class="space-y-3">
            `;

            record.services.forEach(service => {
                content += `
                    <div class="p-3 bg-gray-50 rounded">
                        <div class="flex justify-between">
                            <span class="font-medium">${service.service_type}</span>
                        </div>
                        <div class="text-sm text-gray-600 mt-1">${service.service_description}</div>
                        
                    </div>
                `;
            });

            content += `
                        </div>
                    </div>
            `;

            if (record.parts.length > 0) {
                content += `
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Parts:</h4>
                        <div class="space-y-3">
                `;
                record.parts.forEach(part => {
                    content += `
                        <div class="p-3 bg-gray-50 rounded">
                            <div class="flex justify-between">
                                <span class="font-medium">${part.part_name}</span>
                                <span>$${(part.quantity * part.unit_price).toFixed(2)}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Qty: ${part.quantity} @ $${part.unit_price.toFixed(2)}
                            </div>
                        </div>
                    `;
                });
                content += `
                        </div>
                    </div>
                `;
            }

            content += `
                    <div class="border-t pt-4 space-y-2">
                        
                        <div class="flex justify-between">
                            <span>Parts Total:</span>
                            <span class="font-medium">$${record.total_parts_cost.toFixed(2)}</span>
                        </div>
                        
                        <div class="flex justify-between text-lg font-bold pt-2">
                            <span>Grand Total:</span>
                            <span>$${record.grand_total.toFixed(2)}</span>
                        </div>
                    </div>
            `;

            if (record.notes) {
                content += `
                    <div>
                        <h4 class="font-medium text-gray-700 mb-1">Notes:</h4>
                        <p class="text-gray-600 whitespace-pre-line">${record.notes}</p>
                    </div>
                `;
            }

            content += `</div>`;
            $('#service-detail-content').html(content);
            $('#service-detail-modal').removeClass('hidden').addClass('flex');
        }

        function closeModal() {
            $('#service-detail-modal').removeClass('flex').addClass('hidden');
        }


        function showMessage(message, type) {
            const messageDiv = $('#response-message');
            messageDiv.text(message);
            messageDiv.css({
                'background-color': type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1',
                'color': type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#0c5460',
                'border': '1px solid ' + (type === 'success' ? '#c3e6cb' : type === 'error' ? '#f5c6cb' : '#bee5eb')
            });
            messageDiv.show();

            setTimeout(() => {
                messageDiv.hide();
            }, 3000);
        }
    </script>
</body>

</html>