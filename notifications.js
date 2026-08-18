$(document).ready(function () {
    function loadNotifications() {
        $.ajax({
            url: 'fetch_notifications.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#notifList').empty();
                let totalCount = 0;

                if (data.length > 0) {
                    data.forEach(notification => {
                        $('#notifList').append(
                            `<li><button class="notif-btn" onclick="window.location.href='${notification.url}'">
                                ${notification.message}
                            </button></li>`
                        );
                        totalCount += parseInt(notification.count, 10) || 0;
                    });
                } else {
                    // Show "No notifications" and hide count
                    $('#notifList').append('<li class="no-notif">No new notifications</li>');
                }
                
                if (totalCount > 0) {
                    $('#notifCount').text(totalCount).show();
                } else {
                    $('#notifCount').hide();
                }
            },
            error: function (xhr, status, error) {
                console.error("Error loading notifications:", error);
                $('#notifList').append('<li class="error-notif">Failed to load notifications</li>');
            }
        });
    }

    $('#notifBell').click(function () {
        $('#notifDropdown').toggle();
    });

    loadNotifications();
    setInterval(loadNotifications, 10000); // Auto-refresh every 10 seconds
});
