# SYSTEM BLUEPRINT

## 1. System Familiarization Summary

This document provides a technical overview of the CARELINK system.

### 1.1. Core Technologies

*   **Frontend:**
    *   **HTML5:** For structuring the content and layout of the application's pages.
    *   **CSS3:** For styling the user interface.
    *   **JavaScript (ES6):** For client-side interactivity and dynamic content.
*   **Backend (Planned):**
    *   **PHP:** Will be used for all server-side logic, including database interaction, user authentication, and API endpoints.
*   **External Libraries:**
    *   **Chart.js:** Integrated for data visualization, specifically for rendering charts on the dashboard pages.
    *   **Font Awesome:** Utilized for iconography throughout the application.

### 1.2. System Architecture

*   **Current Architecture:** The project follows a traditional multi-page web application architecture. It does not use a modern single-page application (SPA) framework.
*   **Future Architecture:** The system will evolve into a client-server model. The frontend HTML/CSS/JS will remain as is, but will be enhanced to make API calls to a PHP backend.
*   **Structure:** Each major feature or view is encapsulated in its own `.php` file.
*   **Styling:** The application uses a combination of inline CSS and shared stylesheets. A consistent design language is established through CSS variables.

### 1.3. Data Management and Flow

*   **Database:** A MySQL/MariaDB database is used to persist data. PHP is used to connect to and manage the database.
*   **Data Flow:**
    1.  The client-side JavaScript will use the `fetch` API to make asynchronous requests to the PHP backend.
    2.  The PHP backend will process these requests, interact with the database, and return data in JSON format.
    3.  **Real-time Updates:** JavaScript will implement a polling mechanism using `setInterval` to periodically fetch updated data from dedicated PHP API endpoints. This will allow for real-time (or near real-time) updates of dashboard statistics, notifications, and other dynamic content across all relevant pages.

### 1.4. Development Environment

*   **Local Hosting:** The application is developed and hosted locally using **XAMPP**.
*   **Components:** XAMPP provides the necessary components for development:
    *   **Apache:** As the web server.
    *   **MariaDB:** As the database server (compatible with MySQL).
    *   **PHP:** As the backend scripting language.

## 2. Database Schema

### `users` table

| Column | Type | Modifiers | Description |
| --- | --- | --- | --- |
| id | INT(11) | NOT NULL, AUTO_INCREMENT, PRIMARY KEY | Unique identifier for each user |
| username | VARCHAR(50) | NOT NULL, UNIQUE | User's login name |
| password | VARCHAR(255) | NOT NULL | Hashed password for the user |
| role | ENUM('barangay_staff', 'department_admin') | NOT NULL | Role of the user in the system |
| first_name | VARCHAR(100) | NOT NULL | User's first name |
| last_name | VARCHAR(100) | NOT NULL | User's last name |
| email | VARCHAR(100) | NOT NULL, UNIQUE | User's email address |
| barangay | VARCHAR(100) | DEFAULT NULL | Barangay the user belongs to |
| display_name | VARCHAR(100) | DEFAULT NULL | User's display name |
| phone | VARCHAR(20) | DEFAULT NULL | User's phone number |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Timestamp of when the user was created |

### `settings` table

| Column | Type | Modifiers | Description |
| --- | --- | --- | --- |
| id | INT(11) | NOT NULL, AUTO_INCREMENT, PRIMARY KEY | Unique identifier for each setting |
| user_id | INT(11) | NOT NULL, FOREIGN KEY | Foreign key to the `users` table |
| theme | VARCHAR(50) | NOT NULL, DEFAULT 'light' | User's preferred theme (light, dark, auto) |
| language | VARCHAR(50) | NOT NULL, DEFAULT 'en' | User's preferred language (en, fil) |
| notifications | VARCHAR(50) | NOT NULL, DEFAULT 'all' | User's notification preferences (all, important, none) |

### `notifications` table

| Column | Type | Modifiers | Description |
| --- | --- | --- | --- |
| id | INT(11) | NOT NULL, AUTO_INCREMENT, PRIMARY KEY | Unique identifier for each notification |
| message | TEXT | NOT NULL | Notification message |
| type | VARCHAR(50) | NOT NULL | Type of notification (e.g., new_application, pending_verification) |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Timestamp of when the notification was created |

## 3. File-Specific Instructions

### `index.php`

*   **Functionality:** This is the main entry point of the application. It allows users to select their role (Barangay Staff or Department Admin) and be redirected to the appropriate login page.
*   **Remember Me:** If the user has previously logged in and selected "Remember Me", this page will automatically log them in and redirect them to their dashboard.

### `pages/signup.php`

*   **Functionality:** This page allows new users to register for an account.
*   **Validation:** The page performs validation to ensure that all required fields are filled, the passwords match, the password is at least 8 characters long, and the email format is valid.
*   **User Creation:** Upon successful validation, a new user is created in the `users` table, and a corresponding default entry is created in the `settings` table.

### `pages/Barangay_Staff_LogInPage.php` and `pages/Department_Admin_LogIn_Page.php`

*   **Functionality:** These pages allow users to log in to their accounts.
*   **Validation:** The pages perform validation to ensure that all required fields are filled and that the user exists in the database.
*   **Authentication:** Upon successful validation, the user is authenticated, and a session is created.
*   **Remember Me:** Users can choose to be remembered, which sets a cookie to keep them logged in for 30 days.

### `pages/Settings.php`

*   **Functionality:** This page allows users to manage their profile, system, and security settings.
*   **Profile Settings:** Users can edit their display name, email, and phone number.
*   **System Settings:** Users can customize the theme, language, and notification preferences.
*   **Security Settings:** Users can change their password by providing their current password and a new password.
### `pages/edit_user.php`

*   **Functionality:** This page allows administrators to edit existing user information.
*   **Features:**
    *   Edit user details such as username and email.
    *   Display the user's current hashed password (for administrative reference, though direct display of plain text passwords is a security risk).
    *   Option to change the user's password.
*   **Security Note:** Displaying the raw password is a significant security vulnerability and is implemented here based on explicit user request. In a production environment, only password change functionality should be provided, without displaying the current password.

im using cnn in verifying the documents