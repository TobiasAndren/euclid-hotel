<?php

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/contents/header.php";

?>

<section aria-label="hero">
    <div class="hero">
        <article class="heroTextContainer">
            <h1 class="heroText">Euclid</h1>
            <h3>The Night Belongs To You!</h3>
        </article>
        <img src="assets/images/aurora-borealis.jpg" alt="" class="heroImage">
    </div>
</section>

<section aria-label="welcome-article">
    <article class="welcome">
        <p class="text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere aperiam nesciunt, magnam ullam incidunt, tempore exercitationem natus id repudiandae veritatis alias. Repellat quae harum sunt cumque? Amet recusandae harum laboriosam.</p>
    </article>
</section>

<section aria-label="discount-offer">
    <div class="discountContainer">
        <img src="assets/images/aurora-borealis.jpg" alt="" class="image">
        <article class="discountArticle">
            <h4>Discount</h4>
            <p class="text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore aut laborum, voluptates omnis magni quas quos deserunt quidem animi molestiae dicta quam laboriosam quasi, quae voluptatem blanditiis voluptatibus culpa exercitationem!</p>
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
        <img src="assets/images/aurora-borealis.jpg" alt="" class="image">
        <article class="featuresArticle">
            <p class="text">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Voluptas ut sapiente expedita perspiciatis voluptatem error architecto animi beatae odio nostrum quo maxime ea et, iure laudantium ratione consequuntur numquam pariatur?</p>
        </article>
    </div>
    <div class="featuresContainer featureTwo">
        <article class="featuresArticle">
            <p class="text">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Voluptas ut sapiente expedita perspiciatis voluptatem error architecto animi beatae odio nostrum quo maxime ea et, iure laudantium ratione consequuntur numquam pariatur?</p>
        </article>
        <img src="assets/images/aurora-borealis.jpg" alt="" class="image">
    </div>
    <div class="featuresContainer featureThree">
        <img src="assets/images/aurora-borealis.jpg" alt="" class="image">
        <article class="featuresArticle">
            <p class="text">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Voluptas ut sapiente expedita perspiciatis voluptatem error architecto animi beatae odio nostrum quo maxime ea et, iure laudantium ratione consequuntur numquam pariatur?</p>
        </article>
    </div>

</section>

<section aria-label="sign-up">
    <article class="signUpContainer">
        <h4>Become a member</h4>
        <p class="text">Sign up here</p>
        <button class="signUpButton">Sign Up</button>
    </article>
</section>


<?php

require __DIR__ . "/contents/footer.php";

?>