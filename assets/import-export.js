jQuery(document).ready(function ($) {
    // Import Project
    $('.upload-btn').on('click', function (e) {
        e.preventDefault();

        var formData = new FormData();
        formData.append('action', 'import_project');
        formData.append('security', tanishAjax.nonce);
        formData.append('title', $('#import_title').val());
        formData.append('description', $('#import_description').val());
        formData.append('category', $('#import_category').val());
        formData.append('tags', $('#import_tags').val());
        formData.append('start_date', $('#import_start_date').val());
        formData.append('end_date', $('#import_end_date').val());
        formData.append('image', $('#import_image')[0].files[0]);

        $.ajax({
            url: tanishAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {  
                if (response.success) {
                    $('#import_status').html('<p style="color: green;">' + response.data + '</p>');
                    alert('Success' + response.data.message);
                } else {
                    $('#import_status').html('<p style="color: red;">' + response.data + '</p>');
                    alert('Success' + response.data.message);
                }
            }
        });
    });

    // Export Projects (Batch of 5)
    $('#download_csv').click(function () {
        let page = 1;
        let csvData = [];
        let exportColumns = $('#export_columns').val();
        let exportCategory = $('#export_category').val();
        let exportTags = $('#export_tags').val();

        function exportBatch() {
            $.ajax({
                url: tanishAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'export_projects',
                    security: tanishAjax.nonce,
                    columns: exportColumns,
                    category: exportCategory,
                    tags: exportTags,
                    page: page
                },
                success: function (response) {
                    if (response.success) {
                        csvData = csvData.concat(response.data);

                        // Update Progress Bar
                        let progress = (page * 5);
                        $('#export_progress').text('Exporting... ' + progress + ' records');

                        page++;
                        exportBatch(); // Fetch next batch
                    } else {
                        if (csvData.length > 0) {
                            downloadCSV(csvData);
                        }
                        $('#export_progress').text('Export Complete!');
                    }
                }
            });
        }

        exportBatch();
    });

    // Download CSV Function
    function downloadCSV(csvData) {
        let csvContent = "data:text/csv;charset=utf-8,";
        let headers = Object.keys(csvData[0]).join(",") + "\n";
        csvContent += headers;

        csvData.forEach(function (row) {
            csvContent += Object.values(row).join(",") + "\n";
        });

        let encodedUri = encodeURI(csvContent);
        let link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "projects_export.csv");
        document.body.appendChild(link);
        link.click();
    }
});
