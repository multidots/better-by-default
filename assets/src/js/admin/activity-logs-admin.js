jQuery(document).ready(function ($) {
    // Check if the activity logs container exists on the page before proceeding
    if ($(document).find('.activity-logs-container').length > 0) {
        activity_logs_table_data(); // Load the activity logs table data
    }

    /**
     * Handle the click event for flushing log data
     */
    $(document).on("click", "button#activity-data-flush-btn", function () {
        var selectedRange = $('#flush-log-range').val(); // Get the selected log range

        // Custom message for the user based on selected range
        var confirmMessage;
        if (selectedRange === '30') {
            confirmMessage = "Please ensure that the last 30 days of data is retained, while all older log data is removed. Do you want to flush log data?";
        } else if (selectedRange === '7') {
            confirmMessage = "Please ensure that the last 7 days of data is retained, while all older log data is removed. Do you want to flush log data?";
        } else if (selectedRange === '15') {
            confirmMessage = "Please ensure that the last 15 days of data is retained, while all older log data is removed. Do you want to flush log data?";
        } else {
            confirmMessage = "All log data will be removed. Do you want to flush all log data?";
        }

        // Confirm if the user really wants to flush the logs
        if (confirm(confirmMessage) === true) {
            $.ajax({
                type: 'POST',
                url: betterByDefaultActivityLogs.ajaxUrl, // The AJAX URL provided by WordPress
                data: {
                    action: 'activity_logs_data_flush', // The action tied to the backend handler
                    range: selectedRange, // Send the selected range value
                    security: betterByDefaultActivityLogs.ajax_nonce, // Send the nonce for security
                },
                beforeSend: function () {
                    // Show a loader while the logs are being flushed
                    $(document).find('.activity-logs-container').html('<div class="activity-logs-loader-three-main"><div class="activity-logs-loader-three"><span></span><span></span></div></div>');
                },
                success: function (response) {
                    // Check for successful response
                    if (response.success === 1) {
                        // Reload the logs table after successful flush
                        activity_logs_table_data();
                    } else if (response.success === 2) {
                        // Notify the user if no log data is found for the selected range
                        if (selectedRange === '7') {
                            alert('No logs older than the last 7 days were found.');
                        } else if (selectedRange === '15') {
                            alert('No logs older than the last 15 days were found.');
                        } else if (selectedRange === '30') {
                            alert('No logs older than the last 30 days were found.');
                        }
                        activity_logs_table_data(); // Reload the table even if no data is found
                    } else {
                        alert('Error: Could not flush the logs.');
                    }
                },
                error: function (xhr, status, error) {
                    // Handle any errors that occur during the request
                    console.error('Flush log AJAX request failed:', status, error);
                    alert('An error occurred while flushing the logs. Please try again.');
                }
            });
        }
    });

    /**
     * Load the activity logs data into the table
     */
    function activity_logs_table_data() {
        $.ajax({
            type: 'POST',
            url: betterByDefaultActivityLogs.ajaxUrl, // The AJAX URL provided by WordPress
            data: {
                action: 'activity_logs_data', // The action tied to the backend handler
                security: betterByDefaultActivityLogs.ajax_nonce, // Send the nonce for security
            },
            beforeSend: function () {
                // Show a loader while the table data is being loaded
                $(document).find('.activity-logs-container').html('<div class="activity-logs-loader-three-main"><div class="activity-logs-loader-three"><span></span><span></span></div></div>');
            },
            success: function (response) {
                // Check for successful response
                if (response.success === 1) {
                    // Populate the container with the activity logs table HTML
                    $(document).find('.activity-logs-container').html(response.content);

                    // Initialize DataTables if the activity logs table is present
                    if ($(document).find('.activity-logs-table').length > 0) {
                        // var table = $(document).find('.activity-logs-table').DataTable({
                        //     "order": [[4, "desc"]], // Order by the "Logged Time" column (descending)
                        //     columnDefs: [
                        //         { width: '20%', targets: 0 }, // Set the width of the first column
                        //         //{ orderable: false, targets: -1 } // Disable ordering for the last column
                        //     ],
                        //     dom: 'lf<"toolbar">rtip', // Add a custom toolbar for filters and buttons
                        //     initComplete: function () {
                        //         // Add the flush log dropdown and button to the toolbar
                        //         $("div.toolbar").html(`
                        //             <select id="flush-log-range">
                        //                 <option value="7">Retain Last 7 Days</option>
                        //                 <option value="15">Retain Last 15 Days</option>
                        //                 <option value="30">Retain Last 30 Days</option>
                        //                 <option value="all">Flush All Logs</option>
                        //             </select>
                        //             <button type="button" id="activity-data-flush-btn" class="button button-primary">Flush Log</button>
                        //         `);
                        //     }
                        // });
                        var table = $(document).find('.activity-logs-table').DataTable({
                            "order": [[4, "desc"]], 
                            // Order by the "Logged Time" column (descending)
                            columnDefs: [
                                { width: '20%', targets: 0 },
                                { 
                                    targets: 4, 
                                    type: "date",  // Ensure it's treated as a date
                                    render: function(data, type, row) {
                                        // Ensure the date format is in YYYY-MM-DD or another sortable format
                                        
                                        console.log(data);
                                        data = data.replace("<br>", ' ');
                                        var date = new Date(data);
                                        return date.toISOString(); // Standardize the format as YYYY-MM-DD
                                    }
                                },
                            ],
                            dom: 'lf<"toolbar">rtip', // Add a custom toolbar for filters and buttons
                            initComplete: function () {
                                // Add the flush log dropdown and button to the toolbar
                                $("div.toolbar").html(`
                                    <select id="flush-log-range">
                                        <option value="7">Retain Last 7 Days</option>
                                        <option value="15">Retain Last 15 Days</option>
                                        <option value="30">Retain Last 30 Days</option>
                                        <option value="all">Flush All Logs</option>
                                    </select>
                                    <button type="button" id="activity-data-flush-btn" class="button button-primary">Flush Log</button>
                                `);
                            }
                        });
                        $(document).find('.activity-logs-table').wrap('<div class="activity-logs-table-container" />');
                    }
                } else {
                    // Display an error message if something goes wrong
                    $(document).find('.activity-logs-container').html('<p>Error loading activity logs. Please try again.</p>');
                }
            },
            error: function (xhr, status, error) {
                // Handle any errors that occur during the request
                console.error('Table data AJAX request failed:', status, error);
                $(document).find('.activity-logs-container').html('<p>An error occurred while loading the logs. Please try again later.</p>');
            }
        });
    }
});