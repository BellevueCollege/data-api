# Bellevue College Data API

The Data API is a RESTful, read-only web service for accessing Bellevue College data in JSON format.

## API Endpoints

### Class/course data

- `api/v1/course/{CourseID}` - Return course info for given course

- `api/v1/courses/multiple?courses[]={courseid}&courses[]={courseid}...`       
  - Uses `courses[]` query parameter in repeating fashion to specify multiple courses for which to have information returned.

- `api/v1/quarters` - Return active/viewable quarters

- `api/v1/quarter/current` - Returns the current quarter.
    
- `api/v1/quarter/{YearQuarterID}` - Return info for the specified quarter

- `api/v1/subjects` - Return all subjects
- `api/v1/subjects/{YearQuarterID}` - Return subjects offered for specified quarter
- `api/v1/subject/{Subject}` - Return subject info for given subject (slug)

- `api/v1/classes/{YearQuarterID}/{Subject}` - Return course and class/section info offered given specified quarter and subject/department

- `api/v1/classes/{YearQuarterID}/{Subject}/{CourseNumber}` - Return specific course and section/class info given a quarter, subject/department, and course number

### People data

- `api/v1/auth/login` - The endpoint to authenticate and retrieve a valid token so protected data endpoints can be used.

- `api/v1/employee/{username}` - Return basic employee information given a username _(protected)_

- `api/v1/student/{username}` - Return basic student information given a username _(protected)_


## To Do
The existing routes/endpoints and data transformation/serialization for class data is currently very geared toward what is required by Modolabs. This API will likely evolve to abstract the transformation/serialization of the data from the controller functions that gather the data. In this way there is flexibility to have data wrapped and presented differently depending on the type of endpoint/call. 

## Terminology

For explanation on terminology/objects used in the DataAPI, [refer to the terminology documentation](terminology.md).