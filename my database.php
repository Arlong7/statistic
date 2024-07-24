CREATE TABLE `Employee` (
    `E_ID` INT AUTO_INCREMENT PRIMARY KEY,
    `Name` VARCHAR(255) NOT NULL,
    `Surname` VARCHAR(255) NOT NULL,
    `Gender` VARCHAR(50) NOT NULL,
    `Age` INT NOT NULL,
    `DateOfBirth` DATE NOT NULL,
    `Address` VARCHAR(255) NOT NULL,
    `PhoneNumber` VARCHAR(50) NOT NULL,
    `Status` VARCHAR(50) NOT NULL,
    `Email` VARCHAR(255) NOT NULL UNIQUE,
    `DepartmentID` INT,
    FOREIGN KEY (`DepartmentID`) REFERENCES `Department`(`dep_id`)
);
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Department` (
    `dep_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `location` VARCHAR(255) NOT NULL
);
