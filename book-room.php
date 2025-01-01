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

if (isset($_POST['transferCode'], $_POST['roomType'], $_POST['arrivalDate'], $_POST['departureDate'], $_POST['firstName'], $_POST['lastName'])) {
    $roomType = $_POST['roomType'];
    $arrivalDate = $_POST['arrivalDate'];
    $departureDate = $_POST['departureDate'];
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $transferCode = htmlspecialchars($_POST['transferCode']);
    $features = $_POST["features"];

    $featuresMap = [
        1 => "coffee maker",
        2 => "heated lagoon",
        3 => "snowmobile"
    ];

    if (isset($_POST['features'])) {
        $selectedFeatures = $_POST['features'];
        $selectedNames = [];

        foreach ($selectedFeatures as $feature) {
            if (isset($featuresMap[$feature])) {
                $selectedNames[] = $featuresMap[$feature];
            }
        }
    }

    $confirmation = [];

    $confirmation = [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'roomType' => $roomType,
        'arrivalDate' => $arrivalDate,
        'departureDate' => $departureDate,
        'features' => $selectedNames
    ];

    $fileName = __DIR__ . "/contents/confirmations/" . $arrivalDate . preg_replace('/[^a-zA-Z0-9]/', '_', $firstName) . ".json";

    $booking = json_encode($confirmation, JSON_PRETTY_PRINT);

    // file_put_contents($fileName, $booking);
}


use benhall14\phpCalendar\Calendar as calendar;

$calendar = new calendar;
$calendar->stylesheet();

$calendar->useMondayStartingDate();
$calendar->useFullDayNames();

?>
<section aria-label="popup">
    <?php if (isset($booking)) { ?>
        <div class="popup" id="popup">
            <pre class="popupText" id="pupupText">Booking Confirmation:
    
<?= $booking; ?>
            </pre>
        </div>
    <?php
    } ?>
</section>

<section aria-label="book">
    <div class="calendarContainer">
        <div>
            <?php echo $calendar->draw(date('2025-01-01')); ?>
        </div>
    </div>
    <form action="" method="post" class="bookingsContainer" id="bookForm">
        <label for="roomType" class="labelText">What room do you wish to stay in?</label>
        <select name="roomType" id="roomType" class="input">
            <option value="economy">Economy</option>
            <option value="standard">Standard</option>
            <option value="luxury">Luxury</option>
        </select>
        <label for="arrivalDate" class="labelText">What day do you wish to arrive?</label>
        <input type="date" name="arrivalDate" min="2025-01-01" max="2025-01-31" class="input" required>

        <label for="departureDate" class="labelText">What day do you wish to depart?</label>
        <input type="date" name="departureDate" min="2025-01-01" max="2025-01-31" class="input" required>

        <label for="firstName" class="labelText">First Name</label>
        <input type="text" name="firstName" class="input" placeholder="Your first name.." required>

        <label for="lastName" class="labelText">Last Name</label>
        <input type="text" name="lastName" class="input" placeholder="Your last name.." required>

        <label for="lastName" class="labelText">Transfer Code</label>
        <input type="text" name="transferCode" class="input" placeholder="Your transfer code.." required>

        <label for="features" class="labelText">features</label>
        <div class="block">
            <input type="checkbox" name="features[]" value="1" class="checkbox">
            coffee maker $2
        </div>
        <div class="block">
            <input type="checkbox" name="features[]" value="2" class="checkbox">
            heated lagoon $4
        </div>
        <div class="block">
            <input type="checkbox" name="features[]" value="3" class="checkbox">
            snowmobile $6
        </div>

        <button type="submit" class="bookButton" id="bookButton">Book now</button>
    </form>
</section>




<?php

require __DIR__ . "/contents/footer.php";
