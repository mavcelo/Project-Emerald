# Project-Emerald
This website is in progress. The general design is intended to be flexible for others to use for League of Legends tournaments and is supposed to make it easier for anyone to setup.

Currently, here is what you need for this to work:

on windows: install xammp, and run the apach2 webserver, along with the database. Login to the database and create a 'phplogin' database. Enter this database and add an 'accounts' table. Enter values for email, password, username, and ID (as a primary key)

on linux: install phpmyadmin, mysql-server, and apache2. Set these up. Add the same table and DB as the windows setup.

website will by default be hosted at http://localhost:80 and http://localhost:80/phpmyadmin

More detailed instructions will follow in the future

Both: be sure to install composer: (windows link - https://getcomposer.org/Composer-Setup.exe) in order to 
let the spreadsheet function work 

![image](https://github.com/SYNdiCull/Project-Emerald/assets/77362484/4ac315ea-89e6-46c5-be9a-444aed880d26)
![image](https://github.com/SYNdiCull/Project-Emerald/assets/77362484/8f262873-2346-442f-bb40-436d7026971e)

# Emerald Project README

## Table of Contents

1. Introduction
2. Features
3. Installation
    a. Installing PHP on Mac
    b. Installing PHP on Windows
4. Usage
5. Support
6. Contributing
7. License

## 1. Introduction

Welcome to the README for the Emerald Project! This website provides a comprehensive streaming service for online gamers, designed to enhance viewer enjoyment with features similar to larger gaming tournaments. Our focus is on automation, streamlined viewing, and providing player statistics, all within the context of the League of The Legend.

## 2. Features

- **Automated Streaming:** Enjoy a hassle-free streaming experience with automated processes that keep your content up to date.

- **Streamlined Viewing:** Navigate our website effortlessly to find the latest gaming content, highlights, and live streams with an intuitive user interface.

- **Player Statistics:** Get in-depth player statistics, including win rates, and more, to gain insights into your favorite gamers' performance.

## 3. Installation

### a. Installing PHP on Mac
To run the Emerald Project locally on your Mac, you'll need to install PHP. Follow these steps:
Mac:
Install Homebrew: brew install homebrew
Install PHP: brew install php
Login to the database and create a 'phplogin' database. Enter this database and add an 'accounts' table. Enter values for email, password, username, and ID (as a primary key)

### b. Installing PHP on Windows:
Download the latest version of PHP from the official website.
Run the installer and follow the on-screen instructions.

Install xammp, and run the apach2 webserver, along with the database. Login to the database and create a 'phplogin' database. Enter this database and add an 'accounts' table. Enter values for email, password, username, and ID (as a primary key)

on linux: install phpmyadmin, mysql-server, and apache2. Set these up. Add the same table and DB as the windows setup.

## 4. Usage
Once you have PHP installed, you can start using the Emerald Project locally. Follow these steps:

Clone this repository:

git clone [https://github.com/SYNdiCull/Project-Emerald)]
Configure the website settings as needed.

Start the PHP development server:

php -S localhost:8000
Access the website in your web browser at http://localhost:8000.

Using Emerald Project
To use Emerald Project, simply visit the website and create an account. Once you have an account, you can start watching streams, viewing player statistics, and more.

## 5. Support
If you need assistance or have any questions, please don't hesitate to reach out to us at [e-mail locked]. We are here to help and improve your project experience.

## 6. Contributing
We welcome contributions from the community. To contribute to the development of the Emerald Project, please follow these guidelines:

Fork the repository.
Create a new branch for your feature or bug fix.
Make your changes and test them thoroughly.
Create a pull request with a detailed description of your changes.

## 7. License
Emerald Project is still under development, but we are constantly adding new features and improvements. We hope you enjoy using our service!
