<?php

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/contents/header.php";

?>

<section aria-label="hero">
    <div class="hero">
        <article class="heroTextContainer">
            <h1 class="heroText">Euclid</h1>
            <div class="stars">
                <img src="assets/images/star.png" alt="" class="starImage">
                <img src="assets/images/star.png" alt="" class="starImage">
                <img src="assets/images/star.png" alt="" class="starImage">
                <img src="assets/images/star.png" alt="" class="starImage">
            </div>
            <h3 class="motto">The Night Belongs To You!</h3>
        </article>
        <img src="assets/images/aurora-borealis.jpg" alt="" class="heroImage">
    </div>
</section>

<section aria-label="welcome-article">
    <article class="welcome">
        <p class="text">Welcome to Euclid Hotel, your perfect retreat in the heart of a winter wonderland! Start your day with a refreshing hike through snow-covered landscapes, and in the evening, marvel at the magical northern lights dancing across the starry sky. Whether you're seeking adventure or simply want to relax, we offer a unique and unforgettable experience in a remote winter paradise.</p>
    </article>
</section>

<section aria-label="discount-offer">
    <div class="discountContainer">
        <img src="assets/images/discount.jpeg" alt="" class="image">
        <article class="discountArticle">
            <h4>30% Discount!</h4>
            <p class="text">Book your winter getaway at Euclid Hotel and get 30% off your stay when you book 3 or more days. Take the opportunity to explore the beautiful winter scenery and create unforgettable memories!</p>
        </article>
    </div>
</section>

<section aria-label="cta">
    <article class="bookArticle">
        <h2>Interested?</h2>
        <p class="text">Book a room here</p>
        <a href="book-room.php" class="bookHereButton">Book Here</a>
    </article>
</section>

<section aria-label="features">
    <div class="featuresContainer featureOne">
        <img src="assets/images/snowmobile.jpeg" alt="" class="image">
        <article class="featuresArticle">
            <h4>Snowmobile Adventure</h4>
            <p class="text">Want to explore the surroundings in a new way? Rent a snowmobile and head out on your own adventure! Whether you want to ride through serene snow-covered landscapes or embark on longer excursions, the snowmobile gives you the freedom to discover more and have even more fun.</p>
        </article>
    </div>
    <div class="featuresContainer featureTwo">
        <article class="featuresArticle">
            <h4>Heated Lagoon</h4>
            <p class="text">Treat yourself to a relaxing moment in our heated lagoon while the winter chill surrounds you. It's the perfect spot to unwind after a long day in the snow and enjoy the peace and tranquility beneath the open winter sky.</p>
        </article>
        <img src="assets/images/heated-lagoon.jpeg" alt="" class="image">
    </div>
    <div class="featuresContainer featureThree">
        <img src="assets/images/coffee-maker.jpeg" alt="" class="image">
        <article class="featuresArticle">
            <h4>Coffee Maker</h4>
            <p class="text">There's nothing like a steaming cup of coffee when you return from your outdoor adventures. With our state-of-the-art coffee maker, you can easily brew your favorite drink and warm up before your next adventure.</p>
        </article>
    </div>

</section>

<section aria-label="sign-up">
    <article class="signUpContainer">
        <h3>Become a member</h3>
        <p class="text">Sign up here</p>
        <button class="signUpButton">Sign Up</button>
    </article>
</section>


<?php

require __DIR__ . "/contents/footer.php";

?>