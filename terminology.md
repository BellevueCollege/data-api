# Terminology

Explanation for terminology used in the DataAPI.

### YearQuarter

A YearQuarter is used in the ODS and API to represent a quarter. Modolabs calls it a _term_. Data members of YearQuarter objects returned by the API.

#### Modolabs
- `code` _string_
- `description` _string_

### Subject

A Subject is used in the ODS and API to represent a subject area courses are offered in.  Modolabs calls it a course area or area.

#### Modolabs
- `area` _string_
- `code` _string_

### Course

A Course is general information about a course, non-specific to a quarter.

- `title` _string_
- `subject` _string_
- `courseNumber` _string_
- `description` _string_
- `note` _string_
- `credits` _string_
- `isVariableCredit` _bool_
- `isCommonCourse` _bool_ 

### CourseYearQuarter

A CourseYearQuarter is an offering of a course in a YearQuarter. ODS uses the term "Class" to hold this data, but that terminology is problematic in software.

#### Modolabs

- `title` _string_
- `subject` _string_
- `courseNumber` _string_
- `description` _string_
- `note` _string_
- `credits` _string_
- `isVariableCredit` _bool_
- `isCommonCourse` _bool_ 
- `sections` _Section[]_

### Section

A Section is a section offering of a CourseYearQuarter. 

#### Modolabs

- `crn` _string_
- `courseSection` _string_
- `instructor` _string_
- `beginDate` _string_ (datetime)
- `endDate` _string_ (datetime)
- `room` _string_
- `schedule` _string_