# Bellevue College Data API

The Data API is a RESTful, primarily read-only web service for accessing Bellevue College data in JSON format.

## Documentation Endpoint 📚

- `docs/api` - The documentation endpoint for the Data API (thanks to [Scramble](https://scramble.dedoc.co/))

## API Endpoints 🌐

### Class/course data 🎓

- `api/v1/course/{CourseID}` - Return course info for given course given the CourseID
- `api/v1/course/{Subject}/{CourseNumber}` - Return course info for given course given the subject and course number
- `api/v1/courses/{Subject}` - Return active courses for a given subject
- `api/v1/courses/multiple?courses[]={courseid}&courses[]={courseid}...`

  - Uses `courses[]` query parameter in repeating fashion to specify multiple courses for which to have information returned.
- `api/v1/quarters` - Return active/viewable quarters
- `api/v1/quarter/current` - Returns the current quarter.
- `api/v1/quarter/{YearQuarterID}?format={`strm `or`yrq `}` - Return info for the specified quarter. Defaults to lookup by YRQ. STRM can be used if format is set to `strm`.
- `api/v1/subjects` - Return all subjects
- `api/v1/subjects?filter=active-credit"` - Return only active, non-CE subjects
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
- `api/v1/directory/employees/{part of name}` - Return a list of employee usernames that match the provided substring of name _(protected)_
- `api/v1/directory/employee/{username}` - Return employee directory information given a username _(protected)_

### People data 👩‍🎓👨‍🎓 (🔒Internal Only)

- `api/v1/internal/auth/login` - The endpoint to authenticate and retrieve a valid token so protected data endpoints can be used. _(protected)_
- `api/v1/internal/employee/{username}` - Return basic employee information given a username _(protected)_
- `api/v1/internal/student/{username}` - Return basic student information given a username _(protected)_

### Transaction data (🔒)

- `api/v1/forms/pci/transaction` - Record POSTed transaction data _(protected)_
- `api/v1/forms/pci/transaction/test` - Don't Record *(protected)*

## Admin Interface 👩‍💻 (🔒Internal Only)

There is a simple admin interface to allow creation and removal of API credentials.
It is only available on the internal subdomain.

* `admin/login` - Login interface _(protected)_

## Terminology ℹ️

For explanation on terminology/objects used in the DataAPI, [refer to the terminology documentation](terminology.md).


## Development Environment

Data API uses [Laravel Sail](https://laravel.com/docs/10.x/sail) for a local development environment. 

With [Docker](https://www.docker.com/) installed, run `./vendor/bin/sail up` to build and start the VM.

Alternativly, you can use Podman instead of docker with some additional configuration; good info on this
in [this medium article](https://medium.com/mamitech/the-shortest-path-to-replace-docker-with-podman-for-laravel-sail-b02d184a1b72)

Once Sail informs you of the IP address it is using, add the following entries to your hosts file:
```
# Laravel Sail
0.0.0.0 data-api.test
0.0.0.0 no.data-api.test
```

To support SSL, you'll also need to generate a certificate and key.
```bash
openssl req -newkey rsa:2048 -nodes \
  -keyout docker/nginx/ssl/data-api.key \
  -x509 -days 365 \
  -out docker/nginx/ssl/data-api.crt \
  -subj "/CN=*.data-api.test"
```

### Upgrade Considerations

When upgrading to a new version of PHP, the Dockerfile may need to be updated as well.
Lines 28 and 46 of `/docker/8.1/Dockerfile` were added in order to add SQL Server support.
## Build Status 🚀

| Trunk                                                                                                                                                                     | Dev                                                                                                                                                                    |
| ------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| [![Build status](https://dev.azure.com/bcintegration/data-api/_apis/build/status/data-api-master)](https://dev.azure.com/bcintegration/data-api/_build/latest?definitionId=20) | [![Build status](https://dev.azure.com/bcintegration/data-api/_apis/build/status/data-api-dev)](https://dev.azure.com/bcintegration/data-api/_build/latest?definitionId=19) |

## Configuration 🛠️

### Azure Entra ID
Make sure to set the following environment variables:

- `AZURE_CLIENT_ID` - The client ID of the Azure Entra ID application
- `AZURE_CLIENT_SECRET` - The client secret of the Azure Entra ID application
- `AZURE_TENANT_ID` - The tenant ID of the Azure Entra ID application
- `AZURE_REDIRECT_URI` - The redirect URI of the Azure Entra ID application (e.g. `https://no.data-api.test/admin/login/callback`)

## The BadgeZone 💫

[![emoji-log](https://cdn.rawgit.com/ahmadawais/stuff/ca97874/emoji-log/flat-round.svg)](https://github.com/ahmadawais/Emoji-Log/)
