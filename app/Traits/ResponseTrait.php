<?php
/**
 * Response trait
 *
 * @category ResponseTrait
 * @author   Adsum Originator<developer@adsum.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link    
 */

namespace App\Traits;

/**
 * Response trait
 *
 * @category ResponseTrait
 * @author   Adsum Originator<developer@adsum.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
trait ResponseTrait
{
    /**
     * 200 = HTTP_OK
     *
     * @param array       $data    data
     * @param string|null $message message
     *
     * @return json response
     */
    public function successResponse(array $data, string $message = null)
    {
        return response()->json(['status' => "true",'data' => $data, 'messages' => array($message)]);
    }

    /**
     * 500 = HTTP_INTERNAL_SERVER_ERROR
     *
     * @param Exception $exception
     *
     * @return response
     */
    public function sendErrorResponse(\Exception $exception)
    {
        $data = [
            'file' => __FILE__,
            'line' => __LINE__,
            'code' => HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR,
            'messages' => array($exception->getMessage()),
            'trace' => env('APP_DEBUG') === true ? $exception->getTrace() : null,
            'response' => [
                __('Server error'),
            ],
        ];

        return response()->json($data, HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * 403 = HTTP_FORBIDDEN
     *
     * @param string|null $message message
     *
     * @return response
     */
    public function sendAccessDenied(string $message = null)
    {
        if (empty($message)) {
            $message = 'Access Denied';
        }

        return response()->json(['status' => "false", 'messages' => array($message)], HttpStatusCode::HTTP_FORBIDDEN);
    }

    /**
     * 401 = HTTP_UNAUTHORIZED
     *
     * @param string|null $message message
     *
     * @return response
     */
    public function sendUnauthorised(string $message = null)
    {
        if (empty($message)) {
            $message = 'Access Denied';
        }
        return response()->json(
            [
                'status' => "false",
                'messages' => array($message),
            ],
            HttpStatusCode::HTTP_UNAUTHORIZED
        );
    }

    /**
     * 501 = HTTP_NOT_IMPLEMENTED
     *
     * @param string|null $message message
     *
     * @return response
     */
    public function sendNotImplemented(string $message = null)
    {
        if (empty($message)) {
            $message = 'Method not implemented yet';
        }
        $data = [];

        return response()->json($data, HttpStatusCode::HTTP_NOT_IMPLEMENTED);
    }

    /**
     * 400 = HTTP_BAD_REQUEST
     *
     * @param string $msg message
     *
     * @return response
     */
    protected function sendBadRequest($msg = 'Bad Request')
    {
        // CHECK STRING ARRAY AND OBJECT CONDITION AND RETURN SAME MESSAGE FORMAT.
        if (is_string($msg)) {
            // $msg = json_encode(array('messages' => [$msg]));
            $msg = $msg;
        } else {
            $msg = (is_array($msg)) ? json_encode($msg) : (string) $msg;
        }
        return response()->json(['status' => "false", 'messages' => array($msg)], HttpStatusCode::HTTP_BAD_REQUEST);
    }

    /**
     * 404 = HTTP_NOT_FOUND
     *
     * @param string $msg message
     *
     * @return response
     */
    protected function notFoundRequest(string $msg = '')
    {
        if (empty($msg)) {
            $msg = 'Not Found';
        }
        return response()->json(['status' => "false", 'messages' => array($msg)], HttpStatusCode::HTTP_NOT_FOUND);
    }

    /**
     * 204 = HTTP_NO_CONTENT
     *
     * @param string $msg message
     *
     * @return response
     */
    protected function recordNotFound(string $msg = '')
    {
        if (empty($msg)) {
            $msg = 'Record Not Found';
        }
        return response()->json(['status' => "false", 'messages' => array($msg)], HttpStatusCode::HTTP_NO_CONTENT);
    }

    /**
     * 409 = HTTP_CONFLICT
     *
     * @param string $msg message
     *
     * @return response
     */
    protected function sendConflictResponse(string $msg = '')
    {
        if (empty($msg)) {
            $msg = 'Record Exists';
        }
        return response()->json(['status' => "false", 'messages' => array($msg)], HttpStatusCode::HTTP_CONFLICT);
    }
}
