# StreamHive

YouLite is a lightweight YouTube-inspired video platform built with PHP, PDO, MySQL, HTML, and CSS.

This project was created as a school assignment to practice:
- Database design
- PHP & PDO
- SQL relationships
- Dynamic content rendering
- Basic frontend styling
- Video handling

---

# Features

- View all uploaded videos
- Clickable video cards
- Individual video pages
- Comment system
- PDO database connection
- Responsive video grid layout
- Dummy data support
- Video playback using local uploads

---

# Technologies Used

- PHP
- PDO
- MySQL
- HTML5
- CSS3

---

# Database Structure

The project includes the following tables:

- users
- videos
- comments
- likes
- categories
- video_category
- password_reset

---

# Installation

## 1. Clone the repository

```bash
git clone https://github.com/gmxjnr/StreamHive
```

---

## 2. Move the project

Place the project inside your local server folder:

Example for XAMPP:

```text
htdocs/StreamHive
```

---

# 3. Database Setup

Import the SQL file located in:

```text
/setup/database.sql
```

---

## 4. Configure database connection

Open:

```text
db.php
```

Fill in your database credentials:

```php
$host = "YOUR_HOST";
$dbname = "YOUR_DATABASE";
$username = "YOUR_USERNAME";
$password = "YOUR_PASSWORD";
```

---

## 5. Add video files

Create an uploads folder:

```text
/uploads
```

Example:

```text
/uploads/minecraft1.mp4
```

Make sure the filename matches the filename stored in the database.
---

# Future Improvements

- User authentication
- Real video uploads
- Like system
- Search functionality
- Video categories
- User profiles
- Responsive sidebar
- Recommended videos

---

# Author

Created by Milan R.
