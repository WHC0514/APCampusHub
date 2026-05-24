APCampusHub

Project Overview

APCampusHub is a web-based campus booking and management system developed using PHP, MySQL, and WAMP Server.

It allows users to:

Book campus rooms (discussion rooms, lecture halls, etc.)
View room availability
Manage bookings efficiently

Access role-based dashboards
🚀 Features
🔐 Role-based login system (Admin / Student / Lecturer)
🏫 Room booking system
📅 Booking schedule management
🧑‍🎓 Student access control for special rooms
📊 Admin management panel
📍 Dynamic room display pages
🗂 MySQL database integration
🖥️ UI Preview

Add your actual screenshots in a folder called /screenshots

🔑 Login Page

🏠 Dashboard

🏫 Room Booking Page

📍 Room Display Page

⚙️ Installation Guide

👉 Full installation instructions are available in:

📄 Final Report Section 18.0 (Installation Guide)

⚡ Quick Setup
1. Install Requirements
WAMP Server
Visual C++ Redistributable
2. Project Setup

Copy project folder into:

C:\wamp64\www\APCampusHub
3. Database Setup
Open: http://localhost/phpmyadmin
Create database: apcampushub
Import: apcampushub.sql
4. Run Project
http://localhost/APCampusHub
🧪 Default Accounts
Admin
Email: admin@gmail.com
Password: admin123
Student
Email: student@gmail.com
Password: student123
📁 Project Structure
APCampusHub/
│
├── admin/
├── student/
├── lecturer/
├── config/
│   └── db.php
├── pages/
├── assets/
├── index.php
🧠 Tech Stack
PHP (Backend)
MySQL (Database)
HTML/CSS/JavaScript (Frontend)
WAMP Server (Local environment)
📌 Notes
This project is designed for local deployment using WAMP.
Some pages require URL parameters (e.g. room display pages).
Ensure Apache & MySQL are running before use.
👨‍💻 Developer

Name: Your Name
Institution: Your University/College
Course: Your Course Name

⭐ Acknowledgements

Special thanks to lecturers and peers for guidance and feedback during development.