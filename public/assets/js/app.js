// Ask for confirmation before submitting any form that has a data-confirm attribute
document.addEventListener('submit', function (event) {
    var message = event.target.getAttribute('data-confirm');

    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});
