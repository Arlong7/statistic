-- Employee Table
CREATE TABLE Employee (
    E_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255),
    Surname VARCHAR(255),
    Gender VARCHAR(50),
    Age INT,
    DateOfBirth DATE,
    Address VARCHAR(255),
    PhoneNumber VARCHAR(20),
    Status VARCHAR(50),
    Email VARCHAR(255)

);

-- Stay Member Table
CREATE TABLE StayMember (
    P_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255),
    Surname VARCHAR(255),
    Gender VARCHAR(50),
    Status VARCHAR(50),
    PhoneNumber VARCHAR(20),
    IDNumber VARCHAR(255),
    Email VARCHAR(255),
    DayOfEntry DATE,
    Address VARCHAR(255),
    Position VARCHAR(255)
);

-- Complete Member Table
CREATE TABLE CompleteMember (
    CM_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255),
    Position VARCHAR(255),
    Address VARCHAR(255),
    Email VARCHAR(255),
    EntryDate DATE,
    PhoneNumber VARCHAR(20)
);

-- Alternate Member Table
CREATE TABLE AlternateMember (
    AM_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255),
    Position VARCHAR(255),
    Address VARCHAR(255),
    Email VARCHAR(255),
    EntryDate DATE,
    PhoneNumber VARCHAR(20)
);

-- Member Moves Out Table
CREATE TABLE MemberMovesOut (
    MO_ID INT AUTO_INCREMENT PRIMARY KEY,
    Modate DATE,
    P_ID INT,
    Reason VARCHAR(255)
);

-- Member Moves In Table
CREATE TABLE MemberMovesIn (
    MI_ID INT AUTO_INCREMENT PRIMARY KEY,
    MIdate DATE,
    P_ID INT,
    Reason VARCHAR(255)
);

-- Member Who Left Table ຍັງ
CREATE TABLE MemberWhoLeft (
    MWL_ID INT AUTO_INCREMENT PRIMARY KEY,
    NameOfIssuer VARCHAR(255),
    Gender VARCHAR(50),
    Address VARCHAR(255),
    Email VARCHAR(255),
    Position VARCHAR(255),
    PhoneNumber VARCHAR(20),
    DateOfEmployment DATE

);

-- New Member Table
CREATE TABLE NewMember (
    NM_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255),
    Gender VARCHAR(50),
    Address VARCHAR(255),
    Email VARCHAR(255),
    AccessCode VARCHAR(255),
    Position VARCHAR(255),
    PhoneNumber VARCHAR(20),
    DateOfEmployment DATE
);

-- Retired Table ຍັງ
CREATE TABLE Retired (
    RT_ID INT AUTO_INCREMENT PRIMARY KEY,
    PersonalInformation TEXT,
    WorkInformation TEXT,
    AdmissionToKamaBall TEXT,
    SalaryAndWelfareInformation TEXT
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
