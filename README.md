# Job Application System (Software Engineer)

Simple CRUD project using PHP PDO and MySQL.

## Features
- Submit new job application
- View all applications in HTML table
- Edit/update application
- Delete application with confirmation
- Auto-generated date added

## Technologies
PHP, MySQL, PDO, HTML, minimal CSS.

## Folder Structure
```
job-application-system/
├── config/database.php
├── includes/functions.php
├── index.php
├── edit.php
├── delete.php
├── database.sql
├── .gitignore
└── README.md
```

## Setup
1. Install XAMPP (or similar).
2. Copy project folder to `htdocs`.
3. Start Apache and MySQL.
4. Open phpMyAdmin, import `database.sql`.
5. Check `config/database.php` credentials (default root / empty password).
6. Visit `http://localhost/job-application-system/`.


## GitHub Upload
```
git init
git add .
git commit -m "Initial commit: Job Application System"
git branch -M main
git remote add origin <repo-url>
git push -u origin main
```
