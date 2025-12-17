# Al Ameen Face - Event Management System

A comprehensive Event Management and Attendance Tracking system built with a custom PHP backend and a modern React frontend. This system is designed to handle various event types (Admission, Exam, Student, Admin) with integrated attendance tracking and permission management.

## 🚀 Tech Stack

### Backend
*   **Language**: PHP >= 7.3
*   **Framework**: Custom Lightweight Auto-Router (PSR-4 Autoloading)
*   **Database**: MySQL
*   **Authentication**: JWT (JSON Web Tokens)
*   **Logging**: Monolog
*   **HTTP Client**: Guzzle

### Frontend
*   **Framework**: React 19 (via Vite)
*   **Language**: TypeScript
*   **Styling**: TailwindCSS v4, DaisyUI
*   **State Management**: TanStack Query
*   **Routing**: React Router

## ✨ Key Features

*   **Multi-Type Event Management**: Supports specific workflows for different event types:
    *   `admission`: Linked to admission exam sessions.
    *   `exam`: Linked to exam groups.
    *   `student`: Linked to branch, session, and class.
    *   `admin`: Linked to specific branches.
*   **Attendance Tracking**:
    *   Records entry and exit times.
    *   Validates user codes (Student Register No, Admin Username, Admission Form No).
*   **Permission System**:
    *   Granular event-based permissions.
    *   Creators manage access for other admins.
*   **Face API Integration**: Integration with external Face Recognition API for user verification.

## 🛠️ Installation & Setup

### Prerequisites
*   PHP >= 7.3
*   Composer
*   Node.js & npm
*   MySQL

### 1. Backend Setup

1.  **Clone the repository**:
    ```bash
    git clone <repository_url>
    cd al-ameen-face
    ```

2.  **Install PHP dependencies**:
    ```bash
    composer install
    ```

3.  **Environment Configuration**:
    Copy the example environment file and configure it:
    ```bash
    cp .env.example .env
    ```
    Update `.env` with your database credentials and API keys.

4.  **Database Setup**:
    Ensure your MySQL server is running and create a database named `al_ameen_face` (or whatever you specified in `.env`). Import the necessary SQL schema (if available in the repo).

5.  **Start the Server**:
    ```bash
    composer run serve
    # OR directly:
    php -S localhost:8000 bootstrap.php
    ```

### 2. Frontend Setup

1.  **Navigate to the frontend directory**:
    ```bash
    cd frontend
    ```

2.  **Install Node dependencies**:
    ```bash
    npm install
    ```

3.  **Start the Development Server**:
    ```bash
    npm run dev
    ```
    The frontend will typically run on `http://localhost:5173`.

### 3. Docker Setup (Optional)

A `docker-compose.yml` file is provided for containerized deployment.

```bash
docker-compose up -d --build
```

## ⚙️ Environment Variables

The application is configured via `.env`. Key variables include:

| Category | Variable | Description |
| :--- | :--- | :--- |
| **Database** | `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` | MySQL connection details. |
| **App** | `APP_ENV`, `APP_DEBUG`, `APP_URL` | Application environment settings. |
| **Auth** | `JWT_SECRET`, `JWT_ALGORITHM` | JWT signing configuration. |
| **Face API** | `FACE_API_URL`, `FACE_API_TOKEN` | External Face API credentials. |
| **Logging** | `LOG_LEVEL` | Logging verbosity (e.g., debug, error). |

## 📂 Project Structure

*   `src/`: Backend source code.
    *   `Controllers/`: Handles API requests.
    *   `Core/`: Core framework logic (Router, Request, Response).
    *   `Middlewares/`: Request filtering (Auth, CORS).
    *   `Helpers/`: Utility functions and Database wrappers.
*   `frontend/`: React frontend application.
*   `public/`: Public assets for the backend.
*   `logs/`: Application logs.

## 📝 License

Copyright (c) 2024 Al Ameen Mission. All Rights Reserved.

This project is proprietary software. Unauthorized copying, modification, distribution, or use of this file, via any medium, is strictly prohibited.

