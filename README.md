# Project Title

A modular & multitenant  Laravel application powered by [nWidart/laravel-modules](https://nwidart.com/laravel-modules)&(https://tenancyforlaravel.com/docs/v3/tenants/)  that provides authentication, team availability, booking, team management, tenant management, and user management as separate modules & separate tenants.
# Note
i prefered to use the Stancl\Tenancy package than build the tenants from scratch using tenant_id in each record in database the use scope functions to return the records for the user's tenant so i used multible database design one for each tenant instead of single database 

---

## 📋 Requirements

* PHP >= 8.1
* Composer
* Laravel 12
* MySQL 


---

## ⚙️ Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/samuelshany/Inspection-Booking-System.git
   cd your-repo
   ```
2. Install PHP dependencies:

   ```bash
   composer install
   ```
3. Copy `.env` file and set your environment variables:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Configure your database connection in the `.env` file.
5. Run migrations and seeders:

   ```bash
   php artisan migrate --seed
   ```
6.go to /tenants/create to create new tenant example : test
    then the route used in postman should be like http://test.localhost:8000/
    
7. https://planetary-desert-393870.postman.co/workspace/New~96885989-c01f-4888-9870-bd8f7058ee5c/collection/11045064-6c4fffa2-9e92-490d-adaf-8fdcdd1f1a7e?action=share&creator=11045064
    visit this postman collection to test the apis  

8. to run test cases 
  ```bash
   php artisan tenancy:create-test-tenant
   php artisan test Modules/Booking/tests
   ``` 
---

## 🚀 Modules Overview

This project is organized into the following modules under `Modules/`:

| Module           | Description                                             |
| ---------------- | ------------------------------------------------------- |
| **Auth**         | User registration, login, and authentication endpoints. |
| **Availability** | Manage team availability and time slots.                |
| **Booking**      | Create and manage bookings for available slots.         |
| **Teams**        | CRUD operations for team records.                       |
| **Tenants**      | Multi-tenant management (create/list tenants).          |
| **Users**        | User management (fetch, update user profiles).          |

---


---

## 🗂️ Directory Structure

```
app/
bootstrap/
config/
database/
Modules/
  ├── Auth/
  ├── Availability/
  ├── Booking/
  ├── Teams/
  ├── Tenants/
  └── Users/
public/
resources/
routes/
tests/
vendor/
```

Each module follows the standard structure:

```
Modules/{ModuleName}/
  ├── app/
  │   ├── Http/Controllers
  │   ├── Models
  │   └── Services (optional)
  ├── config/
  ├── database/
  │   ├── migrations
  │   └── seeders
  ├── routes/
  └── tests/
      ├── Feature
      └── Unit
```

---

## 🤝 Contributing

1. Fork the repository.
2. Create a feature branch: `git checkout -b feature/YourFeature`.
3. Commit your changes: `git commit -m 'Add some feature'`.
4. Push to the branch: `git push origin feature/YourFeature`.
5. Open a Pull Request.

Please ensure your code follows PSR standards and includes relevant tests.

---


