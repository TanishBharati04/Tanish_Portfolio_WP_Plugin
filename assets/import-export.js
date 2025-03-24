jQuery(document).ready(function ($) {
    let totalRecords = 0; // Define totalRecords globally

    $('#upload-btn').on('click', function () {
        let fileInput = $('#csv-file')[0].files[0];

        if (!fileInput) {
            alert('Please select a CSV file to upload.');
            return;
        }

        var formData = new FormData();
        formData.append('action', 'tanish_import_csv');
        formData.append('security', tanishAjax.nonce);
        formData.append('csv_file', fileInput);

        console.log("Sending AJAX request...");

        $.ajax({
            url: tanishAjax.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                console.log("Before send - file uploading...");
                $('#progress-modal').show(); // Show modal when upload starts
            },
            success: function (response) {
                console.log("AJAX Response:", response); // Debugging response

                if (response.success) {
                    totalRecords = response.totalRecords; // Store total records

                    if (totalRecords === 0) {
                        alert('The uploaded file contains no valid records.');
                        $('#progress-modal').hide();
                        return;
                    }

                    processBatch(0); // Start batch processing from 0
                } else {
                    alert('Error: ' + (response.data.message ? response.data.message.join("\n") : "Unknown error"));
                    $('#progress-modal').hide();
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Upload Error:", xhr.responseText);
                alert('Something went wrong while uploading the file.');
                $('#progress-modal').hide();
            }
        });
    });

    function processBatch(insertedCount) {
        $.ajax({
            url: tanishAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'tanish_process_csv_batch',
                security: tanishAjax.nonce
            },
            success: function (response) {
                console.log("Batch Process Response:", response);

                if (response.success) {
                    insertedCount += response.data.batchCount;
                    let progress = (insertedCount / totalRecords) * 100;

                    $('#progress-text').text(`Processing... ${insertedCount}/${totalRecords} records`);
                    $('#progress-bar').css('width', progress + '%');

                    if (!response.data.completed) {
                        processBatch(insertedCount); // Continue processing
                    } else {
                        $('#progress-text').text('Import Completed!');
                        setTimeout(function () {
                            $('#progress-modal').fadeOut();
                        }, 1000);
                    }
                } else {
                    alert('Batch processing failed: ' + response.data.message);
                    $('#progress-modal').hide();
                }
            },
            error: function (xhr, status, error) {
                console.error("Batch Processing Error:", xhr.responseText);
                alert('Something went wrong while processing.');
                $('#progress-modal').hide();
            }
        });
    }

    // Export Handling

    // Call function on page load
    fetchTaxonomies();

    // Fetch categories and tags for the export section
    function fetchTaxonomies() {
        // console.log("Fetching taxonomies...");
        // console.log("Nonce before AJAX:", tanishAjax.nonce);

        $.ajax({
            url: tanishAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'tanish_fetch_taxonomies',
                security: tanishAjax.nonce
            },
            success: function (response) {
                console.log("AJAX Response:", response);

                if (response.success) {
                    let categories = response.data.categories;
                    let tags = response.data.tags;

                    let categorySelect = $('#export_category');
                    let tagSelect = $('#export_tags');

                    categorySelect.empty().append('<option value="all">All Categories</option>');
                    tagSelect.empty().append('<option value="all">All Tags</option>');

                    categories.forEach(function (category) {
                        categorySelect.append(`<option value="${category.name}">${category.name}</option>`);
                    });

                    tags.forEach(function (tag) {
                        tagSelect.append(`<option value="${tag.name}">${tag.name}</option>`);
                    });

                    console.log("Categories and Tags Populated!");
                } else {
                    console.error('Failed to fetch categories and tags : ', response);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching taxonomies:', xhr.responseText);
            }
        });
    }

    $('#export-project-btn').on('click', function () {
        let selectedColumns = $('#export_columns').val();
        let category = $('#export_category').val();
        let tags = $('#export_tags').val();

        $.ajax({
            url: tanishAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'export_projects',
                security: tanishAjax.nonce,
                columns: selectedColumns || 'all',
                category: category || 'all',
                tags: tags || 'all'
            },
            success: function (response) {
                console.log("Export AJAX Response:", response); // Debugging response

                if (response.success) {
                    // window.location.href = response.file_url;
                    let fileUrl = response.data.file_url;
                    // console.log("File URL:", fileUrl); // Ensure the correct file URL

                    window.location.href = fileUrl; // Trigger file download
                } else {
                    console.error("Export failed:", response.data.message);
                    alert('No records found for the selected filters.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Export failed:', xhr.responseText);
            }
        });
    });
});
