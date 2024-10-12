document.addEventListener('DOMContentLoaded', () => {
    const disableEndDateCheckbox = document.getElementById('disable_end_date');
    const endDateInput = document.getElementById('end_date');

    disableEndDateCheckbox.addEventListener('change', function() {
        if (this.checked) {
            endDateInput.disabled = true;
            endDateInput.style.opacity = '0.5';
        } else {
            endDateInput.disabled = false;
            endDateInput.style.opacity = '1';
        }
    })

    if (disableEndDateCheckbox.checked) {
        endDateInput.disabled = true;
        endDateInput.style.opacity = '0.5';
    }
})