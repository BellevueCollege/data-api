<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
     /**
     * @OA\Info(
     *      version=APP_VERSION,
     *      title="Bellevue College Data API",
     *      description="The Bellevue College Data API is used to serve and collect BC data.",
     *      @OA\Contact(
     *          email="admin@admin.com"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=PUBLIC_URL,
     *      description="Primary API Server"
     * )
     * @OA\Server(
     *      url=PRIVATE_URL,
     *      description="Private API Server"
     * )
     * @OA\PathItem(
     *      path="/api/v1/internal",
     *      @OA\Server(
     *          url=PRIVATE_URL,
     *          description="Private API Server"
     *      )
     * )
     *
     * @OA\Tag(
     *     name="Employees",
     *     description="API Endpoints for Employee Data"
     * )
     * @OA\Tag(
     *     name="Directory",
     *     description="API Endpoints for Employee Directory Data"
     * )
     * @OA\Tag(
     *     name="Internal",
     *     description="API Endpoints available on the internal domain"
     * )
     */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
