<?php

session_start();


$role = strtoupper($_SESSION["role"]);

if (!isset($_SESSION["staff_code"]) || !isset($_SESSION["phone"])) {
    header("Location: ../../login.php");
}

// if ($role !== "Driver" || $role !== "LOGISTICS") {
//     header("Location: ../../dashboard.php");
// }


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Requests</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'lime': {
                            700: '#4d7c0f'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-br from-lime-50 to-lime-100 min-h-screen">
    <div class="flex">
        <?php
        include "../../components/side.php";
        ?>
        <div class="container mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6 lg:py-8">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-lime-700 text-center mb-2">🔧 Repair Request</h1>
                <p class="text-sm sm:text-base text-gray-600 text-center">Submit and manage multiple repair requests efficiently</p>
            </div>

            <!-- Main Form -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
                <h2 class="text-xl sm:text-2xl font-semibold text-lime-700 mb-4 sm:mb-6 flex items-center">
                    <span class="mr-2">📝</span> Submit Requests
                </h2>

                <form id="repair-form" class="space-y-3 sm:space-y-4">
                    <div id="request-container" class="space-y-3">
                        <div class="request-input flex items-center gap-2 sm:gap-3 p-2 sm:p-3 bg-lime-50 rounded-lg border border-lime-200">
                            <input type="text" name="service[]" placeholder="Enter service description" required
                                class="flex-1 px-3 sm:px-4 py-2 text-sm sm:text-base border border-lime-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-700 focus:border-transparent">
                            <button type="button" onclick="removeRequest(this)"
                                class="text-red-500 hover:text-red-700 text-lg sm:text-xl font-bold transition-colors duration-200 p-1 sm:p-0 min-w-[40px] sm:min-w-auto">❌</button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="button" id="add-btn"
                            class="bg-lime-700 hover:bg-lime-800 text-white px-4 sm:px-6 py-2 sm:py-2 rounded-lg font-semibold transition-colors duration-200 flex items-center justify-center gap-2 text-sm sm:text-base">
                            <span>➕</span> Add Request
                        </button>
                        <button type="submit"
                            class="bg-lime-700 hover:bg-lime-800 text-white px-6 sm:px-8 py-2 sm:py-2 rounded-lg font-semibold transition-colors duration-200 flex items-center justify-center gap-2 text-sm sm:text-base">
                            <span>📤</span> Submit
                        </button>
                    </div>
                </form>

                <div id="response-message" class="mt-4 sm:mt-6 p-3 sm:p-4 rounded-lg hidden text-sm sm:text-base"></div>
            </div>

            <!-- Requests List -->
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 gap-4">
                    <h3 class="text-xl sm:text-2xl font-semibold text-lime-700 flex items-center">
                        <span class="mr-2">📋</span> Submitted Repair Requests
                    </h3>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <button onclick="fetchRequests()"
                            class="bg-lime-700 hover:bg-lime-800 text-white px-3 sm:px-4 py-2 rounded-lg font-semibold transition-colors duration-200 flex items-center justify-center gap-2 text-sm sm:text-base">
                            <span>🔄</span> Refresh
                        </button>
                        <a href="generateRepairPdf.php?action=generate_pdf" target="_blank"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 sm:px-4 py-2 rounded-lg font-semibold transition-colors duration-200 flex items-center justify-center gap-2 text-sm sm:text-base">
                            <span>📄</span> Export PDF
                        </a>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <div class="flex-1">
                        <input type="text" id="search-input" placeholder="Search by user or service..."
                            class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-lime-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-700 focus:border-transparent">
                    </div>
                    <button onclick="searchRequests()"
                        class="bg-lime-700 hover:bg-lime-800 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-sm sm:text-base whitespace-nowrap">
                        🔍 Search
                    </button>
                </div>

                <!-- Requests Table -->
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <div class="min-w-full inline-block align-middle">
                        <table class="w-full border-collapse bg-white rounded-lg overflow-hidden shadow-sm min-w-[640px]">
                            <thead>
                                <tr class="bg-lime-700 text-white">
                                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold text-xs sm:text-sm">#</th>
                                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold text-xs sm:text-sm">👤 Requested By</th>
                                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold text-xs sm:text-sm">🔧 Service</th>
                                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold text-xs sm:text-sm">⏰ Date & Time</th>
                                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold text-xs sm:text-sm">Status</th>
                                </tr>
                            </thead>
                            <tbody id="request-table-body">
                                <!-- Table rows will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <div class="text-gray-600 text-sm sm:text-base text-center sm:text-left">
                        <span id="pagination-info">Showing 0 - 0 of 0 requests</span>
                    </div>
                    <div class="flex flex-wrap justify-center sm:justify-end gap-1 sm:gap-2" id="pagination-controls">
                        <!-- Pagination buttons will be generated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let currentPage = 1;
        let totalPages = 1;
        let allRequests = [];
        let filteredRequests = [];
        const requestsPerPage = 10;

        function fetchRequests() {
            $('#request-table-body').html(`
                <tr>
                    <td colspan="5" class="text-center py-6 sm:py-8">
                        <div class="flex justify-center items-center">
                            <div class="animate-spin rounded-full h-6 w-6 sm:h-8 sm:w-8 border-b-2 border-lime-700"></div>
                            <span class="ml-3 text-lime-700 text-sm sm:text-base">Loading requests...</span>
                        </div>
                    </td>
                </tr>
            `);

            // Uncomment below for real API call
            $.ajax({
                url: '../api/request_repair.php?action=all_requests',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    allRequests = data;
                    filteredRequests = [...allRequests];
                    currentPage = 1;
                    updateTable();
                },
                error: function() {
                    $('#request-table-body').html(`
                        <tr>
                            <td colspan="5" class="text-center py-6 sm:py-8">
                                <div class="text-red-500 text-2xl sm:text-4xl mb-2">⚠️</div>
                                <p class="text-red-700 font-semibold text-sm sm:text-base">Failed to load requests.</p>
                                <p class="text-red-600 text-xs sm:text-sm">Please check your connection and try again.</p>
                            </td>
                        </tr>
                    `);
                }
            });
        }

        function updateTable() {
            const startIndex = (currentPage - 1) * requestsPerPage;
            const endIndex = startIndex + requestsPerPage;
            const pageRequests = filteredRequests.slice(startIndex, endIndex);

            let tableHTML = '';

            if (pageRequests.length === 0) {
                tableHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-8 sm:py-12 text-gray-500">
                            <div class="text-4xl sm:text-6xl mb-2 sm:mb-4">📭</div>
                            <p class="text-base sm:text-lg">No repair requests found.</p>
                            <p class="text-xs sm:text-sm">Submit your first request above to get started!</p>
                        </td>
                    </tr>
                `;
            } else {
                pageRequests.forEach((req, index) => {
                    const globalIndex = startIndex + index + 1;
                    const statusColor = {
                        'Pending': 'bg-yellow-100 text-yellow-800',
                        'In Progress': 'bg-blue-100 text-blue-800',
                        'Completed': 'bg-green-100 text-green-800'
                    };

                    tableHTML += `
                        <tr class="border-b border-gray-200 hover:bg-lime-50 transition-colors duration-200">
                            <td class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-lime-700 text-xs sm:text-sm">${globalIndex}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm">${req.user}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm max-w-[150px] sm:max-w-none truncate sm:whitespace-normal" title="${req.service}">${req.service}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-gray-600 text-xs sm:text-sm">${req.createdAt}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold ${statusColor[req.status] || 'bg-gray-100 text-gray-800'}">
                                    ${req.status || 'Pending'}
                                </span>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#request-table-body').html(tableHTML);
            updatePagination();
        }

        function updatePagination() {
            totalPages = Math.ceil(filteredRequests.length / requestsPerPage);
            const startIndex = (currentPage - 1) * requestsPerPage + 1;
            const endIndex = Math.min(currentPage * requestsPerPage, filteredRequests.length);

            // Update pagination info
            $('#pagination-info').text(`Showing ${startIndex} - ${endIndex} of ${filteredRequests.length} requests`);

            // Generate pagination controls
            let paginationHTML = '';

            // Previous button
            if (currentPage > 1) {
                paginationHTML += `
                    <button onclick="changePage(${currentPage - 1})" 
                        class="px-2 sm:px-3 py-2 bg-lime-700 text-white rounded-lg hover:bg-lime-800 transition-colors duration-200 text-xs sm:text-sm">
                        <span class="hidden sm:inline">← Previous</span>
                        <span class="sm:hidden">←</span>
                    </button>
                `;
            }

            // Page numbers
            const maxVisiblePages = window.innerWidth < 640 ? 3 : 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const isActive = i === currentPage;
                paginationHTML += `
                    <button onclick="changePage(${i})" 
                        class="px-2 sm:px-3 py-2 rounded-lg transition-colors duration-200 text-xs sm:text-sm ${
                            isActive 
                                ? 'bg-lime-700 text-white' 
                                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                        }">
                        ${i}
                    </button>
                `;
            }

            // Next button
            if (currentPage < totalPages) {
                paginationHTML += `
                    <button onclick="changePage(${currentPage + 1})" 
                        class="px-2 sm:px-3 py-2 bg-lime-700 text-white rounded-lg hover:bg-lime-800 transition-colors duration-200 text-xs sm:text-sm">
                        <span class="hidden sm:inline">Next →</span>
                        <span class="sm:hidden">→</span>
                    </button>
                `;
            }

            $('#pagination-controls').html(paginationHTML);
        }

        function changePage(page) {
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                updateTable();
            }
        }

        function searchRequests() {
            const searchTerm = $('#search-input').val().toLowerCase();

            if (searchTerm === '') {
                filteredRequests = [...allRequests];
            } else {
                filteredRequests = allRequests.filter(req =>
                    req.user.toLowerCase().includes(searchTerm) ||
                    req.service.toLowerCase().includes(searchTerm)
                );
            }

            currentPage = 1;
            updateTable();
        }

        // Search on Enter key
        $('#search-input').keypress(function(e) {
            if (e.which == 13) {
                searchRequests();
            }
        });

        // Auto-load on page load
        $(document).ready(function() {
            fetchRequests();
        });

        // Add new input field
        $('#add-btn').click(function() {
            const newInput = `
                <div class="request-input flex items-center gap-2 sm:gap-3 p-2 sm:p-3 bg-lime-50 rounded-lg border border-lime-200">
                    <input type="text" name="service[]" placeholder="Enter service description" required
                        class="flex-1 px-3 sm:px-4 py-2 text-sm sm:text-base border border-lime-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-700 focus:border-transparent">
                    <button type="button" onclick="removeRequest(this)" 
                        class="text-red-500 hover:text-red-700 text-lg sm:text-xl font-bold transition-colors duration-200 p-1 sm:p-0 min-w-[40px] sm:min-w-auto">❌</button>
                </div>`;
            $('#request-container').append(newInput);
        });

        // Remove specific input
        function removeRequest(btn) {
            if ($('.request-input').length > 1) {
                $(btn).closest('.request-input').fadeOut(300, function() {
                    $(this).remove();
                });
            } else {
                showMessage('At least one request field is required!', 'error');
            }
        }

        // Show message function
        function showMessage(message, type) {
            const messageDiv = $('#response-message');
            messageDiv.removeClass('hidden bg-green-50 bg-red-50 text-green-700 text-red-700 border-green-200 border-red-200');

            if (type === 'success') {
                messageDiv.addClass('bg-green-50 text-green-700 border border-green-200');
            } else {
                messageDiv.addClass('bg-red-50 text-red-700 border border-red-200');
            }

            messageDiv.html(`
                <div class="flex items-center gap-2">
                    <span class="text-lg sm:text-xl">${type === 'success' ? '✅' : '❌'}</span>
                    <span class="font-semibold">${message}</span>
                </div>
            `).show();

            setTimeout(() => {
                messageDiv.fadeOut();
            }, 5000);
        }

        // Submit using jQuery AJAX
        $('#repair-form').submit(function(e) {
            e.preventDefault();

            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<span class="animate-spin rounded-full h-3 w-3 sm:h-4 sm:w-4 border-b-2 border-white inline-block mr-2"></span>Submitting...').prop('disabled', true);


            // Uncomment below for real API call
            $.ajax({
                url: '../api/request_repair.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.message) {
                        showMessage(response.message, 'success');
                        // Clear form
                        $('#request-container').html(`
                            <div class="request-input flex items-center gap-2 sm:gap-3 p-2 sm:p-3 bg-lime-50 rounded-lg border border-lime-200">
                                <input type="text" name="service[]" placeholder="Enter service description" required
                                    class="flex-1 px-3 sm:px-4 py-2 text-sm sm:text-base border border-lime-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-700 focus:border-transparent">
                                <button type="button" onclick="removeRequest(this)" 
                                    class="text-red-500 hover:text-red-700 text-lg sm:text-xl font-bold transition-colors duration-200 p-1 sm:p-0 min-w-[40px] sm:min-w-auto">❌</button>
                            </div>
                        `);
                        // Refresh the list
                        fetchRequests();
                    } else {
                        showMessage(response.error || 'An error occurred', 'error');
                    }
                },
                error: function() {
                    showMessage('Something went wrong. Please try again.', 'error');
                },
                complete: function() {
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Update pagination on window resize
        window.addEventListener('resize', function() {
            if (totalPages > 1) {
                updatePagination();
            }
        });
    </script>
</body>

</html>