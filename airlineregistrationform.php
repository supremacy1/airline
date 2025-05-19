
 <!DOCTYPE html>
 <html lang="en">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Airline Company Registration</title>
     <style>
         body {
            font-family: Arial, sans-serif;
            padding: 20px;
           
         }
         .container {
             background: #fff;
             padding: 20px;
             border-radius: 8px;
             box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
             width: 400px;
         }
         h2 {
             text-align: center;
             color: #333;
         }
         label {
             display: block;
             margin-top: 10px;
             font-weight: bold;
         }
         input, select {
             width: 100%;
             padding: 8px;
             margin-top: 5px;
             border: 1px solid #ccc;
             border-radius: 5px;
         }
         .button {
             width: 100%;
             padding: 10px;
             background: #007BFF;
             color: white;
             border: none;
             border-radius: 5px;
             cursor: pointer;
             margin-top: 15px;
         }
         .button:hover {
             background: #0056b3;
         }
         .modal {
             display: none;
             position: fixed;
             z-index: 1;
             left: 0;
             top: 0;
             width: 100%;
             height: 100%;
             background-color: rgba(0, 0, 0, 0.4);
             justify-content: center;
             align-items: center;
         }
         .modal-content {
             background: white;
             padding: 20px;
             border-radius: 8px;
             text-align: center;
         }
         .error {
            color: red;
            font-size: 14px;
        }
     </style>
 </head>
 <body>
 <body>
 <?php include 'header.php'; ?>
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
        <div class="maincontainer">
            <div class="container">
                <h2>Airline Company Registration</h2>
                <form id="registrationForm" action="./db/register.php" method="POST">
                    <label for="company_name">Company Name:</label>
                    <input type="text" id="company_name" name="company_name" required>
                    
                    <label for="iata_code">IATA Code:</label>
                    <input type="text" id="iata_code" name="iata_code" required>
                    
                    <label for="icao_code">ICAO Code:</label>
                    <input type="text" id="icao_code" name="icao_code" required>
                    
                    <label for="contact_person">Contact Person:</label>
                    <input type="text" id="contact_person" name="contact_person" required>
                    
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" required>
                    
                    <label for="country">Country:</label>
                    <input type="text" id="country" name="country" required>

                    <label for="language">Preferred Language:</label>
                    <select id="language" name="language" required>
                        <option value="">Select a Language</option>
                        <option value="english">English</option>
                        <option value="spanish">Spanish</option>
                        <option value="french">French</option>
                        <option value="german">German</option>
                        <option value="chinese">Chinese</option>
                        <option value="arabic">Arabic</option>
                        <option value="hindi">Hindi</option>
                        <option value="portuguese">Portuguese</option>
                        <option value="russian">Russian</option>
                        <option value="japanese">Japanese</option>
                        <option value="korean">Korean</option>
                        <option value="italian">Italian</option>
                        <option value="dutch">Dutch</option>
                        <option value="swedish">Swedish</option>
                        <option value="turkish">Turkish</option>
                        <option value="thai">Thai</option>
                        <option value="vietnamese">Vietnamese</option>
                        <option value="hebrew">Hebrew</option>
                        <option value="polish">Polish</option>
                        <option value="danish">Danish</option>
                        <option value="finnish">Finnish</option>
                        <option value="hungarian">Hungarian</option>
                        <option value="czech">Czech</option>
                        <option value="norwegian">Norwegian</option>
                        <option value="romanian">Romanian</option>
                        <option value="greek">Greek</option>
                        <option value="indonesian">Indonesian</option>
                    </select>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>

                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <p id="passwordError" class="error"></p>
                    <button type="submit" class="button">Register</button>
                </form>
                <div style="text-align: center; margin-top: 20px; font-size: 14px; color: #555;">
                    Already Register 
                    <a href="bookingflight.php" style="color: #007BFF; text-decoration: none; font-weight: bold;" 
                       onmouseover="this.style.textDecoration='underline'; this.style.color='#0056b3';" 
                       onmouseout="this.style.textDecoration='none'; this.style.color='#007BFF';">
                        Book a Flight
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
     <div id="messageModal" class="modal">
         <div class="modal-content">
             <p id="modalMessage"></p>
             <button id="modalButton">OK</button>
         </div>
     </div>
 
     <script>
       
       document.getElementById("registrationForm").addEventListener("submit", function (event) {
    event.preventDefault();

    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;
    var errorMessage = document.getElementById("passwordError");

    // Check if passwords match
    if (password !== confirmPassword) {
        errorMessage.textContent = "Passwords do not match!";
        return;
    } else {
        errorMessage.textContent = ""; // Clear error if passwords match
    }

    let formData = new FormData(this);
    let endpoint = this.getAttribute("action"); // Get the action attribute from the form

    fetch(endpoint, { // Use the action attribute as the endpoint
        method: this.getAttribute("method") || "POST", // Use method from form or default to POST
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById("modalMessage").innerText = data.message;
        document.getElementById("messageModal").style.display = "flex";

        document.getElementById("modalButton").onclick = function () {
            document.getElementById("messageModal").style.display = "none";
            if (data.redirect) {
                window.location.href = "bookingflight.php";
            }
        };
    })
    .catch(error => console.error("Error:", error));
});
  
     </script>
 </body>
 </html>
 