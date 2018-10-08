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

- `api/v1/auth/login` - The endpoint to authenticate and retrieve a valid token so protected data endpoints can be used.

- `api/v1/employee/{username}` - Return basic employee information given a username _(protected)_

- `api/v1/student/{username}` - Return basic student information given a username _(protected)_


## To Do
The existing routes/endpoints and data transformation/serialization for class data is currently very geared toward what is required by Modolabs. This API will likely evolve to abstract the transformation/serialization of the data from the controller functions that gather the data. In this way there is flexibility to have data wrapped and presented differently depending on the type of endpoint/call. 

## Terminology

For explanation on terminology/objects used in the DataAPI, [refer to the terminology documentation](terminology.md).