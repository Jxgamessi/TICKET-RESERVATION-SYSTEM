<?php
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');
// Define a class to store bus information 
class Bus {
    public $busNumber;
    public $source;
    public $destination;
    public $totalSeats;
    public $availableSeats;
    public $fare;

    function __construct($busNumber, $source, $destination, $totalSeats, $availableSeats, $fare) {
        $this->busNumber = $busNumber;
        $this->source = $source;
        $this->destination = $destination;
        $this->totalSeats = $totalSeats;
        $this->availableSeats = $availableSeats;
        $this->fare = $fare;
    }
}

// Define a class to store passenger information 
class Passenger {
    public $name;
    public $age;
    public $seatNumber;
    public $busNumber;
    public $numberOfTickets;

    function __construct($name, $age, $seatNumber, $busNumber, $numberOfTickets) {
        $this->name = $name;
        $this->age = $age;
        $this->seatNumber = $seatNumber;
        $this->busNumber = $busNumber;
        $this->numberOfTickets = $numberOfTickets;
    }
}

// Define a class to store user login information 
class User {
    public $username;
    public $password;
    public $isAdmin;

    function __construct($username, $password, $isAdmin = false) {
        $this->username = $username;
        $this->password = $password;
        $this->isAdmin = $isAdmin;
    }
}

// Function to display the main menu 
function displayMainMenu() {
    echo "\n=== Bus Reservation System ===\n";
    echo "1. Login\n";
    echo "2. Register\n"; 
    echo "3. View Available Buses\n";
    echo "4. Admin Login\n"; 
    echo "5. Logout\n";  // Added option for Logout
    echo "Enter your choice: ";
}


// Function to display the user menu 
function displayUserMenu() {
    echo "\n=== User Menu ===\n";
    echo "1. Book a Ticket\n";
    echo "2. Cancel a Ticket\n";
    echo "3. Check Bus Status\n";
    echo "4. View Booked Tickets\n";
    echo "5. Search for a Bus\n"; 
    echo "6. View Passenger List for a Bus\n"; 
    echo "7. Calculate Total Fare for a Passenger\n"; 
    echo "8. View User Profile\n"; 
    echo "9. View Bus Schedule\n"; 
    echo "10. Change Password\n";
    echo "11. Logout\n";
    echo "Enter your choice: ";
}

// Function to perform user registration 
function registerUser(&$users, $username, $password) {
    // Check if username already exists
    foreach ($users as $user) {
        if ($user->username === $username) {
            echo "Username already exists. Please choose a different one or LOGIN.\n";
            return false;
        }
    }
    // Add new user
    $users[] = new User($username, $password);
    echo "Registration successful. You can now login with your new credentials.\n";

    // Write the new user data to the users.csv file
    $newUserData = array(
        array($username, $password)
    );
    appendCsvData("C:\\xampp\\htdocs\\users.csv", $newUserData);

    return true;
}

// Function to perform user login 
function loginUser($users, $username, $password) {
    foreach ($users as $index => $user) {
        if ($user->username === $username && $user->password === $password) {
            return $index; // Return the index of the logged-in user 
        }
    }
    return -1; // Return -1 if login fails 
}


// Function to search for a bus by source or destination and display in table view
function searchBus($buses, $keyword) {
    $matchedBuses = array();
    foreach ($buses as $bus) {
        if (stripos($bus->source, $keyword) !== false || stripos($bus->destination, $keyword) !== false) {
            $matchedBuses[] = $bus;
        }
    }
    if (empty($matchedBuses)) {
        echo "No buses found matching the search criteria.\n";
    } else {
        echo "\n=== Matched Buses ===\n";
        // Header row
        printf("%-15s %-15s %-15s\n", "Bus Number", "Source", "Destination");

        // Data rows
        foreach ($matchedBuses as $matchedBus) {
            printf("%-15d %-15s %-15s\n", $matchedBus->busNumber, $matchedBus->source, $matchedBus->destination);
        }
    }
}

// Function to view passenger list for a bus and display in table view
function viewPassengerList($passengers, $busNumber) {
    $busPassengers = array();
    foreach ($passengers as $passenger) {
        if ($passenger->busNumber === $busNumber) {
            $busPassengers[] = $passenger;
        }
    }
    if (empty($busPassengers)) {
        echo "No passengers booked for this bus.\n";
    } else {
        echo "\n=== Passenger List ===\n";
        // Header row
        printf("%-20s %-15s\n", "Passenger Name", "Seat Number");

        // Data rows
        foreach ($busPassengers as $busPassenger) {
            printf("%-20s %-15d\n", $busPassenger->name, $busPassenger->seatNumber);
        }
    }
}


// Function to calculate total fare for a passenger
function calculateTotalFare($buses, $busNumber, $numSeats) {
    $busNumber = (int)$busNumber; // Convert busNumber to integer
    foreach ($buses as $bus) {
        if ((int)$bus->busNumber === $busNumber) { // Convert busNumber to integer
            $totalFare = $bus->fare * $numSeats;
            echo "Total fare for $numSeats seats on Bus Number $busNumber: $totalFare\n";
            return;
        }
    }
    echo "Bus with Bus Number $busNumber not found.\n";
}


// Function to view user profile
function viewUserProfile($users, $userId) {
    echo "\n=== User Profile ===\n";
    echo "Username: {$users[$userId]->username}\n";
    // Additional user profile information can be displayed here
}

// Function to view bus schedule and display in table view
function viewBusSchedule($buses) {
    echo "\n=== Bus Schedule ===\n";
    // Header row
    printf("%-12s %-20s %-20s\n", "Bus Number", "Source", "Destination");

    // Data rows
    foreach ($buses as $bus) {
        printf("%-12d %-20s %-20s\n", $bus->busNumber, $bus->source, $bus->destination);
        // Additional schedule information can be displayed here
    }
}


// Function to book a ticket 
function bookTicket($buses, &$passengers, &$numPassengers, $userId) {
    echo "\nEnter Bus Number: ";
    $busNumber = (int)readline();

    // Find the bus with the given busNumber 
    $busIndex = -1;
    foreach ($buses as $index => $bus) {
        if ((int)$bus->busNumber === $busNumber) {
            
            $busIndex = $index;
            break;
        }
    }

    if ($busIndex === -1) {
        echo "Bus with Bus Number $busNumber not found.\n";
    } elseif ($buses[$busIndex]->availableSeats === 0) {
        echo "Sorry, the bus is fully booked.\n";
    } else {
        echo "Enter Passenger Name: ";
        $name = readline();

        echo "Enter Passenger Age: ";
        $age = (int)readline();

        echo "Enter Number of Tickets: ";
        $numberOfTickets = (int)readline();
        // echo "Number of Tickets entered: $numberOfTickets\n";

        if ($numberOfTickets > $buses[$busIndex]->availableSeats) {
            echo "Sorry, there are not enough seats available.\n";
            return;
        }

        // Prompt user for seat numbers for each ticket
        $seatNumbers = array();
        for ($i = 0; $i < $numberOfTickets; $i++) {
            echo "Enter Seat Number for Ticket " . ($i + 1) . ": ";
            $seatNumber = (int)readline();
            // echo "Seat Number entered: $seatNumber\n";

            // Validate seat number
            if ($seatNumber < 1 || $seatNumber > $buses[$busIndex]->totalSeats) {
                echo "Invalid seat number. Please enter a valid seat number.\n";
                $i--; // Decrement $i to repeat the prompt for this ticket
                continue;
            }

            // Check if seat is available
            $seatIndex = array_search($seatNumber, $seatNumbers);
            if ($seatIndex !== false) {
                echo "Seat $seatNumber is already selected. Please choose another seat.\n";
                $i--; // Decrement $i to repeat the prompt for this ticket
                continue;
            }

            // Add seat number to the list
            $seatNumbers[] = $seatNumber;
        }

        // Update available seats 
        $buses[$busIndex]->availableSeats -= $numberOfTickets;

        // Create Passenger objects for each ticket
        for ($i = 0; $i < $numberOfTickets; $i++) {
            $passenger = new Passenger($name, $age, $seatNumbers[$i], $busNumber, 1);
            $passengers[] = $passenger;
            $numPassengers++;
        }

        echo "Tickets booked successfully!\n";
    }
}





//cancel ticket
function cancelTicket($buses, &$passengers, &$numPassengers, $userId) {
    echo "\nEnter Bus Number: ";
    $busNumber = (int)readline();

    // Find the index of the bus with the given busNumber
    $busIndex = -1;
    foreach ($buses as $index => $bus) {
        if ((int)$bus->busNumber === $busNumber) {
            $busIndex = $index;
            break;
        }
    }

    // Check if the bus with the given busNumber exists
    if ($busIndex === -1) {
        echo "Bus with Bus Number $busNumber not found.\n";
        return;
    }

    echo "Enter Passenger Name: ";
    $name = readline();

    // Check if passenger with the given name is booked on the specified bus
    $found = false;
    foreach ($passengers as $index => $passenger) {
        if ($passenger->name === $name && $passenger->busNumber === $busNumber) {
            // Increase available seats 
            $buses[$busIndex]->availableSeats++;

            // Remove the passenger entry 
            array_splice($passengers, $index, 1);
            $numPassengers--;
            $found = true;
            echo "Ticket canceled successfully!\n";
            break;
        }
    }

    if (!$found) {
        echo "Passenger with name $name not found on Bus Number $busNumber.\n";
    }
}


// Function to check bus status and display in table view
function checkBusStatus($buses, $busNumber) {
    $found = false;
    foreach ($buses as $bus) {
        if ((int)$bus->busNumber === $busNumber) { // Convert busNumber to integer
            echo "\n=== Bus Status ===\n";
            printf("%-15s %-15s %-15s %-15s %-15s %-15s\n", "Bus Number", "Source", "Destination", "Total Seats", "Available Seats", "Fare");
            printf("%-15d %-15s %-15s %-15d %-15d %-15f\n", $bus->busNumber, $bus->source, $bus->destination, $bus->totalSeats, $bus->availableSeats, $bus->fare);
            $found = true;
            break;
        }
    }
    if (!$found) {
        echo "Bus with Bus Number $busNumber not found.\n";
    }
}


// Function to view booked tickets in a table view
function viewBookedTickets($passengers) {
    if (empty($passengers)) {
        echo "No tickets booked yet.\n";
    } else {
        echo "\n=== Booked Tickets ===\n";
        // Header row
        printf("%-20s %-20s %-20s\n", "Passenger Name", "Seat Number", "Bus Number");

        // Data rows
        foreach ($passengers as $passenger) {
            printf("%-20s %-20d %-20d\n", $passenger->name, $passenger->seatNumber, $passenger->busNumber);
        }
    }
}

// Function to add a new bus
function addBus(&$buses) {
    echo "\nEnter Bus Number: ";
    $busNumber = (int)readline();

    echo "Enter Source: ";
    $source = readline();

    echo "Enter Destination: ";
    $destination = readline();

    echo "Enter Total Seats: ";
    $totalSeats = (int)readline();

    echo "Enter Available Seats: ";
    $availableSeats = (int)readline();

    echo "Enter Fare: ";
    $fare = (float)readline();

    $buses[] = new Bus($busNumber, $source, $destination, $totalSeats, $availableSeats, $fare);
    echo "Bus added successfully!\n";

    // Write the new bus data to the buses.csv file
    $newBusData = array(
        array($busNumber, $source, $destination, $totalSeats, $availableSeats, $fare)
    );
    appendCsvData("C:\\xampp\\htdocs\\buses.csv", $newBusData);
}

// Function to edit an existing bus
function editBus(&$buses) {
    echo "\nEnter Bus Number to edit: ";
    $busNumber = (int)readline();

    $found = false;
    foreach ($buses as $bus) {
        if ((int)$bus->busNumber === $busNumber) { // Convert busNumber to integer
            echo "Enter New Source (leave empty to keep current): ";
            $source = readline();

            echo "Enter New Destination (leave empty to keep current): ";
            $destination = readline();

            echo "Enter New Total Seats (leave empty to keep current): ";
            $totalSeats = readline();

            echo "Enter New Available Seats (leave empty to keep current): ";
            $availableSeats = readline();

            echo "Enter New Fare (leave empty to keep current): ";
            $fare = readline();

            // Update the bus information if new values are provided
            if (!empty($source)) {
                $bus->source = $source;
            }
            if (!empty($destination)) {
                $bus->destination = $destination;
            }
            if (!empty($totalSeats)) {
                $bus->totalSeats = $totalSeats;
            }
            if (!empty($availableSeats)) {
                $bus->availableSeats = $availableSeats;
            }
            if (!empty($fare)) {
                $bus->fare = $fare;
            }
            echo "Bus updated successfully!\n";

            // Update the buses.csv file with the edited data
            updateCsvData("C:\\xampp\\htdocs\\buses.csv", $buses);
            
            $found = true;
            break;
        }
    }
    if (!$found) {
        echo "Bus with Bus Number $busNumber not found.\n";
    }
}


// Function to delete a bus
function deleteBus(&$buses) {
    echo "\nEnter Bus Number to delete: ";
    $busNumber = (int)readline();

    $found = false;
    foreach ($buses as $index => $bus) {
        if ((int)$bus->busNumber === $busNumber) { // Convert busNumber to integer
            array_splice($buses, $index, 1);
            echo "Bus deleted successfully!\n";

            // Update the buses.csv file after deletion
            updateCsvData("C:\\xampp\\htdocs\\buses.csv", $buses);
            
            $found = true;
            break;
        }
    }
    if (!$found) {
        echo "Bus with Bus Number $busNumber not found.\n";
    }
}

// Function to delete a ticket by name and seat number
function deleteTicket(&$passengers, &$numPassengers) {
    echo "\nEnter Passenger Name: ";
    $name = readline();

    echo "Enter Seat Number: ";
    $seatNumber = (int)readline();

    $found = false;
    foreach ($passengers as $index => $passenger) {
        if ($passenger->name === $name && $passenger->seatNumber === $seatNumber) {
            // Increase available seats 
            $busNumber = $passenger->busNumber;
            $found = true;
            echo "Ticket for Passenger $name with Seat Number $seatNumber on Bus Number $busNumber deleted successfully!\n";
            array_splice($passengers, $index, 1);
            $numPassengers--;
            break;
        }
    }
    if (!$found) {
        echo "Ticket for Passenger $name with Seat Number $seatNumber not found.\n";
    }
}
// Function to update password in the users.csv file
function updatePasswordInCSV($users) {
    $csvData = [];
    foreach ($users as $user) {
        $csvData[] = [$user->username, $user->password, $user->isAdmin];
    }
    $fp = fopen('users.csv', 'w');
    foreach ($csvData as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
}


// Function to view all booked tickets in a table format
function viewAllBookedTickets($passengers) {
    if (empty($passengers)) {
        echo "No tickets booked yet.\n";
    } else {
        echo "\n=== All Booked Tickets ===\n";
        printf("%-20s %-20s %-20s\n", "Passenger Name", "Seat Number", "Bus Number");
        foreach ($passengers as $passenger) {
            printf("%-20s %-20d %-20d\n", $passenger->name, $passenger->seatNumber, $passenger->busNumber);
        }
    }
}


// Function to read CSV data
function readCsvData($filePath) {
    $data = array();
    if (($handle = fopen($filePath, "r")) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            $data[] = $row;
        }
        fclose($handle);
    }
    return $data;
}

// Function to append data to a CSV file
function appendCsvData($filePath, $data) {
    if (($handle = fopen($filePath, "a")) !== false) {
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }
}

// Function to update CSV data
function updateCsvData($filePath, $data) {
    if (($handle = fopen($filePath, "w")) !== false) {
        foreach ($data as $row) {
            fputcsv($handle, (array)$row);
        }
        fclose($handle);
    }
}



// Initialize user data from CSV file
function initializeUserDataFromCSV($filePath) {
    $users = array();
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $username = $data[0];
            $password = $data[1];
            $isAdmin = isset($data[2]) ? filter_var($data[2], FILTER_VALIDATE_BOOLEAN) : false;
            $users[] = new User($username, $password, $isAdmin);
        }
        fclose($handle);
    }
    return $users;
}

// Function to read user login data from CSV file
function readUserLoginDataFromCSV($filePath) {
    $users = array();
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $username = isset($data[0]) ? $data[0] : null;
            $password = isset($data[1]) ? $data[1] : null;
            $isAdmin = isset($data[2]) ? filter_var($data[2], FILTER_VALIDATE_BOOLEAN) : false;
            if ($username !== null && $password !== null) {
                $users[] = new User($username, $password, $isAdmin);
            }
        }
        fclose($handle);
    }
    return $users;
}


// Initialize user login data from CSV file
$usersFilePath = "C:\\xampp\\htdocs\\users.csv";
$users = readUserLoginDataFromCSV($usersFilePath);

// Initialize admin login data from CSV file
$adminFilePath = "C:\\xampp\\htdocs\\admin.csv";
$admins = readUserLoginDataFromCSV($adminFilePath);

// Merge admin data into the users array
$users = array_merge($users, $admins);
$numUsers = count($users);



// Read bus data from CSV file
$busesData = readCsvData("C:\\xampp\\htdocs\\buses.csv");
$buses = array();

// Loop through each row of data
foreach ($busesData as $row) {
    // Create a new Bus object with data from the CSV row
    $bus = new Bus($row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
    $buses[] = $bus;
}

$numBuses = count($buses);


// Initialize passenger data 
$passengers = array();
$numPassengers = 0;

// Main program 
$loggedInUserId = -1;
$isAdmin = false;
do {
    displayMainMenu();
    $choice = (int)readline();

    switch ($choice) {
        case 1: // Login
            echo "\nEnter Username: ";
            $username = readline();
            echo "Enter Password: ";
            $password = readline();
            $loggedInUserId = loginUser($users, $username, $password);
            if ($loggedInUserId !== -1) {
                $isAdmin = $users[$loggedInUserId]->isAdmin;
                echo "Login successful! WELCOME {$users[$loggedInUserId]->username}\n";

                if (!$isAdmin) {
                    do {
                        displayUserMenu();
                        $userChoice = (int)readline();

                        switch ($userChoice) {
                            case 1: // Book a Ticket
                                bookTicket($buses, $passengers, $numPassengers, $loggedInUserId);
                                break;
                            case 2: // Cancel a Ticket
                                cancelTicket($buses, $passengers, $numPassengers, $loggedInUserId);
                                break;
                            case 3: // Check Bus Status
                                echo "\nEnter Bus Number: ";
                                $busNumber = (int)readline();
                                checkBusStatus($buses, $busNumber);
                                break;
                            case 4: // View Booked Tickets
                                viewBookedTickets($passengers);
                                break;
                            case 5: // Search for a Bus
                                echo "\nEnter Source or Destination: ";
                                $keyword = readline();
                                searchBus($buses, $keyword);
                                break;
                            case 6: // View Passenger List for a Bus
                                echo "\nEnter Bus Number: ";
                                $busNumber = (int)readline();
                                viewPassengerList($passengers, $busNumber);
                                break;
                            case 7: // Calculate Total Fare for a Passenger
                                echo "\nEnter Bus Number: ";
                                $busNumber = (int)readline();
                                echo "Enter Number of Seats: ";
                                $numSeats = (int)readline();
                                calculateTotalFare($buses, $busNumber, $numSeats);
                                break;
                            case 8: // View User Profile
                                viewUserProfile($users, $loggedInUserId);
                                break;
                            case 9: // View Bus Schedule
                                viewBusSchedule($buses);
                                break;
                            case 10: // Change Password
                                echo "\nEnter New Password: ";
                                $newPassword = readline();
                                $users[$loggedInUserId]->password = $newPassword;
                                updatePasswordInCSV($users);
                                echo "Password changed successfully!\n";
                                break;
                            case 11: // Logout
                                echo "Logged out successfully!\n";
                                break;
                            default:
                                echo "Invalid choice. Please enter a number between 1 and 11.\n";
                        }
                    } while ($userChoice !== 11);
                }
            } else {
                echo "Login failed. Invalid username or password.\n";
            }
            break;
        case 2: // Register
            echo "\nEnter Username: ";
            $newUsername = readline();
            echo "Enter Password: ";
            $newPassword = readline();
            registerUser($users, $newUsername, $newPassword);
            break;
        case 3: // View Available Buses
            echo "\n=== Available Buses ===\n";
                // Header row
            printf("%-12s %-20s %-20s %-18s\n", "Bus Number", "Source", "Destination", "Available Seats");
            
                // Data rows
            foreach ($buses as $bus) {
                printf("%-12d %-20s %-20s %-18d\n", $bus->busNumber, $bus->source, $bus->destination, $bus->availableSeats);
            }
            break;
            
        case 4: // Admin Login
            echo "\nEnter Username: ";
            $username = readline();
            echo "Enter Password: ";
            $password = readline();
            
            if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
                echo "Admin login successful! WELCOME Admin.\n";
                do {
                    echo "\n=== Admin Menu ===\n";
                    echo "1. Add Bus\n";
                    echo "2. Edit Bus\n";
                    echo "3. Delete Bus\n";
                    echo "4. View All Booked Tickets\n";
                    echo "5. Delete Ticket by Name and Seat Number\n";
                    echo "6. Logout\n";
                    echo "Enter your choice: ";
                    $adminChoice = (int)readline();

                    switch ($adminChoice) {
                        case 1: // Add Bus
                            addBus($buses);
                            break;
                        case 2: // Edit Bus
                            editBus($buses);
                            break;
                        case 3: // Delete Bus
                            deleteBus($buses);
                            break;
                        case 4: // View All Booked Tickets
                            viewAllBookedTickets($passengers);
                            break;
                        case 5: // Delete Ticket by Name
                            deleteTicket($passengers, $numPassengers);
                            break;
                        case 6: // Logout
                            echo "Logged out successfully!\n";
                            break;
                        default:
                            echo "Invalid choice. Please enter a number between 1 and 6.\n";
                    }
                } while ($adminChoice !== 6);
            } else {
                echo "Admin login failed. Invalid username or password.\n";
            }
            break;
        case 5: // Exit
            echo "Exiting...\n";
            break;
        default:
            echo "Invalid choice. Please enter a number between 1 and 5.\n";
    }
} while ($choice !== 5);

?>
