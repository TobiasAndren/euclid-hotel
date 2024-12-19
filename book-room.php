<?php

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/contents/header.php";

use benhall14\phpCalendar\Calendar as calendar;

$calendar = new calendar;
$calendar->stylesheet();

$calendar->useMondayStartingDate();
$calendar->useFullDayNames();

?>

<section aria-label="calendar">
    <div class="calendarContainer">
        <div>
            <select name="room-type" id="room-type" class="roomType">
                <option value="economy">Economy</option>
                <option value="standard">Standard</option>
                <option value="luxury">Luxury</option>
            </select>
            <?php echo $calendar->draw(date('Y-01-01')); ?>
        </div>
    </div>
</section>

<section aria-label="book">
    <form action="submit" method="post" class="bookingsContainer">
        <label for="arrivalDate" class="labelText">What day do you wish to arrive?</label>
        <input type="date" name="arrivalDate" min="2025-01-01" max="2025-01-31" class="input">

        <label for="departureDate" class="labelText">What day do you wish to depart?</label>
        <input type="date" name="departureDate" min="2025-01-01" max="2025-01-31" class="input">

        <label for="firstName" class="labelText">First Name</label>
        <input type="text" name="firstName" class="input" placeholder="Your first name..">

        <label for="lastName" class="labelText">Last Name</label>
        <input type="text" name="lastName" class="input" placeholder="Your last name..">

        <label for="lastName" class="labelText">Transfer Code</label>
        <input type="text" name="transferCode" class="input" placeholder="Your transfer code.." required>

        <button class="bookButton">Book now</button>
    </form>
</section>


<?php

require __DIR__ . "/contents/footer.php";
