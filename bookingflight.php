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
    background-color: #fff;
    box-shadow: 0 0 10px rgba(14, 7, 112, 0.1);
}

input, select, button {
    display: block;
    width: 100%;
    margin-bottom: 10px;
    padding: 5px;
    box-shadow: 0 0 10px rgba(24, 8, 252, 0.1);
    border: 1px solid #ddd;
    border-radius: 5px;
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
    margin: 10% auto;
    width: fit-content;
    color: white;
    box-shadow: 0 0 10px rgba(14, 7, 112, 0.1);
    border-radius: 5px;
    position: relative;
    overflow: hidden;
}

.modal-content::before {
    content: "";
    display: block;
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: 
        linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), /* dark overlay */
        url('./images/airline.gif') center/100% no-repeat;
    z-index: 1;
    border-radius: 5px;
    pointer-events: none;
}

.modal-content > * {
    position: relative;
    z-index: 2;
}

.modal button {
    margin-top: 10px;
}
.container h2{
    display:flex;
    justify-content:center;
}
    </style>
</head>
<body>
<div class="container">
        
        <?php include 'header.php'; ?>
    <h2>Flight Booking Form</h2>

    <form id="bookingForm" action="./db/process_booking.php" method="POST">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" required>

        <!-- <label for="departure">Departure:</label>
        <input type="text" id="departure" name="departure" required> -->

        <label for="destination">Address:</label>
        <input type="text" id="destination" name="destination" required>

        <label for="departure_date">Date of Birth:</label>
        <input type="date" id="departure_date" name="departure_date" required>

        <!-- <label for="return_date">Return Date:</label>
        <input type="date" id="return_date" name="return_date"> -->

        <label for="payment_method">Payment Method:</label>
        <select id="payment_method" name="payment_method" required>
            <option value="Bitcoin">Bitcoin</option>
            <option value="PayPal">PayPal</option>
            <option value="Apple">Apple</option>
            
        </select>

        <button type="button" onclick="openModal()">Submit Booking</button>
    </form>
</container>
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
    // let departure = document.getElementById("departure").value;
    let destination = document.getElementById("destination").value;
    let departureDate = document.getElementById("departure_date").value;
    // let returnDate = document.getElementById("return_date").value || "N/A";
    let paymentMethod = document.getElementById("payment_method").value;

    let modalDetails = `
        <strong>Name:</strong> ${name} <br>
        <strong>Email:</strong> ${email} <br>
        <strong>Phone:</strong> ${phone} <br>
       
        <strong>Destination:</strong> ${destination} <br>
        <strong>Departure Date:</strong> ${departureDate} <br>
        
        <strong>Payment Method:</strong> ${paymentMethod}
    `;

    document.getElementById("modalDetails").innerHTML = modalDetails;
    document.getElementById("confirmationModal").style.display = "block";
}
 // <strong>Departure:</strong> ${departure} <br>
 // <strong>Return Date:</strong> ${returnDate} <br>
function closeModal() {
    document.getElementById("confirmationModal").style.display = "none";
}

    </script> 
</body>
</html>
