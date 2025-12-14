# Al-Ameen Face API

A comprehensive face recognition API system for Al-Ameen educational institution, built with PHP auto-router, featuring user authentication, event management, and face registration capabilities.

## Features

- **Face Recognition Integration**: Seamless integration with external face recognition services for student and admin identification
- **User Management**: Support for students and admins with branch-based organization
- **Event System**: Attendance tracking and event management with face verification
- **Auto Router**: Lightweight PHP router with middleware support, PSR-4 autoloading, and dynamic parameters
- **Authentication**: JWT-based authentication with role-based permissions
- **Third-Party Integration**: API endpoints for external face capture services
- **Modern Frontend**: React-based registration interface with real-time face capture
- **Docker Support**: Containerized deployment with nginx and PHP-FPM

## Project Structure

```
├── src/
│   ├── Controllers/          # API controllers (Auth, User, Event, etc.)
│   ├── Core/                 # Core framework classes (AutoRouter, Request, Response)
│   ├── Helpers/              # Utility helpers (DB, Token, Face API, etc.)
│   ├── Middlewares/          # Request middlewares (Auth, CORS, JSON, Logging)
│   ├── Routes/               # Route definitions
│   └── Views/                # Template views
├── frontend/                 # React frontend application
│   ├── src/
│   │   ├── components/       # React components
│   │   ├── pages/           # Page components (Home, Register)
│   │   ├── utils/           # API utilities
│   │   └── @types/          # TypeScript type definitions
│   └── public/              # Static assets
├── bootstrap.php             # Application entry point
├── composer.json             # PHP dependencies
├── Dockerfile                # Docker container configuration
├── docker-compose.yml        # Docker services orchestration
├── nginx.conf                # Nginx web server configuration
└── Makefile                  # Docker management commands
```

## Installation

### Prerequisites

- PHP 7.3+
- Composer
- Node.js 16+ (for frontend)
- Docker & Docker Compose (optional, for containerized deployment)
- MySQL database

### Setup

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd al-ameen-face
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install frontend dependencies:**
   ```bash
   cd frontend
   npm install
   cd ..
   ```

4. **Environment Configuration:**
   Create a `.env` file in the root directory with required environment variables:
   ```env
   DB_HOST=localhost
   DB_NAME=al_ameen_db
   DB_USER=your_db_user
   DB_PASS=your_db_password
   JWT_SECRET=your_jwt_secret
   FACE_API_URL=https://face.nafish.me
   ```

5. **Database Setup:**
   Import the database schema and initial data.

### Running the Application

#### Development Mode

**Backend:**
```bash
composer run serve
# or
php -S localhost:8000 bootstrap.php
```

**Frontend:**
```bash
cd frontend
npm run dev
```

#### Production Mode (Docker)

```bash
make build
make up
```

The application will be available at `http://localhost:8080`

## API Documentation

All API endpoints require the following headers for app version validation:

- `X-Device-Type`: Device type (ios, android, linux, macos, windows)
- `X-App-Build-Number`: Build number (minimum varies by platform: iOS >= 100, Android >= 50, others >= 1)

### Authentication Endpoints

#### POST `/api/auth/login`
Authenticate admin users.
```json
{
  "username": "admin_username",
  "password": "admin_password"
}
```

#### POST `/api/auth/token`
Generate authentication token.

#### GET `/api/auth/verify`
Verify authentication token (requires AuthMiddleware).

### User Management

#### POST `/api/users/types`
Get available user types.
```json
{
  "types": [
    {"value": "student", "label": "Student"},
    {"value": "admin", "label": "Employee"}
  ]
}
```

#### POST `/api/users/lookup`
Lookup user by type and code.
```json
{
  "type": "student|admin",
  "code": "user_code"
}
```

#### POST `/api/users/register`
Register new user.

### Event Management

#### POST `/api/events`
Get events list (requires authentication).

#### POST `/api/events/attend`
Mark attendance for an event (requires authentication).

### Third-Party Integration

#### POST `/api/third-party`
Lookup student information for face registration.
```json
{
  "form_no": "student_form_number",
  "session": "academic_session"
}
```

### Webhooks

#### POST `/api/webhooks/event`
Handle external event webhooks.

## Frontend Usage

The React frontend provides a face registration interface:

1. **Home Page** (`/`): Access control page
2. **Registration Page** (`/register`): Face registration with parameters
   - `form_no`: Student form number
   - `session`: Academic session
   - `redirect`: Redirect URL after registration

### Face Registration Flow

1. User accesses registration URL with parameters
2. System looks up student information via third-party API
3. Face capture iframe loads from external service
4. User captures face image
5. System registers face with face recognition API
6. Success/failure feedback displayed

## Architecture

### Backend (PHP)

- **AutoRouter**: Custom lightweight router supporting:
  - Dynamic route parameters (`{id}`)
  - Middleware chains
  - Route groups
  - Controller method routing (`Controller@method`)

- **Request/Response Objects**: Encapsulate HTTP data with helper methods

- **Middleware System**: Supports global and route-specific middleware (CORS, JSON validation, logging, authentication, app version check)

- **Database Layer**: Direct SQL queries with helper functions

### Frontend (React + TypeScript)

- **React Router**: Client-side routing
- **Axios**: HTTP client for API communication
- **React Query**: Data fetching and caching
- **Tailwind CSS + DaisyUI**: Styling framework
- **Vite**: Build tool and development server

### Face Recognition Integration

- External face API service for:
  - Face registration
  - Face search
  - Face deletion
- Secure token-based authentication
- Image upload and processing

## Database Schema

Key tables:
- `admin`: Administrative users
- `student`: Student records
- `branch`: Branch information
- `history`: Student academic history
- `events`: Event definitions
- `attendance`: Attendance records

## Security

- JWT token authentication
- Role-based access control
- CORS protection
- Input validation and sanitization
- Secure file upload handling

## Development

### Adding New Routes

1. Define routes in `src/Routes/api.php`
2. Create controller methods in appropriate controller
3. Add middleware if required

### Adding New Controllers

1. Create class in `src/Controllers/`
2. Follow PSR-4 naming: `App\Controllers\ControllerName`
3. Implement methods with `Request` and `Response` parameters

### Frontend Development

1. Add components in `frontend/src/components/`
2. Add pages in `frontend/src/pages/`
3. Update routes in `Router.tsx`
4. Use API service in `utils/api.ts`

## Deployment

### Docker Deployment

```bash
make build
make up
```

### Manual Deployment

1. Configure web server (nginx/apache)
2. Set up PHP-FPM
3. Configure environment variables
4. Run database migrations
5. Build frontend assets: `cd frontend && npm run build`

## Requirements

- PHP 7.3+
- MySQL 5.7+
- Node.js 16+
- Composer
- Docker (optional)

## License

MIT
