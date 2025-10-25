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
*   **Structure:** Each major feature or view is encapsulated in its own `.html` or `.php` file.
*   **Styling:** The application uses a combination of inline CSS and shared stylesheets. A consistent design language is established through CSS variables.

### 1.3. Data Management and Flow

*   **Current Data Storage:** The system currently uses in-memory JavaScript arrays to store data. There is no backend database or server-side persistence.
*   **Future Data Storage:** A MySQL/MariaDB database will be implemented to persist data. PHP will be used to connect to and manage the database.
*   **Data Flow:**
    1.  Currently, data is hardcoded in JavaScript.
    2.  In the future, the client-side JavaScript will use the `fetch` API to make asynchronous requests to the PHP backend.
    3.  The PHP backend will process these requests, interact with the database, and return data in JSON format.

### 1.4. Development Environment

*   **Local Hosting:** The application is developed and hosted locally using **XAMPP**.
*   **Components:** XAMPP provides the necessary components for development:
    *   **Apache:** As the web server.
    *   **MariaDB:** As the database server (compatible with MySQL).
    *   **PHP:** As the backend scripting language.

## 2. Custom Interaction Instructions

To ensure consistency and maintainability, all future modifications and additions to the CARELINK system must adhere to the following rules:

1.  **Technology Stack:**
    *   **Frontend:** All new features and pages must be implemented using only **HTML, CSS, and vanilla JavaScript**.
    *   **Backend:** All server-side logic must be implemented using **PHP**.
2.  **Styling and UI:**
    *   All styling must conform to the existing visual identity.
    *   Utilize the predefined CSS variables (e.g., `--primary`, `--secondary`, `--accent`) for all color definitions.
3.  **JavaScript Implementation:**
    *   All JavaScript code must be written within `<script>` tags at the end of the `<body>`.
    *   Future JavaScript should transition from using hardcoded data to fetching data from the PHP backend using the `fetch()` API and handling JSON responses.
4.  **PHP Development:**
    *   PHP code should follow modern practices (e.g., using PDO for database connections, prepared statements to prevent SQL injection).
    *   A clear and consistent file structure for the backend (e.g., separating database connection logic, API endpoints, and utility functions) must be maintained.
5.  **Data Handling:** The system will be migrated from in-memory JavaScript arrays to a persistent database managed by PHP. All new features requiring data persistence must be built with this in mind.
6.  **File and Directory Structure:**
    *   Frontend files will remain as `.html` files.
    *   Backend files will be `.php` files, organized into a logical structure (e.g., `/api`, `/includes`).
7.  **Iconography:** Only use icons from the **Font Awesome** library to maintain visual consistency.