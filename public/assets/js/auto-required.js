$(document).ready(function () {
    // Select all required form controls and append an asterisk (*) to their label
    $(':input[required], select[required], textarea[required]').each(function () {
        const label = $(this).closest('form').find(`label[for="${this.id}"]`);
        if (label.length && !label.html().includes('*')) {
            label.append(' <span style="color: red;">*</span>');
        }
    });
});
