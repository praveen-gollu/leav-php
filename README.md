# Leave Management System - Angular Frontend with PHP Backend

This project is a modern implementation of the Leave Management System using Angular 20 frontend and PHP 7.4 backend APIs, maintaining the same database structure and business logic from the original PHP project.

## Project Structure

```
├── angular-frontend/          # Angular 20 Frontend Application
│   ├── src/
│   │   ├── app/
│   │   │   ├── components/    # Shared components (login, dashboard, navbar, sidebar)
│   │   │   ├── modules/       # Feature modules (employee, leave, company, department)
│   │   │   ├── services/      # Core services (auth, api)
│   │   │   ├── models/        # TypeScript interfaces
│   │   │   └── guards/        # Route guards
│   │   ├── assets/           # Static assets and CSS files
│   │   └── styles.css        # Global styles
└── php-backend/              # PHP 7.4 Backend APIs
    ├── api/                  # API endpoints
    │   ├── auth/            # Authentication endpoints
    │   ├── employees/       # Employee management
    │   ├── companies/       # Company management
    │   ├── departments/     # Department management
    │   ├── leaves/          # Leave management
    │   └── leave-types/     # Leave types
    └── config/              # Database and CORS configuration
```

## Features

### Frontend (Angular 20)
- **Modern UI**: Bootstrap 5 with Font Awesome icons
- **Responsive Design**: Mobile-friendly interface
- **Role-based Access**: Different views for Admin, Supervisor, Manager, and Normal users
- **Modular Architecture**: Feature modules for better code organization
- **Type Safety**: Full TypeScript implementation
- **Route Guards**: Protected routes with authentication

### Backend (PHP 7.4)
- **RESTful APIs**: Clean API endpoints following REST principles
- **CORS Support**: Cross-origin resource sharing enabled
- **PDO Database**: Secure database operations with prepared statements
- **Same Logic**: Maintains original business logic from PHP project
- **Error Handling**: Comprehensive error handling and responses

## Database

Uses the existing `leavedb` database without any modifications. The system works with the current tables:
- `tblemployee` - Employee information
- `tblcompany` - Company data
- `tbldepts` - Department information
- `tblleave` - Leave applications
- `tblleavetype` - Leave types

## Setup Instructions

### Prerequisites
- Node.js 18+ and npm
- PHP 7.4+
- MySQL/MariaDB
- Web server (Apache/Nginx)

### Frontend Setup
1. Navigate to the angular-frontend directory
2. Install dependencies: `npm install`
3. Update API URL in `src/environments/environment.ts`
4. Start development server: `ng serve`

### Backend Setup
1. Place php-backend files in your web server directory
2. Update database credentials in `config/database.php`
3. Ensure mod_rewrite is enabled for .htaccess
4. Test API endpoints

### Default Login Credentials
Use any existing employee credentials from your database, for example:
- Email: joken@yahoo.com
- Password: admin (or the actual password from your database)

## API Endpoints

- `POST /api/auth/login` - User authentication
- `GET /api/employees` - Get all employees
- `POST /api/employees` - Create new employee
- `PUT /api/employees/{id}` - Update employee
- `DELETE /api/employees/{id}` - Delete employee
- `GET /api/companies` - Get all companies
- `GET /api/departments` - Get all departments
- `GET /api/leaves` - Get leave applications
- `POST /api/leaves` - Create leave application
- `PUT /api/leaves/{id}` - Update leave application

## User Roles & Permissions

### Administrator
- Full access to all modules
- Can manage employees, companies, departments
- Can approve/reject leave applications
- Can view all leave applications

### Supervisor/Manager
- Can apply for leave
- Can approve/reject leave applications for their department
- Limited employee management

### Normal User
- Can apply for leave
- Can view their own leave applications
- Can update profile

## Security Features

- Password hashing using SHA1 (maintaining compatibility)
- JWT-like token authentication
- CORS protection
- SQL injection prevention with prepared statements
- Role-based access control

## Styling

The project uses the same CSS styling from the original SB Admin template:
- Bootstrap 5 framework
- Font Awesome icons
- Custom SB Admin styles in `assets/css/sb-admin.css`
- Responsive design with sidebar navigation

This implementation provides a modern, maintainable frontend while preserving all the business logic and database structure from your original PHP project.