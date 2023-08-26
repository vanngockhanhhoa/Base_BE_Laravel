<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ApiResponseXML
{
    /**
     * Prepare response.
     *
     * @param string $status
     * @param string $message
     * @param int $statusCode
     * @return array
     */
    protected function prepareResponseTLL($responseTitle, $resultCode, $commonResponseBody = '', $responseBody = '')
    {
        // Create the SOAP XML response
        $soapResponse = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.next.push.lincoln.seanuts.co.jp/">
                            <soapenv:Header/>
                            <soapenv:Body>
                            <ser:' . $responseTitle . 'Response>
                                <' . $responseTitle . 'Result>
                                <commonResponse>
                                    <resultCode>' . $resultCode . '</resultCode>'
                                    . $commonResponseBody .
                                '</commonResponse>'
                                . $responseBody .
                                '</' . $responseTitle . 'Result>
                            </ser:' . $responseTitle . 'Response>
                            </soapenv:Body>
                        </soapenv:Envelope>';

        return $soapResponse;
    }

    /**
     * @param string $responseTitle
     * @param $resultCode
     * @param string $commonResponseBody
     * @param string $responseBody
     * @param int $statusCode
     * @return mixed
     */
    public function successXML($responseTitle, $resultCode = 0, $commonResponseBody = '', $responseBody = '', $statusCode = Response::HTTP_OK)
    {
        $response = $this->prepareResponseTLL($responseTitle, $resultCode, $commonResponseBody, $responseBody);

        // Set the response headers
        $headers = [
            'Content-Type' => 'text/xml; charset=utf-8',
        ];

        return response($response, $statusCode, $headers);
    }

    /**
     * Error Response
     *
     * @param  $responseTitle
     * @param int $errorCode
     * @param string $commonErrorBody
     * @param string $errorBody
     * @param int $statusCode
     * @return mixed
     */
    public function errorXML($responseTitle, $errorCode = 1, $commonErrorBody = '', $errorBody = '', $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        $response = $this->prepareResponseTLL($responseTitle, $errorCode, $commonErrorBody, $errorBody);

        // Set the response headers
        $headers = [
            'Content-Type' => 'text/xml; charset=utf-8',
        ];

        return response($response, $statusCode, $headers);
    }
}
