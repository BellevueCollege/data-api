# Bellevue College Data API

The Data API is a RESTful, primarily read-only web service for accessing Bellevue College data in JSON format.

## API Endpoints 🌐

### OpenAPI 3.0 Documentation
As endpoints are added or updated, OpenAPI documentation is being added.

- [JSON OpenAPI Documentation Endpoint](https://www2.bellevuecollege.edu/data/api/v1/docs/)
- [Swagger Docs for API (in progress)](https://www2.bellevuecollege.edu/data/documentation/)

### Class/course data 🎓

- `api/v1/course/{CourseID}` - Return course info for given course given the CourseID

- `api/v1/course/{Subject}/{CourseNumber}` - Return course info for given course given the subject and course number

- `api/v1/courses/{Subject}` - Return active courses for a given subject

- `api/v1/courses/multiple?courses[]={courseid}&courses[]={courseid}...`       
  - Uses `courses[]` query parameter in repeating fashion to specify multiple courses for which to have information returned.

- `api/v1/quarters` - Return active/viewable quarters

- `api/v1/quarter/current` - Returns the current quarter.
    
- `api/v1/quarter/{YearQuarterID}?format={`strm` or `yrq`}` - Return info for the specified quarter. Defaults to lookup by YRQ. STRM can be used if format is set to `strm`.

- `api/v1/subjects` - Return all subjects
- `api/v1/subjects/{YearQuarterID}` - Return subjects offered for specified quarter
- `api/v1/subject/{Subject}` - Return subject info for given subject (slug)

- `api/v1/classes/{YearQuarterID}/{Subject}` - Return course and class/section info offered given specified quarter and subject/department

- `api/v1/classes/{YearQuarterID}/{Subject}/{CourseNumber}` - Return specific course and section/class info given a quarter, subject/department, and course number

### Form Data ⌨️

Form Data endpoints are write-only, and accept POSTS with specific parameters and store the data for future reporting. Due to external requirements, they require HTTP Basic authentication using a Client ID and Client Key generated within the Admin interface.

- `api/v1/forms/pci/transactions` - Accepts POST of transaction data _(protected - HTTP Basic Auth)_

### Directory data 👩‍🎓👨‍🎓
- `api/v1/auth/login` - The endpoint to authenticate and retrieve a valid token so protected data endpoints can be used. _(protected)_
- `api/v1/directory/employees` - Return list of all employee usernames _(protected)_
- `api/v1/directory/employee/{username}` - Return employee directory information given a username _(protected)_


### People data 👩‍🎓👨‍🎓 (🔒Internal Only)

- `api/v1/internal/auth/login` - The endpoint to authenticate and retrieve a valid token so protected data endpoints can be used. _(protected)_

- `api/v1/internal/employee/{username}` - Return basic employee information given a username _(protected)_

- `api/v1/internal/student/{username}` - Return basic student information given a username _(protected)_

## Admin Interface 👩‍💻 (🔒Internal Only)

There is a simple admin interface to allow creation and removal of API credentials. 
It is only available on the internal subdomain.

* `admin/login` - Login interface _(protected)_


## Terminology ℹ️

For explanation on terminology/objects used in the DataAPI, [refer to the terminology documentation](terminology.md).

API Documentation is being written through [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger). Run the following to compile:
```bash
php artisan l5-swagger:generate 
```

## Build Status 🚀

| Trunk | Dev |
|---|---|
| [![Build status](https://dev.azure.com/bcintegration/data-api/_apis/build/status/data-api-master)](https://dev.azure.com/bcintegration/data-api/_build/latest?definitionId=20) | [![Build status](https://dev.azure.com/bcintegration/data-api/_apis/build/status/data-api-dev)](https://dev.azure.com/bcintegration/data-api/_build/latest?definitionId=19) |

## The BadgeZone 💫

[![emoji-log](https://cdn.rawgit.com/ahmadawais/stuff/ca97874/emoji-log/flat-round.svg)](https://github.com/ahmadawais/Emoji-Log/)

