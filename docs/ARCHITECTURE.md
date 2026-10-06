# Architecture

The goal is code that is easy to read and easy to change. There are only three layers:

```
Browser → public/index.php → Router → Controller → Model → MySQL
                                          ↓
                                        View
```

- **Controller** reads the request, validates input, calls models, then shows a view or redirects.
- **Model** contains the SQL for one table. Every query is a prepared statement.
- **View** is plain PHP/HTML. Views never run SQL.
- **AppointmentService** is the one service class. It holds the booking rules because the
  patient, doctor and admin parts all use them.

## Folder structure

```
app/
  bootstrap.php        autoloader, error handling, session start
  routes.php           every URL of the app in one place
  helpers.php          small functions used in views: e(), url(), old(), money(), ...
  Core/                Database, Router, Controller, Model, View, Auth, Session, Csrf, Validator, Upload
  Models/              User, Hospital, Category, Doctor, Schedule, Appointment
  Services/            AppointmentService, BookingException
  Controllers/
    HomeController, DoctorController, BookingController, AuthController   (public site)
    Patient/           extends PatientController     -> patients only
    Admin/             extends AdminController       -> admins only
    Doctor/            extends DoctorPanelController -> doctors only
views/
  layouts/             main (website), panel (admin + doctor), error
  partials/            shared pieces: flash, pagination, doctor card, schedule manager, ...
  home/ doctors/ booking/ auth/ patient/ admin/ doctor/ errors/
config/config.php      database + app settings
database/              schema.sql, seed.sql
public/                web root: index.php, assets/, uploads/
storage/logs/          error log
```

## Role protection

Each panel has a base controller that checks the role in its constructor:

```php
abstract class AdminController extends Controller
{
    public function __construct()
    {
        Auth::requireRole('admin');   // not logged in -> /login, wrong role -> 403
    }
}
```

Every admin controller extends it, so a new admin page is protected automatically.
Doctors and patients additionally only see their own rows (`doctor_id = me`, `patient_id = me`).

## Database

```
users (role: admin | doctor | patient)
  ├── 1:1 ── doctors ── N:1 ── hospitals
  │            ├── 1:N ── doctor_schedules
  │            ├── N:M ── categories   (through doctor_category)
  │            └── 1:N ── appointments
  └── 1:N ── appointments (as patient)
```

- One `users` table for all logins, so there is one login form, one password hash and one session.
  Extra doctor details live in `doctors`.
- `doctor_schedules` stores weekly hours (`day_of_week` 0 = Sunday … 6 = Saturday) and
  how long each patient gets (`slot_minutes`). Slots are calculated, not stored.
- Appointment status is an ENUM: `confirmed`, `completed`, `cancelled`, `no_show`.

### How double booking is prevented

`appointments` has a generated column and a unique key:

```sql
slot_lock TINYINT GENERATED ALWAYS AS (IF(status = 'cancelled', NULL, 1)) STORED,
UNIQUE KEY uq_doctor_slot (doctor_id, appointment_date, appointment_time, slot_lock)
```

An active appointment has `slot_lock = 1`, so a second booking of the same doctor/date/time is
rejected by the database itself. A cancelled appointment has `slot_lock = NULL`, and MySQL allows
repeated NULLs in a unique key, so the slot becomes free again.

## Booking flow

`AppointmentService::book()` checks, in order:

1. Doctor exists
2. Doctor, their account and their hospital are active
3. Doctor has a schedule on that date (date is within `booking_days`)
4. The time is one of the generated slots and is not in the past
5. The slot is not already booked
6. The user is an active patient with no other booking with this doctor that day
7. Insert. If two people submit at the same moment, the unique key lets only one through and the
   other gets "this slot was just booked".

Any failed rule throws `BookingException` with a message that is shown to the user.

## Status rules

| Who     | Can do                                                                     |
|---------|----------------------------------------------------------------------------|
| Patient | Cancel own confirmed appointment before it starts                          |
| Doctor  | Confirmed → completed / no show / cancelled (completed & no show not for future dates) |
| Admin   | Any status (re-confirming fails if someone else has taken the slot)        |

## Security checklist

| Item             | Where                                                               |
|------------------|---------------------------------------------------------------------|
| Password hashing | `password_hash()` in `User`, `password_verify()` in `AuthController` |
| SQL injection    | PDO prepared statements in every model                              |
| XSS              | `e()` on every printed value                                        |
| CSRF             | Token in every form, checked in `Router::dispatch()` for all POSTs  |
| Session          | Only `user_id` stored, id regenerated on login, HttpOnly + SameSite cookie |
| Authorization    | Base controllers per role + ownership checks                       |
| Disabled users   | Checked on every request in `Auth::user()`                          |
| Uploads          | Type detected from file content, 2 MB max, random name, no scripts in `uploads/` |
| Errors           | Logged to `storage/logs/app.log`; details hidden when `debug` is false |

## Adding a new page (example)

1. Add a route in `app/routes.php`
2. Add a method in the right controller
3. Add SQL in the model if needed
4. Add a view file in `views/`
