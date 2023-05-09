
document.addEventListener('DOMContentLoaded', function () {

    fetch('/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ data: document.getElementById('total').value })
    })
        .then(response => response.json())
        .then(data => console.log(data));



});

