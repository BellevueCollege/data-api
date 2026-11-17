---
paths:
  - 'app/Models/**'
  - app/Models/Client.php
---

# Models

## Legacy accessors
Define accessors and mutators with getXxxAttribute() and setXxxAttribute(), not the Attribute class.

## ODS Eloquent models
Read-only catalog data uses the ods connection, vw_* or PeopleSoft table names, and $timestamps = false unless the model already differs.

## Compoships
Use Awobaz\Compoships\Compoships for composite-key relationships where Section and CourseYearQuarter already do.

## Casts property
Use protected $casts arrays on models that need casting. Do not migrate existing models to casts() unless doing a deliberate project-wide change.

## JWT client auth
Client is the api guard user via tymon/jwt-auth. Admin UI uses App\User and the admin guard separately.
