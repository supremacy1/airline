<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Booking</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
    <style>
        body {
    font-family: Arial, sans-serif;
    padding: 20px;
}

form {
    max-width: 400px;
    margin: auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

input, select, button {
    display: block;
    width: 100%;
    margin-bottom: 10px;
    padding: 8px;
}

button {
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background: white;
    padding: 20px;
    margin: 15% auto;
    width: 50%;
    text-align: center;
    border-radius: 5px;
}

.modal button {
    margin-top: 10px;
}

    </style>
</head>
<body>

    <h2>Flight Booking Form</h2>

    <form id="bookingForm" action="./db/process_booking.php" method="POST">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" required>

        <label for="departure">Departure:</label>
        <input type="text" id="departure" name="departure" required>

        <label for="destination">Destination:</label>
        <input type="text" id="destination" name="destination" required>

        <label for="departure_date">Departure Date:</label>
        <input type="date" id="departure_date" name="departure_date" required>

        <label for="return_date">Return Date:</label>
        <input type="date" id="return_date" name="return_date">

        <label for="payment_method">Payment Method:</label>
        <select id="payment_method" name="payment_method" required>
            <option value="Credit Card">Credit Card</option>
            <option value="PayPal">PayPal</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Cash">Cash</option>
        </select>

        <button type="button" onclick="openModal()">Submit Booking</button>
    </form>

    <!-- Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Your Booking</h3>
            <p id="modalDetails"></p>
            <button type="button" onclick="closeModal()">Cancel</button>
            <button type="submit" form="bookingForm">Confirm</button>
        </div>
    </div>

    <script>
        function openModal() {
    let name = document.getElementById("name").value;
    let email = document.getElementById("email").value;
    let phone = document.getElementById("phone").value;
    let departure = document.getElementById("departure").value;
    let destination = document.getElementById("destination").value;
    let departureDate = document.getElementById("departure_date").value;
    let returnDate = document.getElementById("return_date").value || "N/A";
    let paymentMethod = document.getElementById("payment_method").value;

    let modalDetails = `
        <strong>Name:</strong> ${name} <br>
        <strong>Email:</strong> ${email} <br>
        <strong>Phone:</strong> ${phone} <br>
        <strong>Departure:</strong> ${departure} <br>
        <strong>Destination:</strong> ${destination} <br>
        <strong>Departure Date:</strong> ${departureDate} <br>
        <strong>Return Date:</strong> ${returnDate} <br>
        <strong>Payment Method:</strong> ${paymentMethod}
    `;

    document.getElementById("modalDetails").innerHTML = modalDetails;
    document.getElementById("confirmationModal").style.display = "block";
}

function closeModal() {
    document.getElementById("confirmationModal").style.display = "none";
}

    </script> <!-- JavaScript file -->
</body>
</html>
