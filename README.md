# Euclid Hotel

This is a project where i made a website for my own fictional hotel called Euclid Hotel. In this project i used html, css, php, sqlite and a little bit of javascript to make a functional booking service for a hotel.

## Installation Guide

If you would want to test this project yourself you would need a database i would also recommend a .env file for contents look at .env.example so that you can also fetch this database with ease. Now to structure my database i used these table querys:

CREATE TABLE IF NOT EXISTS Guests(
id INTEGER PRIMARY KEY AUTOINCREMENT,
first_name VARCHAR(100),
last_name VARCHAR(100));

CREATE TABLE IF NOT EXISTS Rooms(
id INTEGER PRIMARY KEY AUTOINCREMENT,
room_type VARCHAR(100),
price INTEGER);

CREATE TABLE IF NOT EXISTS Bookings(
id INTEGER PRIMARY KEY AUTOINCREMENT,
guest_id INTEGER,
room_id INTEGER,
arrival_date DATE,
departure_date DATE,
total_price INTEGER,
FOREIGN KEY (guest_id) REFERENCES Guest(id),
FOREIGN KEY (room_id) REFERENCES Rooms(id));

CREATE TABLE IF NOT EXISTS Features(
id INTEGER PRIMARY KEY AUTOINCREMENT,
feature_name VARCHAR(100),
price INTEGER);

CREATE TABLE IF NOT EXISTS BookingFeaturesJunction(
id INTEGER PRIMARY KEY AUTOINCREMENT,
feature_id INTEGER,
booking_id INTEGER,
FOREIGN KEY (feature_id) REFERENCES Features(id),
FOREIGN KEY (booking_id) REFERENCES Bookings(id));

INSERT INTO Rooms(room_type, price)
VALUES ("economy", 2), ("standard", 3), ("luxury", 5);

INSERT INTO Features(feature_name, price)
VALUES ("coffee maker", 2), ("heated lagoon", 4), ("snowmobile", 6);

You can of course change the contents to your liking. This website also uses an external api to consume transferCodes made from a website, therefore there are functions made in the booking file that checks if the given transferCode is a UUID and where it also sends the transferCode to the api to check if it is used and also have enough "money" on it and after that there is another function that then consumes the transferCode into "money" in the api. If you cant access this kind of api you can remove or just comment out the if statements where it checks the transferCode and the website should work just fine.

## Feedback from Malin

### General
- Comments that explain the code are missing throughout the entire project.

### `index.php`
- Missing `alt` text for images. Adding this would improve accessibility.

### `book-room.php`
- Comments would have been especially beneficial in this file.
- The file structure could be improved by separating:
  - Functions
  - Booking process
  - Variables
  - HTML
  - PHP etc.
- Splitting these into multiple files, or even separating them within the same file would enhance readability, maintainability, and reusability.
- More functions could have been used to make the booking sequence clearer.
- There are many nested `if` statements. A large number of if statements can increase the complexity of your code and make it more prone to errors.
- PHP and HTML are heavily mixed; separating them would improve readability and maintain a clear distinction between logic and presentation.

### `footer.php`
- Using kebab-case for class names is more consistent with HTML/CSS standards and makes longer names easier to read.
