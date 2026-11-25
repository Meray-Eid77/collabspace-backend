# Collabspace

A team collaboration platform built with Laravel. It has a web interface (Blade) and a JSON API, both using the same models. The design came from the Collabspace Figma file.

## Stack

- Laravel 13, PHP 8.5
- MySQL
- Laravel Sanctum for API tokens, session auth for the web pages
- Blade + Tailwind (loaded from CDN, no build step)

## Features

- Sign up, sign in, sign out
- Dashboard with task stats, active projects, upcoming tasks, team status, recent files and upcoming meetings
- Projects: list with search and filter, details page (overview, tasks, teams, files)
- Tasks board with four columns: to-do, in progress, in review, completed
- Teams and members per project
- File upload and download per project
- Direct chats and project channels, with attachments
- Meetings calendar, transcript search and a summary button
- Reports page with completion rates and a report form
- Profile and settings
- Ask AI page

## Setup

```bash
git clone https://github.com/Meray-Eid77/collabspace-backend.git
cd collabspace-backend
composer install
cp .env.example .env
php artisan key:generate
```

Create an empty MySQL database called `collabspace`, then check these values in `.env`:

```
DB_DATABASE=collabspace
DB_USERNAME=root
DB_PASSWORD=
```

Then:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

The app runs at http://127.0.0.1:8000.

## Demo accounts

All accounts use the password `password`.

| Email | Role |
|---|---|
| meray@test.com | Backend Developer (owner of the demo projects) |
| mohanad@test.com | UI/UX Designer |
| sara@test.com | Frontend Developer |
| ali@test.com | Backend Developer |
| khalid@test.com | Marketing Specialist |

The seeder also creates two projects (Alpha, SepetGo), some tasks, teams, a couple of chats and two meetings.

## API

Base URL: `http://127.0.0.1:8000/api`

Send `Accept: application/json` with every request. Every route except `register` and `login` needs a token:

```
Authorization: Bearer <token>
```

You get the token from `POST /api/login`. Responses use one shape:

```json
{ "success": true, "message": "...", "data": {} }
```

Main groups: auth, dashboard, projects, tasks, teams, files, meetings, conversations, profile, reports, ai. Run `php artisan route:list --path=api` to see all of them.

## Postman

The collection and environment are in the `postman` folder:

- `Collabspace.postman_collection.json`
- `CollabspaceLocal.postman_environment.json`

Import both, select the environment, and run **Login** first. Its script saves the token into the `token` variable, so the other requests pick it up automatically. Run **Logout** last, since it deletes the token.

Upload File: in the Body tab (form-data), set the key to `file`, type File, and pick any file before sending. Postman does not keep the file path after import.

## Notes and limitations

- Ask AI and the meeting summary are not connected to a real AI service. They build the answer from the database data. The logic is in one method each, so swapping in a real API would be easy.
- Any signed-in user can see all projects. There is no per-project permission check yet.
- Uploaded files are stored on the private disk and served through a controller, not from `public`.
- The dashboard query uses MySQL's `FIELD()` for priority ordering, so it will not run as is on SQLite.
- If you change a migration after running it, use `php artisan migrate:fresh --seed`.
