<?php

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/contents/header.php";

function isValidUuid(string $uuid): bool
{

    if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
        return false;
    }

    return true;
}

if (isset($_POST['transferCode'])) {
    $roomType = htmlspecialchars($_POST['roomType']);
    $arrivalDate = htmlspecialchars($_POST['arrivalDate']);
    $departureDate = htmlspecialchars($_POST['departureDate']);
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $transferCode = htmlspecialchars($_POST['transferCode']);

    $confirmation = [];

    $confirmation[] = [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'roomType' => $roomType,
        'arrivalDate' => $arrivalDate,
        'departureDate' => $departureDate
    ];

    $fileName = __DIR__ . "/contents/confirmations/" . preg_replace('/[^a-zA-Z0-9]/', '_', $firstName) . ".json";

    $booking = json_encode($confirmation, JSON_PRETTY_PRINT);

    file_put_contents($fileName, $booking);
}


use benhall14\phpCalendar\Calendar as calendar;

$calendar = new calendar;
$calendar->stylesheet();

$calendar->useMondayStartingDate();
$calendar->useFullDayNames();

?>

<section aria-label="book">
    <div class="calendarContainer">
        <div>
            <?php echo $calendar->draw(date('Y-01-01')); ?>
        </div>
    </div>
    <form action="" method="post" class="bookingsContainer">
        <select name="roomType" id="roomType" class="roomType">
            <option value="economy">Economy</option>
            <option value="standard">Standard</option>
            <option value="luxury">Luxury</option>
        </select>
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

        <button type="submit" class="bookButton" id="bookButton">Book now</button>
    </form>
</section>


<?php

require __DIR__ . "/contents/footer.php";
