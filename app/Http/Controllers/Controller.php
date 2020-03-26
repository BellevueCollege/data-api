<?php
/**
 * @OA\Info(
 *      version="1.3",
 *      title="Bellevue College Data API",
 *      description="Bellevue College Data API Description",
 *      @OA\Contact(
 *          email="webmaster@bellevuecollege.edu"
 *      ),
 * )
 */
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
