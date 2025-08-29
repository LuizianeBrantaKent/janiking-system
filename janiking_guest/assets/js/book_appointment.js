document.addEventListener('DOMContentLoaded', function() {
    // Initialize datepicker
    $('#appointmentDate').datepicker({
        format: 'yyyy-mm-dd',
        startDate: '0d',
        autoclose: true,
        daysOfWeekDisabled: [0, 6] // Disable Sundays and Saturdays
    }).on('changeDate', function(e) {
        const selectedDate = e.format();
        // Simulate fetching available time slots from database
        const timeSlots = ['9:00 AM', '10:30 AM', '2:30 PM', '4:00 PM']; // Replace with AJAX call to check database
        let slotsHtml = '';
        timeSlots.forEach(slot => {
            slotsHtml += `<button class="btn btn-outline-primary time-slot">${slot}</button>`;
        });
        document.getElementById('timeSlots').innerHTML = slotsHtml;
    });

    // Select time slot
    document.getElementById('timeSlots').addEventListener('click', function(e) {
        if (e.target.classList.contains('time-slot')) {
            document.querySelectorAll('.time-slot').forEach(btn => btn.classList.remove('btn-primary'));
            e.target.classList.add('btn-primary');
            document.getElementById('appointmentTime').value = e.target.textContent; // Fixed typo
        }
    });

    // Debug modal close button
    $('.close').on('click', function() {
        console.log('Close button clicked on modal:', $(this).closest('.modal').attr('id'));
        $(this).closest('.modal').modal('hide');
    });
});