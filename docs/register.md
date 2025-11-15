# Register

This document explains the registration flow and how passwords are hashed and stored.

## Flow summary

1. User submits the register form (`POST /register`) from `app/Views/auth/register.php`.
2. `public/index.php` maps the route to `App\Controllers\AuthController::register()`.
3. The controller validates the email and password length, then calls `password_hash()` to hash the password.
4. `App\Models\User::create()` inserts the user record into the `users` table.
5. Optionally a welcome email is sent via `App\Core\Mailer` and the user is redirected to `/login`.

## Key code (simplified)

AuthController::register (simplified):

```php
// app/Controllers/AuthController.php
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { /* reject */ }
if (strlen($password) < 6) { /* reject */ }

$hashed = password_hash($password, PASSWORD_BCRYPT);
User::create($name, $email, $hashed);

Mailer::send($email, 'Welcome to Catgram', 'Thanks for registering...');
header('Location: /login');
```

User::create inserts into `users(name, email, password)` — the password column holds the bcrypt hash. See `sql/schema.sql` for the `users` table definition.

## Security notes

- `password_hash()` with `PASSWORD_BCRYPT` is used; do not implement your own password hashing.
- Validate and sanitize inputs server-side even if there is client-side validation.
- Consider adding email verification or rate-limiting to prevent spam registrations.
