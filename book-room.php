<?php

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/contents/header.php";


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$database = new PDO('sqlite:' . $_ENV['DATABASE']);

function isValidUuid(string $uuid): bool
{

    if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
        return false;
    }

    return true;
}

$errors = [];

if (isset($_POST['transferCode'], $_POST['roomType'], $_POST['arrivalDate'], $_POST['departureDate'], $_POST['firstName'], $_POST['lastName'])) {
    $roomType = $_POST['roomType'];
    $arrivalDate = $_POST['arrivalDate'];
    $departureDate = $_POST['departureDate'];
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $transferCode = htmlspecialchars($_POST['transferCode']);

    $featuresMap = [
        1 => "coffee maker",
        2 => "heated lagoon",
        3 => "snowmobile"
    ];

    $insertGuestsQuery = 'INSERT INTO Guests (first_name, last_name) VALUES (:firstName, :lastName)';
    $insertGuests = $database->prepare($insertGuestsQuery);

    $insertGuests->bindParam(':firstName', $firstName, PDO::PARAM_STR);
    $insertGuests->bindParam(':lastName', $lastName, PDO::PARAM_STR);
    $insertGuests->execute();

    $guestIdQuery = $database->query('SELECT id FROM Guests WHERE first_name = :firstName');
    $guestIdQuery->bindParam(':firstName', $firstName, PDO::PARAM_STR);
    $guestIdQuery->execute();

    $guestIdResult = $guestIdQuery->fetch(PDO::FETCH_ASSOC);
    $guestId = $guestIdResult['id'];

    $roomIdQuery = $database->query('SELECT id FROM Rooms WHERE room_type = :roomType');
    $roomIdQuery->bindParam(':roomType', $roomType, PDO::PARAM_STR);
    $roomIdQuery->execute();

    $roomIdResult = $roomIdQuery->fetch(PDO::FETCH_ASSOC);
    $roomId = $roomIdResult['id'];

    $insertBookingsQuery = 'INSERT INTO Bookings (guest_id, room_id, arrival_date, departure_date) VALUES (:guestId, :roomId, :arrivalDate, :departureDate)';

    $insertBookings = $database->prepare($insertBookingsQuery);
    $insertBookings->bindParam(':guestId', $guestId, PDO::PARAM_INT);
    $insertBookings->bindParam(':roomId', $roomId, PDO::PARAM_INT);
    $insertBookings->bindParam(':arrivalDate', $arrivalDate);
    $insertBookings->bindParam(':departureDate', $departureDate);
    $insertBookings->execute();

    if (isset($_POST["features"])) {
        $features = $_POST["features"];

        $featuresMap = [
            1 => "coffee maker",
            2 => "heated lagoon",
            3 => "snowmobile"
        ];

        $selectedNames = [];

        foreach ($features as $feature) {
            if (isset($featuresMap[$feature])) {
                $selectedNames[] = $featuresMap[$feature];
            }
        }

        $bookingsIdQuery = $database->query('SELECT id FROM Bookings WHERE guest_id = :guestId');
        $bookingsIdQuery->bindParam(':guestId', $guestId, PDO::PARAM_INT);
        $bookingsIdQuery->execute();

        $bookingIdResult = $bookingsIdQuery->fetch(PDO::FETCH_ASSOC);
        $bookingId = $bookingIdResult['id'];

        $junctionInsertQuery = "INSERT INTO Junction (feature_id, booking_id) VALUES (:featureId, :bookingId)";


        foreach ($features as $feature) {
            if (isset($featuresMap[$feature])) {
                $junctionInsert = $database->prepare($junctionInsertQuery);

                $junctionInsert->bindParam(':featureId', $feature, PDO::PARAM_INT);
                $junctionInsert->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);

                $junctionInsert->execute();
            }
        }
    }

    $confirmation = [];

    if (isset($selectedNames)) {
        $confirmation = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'roomType' => $roomType,
            'arrivalDate' => $arrivalDate,
            'departureDate' => $departureDate,
            'features' => $selectedNames
        ];
    } else {
        $confirmation = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'roomType' => $roomType,
            'arrivalDate' => $arrivalDate,
            'departureDate' => $departureDate,
            'features' => 'none'
        ];
    }

    $booking = json_encode($confirmation, JSON_PRETTY_PRINT);
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
            <div class="popupContainer">
                <button class="closeWindow" id="closeWindow">
                    <img src="assets/images/window-close.png" alt="" class="closeWindowImage">
                </button>
                <pre class="popupText" id="pupupText">Booking Confirmation:
        
<?= $booking; ?>
                </pre>
            </div>
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
