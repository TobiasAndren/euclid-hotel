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

</section>


<?php

require __DIR__ . "/contents/footer.php";
