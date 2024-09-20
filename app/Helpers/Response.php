<?php

namespace App\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response as LaravelResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class Response
{
    public const HTTP_CONTINUE = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    public const HTTP_PROCESSING = 102;            // RFC2518
    public const HTTP_EARLY_HINTS = 103;           // RFC8297
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_RESET_CONTENT = 205;
    public const HTTP_PARTIAL_CONTENT = 206;
    public const HTTP_MULTI_STATUS = 207;          // RFC4918
    public const HTTP_ALREADY_REPORTED = 208;      // RFC5842
    public const HTTP_IM_USED = 226;               // RFC3229
    public const HTTP_MULTIPLE_CHOICES = 300;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_SEE_OTHER = 303;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_USE_PROXY = 305;
    public const HTTP_RESERVED = 306;
    public const HTTP_TEMPORARY_REDIRECT = 307;
    public const HTTP_PERMANENTLY_REDIRECT = 308;  // RFC7238
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_NOT_ACCEPTABLE = 406;
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const HTTP_REQUEST_TIMEOUT = 408;
    public const HTTP_CONFLICT = 409;
    public const HTTP_GONE = 410;
    public const HTTP_LENGTH_REQUIRED = 411;
    public const HTTP_PRECONDITION_FAILED = 412;
    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    public const HTTP_REQUEST_URI_TOO_LONG = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_EXPECTATION_FAILED = 417;
    public const HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
    public const HTTP_MISDIRECTED_REQUEST = 421;                                         // RFC7540
    public const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    public const HTTP_LOCKED = 423;                                                      // RFC4918
    public const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    public const HTTP_TOO_EARLY = 425;                                                   // RFC-ietf-httpbis-replay-04
    public const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    public const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    public const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;                               // RFC7725
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;
    public const HTTP_VERSION_NOT_SUPPORTED = 505;
    public const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    public const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    public const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    public const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

    /**
     * Send succes response
     *
     * @param array|Arrayable|LengthAwarePaginator|null $data
     * @param array|Arrayable|null $additional
     * @param string $message
     * @param integer $code
     * @return JsonResponse
     */
    public static function success(
        array|Arrayable|LengthAwarePaginator|JsonResource|null $data = null,
        array|Arrayable|null $additional = null,
        string $message = 'Success',
        int $code = self::HTTP_OK
    ): JsonResponse {
        // default template
        $result = [
            'status' => 'success',
            'message' => $message,
            'data' => null,
        ];
        // transform
        $data = static::transform($data);
        $additional = static::transform($additional);
        // validate data
        if (! is_null($data)) {
            // if additional data have data
            if (is_array($additional) && count($additional) > 0) {
                $result['data'] = array_merge(['data' => $data], $additional);
            } else {
                $result['data'] = $data;
            }
        } else {
            unset($result['data']);
        }
        // send response
        return LaravelResponse::json($result, $code);
    }

    /**
     * Send error response
     *
     * @param array|Arrayable|JsonSerializable|null|null $errors
     * @param string $message
     * @param integer $code
     * @return JsonResponse
     */
    public static function error(
        array|Arrayable|JsonSerializable|null $errors = null,
        string $message = 'Error',
        int $code = self::HTTP_BAD_REQUEST
    ): JsonResponse {
        // default template
        $result = [
            'status' => 'error',
            'message' => $message,
            'errors' => null,
        ];
        // transform errors
        $errors = static::transform($errors);
        // validate error
        if ($errors) {
            $result['errors'] = $errors;
        } else {
            unset($result['errors']);
        }
        // send response
        return LaravelResponse::json($result, $code);
    }

    /**
     * Transform data
     *
     * @param array|Arrayable|JsonSerializable|LengthAwarePaginator|null $data
     * @return array|null
     */
    private static function transform(
        array|Arrayable|JsonSerializable|LengthAwarePaginator|null $data
    ): ?array {
        // if data is laravel paginator
        if ($data instanceof LengthAwarePaginator) {
            return $data->toArray();
        }
        // if data is Arrayable object
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }
        // if data is JsonSerializable object
        if ($data instanceof JsonSerializable) {
            return $data->jsonSerialize();
        }
        // otherwise return the raw data
        return $data;
    }
}
