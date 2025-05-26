<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Service Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div>
        <h1>Garage Service Form</h1>

        <!-- Service Form -->
        <form id="service-form">
            <!-- Vehicle Information -->
            <fieldset>
                <legend>Vehicle Information</legend>
                <label>License Plate: <input type="text" name="license_plate" required></label><br>
                <label>Make: <input type="text" name="make" required></label><br>
                <label>Model: <input type="text" name="model" required></label><br>
            </fieldset>

            <!-- Services -->
            <fieldset>
                <legend>Services Performed</legend>
                <button type="button" id="add-service-btn">Add Service</button>
                <div id="services-container">
                    <div class="service-item">
                        <h4>Service #1</h4>
                        <button type="button" onclick="removeService(this)">Remove</button><br>
                        <label>Service Type: <input type="text" name="service_type[]" required></label><br>
                        <label>Labor Hours: <input type="number" name="labor_hours[]" step="0.5" min="0" required onchange="calculateTotals()"></label><br>
                        <label>Labor Rate ($/hr): <input type="number" name="labor_rate[]" step="0.01" min="0" value="85.00" required onchange="calculateTotals()"></label><br>
                        <label>Description: <textarea name="service_description[]" required></textarea></label><br>
                    </div>
                </div>
            </fieldset>

            <!-- Parts -->
            <fieldset>
                <legend>Parts & Materials</legend>
                <button type="button" id="add-part-btn">Add Part</button>
                <div id="parts-container">
                    <div class="part-item">
                        <h4>Part #1</h4>
                        <button type="button" onclick="removePart(this)">Remove</button><br>
                        <label>Part Name: <input type="text" name="part_name[]" required></label><br>
                        <label>Quantity: <input type="number" name="part_quantity[]" min="1" value="1" required onchange="calculatePartTotal(this)"></label><br>
                        <label>Unit Price ($): <input type="number" name="part_price[]" step="0.01" min="0" required onchange="calculatePartTotal(this)"></label><br>
                        <label>Total ($): <input type="number" name="part_total[]" step="0.01" readonly></label><br>
                    </div>
                </div>
            </fieldset>

            <!-- Summary -->
            <fieldset>
                <legend>Cost Summary</legend>
                <p>Total Labor: <span id="total-labor">$0.00</span></p>
                <p>Total Parts: <span id="total-parts">$0.00</span></p>
                <p>Tax (8.5%): <span id="total-tax">$0.00</span></p>
                <p><strong>Grand Total: <span id="grand-total">$0.00</span></strong></p>
            </fieldset>

            <!-- Notes -->
            <fieldset>
                <legend>Additional Notes</legend>
                <textarea name="notes" rows="4" cols="50"></textarea>
            </fieldset>

            <!-- Buttons -->
            <button type="submit">Save Record</button>
            <button type="button" onclick="generateInvoice()">Generate Invoice</button>
            <button type="button" onclick="resetForm()">Reset Form</button>
        </form>

        <!-- Message Area -->
        <div id="response-message" style="display:none; padding:10px; margin:10px 0;"></div>

        <!-- Service Records -->
        <h2>Service Records</h2>
        <button onclick="loadServiceRecords()">Refresh</button>
        <div id="service-records-table">
            <p>No service records found.</p>
        </div>

        <!-- Modal -->
        <div id="service-detail-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
            <div style="background:white; margin:50px auto; padding:20px; width:80%; max-width:600px;">
                <span onclick="closeModal()" style="float:right; cursor:pointer; font-size:20px;">&times;</span>
                <h3>Service Record Details</h3>
                <div id="service-detail-content"></div>
            </div>
        </div>
    </div>

    <script>
        let serviceCounter = 1;
        let partCounter = 1;
        const TAX_RATE = 0.085;
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
                <div class="service-item">
                    <h4>Service #${serviceCounter}</h4>
                    <button type="button" onclick="removeService(this)">Remove</button><br>
                    <label>Service Type: <input type="text" name="service_type[]" required></label><br>
                    <label>Labor Hours: <input type="number" name="labor_hours[]" step="0.5" min="0" required onchange="calculateTotals()"></label><br>
                    <label>Labor Rate ($/hr): <input type="number" name="labor_rate[]" step="0.01" min="0" value="85.00" required onchange="calculateTotals()"></label><br>
                    <label>Description: <textarea name="service_description[]" required></textarea></label><br>
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
                <div class="part-item">
                    <h4>Part #${partCounter}</h4>
                    <button type="button" onclick="removePart(this)">Remove</button><br>
                    <label>Part Name: <input type="text" name="part_name[]" required></label><br>
                    <label>Quantity: <input type="number" name="part_quantity[]" min="1" value="1" required onchange="calculatePartTotal(this)"></label><br>
                    <label>Unit Price ($): <input type="number" name="part_price[]" step="0.01" min="0" required onchange="calculatePartTotal(this)"></label><br>
                    <label>Total ($): <input type="number" name="part_total[]" step="0.01" readonly></label><br>
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
            let totalLabor = 0;
            let totalParts = 0;

            $('.service-item').each(function() {
                const hours = parseFloat($(this).find('input[name="labor_hours[]"]').val()) || 0;
                const rate = parseFloat($(this).find('input[name="labor_rate[]"]').val()) || 0;
                totalLabor += hours * rate;
            });

            $('.part-item').each(function() {
                const quantity = parseFloat($(this).find('input[name="part_quantity[]"]').val()) || 0;
                const price = parseFloat($(this).find('input[name="part_price[]"]').val()) || 0;
                const partTotal = quantity * price;
                $(this).find('input[name="part_total[]"]').val(partTotal.toFixed(2));
                totalParts += partTotal;
            });

            const subtotal = totalLabor + totalParts;
            const tax = subtotal * TAX_RATE;
            const grandTotal = subtotal + tax;

            $('#total-labor').text('$' + totalLabor.toFixed(2));
            $('#total-parts').text('$' + totalParts.toFixed(2));
            $('#total-tax').text('$' + tax.toFixed(2));
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

        function saveServiceRecord() {
            const formData = collectFormData();
            if (!validateFormData(formData)) {
                showMessage('Please fill in all required fields.', 'error');
                return;
            }

            $.ajax({
                url: '../api/save_service.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.status === 'success') {
                        showMessage('Service record saved successfully!', 'success');
                        resetForm();
                        loadServiceRecords();
                    } else {
                        showMessage('Error: ' + response.message, 'error');
                    }
                },
                error: function() {
                    showMessage('Failed to connect to server.', 'error');
                }
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
                total_labor_cost: 0,
                total_parts_cost: 0,
                tax_amount: 0,
                grand_total: 0
            };

            $('.service-item').each(function() {
                const service = {
                    service_type: $(this).find('input[name="service_type[]"]').val().trim(),
                    service_description: $(this).find('textarea[name="service_description[]"]').val().trim(),
                    labor_hours: parseFloat($(this).find('input[name="labor_hours[]"]').val()) || 0,
                    labor_rate: parseFloat($(this).find('input[name="labor_rate[]"]').val()) || 0
                };

                if (service.service_type && service.service_description) {
                    data.services.push(service);
                    data.total_labor_cost += service.labor_hours * service.labor_rate;
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

            const subtotal = data.total_labor_cost + data.total_parts_cost;
            data.tax_amount = subtotal * TAX_RATE;
            data.grand_total = subtotal + data.tax_amount;

            return data;
        }

        function validateFormData(data) {
            return data.license_plate && data.make && data.model && data.services.length > 0;
        }

        function loadServiceRecords() {
            displayServiceRecords(serviceRecords);
        }

        function displayServiceRecords(records) {
            if (!records || records.length === 0) {
                $('#service-records-table').html('<p>No service records found.</p>');
                return;
            }

            let tableHTML = '<table border="1"><tr><th>Date</th><th>Vehicle</th><th>Services</th><th>Total</th><th>Actions</th></tr>';

            records.forEach(function(record) {
                const serviceDate = new Date(record.service_date).toLocaleDateString();
                const servicesList = record.services.map(s => s.service_type).join(', ');

                tableHTML += `<tr>
                    <td>${serviceDate}</td>
                    <td>${record.license_plate}<br>${record.make} ${record.model}</td>
                    <td>${servicesList}</td>
                    <td>$${parseFloat(record.grand_total).toFixed(2)}</td>
                    <td>
                        <button onclick="viewServiceDetails(${record.id})">View</button>
                        <button onclick="generateInvoiceForRecord(${record.id})">Invoice</button>
                    </td>
                </tr>`;
            });

            tableHTML += '</table>';
            $('#service-records-table').html(tableHTML);
        }

        function viewServiceDetails(serviceRecordId) {
            const record = serviceRecords.find(r => r.id === serviceRecordId);
            if (!record) return;

            let content = `
                <p><strong>Date:</strong> ${new Date(record.service_date).toLocaleDateString()}</p>
                <p><strong>Vehicle:</strong> ${record.license_plate} - ${record.make} ${record.model}</p>
                <h4>Services:</h4>
                <ul>`;

            record.services.forEach(service => {
                content += `<li>${service.service_type} - ${service.labor_hours}h @ $${service.labor_rate}/hr = $${(service.labor_hours * service.labor_rate).toFixed(2)}<br>
                    <em>${service.service_description}</em></li>`;
            });

            content += '</ul>';

            if (record.parts.length > 0) {
                content += '<h4>Parts:</h4><ul>';
                record.parts.forEach(part => {
                    content += `<li>${part.part_name} - Qty: ${part.quantity} @ $${part.unit_price} = $${(part.quantity * part.unit_price).toFixed(2)}</li>`;
                });
                content += '</ul>';
            }

            content += `
                <p><strong>Labor Total:</strong> $${record.total_labor_cost.toFixed(2)}</p>
                <p><strong>Parts Total:</strong> $${record.total_parts_cost.toFixed(2)}</p>
                <p><strong>Tax:</strong> $${record.tax_amount.toFixed(2)}</p>
                <p><strong>Grand Total:</strong> $${record.grand_total.toFixed(2)}</p>`;

            if (record.notes) {
                content += `<p><strong>Notes:</strong> ${record.notes}</p>`;
            }

            $('#service-detail-content').html(content);
            $('#service-detail-modal').show();
        }

        function closeModal() {
            $('#service-detail-modal').hide();
        }

        function generateInvoice() {
            const formData = collectFormData();
            if (!validateFormData(formData)) {
                alert('Please fill in all required fields before generating invoice.');
                return;
            }
            generateInvoiceContent(formData);
        }

        function generateInvoiceForRecord(serviceRecordId) {
            const record = serviceRecords.find(r => r.id === serviceRecordId);
            if (record) {
                generateInvoiceContent(record);
            }
        }

        function generateInvoiceContent(data) {
            let invoiceContent = `
                <h2>SERVICE INVOICE</h2>
                <p><strong>Date:</strong> ${data.service_date ? new Date(data.service_date).toLocaleDateString() : new Date().toLocaleDateString()}</p>
                <p><strong>Vehicle:</strong> ${data.license_plate} - ${data.make} ${data.model}</p>
                
                <h3>Services Performed:</h3>
                <table border="1">
                    <tr><th>Service</th><th>Hours</th><th>Rate</th><th>Amount</th></tr>`;

            data.services.forEach(service => {
                const amount = service.labor_hours * service.labor_rate;
                invoiceContent += `<tr>
                    <td>${service.service_type}<br><small>${service.service_description}</small></td>
                    <td>${service.labor_hours}</td>
                    <td>$${service.labor_rate.toFixed(2)}</td>
                    <td>$${amount.toFixed(2)}</td>
                </tr>`;
            });

            invoiceContent += '</table>';

            if (data.parts && data.parts.length > 0) {
                invoiceContent += `
                    <h3>Parts & Materials:</h3>
                    <table border="1">
                        <tr><th>Part</th><th>Qty</th><th>Price</th><th>Amount</th></tr>`;

                data.parts.forEach(part => {
                    const amount = part.quantity * part.unit_price;
                    invoiceContent += `<tr>
                        <td>${part.part_name}</td>
                        <td>${part.quantity}</td>
                        <td>$${part.unit_price.toFixed(2)}</td>
                        <td>$${amount.toFixed(2)}</td>
                    </tr>`;
                });

                invoiceContent += '</table>';
            }

            invoiceContent += `
                <h3>Summary:</h3>
                <p>Labor Total: $${data.total_labor_cost.toFixed(2)}</p>
                <p>Parts Total: $${data.total_parts_cost.toFixed(2)}</p>
                <p>Tax (8.5%): $${data.tax_amount.toFixed(2)}</p>
                <p><strong>Grand Total: $${data.grand_total.toFixed(2)}</strong></p>`;

            if (data.notes) {
                invoiceContent += `<p><strong>Notes:</strong> ${data.notes}</p>`;
            }

            const newWindow = window.open('', '_blank');
            newWindow.document.write(`
                <html>
                    <head><title>Service Invoice</title></head>
                    <body style="font-family: Arial, sans-serif;">${invoiceContent}</body>
                </html>
            `);
            newWindow.document.close();
            newWindow.print();
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