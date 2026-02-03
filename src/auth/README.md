Auth (Login + Protected Endpoints)
We use PHP sessions (cookies) for both the website and the Flutter iOS/Android app.

Endpoints
POST /auth/login.php

Request JSON:

json
{ "email": "parent@example.com", "password": "secret123" }
Response (success):

JSON with user { id, email, role_id }

Also sets a session cookie (e.g., PHPSESSID) which must be sent on future requests.

POST /auth/logout.php

Destroys the session and logs the user out.

Protecting endpoints
Any endpoint that changes data or shows private data must require login, for example:

Register child/player, update player info

Payments / fee status

Create teams, manage rosters

Coach updates (team notes, practice/game info)

League/tournament updates (schedules, results)

Admin account/role management, reports

Implementation: at the top of protected endpoints, call session_start() and block with 401 if $_SESSION["user_id"] is missing.

Flutter note
Flutter must store and resend cookies from the login response, otherwise every request will appear logged out.
