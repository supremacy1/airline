<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyFly Airlines</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function toggleMenu() {
            var nav = document.getElementById("nav-menu");
            if (nav.style.display === "block") {
                nav.style.display = "none";
            } else {
                nav.style.display = "block";
            }
        }

        function checkScreenSize() {
            var nav = document.getElementById("nav-menu");
            if (window.innerWidth > 600) {
                nav.style.display = "flex";
            } else {
                nav.style.display = "none";
            }
        }

        window.addEventListener("resize", checkScreenSize);
        window.addEventListener("load", checkScreenSize);
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background:rgb(8, 67, 129);
            padding: 10px;
        }
        .logo-img {
            width: 150px;
        }
        .menu-button {
            display: none;
            background-color:rgb(8, 67, 129);
            color: white;
            border: none;
            padding: 15px 15px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
            padding-left: 50%;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
        }
        nav ul li {
            margin: 0 15px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
        }
        @media screen and (max-width: 600px) {
            .menu-button {
                display: block;
            }
            nav ul {
                display: none;
                background:rgb(8, 67, 129);
                position: absolute;
                /* width: 100%; */
                right: 5%;
                top: 60px;
                flex-direction: column;
                text-align: center;
                padding: 10px 0;
                opacity: 0.9;
            }
            nav ul li {
                padding: 10px 0;
            }
        }
        .logo-img{
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }
        .logo{
            display: flex;
            align-items: center;
            color: #000;

        }
        .logo h2{
            margin-left: 10px;
            color: #fff;
           
        }

    </style>
</head>
<body>
    <div class="header">
        <div class="logo">

            <img src="./images/logo.jpg" alt="SkyFly Airlines Logo" class="logo-img">
            <h2>SkyFly Airlines</h2>
        </div>
        <button class="menu-button" onclick="toggleMenu()">☰</button>
        <nav>
            <ul id="nav-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="mailto:travelairlines210america@gmail.com">Contact Us</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>