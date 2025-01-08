<?php

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/contents/header.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

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

function checkTransferCode($transferCode, $totalPrice)
{
    $client = new Client();

    try {

        $response = $client->post('https://www.yrgopelago.se/centralbank/transferCode', [
            'json' => [
                'transferCode' => $transferCode,
                'totalcost' => $totalPrice
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['status'] ?? false;
    } catch (ClientException $used) {
        $response = $used->getResponse()->getBody()->getContents();

        return false;
    }
}

function depositTransferCode($transferCode, $numberOfDays)
{
    $client = new Client();

    try {


        $response = $client->post('https://www.yrgopelago.se/centralbank/deposit', [
            'json' => [
                'user' => 'Tobias',
                'transferCode' => $transferCode,
                'numberOfDays' => $numberOfDays
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['status'] ?? false;
    } catch (ClientException $deposit) {
        $response = $deposit->getResponse()->getBody()->getContents();

        return false;
    }
}

$errors = [];

if (isset($_POST['transferCode'], $_POST['roomType'], $_POST['arrivalDate'], $_POST['departureDate'], $_POST['firstName'], $_POST['lastName'])) {
    $roomType = $_POST['roomType'];
    $arrivalDate = $_POST['arrivalDate'];
    $departureDate = $_POST['departureDate'];
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $transferCode = htmlspecialchars($_POST['transferCode']);

    $arrivalDate = date('Y-m-d', strtotime($arrivalDate));
    $departureDate = date('Y-m-d', strtotime($departureDate));

    $featuresMap = [
        1 => "coffee maker",
        2 => "heated lagoon",
        3 => "snowmobile"
    ];

    $roomAvailabilityQuery = $database->query('SELECT count(*) AS overlappingBookings 
    FROM Bookings 
    INNER JOIN Rooms 
    ON Bookings.room_id = Rooms.id 
    WHERE Rooms.room_type = :roomType 
    AND (
    (Bookings.arrival_date < :departureDate AND Bookings.departure_date > :arrivalDate)
    )');

    $roomAvailabilityQuery->bindParam(':roomType', $roomType, PDO::PARAM_STR);
    $roomAvailabilityQuery->bindParam(':arrivalDate', $arrivalDate, PDO::PARAM_STR);
    $roomAvailabilityQuery->bindParam(':departureDate', $departureDate, PDO::PARAM_STR);
    $roomAvailabilityQuery->execute();

    $roomAvailabilityResult = $roomAvailabilityQuery->fetch(PDO::FETCH_ASSOC);
    $roomAvailability = $roomAvailabilityResult['overlappingBookings'];

    $arrival = new DateTime($arrivalDate);
    $departure = new DateTime($departureDate);

    $interval = $arrival->diff($departure);

    $numberOfDays = $interval->days + 1;

    $roomPriceQuery = $database->query('SELECT price FROM Rooms WHERE room_type = :roomType');
    $roomPriceQuery->bindParam(':roomType', $roomType, PDO::PARAM_STR);
    $roomPriceQuery->execute();

    $roomPriceResult = $roomPriceQuery->fetch(PDO::FETCH_ASSOC);
    $roomPrice = $roomPriceResult['price'];

    if ($numberOfDays >= 3) {
        $discount = 0.30;
    } else {
        $discount = 0;
    }

    $featurePrice = 0;

    if (isset($_POST["features"])) {
        $features = $_POST["features"];

        $selectedFeatures  = str_repeat('?,', count($features) - 1) . '?';
        $featurePriceQuery = $database->prepare('SELECT price FROM Features WHERE id IN (' . $selectedFeatures . ')');
        $featurePriceQuery->execute($features);

        $featurePriceResult = $featurePriceQuery->fetchAll(PDO::FETCH_ASSOC);
        foreach ($featurePriceResult as $feature) {
            $featurePrice += $feature['price'];
        }
    }

    $totalRoomPrice = $roomPrice * $numberOfDays;

    $totalPrice = $totalRoomPrice + $featurePrice;

    if ($discount > 0) {
        $totalPrice = $totalPrice - ($totalPrice * $discount);
    }

    $totalPrice = round($totalPrice, 2);

    if ($roomAvailability > 0) {
        $errors[] = 'room is not available';
    } else {
        if (isValidUuid($transferCode)) {
            if (checkTransferCode($transferCode, $totalPrice)) {
                if (depositTransferCode($transferCode, $numberOfDays)) {

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

                    $insertBookingsQuery = 'INSERT INTO Bookings (guest_id, room_id, arrival_date, departure_date, total_price) VALUES (:guestId, :roomId, :arrivalDate, :departureDate, :totalPrice)';

                    $insertBookings = $database->prepare($insertBookingsQuery);
                    $insertBookings->bindParam(':guestId', $guestId, PDO::PARAM_INT);
                    $insertBookings->bindParam(':roomId', $roomId, PDO::PARAM_INT);
                    $insertBookings->bindParam(':arrivalDate', $arrivalDate, PDO::PARAM_STR);
                    $insertBookings->bindParam(':departureDate', $departureDate, PDO::PARAM_STR);
                    $insertBookings->bindParam(':totalPrice', $totalPrice, PDO::PARAM_INT);
                    $insertBookings->execute();

                    if (isset($features)) {

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

                        $bookingsIdQuery = $database->query('SELECT Bookings.id 
                        FROM Bookings 
                        INNER JOIN Guests
                        ON Guests.id = Bookings.guest_id
                        INNER JOIN Rooms
                        ON Rooms.id = Bookings.room_id
                        WHERE Guests.id = :guestId
                        AND Rooms.room_type = :roomType
                        AND Bookings.arrival_date = :arrivalDate
                        AND Bookings.departure_date = :departureDate');
                        $bookingsIdQuery->bindParam(':guestId', $guestId, PDO::PARAM_STR);
                        $bookingsIdQuery->bindParam(':roomType', $roomType, PDO::PARAM_STR);
                        $bookingsIdQuery->bindParam(':arrivalDate', $arrivalDate, PDO::PARAM_STR);
                        $bookingsIdQuery->bindParam(':departureDate', $departureDate, PDO::PARAM_STR);
                        $bookingsIdQuery->execute();

                        $bookingIdResult = $bookingsIdQuery->fetch(PDO::FETCH_ASSOC);
                        $bookingId = $bookingIdResult['id'];

                        $junctionInsertQuery = "INSERT INTO BookingFeaturesJunction (feature_id, booking_id) VALUES (:featureId, :bookingId)";


                        foreach ($features as $feature) {
                            if (isset($featuresMap[$feature])) {
                                $junctionInsert = $database->prepare($junctionInsertQuery);

                                $junctionInsert->bindParam(':featureId', $feature, PDO::PARAM_INT);
                                $junctionInsert->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);

                                $junctionInsert->execute();
                            }
                        }
                    }

                    $featurePriceQuery = $database->query('SELECT sum(Features.price) AS featurePrice
                    FROM Features 
                    INNER JOIN BookingFeaturesJunction 
                    ON Features.id = BookingFeaturesJunction.feature_id 
                    INNER JOIN Bookings 
                    ON Bookings.id = BookingFeaturesJunction.booking_id
                    WHERE BookingFeaturesJunction.booking_id = :bookingId');

                    $featurePriceQuery->bindParam('bookingId', $bookingId, PDO::PARAM_INT);
                    $featurePriceQuery->execute();

                    $featurePriceResult = $featurePriceQuery->fetch(PDO::FETCH_ASSOC);
                    $featurePrice = $featurePriceResult['featurePrice'];

                    $confirmation = [];

                    if (isset($selectedNames)) {
                        $confirmation = [
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'island' => 'Isle of Eden',
                            'hotel' => 'Euclid Hotel',
                            'roomType' => $roomType,
                            'arrivalDate' => $arrivalDate,
                            'departureDate' => $departureDate,
                            'features' => $selectedNames,
                            'stars' => 4,
                            'totalPrice' => $totalPrice,
                            'additonalInfo' => [
                                'greeting' => 'Thanks for your booking at the Euclid hotel! We hope you look forward to your stay just as much as us. If you have any additional questions feel free to contact us!',
                                'videoUrl' => 'https://youtu.be/DDdByJYUVeA?si=yvdKyw25SVkdzNPU'
                            ]
                        ];
                    } else {
                        $confirmation = [
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'island' => 'Isle of Eden',
                            'roomType' => $roomType,
                            'arrivalDate' => $arrivalDate,
                            'departureDate' => $departureDate,
                            'features' => 'none',
                            'stars' => 4,
                            'totalPrice' => $totalPrice,
                            'additonalInfo' => [
                                'greeting' => 'Thanks for your booking at the Euclid hotel! We hope you look forward to your stay just as much as us. If you have any additional questions feel free to contact us!',
                                'videoUrl' => 'https://youtu.be/DDdByJYUVeA?si=yvdKyw25SVkdzNPU'
                            ]
                        ];

                        $booking = json_encode($confirmation, JSON_PRETTY_PRINT);
                    }
                } else {
                    $errors[] = "payment failed";
                }
            } else {
                $errors[] = "transferCode is invalid";
            }
        } else {
            $errors[] = "transferCode is not valid uuid";
        }
    }
}

use benhall14\phpCalendar\Calendar as calendar;

$economyCalendar = new calendar;
$economyCalendar->stylesheet();

$economyCalendar->useMondayStartingDate();
$economyCalendar->useFullDayNames();

$standardCalendar = new calendar;
$standardCalendar->stylesheet();

$standardCalendar->useMondayStartingDate();
$standardCalendar->useFullDayNames();

$luxuryCalendar = new calendar;
$luxuryCalendar->stylesheet();

$luxuryCalendar->useMondayStartingDate();
$luxuryCalendar->useFullDayNames();

?>

<?php if (isset($booking)) { ?>
    <section aria-label="popup">
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
} else if ($errors) { ?>
        <div class="popup" id="popup">
            <div class="popupContainer">
                <button class="closeWindow" id="closeWindow">
                    <img src="assets/images/window-close.png" alt="" class="closeWindowImage">
                </button>
                <?php foreach ($errors as $error) { ?>
                    <p class="popupText" id="pupupText">
                        <?= $error ?>
                    </p>
                <?php
                } ?>
            </div>
        </div>
    </section>
<?php
} ?>

<section aria-label="book">
    <div class="calendarRoomContainer">
        <div class="calendarContainer">
            <h2 class="labelText">Economy</h2>
            <div class="calendar">
                <?php
                $economyBookingsQuery = $database->query('SELECT arrival_date, departure_date FROM Bookings WHERE room_id = 1');
                $economyBookingsQuery->execute();

                $economyBookings = $economyBookingsQuery->fetchAll(PDO::FETCH_ASSOC);

                foreach ($economyBookings as $economyBooking) {
                    $economyCalendar->addEvent(
                        $economyBooking['arrival_date'],
                        $economyBooking['departure_date'],
                        "",
                        true
                    );
                }

                echo $economyCalendar->draw(date('2025-01-01'));

                ?>
            </div>
            <h2 class="labelText">Standard</h2>
            <div class="calendar">
                <?php
                $standardBookingsQuery = $database->query('SELECT arrival_date, departure_date FROM Bookings WHERE room_id = 2');
                $standardBookingsQuery->execute();

                $standardBookings = $standardBookingsQuery->fetchAll(PDO::FETCH_ASSOC);

                foreach ($standardBookings as $standardBooking) {
                    $standardCalendar->addEvent(
                        $standardBooking['arrival_date'],
                        $standardBooking['departure_date'],
                        "",
                        true
                    );
                }

                echo $standardCalendar->draw(date('2025-01-01'));

                ?>
            </div>
            <h2 class="labelText">Luxury</h2>
            <div class="calendar">
                <?php
                $luxuryBookingsQuery = $database->query('SELECT arrival_date, departure_date FROM Bookings WHERE room_id = 3');
                $luxuryBookingsQuery->execute();

                $luxuryBookings = $luxuryBookingsQuery->fetchAll(PDO::FETCH_ASSOC);

                foreach ($luxuryBookings as $luxuryBooking) {
                    $luxuryCalendar->addEvent(
                        $luxuryBooking['arrival_date'],
                        $luxuryBooking['departure_date'],
                        "",
                        true
                    );
                }

                echo $luxuryCalendar->draw(date('2025-01-01'));
                ?>
            </div>
        </div>
        <div class="roomsContainer">
            <div class="rooms">
                <h2 class="roomName">Economy:</h2>
                <img src="assets/images/economy-room.jpeg" alt="" class="roomImage">
                <p class="roomCost">Cost: 2$</p>
            </div>
            <div class="rooms">
                <h2 class="roomName">Standard:</h2>
                <img src="assets/images/standard-room.jpeg" alt="" class="roomImage">
                <p class="roomCost">Cost: 3$</p>
            </div>
            <div class="rooms">
                <h2 class="roomName">Luxury:</h2>
                <img src="assets/images/luxury-room.jpeg" alt="" class="roomImage">
                <p class="roomCost">Cost: 5$</p>
            </div>
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
        <input type="text" name="transferCode" class="input" id="transferCode" placeholder="Your transfer code.." required>

        <label for="features" class="labelText">Features</label>
        <div class="block">
            <input type="checkbox" name="features[]" value="1" class="checkbox">
            Coffee maker $2
        </div>
        <div class="block">
            <input type="checkbox" name="features[]" value="2" class="checkbox">
            Heated lagoon $4
        </div>
        <div class="block">
            <input type="checkbox" name="features[]" value="3" class="checkbox">
            Snowmobile $6
        </div>

        <button type="submit" class="bookButton" id="bookButton">Book now</button>
    </form>
</section>




<?php

require __DIR__ . "/contents/footer.php";
