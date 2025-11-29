Google Form Integration Deployment ID: AKfycbxMMdl1yjOoNLAv9xptOT9lqkPmKy0XrHx5pql-7rz2S4H_qM1r_R1_Bg4bfFPksF2_ 
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
*   **Application Forms:** The system supports distinct application processes for Senior Citizens and Persons With Disabilities (PWD), each potentially requiring different forms or specific data fields to be filled.
*   **Styling:** The application uses a combination of inline CSS and shared stylesheets. A consistent design language is established through CSS variables.
*   **Real-time Updates:** The system uses a polling mechanism to fetch real-time data from the server. The `assets/js/realtime_updates.js` file contains the logic for fetching and updating the dashboard statistics and notifications every 5 seconds.

### 1.3. Data Management and Flow

*   **Data Segregation by Barangay:** Data access within the system is strictly segregated by barangay. Barangay staff users can only view and manage application data pertinent to their assigned barangay, ensuring data relevance and operational focus.

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

### `remember_tokens` table

| Column | Type | Modifiers | Description |
| --- | --- | --- | --- |
| id | INT(11) | NOT NULL, AUTO_INCREMENT, PRIMARY KEY | Unique identifier for each token |
| user_id | INT(11) | NOT NULL, FOREIGN KEY | Foreign key to the `users` table |
| selector | VARCHAR(12) | NOT NULL, UNIQUE | Selector for the remember me token |
| validator_hash | VARCHAR(64) | NOT NULL | Hashed validator for the remember me token |
| expires | DATETIME | NOT NULL | Expiry date for the token |

### `applications` table

| Column                 | Type                                  | Null | Key | Default             | Extra | Description                                       |
| :--------------------- | :------------------------------------ | :--- | :-- | :------------------ | :---- | :------------------------------------------------ |
| id_number              | varchar(255)                          | NO   | PRI | NULL                |       | Unique application ID, serves as Primary Key      |
| full_name              | varchar(255)                          | NO   |     | NULL                |       | Applicant's full name (generated)                 |
| application_type       | enum('pwd','senior')                  | NO   |     | NULL                |       | Type of application (e.g., PWD, Senior)           |
| birth_date             | date                                  | NO   |     | NULL                |       | Applicant's birth date                            |
| contact_number         | varchar(20)                           | NO   |     | NULL                |       | Applicant's contact number                        |
| complete_address       | text                                  | NO   |     | NULL                |       | Applicant's complete address                      |
| emergency_contact      | varchar(20)                           | NO   |     | NULL                |       | Emergency contact number                          |
| emergency_contact_name | varchar(255)                          | YES  |     | NULL                |       | Emergency contact name                            |
| date_submitted         | timestamp                             | NO   |     | current_timestamp() |       | Timestamp of when the application was submitted   |
| status                 | enum('pending','approved','rejected') | NO   |     | pending             |       | Status of the application                         |
| barangay               | varchar(100)                          | NO   |     | NULL                |       | Barangay of the applicant                         |
| proof_of_address       | mediumblob                            | YES  |     | NULL                |       | Proof of address document (BLOB)                  |
| proof_of_address_type  | varchar(100)                          | YES  |     | NULL                |       | Mime type of the proof of address                 |
| id_image               | mediumblob                            | YES  |     | NULL                |       | ID image (BLOB)                                   |
| id_image_type          | varchar(100)                          | YES  |     | NULL                |       | Mime type of the ID image                         |
| lastName               | varchar(255)                          | YES  |     | NULL                |       | Applicant's last name                             |
| firstName              | varchar(255)                          | YES  |     | NULL                |       | Applicant's first name                            |
| middleName             | varchar(255)                          | YES  |     | NULL                |       | Applicant's middle name                           |
| suffix                 | varchar(255)                          | YES  |     | NULL                |       | Applicant's suffix                                |
| disability_type        | varchar(255)                          | YES  |     | NULL                |       | Type of disability (for PWD applications)         |

## 3. API Endpoints

### `api/admin_approved_application.php`

*   **Functionality:** Approves an application.
*   **Method:** POST
*   **Parameters:** `id` (application ID)

### `api/admin_rejected_application.php`

*   **Functionality:** Rejects an application.
*   **Method:** POST
*   **Parameters:** `id` (application ID)

### `api/approve_application.php`

*   **Functionality:** Approves an application.
*   **Method:** POST
*   **Parameters:** `id` (application ID)

### `api/delete_application.php`

*   **Functionality:** Deletes an application.
*   **Method:** POST
*   **Parameters:** `id` (application ID)

### `api/get_all_applications.php`

*   **Functionality:** Retrieves all applications.
*   **Method:** GET

### `api/get_application_details.php`

*   **Functionality:** Retrieves the details of a specific application.
*   **Method:** GET
*   **Parameters:** `id` (application ID)

### `api/get_document.php`

*   **Functionality:** Retrieves a specific document for an application.
*   **Method:** GET
*   **Parameters:** `id` (application ID), `doc_type` (document type)

### `api/get_realtime_data.php`

*   **Functionality:** Retrieves real-time data for the dashboard.
*   **Method:** GET

### `api/gform_submit.php`

*   **Functionality:** Submits an application from a Google Form.
*   **Method:** POST

### `api/import_applications.php`

*   **Functionality:** Imports applications from a CSV file.
*   **Method:** POST

### `api/search_applications.php`

*   **Functionality:** Searches for applications based on a query.
*   **Method:** GET
*   **Parameters:** `query`, `type`, `status`

## 4. File-Specific Instructions

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

### `pages/new_application.php`

*   **Functionality:** This page allows barangay staff to submit a new application.
*   **Features:**
    *   A form to enter all the applicant's information.
    *   File uploads for required documents.

### `pages/submit_application.php`

*   **Functionality:** This page allows barangay staff to view and manage applications.
*   **Features:**
    *   A table of all applications with search and filter functionality.
    *   A modal to view the details of a specific application.
    *   A modal to import applications from a CSV file.

### `pages/barangay_records.php`

*   **Functionality:** This page allows barangay staff to view and manage records.
*   **Features:**
    *   A table of all records with search and filter functionality.
    *   A yearly record view.

### `pages/department_records.php`

*   **Functionality:** This page allows department admins to view and manage records.
*   **Features:**
    *   A table of all records with search and filter functionality.

### `pages/verify_document.php`

*   **Functionality:** This page allows department admins to verify documents.
*   **Features:**
    *   A table of all applications with a button to view the details and verify the documents.

## 5. CSS and JS

### `assets/css/barangay-sidebar.css`

*   **Functionality:** This file contains the styles for the sidebar used in the barangay pages.

### `assets/css/department-sidebar.css`

*   **Functionality:** This file contains the styles for the sidebar used in the department pages.

### `assets/css/loading-spinner.css`

*   **Functionality:** This file contains the styles for the loading spinner.

### `assets/js/dynamic-loader.js`

*   **Functionality:** This file contains the logic for showing and hiding the loading spinner.

### `assets/js/realtime_updates.js`

*   **Functionality:** This file contains the logic for fetching and updating the dashboard statistics and notifications in real-time.

im using cnn in verifying the documents

## 6. CNN-based Document Verification Systems

This section outlines the setup and usage of the Convolutional Neural Network (CNN) systems for document verification: a multi-class classifier and an autoencoder for anomaly detection.

### 6.1. Python API Configuration (`python_api/config.py`)

The `python_api/config.py` file defines crucial parameters for both CNN models, including:
*   **Image dimensions:** `IMG_HEIGHT`, `IMG_WIDTH` (e.g., `128x128` pixels).
*   **Model Paths:** `MODEL_PATH` for the multi-class classifier (`pasig_id_verifier_model.h5`) and `AUTOENCODER_MODEL_PATH` for the autoencoder (`pasig_id_autoencoder_model.h5`).
*   **Class Names:** `CLASS_NAMES` for the classifier, which includes `PWD_ID`, `Fake_PWD_ID`, `Senior_ID`, `Fake_Senior_ID`, and `Not_An_ID`.
*   **Autoencoder Threshold:** `RECONSTRUCTION_THRESHOLD` for anomaly detection.

### 6.2. Training Data Preparation

To train the CNN models, you need to organize your image data into specific folders. The system infers labels from these subdirectory names.

1.  **Create Folders:** Ensure the following directories exist inside `D:\xampp1\htdocs\Carelink_2.0\python_api\training_data\`:
    *   `PWD_ID` (Genuine PWD ID images)
    *   `Fake_PWD_ID` (Fake PWD ID images)
    *   `Senior_ID` (Genuine Senior Citizen ID images)
    *   `Fake_Senior_ID` (Fake Senior Citizen ID images)
    *   `Not_An_ID` (Images that are neither PWD nor Senior Citizen IDs)
    (These folders have been created for you.)

2.  **Populate Folders with Images:**
    *   **For Multi-Class Classifier (trained by `train_model.py`):** Place diverse images in *all* five categories.
    *   **For Autoencoder Anomaly Detector (trained by `train_autoencoder.py`):** Place diverse images *only* in `PWD_ID` and `Senior_ID` folders. The autoencoder learns the "normal" format from these genuine IDs.

    *Important:* Ensure you have a sufficient number of diverse images for *each relevant* category for effective model training.

### 6.3. Install Python Dependencies

Before training or running the API, install the necessary Python libraries:

1.  Open your terminal or command prompt.
2.  Navigate to the `python_api` directory:
    ```bash
    cd D:\xampp1\htdocs\Carelink_2.0\python_api
    ```
3.  Activate your Python virtual environment:
    ```bash
    .\venv\Scripts\activate
    ```
4.  Install the required packages:
    ```bash
    pip install tensorflow numpy Pillow
    ```

### 6.4. Train the Multi-Class Classification Model

A training script `python_api/train_model.py` has been provided. This script will train the multi-class CNN classifier using your prepared data.

1.  Ensure your training data is organized as described in Section 6.2 (all five categories populated).
2.  Open your terminal or command prompt.
3.  Navigate to the `python_api` directory:
    ```bash
    cd D:\xampp1\htdocs\Carelink_2.0\python_api
    ```
4.  Activate your Python virtual environment:
    ```bash
    .\venv\Scripts\activate
    ```
5.  Run the training script:
    ```bash
    python train_model.py
    ```
    This process will take some time. Upon completion, a trained model file named `pasig_id_verifier_model.h5` will be saved in the `python_api` directory.

### 6.5. Autoencoder Anomaly Detection Setup and Usage

This section describes how to set up and use the autoencoder to detect anomalies based on the format and appearance of genuine IDs. This system learns what "normal" looks like from genuine IDs and flags deviations.

1.  **Train the Autoencoder Model:**
    *   A training script `python_api/train_autoencoder.py` has been provided. This script will train the autoencoder using *only* your genuine ID data.
    *   Ensure your training data for genuine IDs is organized as described in Section 6.2 (specifically `PWD_ID` and `Senior_ID` folders).
    *   Open your terminal or command prompt.
    *   Navigate to the `python_api` directory:
        ```bash
        cd D:\xampp1\htdocs\Carelink_2.0\python_api
        ```
    *   Activate your Python virtual environment:
        ```bash
        .\venv\Scripts\activate
        ```
    *   Run the training script:
        ```bash
        python train_autoencoder.py
        ```
        This process will train the autoencoder. Upon completion, a trained model file named `pasig_id_autoencoder_model.h5` will be saved in the `python_api` directory.
    *   **Reconstruction Threshold (`RECONSTRUCTION_THRESHOLD`):** After training, you will need to fine-tune the `RECONSTRUCTION_THRESHOLD` in `config.py`. This value determines the sensitivity of anomaly detection. You can do this by running genuine and known fake (if available) images through the `/verify_document_anomaly` endpoint and observing their reconstruction errors to find an appropriate cutoff.

### 6.6. Run the Verification API

Once both models (if applicable) are trained and saved, you can start the Flask API for document verification:

1.  Open your terminal or command prompt.
2.  Navigate to the `python_api` directory:
    ```bash
    cd D:\xampp1\htdocs\Carelink_2.0\python_api
    ```
3.  Activate your Python virtual environment:
    ```bash
    .\venv\Scripts\activate
    ```
4.  Run the API script:
    ```bash
    python app.py
    ```
    The Flask application will start, typically on `http://127.0.0.1:5000/`.
    Two main endpoints will be available for use:
    *   **`/verify_document` (for multi-class classification):** Accepts image files for classification into predefined categories (e.g., PWD ID, Fake PWD ID, Senior ID).
    *   **`/verify_document_anomaly` (for anomaly detection):** Accepts image files and determines if their format and appearance are "normal" (genuine) or "anomalous" (potentially fake or unrecognized) based on the autoencoder's learned patterns.
5.  The Flask application will start, typically on `http://127.0.0.1:5000/`.
6.  Two main endpoints will be available for use:
    *   **`/verify_document` (for multi-class classification):** Accepts image files for classification into predefined categories (e.g., PWD ID, Fake PWD ID, Senior ID).
    *   **`/verify_document_anomaly` (for anomaly detection):** Accepts image files and determines if their format and appearance are "normal" (genuine) or "anomalous" (potentially fake or unrecognized) based on the autoencoder's learned patterns.

### 6.7. Troubleshooting VS Code Warnings

If you see a "yellow warning" in VS Code on `import tensorflow as tf`:

1.  Open the VS Code Command Palette (`Ctrl+Shift+P` or `Cmd+Shift+P`).
2.  Type "Python: Select Interpreter" and choose the Python interpreter located within your `python_api/venv` directory. The exact path you should select is:
    `D:\xampp1\htdocs\Carelink_2.0\python_api\venv\Scripts\python.exe`
3.  Restart VS Code if the warning persists.
