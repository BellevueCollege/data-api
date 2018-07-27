# Bellevue College Data API

The Data API is a RESTful, read-only web service for accessing Bellevue College data in JSON format.

## API Endpoints

### Class data

- `api/v1/courses/multiple?courses[]={courseid}&courses[]={courseid}...` - Uses `courses[]` query parameter in repeating fashion to specify multiple courses for which to have information returned.

- `api/v1/quarters/current` - Returns the current term/quarter.
    
#### Modolabs-specific endpoints

- `api/v1/catalog/terms` - Return active/viewable terms/quarters

- `api/v1/catalog/terms/{YearQuarterID}` - Return term info for the specified term/quarter

- `api/v1/catalog/catalogAreas/{YearQuarterID}` - Return subjects offered for specified term/quarter

- `api/v1/catalog/{YearQuarterID}/{Subject}` - Return courses offered given specified term/quarter and subject/department

- `api/v1/catalog/{YearQuarterID}/{Subject}/{CourseNumber}` - Return specific course offered given term/quarter, subject/department, and course number

### People data

- `v1/auth/login` - The endpoint to authenticate and retrieve a valid token so protected data endpoints can be used.

- `api/v1/employee/{username}` - Return basic employee information given a username _(protected)_

- `api/v1/student/{username}` - Return basic student information given a username _(protected)_

## Configuration

### .env
The .env file is the primary location for sensitive application configuration values. It has a number of values you need to/may want to update depending on what you're setting up.

 - `APP_ENV` - Values `local`, `staging`, `production`
 - `APP_DEBUG` - Values `true`, `false` (should always be false in production)
 - `JWT_SECRET` - The secret generated when setting up jwt-auth. This is necessary for token authentication of the protected endpoints.
 - `CACHE_DRIVER` - Options available depend on what you've included in project. `file` should always work. `memcached` or `redis` could be other options depending on what packages you've set up.
 - `TEST_CLIENTID` and `TEST_CLIENTKEY` - Test credentials to use when executing test cases
 - The various database connection variables should be updated accordingly for valid connections to the correct databases.
 - `ADLDAP` - These variables define connection information for LDAP to authenticate and authorize people to the admin backend. 

#### config/dataapi.php

 - `yearquarter_lookahead` - This is the number of days the application will look forward for terms/quarters with web registration opening.
 - `yearquarter_maxactive` - The number of YearQuarters that are active/viewable at a time.
 - `yearquarter_max` - The YearQuarterID of the maximum YearQuarter, currently set as _Z999_.
 - `timezone` - Your timezone, e.g. America/Los_Angeles or America/Denver. Used when building dates used in comparisons.
 - `common_course_char` - The character that designates a course as a common course.  Currently, _&_ is used in ODS (e.g. _ACCT& 201_).
 - `app_version` - The current application version.

#### config/auth.php
This is where authentication guards and providers for the DataApi are configured. In our case, there are two different guards we use - one for token authentication (jwt) and one the admin area (adldap).

#### config/jwt.php
This is the configuration for jwt-auth. It shouldn't need to be edited.

#### config/adldap.php

#### config/adldap_auth.php


## Authentication

Some of the data endpoints are protected and require authentication to use. If your application requires use of these endpoints, you will need to be issued a clientid and clientkey. When you log in with these, you will be returned a token that is valid for the configured amount of time (60 minutes by default).

Example `curl` authentication:

```console
curl -i https://localhost/v1/auth/login -d clientid={clientid} -d clientkey={clientkey}
```

Example WordPress/PHP authentication:

```php
$body = array('clientid' => $clientid, 'clientkey' => $clientkey);
$auth_url = 'https://localhost/api/v1/auth/login';
$resp = wp_remote_post($auth_url, array(
                        'method' => 'POST', 'sslverify' => true,
                        'body' => $body));
```

A web token will be returned to you with a successful login. Include this bearer token in the header of your data request.

Example `curl` request with token:

```console
curl -H "Authorization: Bearer {token}" https://localhost/api/v1/student/student.test
```

Example WordPress/PHP request with token:

```php
$headers = array('Authorization' => 'Bearer ' . $token);

$resp = wp_remote_get( 'https://localhost/api/v1/student/student.test', array( 'headers' => $headers, 'sslverify' => true ) );
```

## To Do
The existing routes/endpoints and data transformation/serialization for class data is currently very geared toward what is required by Modolabs. This API will likely evolve to abstract the transformation/serialization of the data from the controller functions that gather the data. In this way there is flexibility to have data wrapped and presented differently depending on the type of endpoint/call. 

## Terminology

For explanation on terminology/objects used in the DataAPI, [refer to the terminology documentation](terminology.md).