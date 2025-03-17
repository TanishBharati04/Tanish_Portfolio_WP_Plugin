jQuery(document).ready(function ($) {
    let profileVisitsCtx = document.getElementById('profileVisitsChart');
    let popularProjectsCtx = document.getElementById('popularProjectsChart').getContext('2d');

    if (profileVisitsCtx) {
        profileVisitsCtx = profileVisitsCtx.getContext('2d');
        
        let profileVisitsData = analyticsData.profileVisits.length ? {
            labels: analyticsData.profileVisits.map(item => item.visit_date),
            datasets: [{
                label: 'Profile Visits',
                data: analyticsData.profileVisits.map(item => item.count),
                borderColor: 'blue',
                backgroundColor: 'rgba(0, 0, 255, 0.2)',
                fill: true
            }]
        } : { labels: [], datasets: [] };

        new Chart(profileVisitsCtx, { type: 'line', data: profileVisitsData });
    }

    // Popular Projects Data
    let popularProjectsData = {
        labels: analyticsData.popularProjects.map(item => `Project ${item.project_id}`),
        datasets: [{
            label: 'Shares',
            data: analyticsData.popularProjects.map(item => item.share_count),
            backgroundColor: 'green'
        }]
    };

    new Chart(popularProjectsCtx, { type: 'bar', data: popularProjectsData });

    $("#profile-visit-filter").change(function () {
        let filter = $(this).val();

        $.ajax({
            url: tanishAjax.ajaxurl,
            type: "POST",
            data: {
                action: "tanish_portfolio_get_profile_visits",
                filter: filter,
                nonce: tanishAjax.nonce
            },
            success: function (response) {
                if (response.success) {
                    $("#profile-visit-count").text(response.data.total_visits);
                } else {
                    console.error("Failed to fetch profile visits.");
                }
            }
        });
    });

});
