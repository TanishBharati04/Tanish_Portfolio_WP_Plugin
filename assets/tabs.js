jQuery(document).ready(function ($) {
     // Initially show only Import section
     $('#import-section').show();
     $('#export-section').hide();
 
     // Tab Click Event
     $('.tab').on('click', function () {
         $('.tab').removeClass('active'); // Remove active class from all tabs
         $(this).addClass('active'); // Add active class to clicked tab
 
         if ($(this).attr('id') === 'import-tab') {
             $('#import-section').show();
             $('#export-section').hide(); // Hide export section when import is active
         } else {
             $('#import-section').hide();
             $('#export-section').show();
         }
     });

    // Simulated Batch Upload with Progress Bar
    $("#upload-btn").on("click", function () {
        let fileInput = $("#csv-file")[0].files[0];

        if (!fileInput) {
            alert("Please select a CSV file.");
            return;
        }

        $("#progress-modal").fadeIn();

        let uploaded = 0;
        let totalRecords = 50; // Simulating 50 records
        let batchSize = 5;

        function processBatch() {
            if (uploaded >= totalRecords) {
                $("#progress-text").text("Upload Complete!");
                setTimeout(() => {
                    $("#progress-modal").fadeOut();
                }, 1000);
                return;
            }

            uploaded += batchSize;
            $("#progress-text").text(`${uploaded} records uploaded`);
            $("#progress-bar").css("width", `${(uploaded / totalRecords) * 100}%`);

            setTimeout(processBatch, 1000);
        }

        processBatch();
    });
});
