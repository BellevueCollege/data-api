<?php
/**
 * @OA\Schema(
 *     title="Directory Employee",
 *     description="Employee Directory information",
 *     @OA\Xml(
 *         name="DirectoryEmployee"
 *     )
 * )
 */
class DirectoryEmployee
{

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
     *     title="Display Name",
     *     description="Display name, using AKA as first name",
     *     example="Jo Doe",
     * )
     * @var string
     */
    public $displayName;

    /**
     * @OA\Property(
     *     title="Title",
     *     description="Job title- prefers acting title",
     *     example="Manager",
     * )
     * @var string
     */
    public $title;

    /**
     * @OA\Property(
     *     title="Department",
     *     description="Name of department to which employee reports",
     *     example="English Department",
     * )
     * @var string
     */
    public $department;

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
     *     title="Display Phone",
     *     description="Phone number with separators",
     *     example="425.564.1000",
     * )
     * @var string
     */
    public $displayPhone;

    /**
     * @OA\Property(
     *     title="Office",
     *     description="Office Number",
     *     example="U101A",
     * )
     * @var string
     */
    public $office;

    /**
     * @OA\Property(
     *     title="mailstop",
     *     description="Mailing office",
     *     example="U100",
     * )
     * @var string
     */
    public $mailstop;

}
