<?php
/**
 * @OA\Schema(
 *     title="Employee",
 *     description="Employee information",
 *     @OA\Xml(
 *         name="Employee"
 *     )
 * )
 */
class Employee
{
    /**
     * @OA\Property(
     *     title="SID",
     *     description="Employee ID",
     *     example="950000000",
     * )
     * @var int
     */
    public $SID;

    /**
     * @OA\Property(
     *     title="First Name",
     *     description="Employee first name",
     *     example="Joanne",
     * )
     * @var string
     */
    public $firstName;

    /**
     * @OA\Property(
     *     title="Last Name",
     *     description="Employee last name",
     *     example="Doe",
     * )
     * @var string
     */
    public $lastName;

    /**
     * @OA\Property(
     *     title="Alias Name",
     *     description="Preferred first name",
     *     example="Jo",
     * )
     * @var string
     */
    public $aliasName;

    /**
     * @OA\Property(
     *     title="Email",
     *     description="Employee email address",
     *     example="j.doe@bellevuecollege.edu",
     * )
     * @var string
     */
    public $email;

    /**
     * @OA\Property(
     *     title="Phone",
     *     description="Phone number without separators",
     *     example="4255641000",
     * )
     * @var string
     */
    public $phone;

    /**
     * @OA\Property(
     *     title="Username",
     *     description="Employee username",
     *     example="j.doe",
     * )
     * @var string
     */
    public $username;

}
