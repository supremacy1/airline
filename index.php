<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Airline Travel - Home</title>
    <!-- <link rel="stylesheet" href="styles.css"> -->
        <link rel="stylesheet" href="slideshow.css">


</head>
<body>
    <header>
        <!-- <div class="logo">
            <img src="logo.png" alt="SkyFly Airlines Logo" class="logo-img">
        </div>
        <h1>Welcome to SkyFly Airlines</h1>
     
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="mailto:travelairlines210america@gmail.com">Contact Us</a></li>
            </ul>
        </nav> -->
        <?php include 'header.php'; ?>
    </header>
    
    
    <div class="slideshow-container">
        <div class="slide active" style="background-image: url('./images/im1.jpg');">
            <div class="overlay"></div>
            <div class="text-content">Discover Amazing Destinations</div>
        </div>
        <div class="slide" style="background-image: url('./images/im2.jpg');">
            <div class="overlay"></div>
            <div class="text-content">Experience Luxury Travel</div>
        </div>
        <div class="slide" style="background-image: url('./images/im3.jpg');">
            <div class="overlay"></div>
            <div class="text-content">Adventure Awaits You</div>
        </div>
        <button class="prev" onclick="prevSlide()">&#10094;</button>
        <button class="next" onclick="nextSlide()">&#10095;</button>
    </div>
    <section id="introduction">
        <h2>Experience the Sky Like Never Before</h2>
        <p>At TRAVEL AIRWAYS Airlines, we are dedicated to providing top-notch travel experiences with comfort, luxury, and affordability. Whether you are traveling for business or leisure, we ensure a seamless journey with world-class service, modern aircraft, and exceptional hospitality. Explore global destinations with ease and confidence.</p>
    </section>
    
    <section id="services">
        <h2>Our Services</h2>
        <ul>
            <li>Luxury Flights</li>
            <li>Affordable Travel</li>
            <li>24/7 Customer Support</li>
            <li>Global Destinations</li>
            <li>Frequent Flyer Benefits</li>
        </ul>
    </section>
    
    <section id="booking">
        <h2>Book Your Flight</h2>
        <a href="airlineregistrationform.php" 
class="button" type="submit">Book Now!</a>
        </form>
    </section>
    
    <footer>
        <p>&copy; 2025 TRAVEL AIRWAYS Airlines. All rights reserved.</p>
    </footer>
    
   

    <script>
        let currentIndex = 0;
        const slides = document.querySelectorAll(".slide");
        
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle("active", i === index);
            });
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            showSlide(currentIndex);
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            showSlide(currentIndex);
        }

        setInterval(nextSlide, 5000);
    </script>
</body>
</html>