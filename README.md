# 📰 Laravel CMS Project

This is a Laravel-based Content Management System (CMS) supporting user roles, article management, AI-powered summarization, and OpenAI integration.

---

## 📦 Prerequisites

Make sure the following are installed on your system:

- PHP >= 8.2
- Composer
- MySQL
- Laravel CLI 12
- Postman (for testing API endpoints)

---

### 1️⃣ Clone and Install

git clone from the link https://github.com/pritamrajbhar06/CMS-app

composer install

copy .env.example to .env

### 2️⃣ Configure Database

Update the `.env` file with your database credentials

#### Set Default Database Connection

In `config/database.php`, ensure the default connection is set to `mysql`. Under the `connections` array, update the `mysql` section with your database credentials.


#### 3️⃣ Generate Key & Run Migrations

php artisan key:generate
php artisan migrate
php artisan db:seed --class=AdminAndAuthorSeeder

#### 4️⃣ Serve the Application

php artisan serve

Follow the link provided in the terminal to verify the application is running as expected.

#### 5️⃣ OpenAI Integration

Using this link register/Login and Create API Key -> https://platform.openai.com/settings/organization/api-keys

Note : Trial version is available for few requests after that you need to add billing details.

Update the .env file with your OpenAI API key (OPENAI_API_KEY)

#### 6️⃣ API Testing

Use Postman to test the API endpoints. Import the provided Postman collection attached in the E-mail.

### 7️⃣ Run Tests
Run the Login Request Tests first and then add that token to the other requests in Bear Token Authorization header in Postman.

Also Set Header `Accept` to `application/json` for all requests.

Run the requests and update values as needed.(e.g. article, category, etc.)


📩 Contact
For any queries or clarifications, feel free to reach out:
📧 Email: pritamrajbhar2001@gmail.com
