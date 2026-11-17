---
paths:
  - 'app/Http/Controllers/**'
  - app/Http/Controllers/AuthController.php
---

# Controllers

## ApiController JSON responses
Extend ApiController for versioned public read endpoints. Use respond() for JSON payloads unless returning a JsonResource.

## Inline validation
Validate in the controller with $request->validate() or $this->validate(). Do not add FormRequest classes unless the project adopts them consistently.

## No service layer
Query Eloquent or the DB facade directly in controllers. Do not introduce Actions, Services, or Repositories for existing API patterns.

## JWT client auth
Client is the api guard user via tymon/jwt-auth. Admin UI uses App\User and the admin guard separately.
