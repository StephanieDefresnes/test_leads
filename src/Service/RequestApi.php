<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class RequestApi
{
    public function __construct(
        private ParameterBagInterface $parameters,
        private SerializerInterface $serializer,
        private HttpClientInterface $httpClient ){}

    public function loginApi()
    {
        $user_data  = ["username" => "stephanie", "password" => "bAMfwG99pHA1"];
        $login_req  = 'api/login_check';

        try {
            $response = self::callApi("POST", $login_req, null, $user_data);
            return $response->toArray();
        } catch (\Exception $e) {
            return json_decode($response->getContent(false), true);
        }
    }

    public function callApi($method, $api_route, $token = null, $data = array(), $send = false)
    {
        $url = $this->parameters->get('api_url') . $api_route;

        $headers['Content-Type'] = 'application/json';
        if ($token) $headers['Authorization'] = 'Bearer ' . $token;

        $body = empty($data) ? $data : $this->serializer->serialize($data, 'json');

        return $response = $this->httpClient->request($method, $url, [
            'headers'   => $headers,
            'body'      => $body,
        ]);
    }

}