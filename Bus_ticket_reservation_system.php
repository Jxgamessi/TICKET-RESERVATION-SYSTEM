<?php

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

    function __construct($name, $age, $seatNumber, $busNumber) {
        $this->name = $name;
        $this->age = $age;
        $this->seatNumber = $seatNumber;
        $this->busNumber = $busNumber;
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
    echo "2. Register\n"; // New feature
    echo "3. View Available Buses\n";
    echo "4. Admin Login\n"; // New feature
    echo "5. Exit\n";
    echo "Enter your choice: ";
}

// Function to display the user menu 
function displayUserMenu() {
    echo "\n=== User Menu ===\n";
    echo "1. Book a Ticket\n";
    echo "2. Cancel a Ticket\n";
    echo "3. Check Bus Status\n";
    echo "4. View Booked Tickets\n";
    echo "5. Search for a Bus\n"; // New feature
    echo "6. View Passenger List for a Bus\n"; // New feature
    echo "7. Calculate Total Fare for a Passenger\n"; // New feature
    echo "8. View User Profile\n"; // New feature
    echo "9. View Bus Schedule\n"; // New feature
    echo "10. Change Password\n";
    echo "11. Logout\n";
    echo "Enter your choice: ";
}

// Function to perform user registration 
function registerUser(&$users, $username, $password) {
    // Check if username already exists
    foreach ($users as $user) {
        if ($user->username === $username) {
            echo "Username already exists. Please choose a different one.\n";
            return false;
        }
    }
    // Add new user
    $users[] = new User($username, $password);
    echo "Registration successful. You can now login with your new credentials.\n";
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

// Function to search for a bus by source or destination
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
        foreach ($matchedBuses as $matchedBus) {
            echo "Bus Number: {$matchedBus->busNumber}, Source: {$matchedBus->source}, Destination: {$matchedBus->destination}\n";
        }
    }
}

// Function to view passenger list for a bus
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
        foreach ($busPassengers as $busPassenger) {
            echo "Passenger Name: {$busPassenger->name}, Seat Number: {$busPassenger->seatNumber}\n";
        }
    }
}

// Function to calculate total fare for a passenger
function calculateTotalFare($buses, $busNumber, $numSeats) {
    foreach ($buses as $bus) {
        if ($bus->busNumber === $busNumber) {
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

// Function to view bus schedule
function viewBusSchedule($buses) {
    echo "\n=== Bus Schedule ===\n";
    foreach ($buses as $bus) {
        echo "Bus Number: {$bus->busNumber}, Source: {$bus->source}, Destination: {$bus->destination}\n";
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
        if ($bus->busNumber === $busNumber) {
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

        // Assign a seat number to the passenger 
        $seatNumber = $buses[$busIndex]->totalSeats - $buses[$busIndex]->availableSeats + 1;

        // Update available seats 
        $buses[$busIndex]->availableSeats--;

        $passenger = new Passenger($name, $age, $seatNumber, $busNumber);
        $passengers[] = $passenger;
        $numPassengers++;
        echo "Ticket booked successfully!\n";
    }
}

// Function to cancel a ticket 
function cancelTicket($buses, &$passengers, &$numPassengers, $userId) {
    echo "\nEnter Passenger Name: ";
    $name = readline();

    $found = false;
    foreach ($passengers as $index => $passenger) {
        if ($passenger->name === $name && $passenger->busNumber === $buses[$userId]->busNumber) {
            // Increase available seats 
            $buses[$userId]->availableSeats++;

            // Remove the passenger entry 
            array_splice($passengers, $index, 1);
            $numPassengers--;
            $found = true;
            echo "Ticket canceled successfully!\n";
            break;
        }
    }
    if (!$found) {
        echo "Passenger with name $name not found on this bus.\n";
    }
}

// Function to check bus status 
function checkBusStatus($buses, $busNumber) {
    $found = false;
    foreach ($buses as $bus) {
        if ($bus->busNumber === $busNumber) {
            echo "\nBus Number: {$bus->busNumber}\n";
            echo "Source: {$bus->source}\n";
            echo "Destination: {$bus->destination}\n";
            echo "Total Seats: {$bus->totalSeats}\n";
            echo "Available Seats: {$bus->availableSeats}\n";
            echo "Fare: {$bus->fare}\n";
            $found = true;
            break;
        }
    }
    if (!$found) {
        echo "Bus with Bus Number $busNumber not found.\n";
    }
}

// Function to view booked tickets 
function viewBookedTickets($passengers) {
    $bookedTickets = array();
    foreach ($passengers as $passenger) {
        $bookedTickets[] = "Passenger Name: {$passenger->name}, Seat Number: {$passenger->seatNumber}, Bus Number: {$passenger->busNumber}";
    }
    if (empty($bookedTickets)) {
        echo "No tickets booked yet.\n";
    } else {
        echo "\n=== Booked Tickets ===\n";
        foreach ($bookedTickets as $ticket) {
            echo $ticket . "\n";
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
}

// Function to edit an existing bus
function editBus(&$buses) {
    echo "\nEnter Bus Number to edit: ";
    $busNumber = (int)readline();

    $found = false;
    foreach ($buses as $bus) {
        if ($bus->busNumber === $busNumber) {
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
        if ($bus->busNumber === $busNumber) {
            array_splice($buses, $index, 1);
            echo "Bus deleted successfully!\n";
            $found = true;
            break;
        }
    }
    if (!$found) {
        echo "Bus with Bus Number $busNumber not found.\n";
    }
}

// Function to delete a ticket by name
function deleteTicket(&$passengers, &$numPassengers) {
    echo "\nEnter Passenger Name to delete ticket: ";
    $name = readline();

    $found = false;
    foreach ($passengers as $index => $passenger) {
        if ($passenger->name === $name) {
            // Increase available seats 
            $busNumber = $passenger->busNumber;
            $found = true;
            echo "Ticket for Passenger $name on Bus Number $busNumber deleted successfully!\n";
            array_splice($passengers, $index, 1);
            $numPassengers--;
            break;
        }
    }
    if (!$found) {
        echo "Ticket for Passenger $name not found.\n";
    }
}

// Function to view all booked tickets
function viewAllBookedTickets($passengers) {
    if (empty($passengers)) {
        echo "No tickets booked yet.\n";
    } else {
        echo "\n=== All Booked Tickets ===\n";
        foreach ($passengers as $passenger) {
            echo "Passenger Name: {$passenger->name}, Seat Number: {$passenger->seatNumber}, Bus Number: {$passenger->busNumber}\n";
        }
    }
}

// Initialize user data 
$users = array(
    new User("user1", "password1"),
    new User("user2", "password2"),
    new User("user3", "password3"),
    new User("user4", "password4"),
    new User("user5", "password5"),
    new User("admin", "admin123", true) // Admin user
);
$numUsers = count($users);

// Initialize bus data 
$buses = array(
    new Bus(10, "CHENNAI", "GOA", 50, 50, 2500.0),
    new Bus(11, "MADURAI", "TRICHY", 40, 40, 500.0),
    new Bus(12, "DINDIGUL", "CHENNAI", 30, 30, 1500.0)
);
$numBuses = count($buses);

$passengers = array(); // Array to store passenger information 
$numPassengers = 0; // Number of passengers 

$loggedInUserId = -1; // Index of the logged-in user 

while (true) {
    if ($loggedInUserId === -1) {
        displayMainMenu();
        $choice = (int)readline();

        if ($choice === 1) {
            echo "Enter Username: ";
            $username = readline();
            echo "Enter Password: ";
            $password = readline();

            $loggedInUserId = loginUser($users, $username, $password);
            if ($loggedInUserId === -1) {
                echo "Login failed. Please check your username and password.\n";
            } else {
                echo "Login successful. Welcome, {$users[$loggedInUserId]->username}!\n";
            }
        } elseif ($choice === 2) {
            echo "Enter Username: ";
            $username = readline();
            echo "Enter Password: ";
            $password = readline();

            registerUser($users, $username, $password);
        } elseif ($choice === 3) {
            viewBusSchedule($buses);
        } elseif ($choice === 4) {
            echo "Enter Username: ";
            $username = readline();
            echo "Enter Password: ";
            $password = readline();

            $loggedInUserId = loginUser($users, $username, $password);
            if ($loggedInUserId !== -1 && $users[$loggedInUserId]->isAdmin) {
                echo "Admin login successful. Welcome, {$users[$loggedInUserId]->username}!\n";
            } else {
                echo "Admin login failed. Please check your username and password.\n";
                $loggedInUserId = -1; // Reset login status
            }
        } elseif ($choice === 5) {
            echo "Exiting the program.\n";
            break;
        } else {
            echo "Invalid choice. Please try again.\n";
        }
    } else {
        // If logged in user is an admin
        if ($users[$loggedInUserId]->isAdmin) {
            echo "\n=== Admin Menu ===\n";
            echo "1. Add Bus\n";
            echo "2. Edit Bus\n";
            echo "3. Delete Bus\n";
            echo "4. Delete Ticket\n"; // New feature
            echo "5. View All Booked Tickets\n"; // New feature
            echo "6. Logout\n";
            echo "Enter your choice: ";
            $adminChoice = (int)readline();

            switch ($adminChoice) {
                case 1:
                    addBus($buses);
                    break;
                case 2:
                    editBus($buses);
                    break;
                case 3:
                    deleteBus($buses);
                    break;
                case 4:
                    deleteTicket($passengers, $numPassengers);
                    break;
                case 5:
                    viewAllBookedTickets($passengers);
                    break;
                case 6:
                    echo "Logging out.\n";
                    $loggedInUserId = -1;
                    break;
                default:
                    echo "Invalid choice. Please try again.\n";
            }
        } else {
            displayUserMenu();
            $userChoice = (int)readline();

            switch ($userChoice) {
                case 1:
                    bookTicket($buses, $passengers, $numPassengers, $loggedInUserId);
                    break;
                case 2:
                    cancelTicket($buses, $passengers, $numPassengers, $loggedInUserId);
                    break;
                case 3:
                    echo "Enter Bus Number: ";
                    $busNumber = (int)readline();
                    checkBusStatus($buses, $busNumber);
                    break;
                case 4:
                    viewBookedTickets($passengers);
                    break;
                case 5:
                    echo "Enter keyword (source/destination): ";
                    $keyword = readline();
                    searchBus($buses, $keyword);
                    break;
                case 6:
                    echo "Enter Bus Number: ";
                    $busNumber = (int)readline();
                    viewPassengerList($passengers, $busNumber);
                    break;
                case 7:
                    echo "Enter Bus Number: ";
                    $busNumber = (int)readline();
                    echo "Enter Number of Seats: ";
                    $numSeats = (int)readline();
                    calculateTotalFare($buses, $busNumber, $numSeats);
                    break;
                case 8:
                    viewUserProfile($users, $loggedInUserId);
                    break;
                case 9:
                    viewBusSchedule($buses);
                    break;
                case 10:
                    echo "Enter New Password: ";
                    $newPassword = readline();
                    $users[$loggedInUserId]->password = $newPassword;
                    echo "Password changed successfully!\n";
                    break;
                case 11:
                    echo "Logging out.\n";
                    $loggedInUserId = -1;
                    break;
                default:
                    echo "Invalid choice. Please try again.\n";
            }
        }
    }
}

?>
