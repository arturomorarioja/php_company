<?php

require_once 'ApiConfig.php';

class ApiConnection extends ApiConfig
{

    // Formats customer parameters according to the content type x-www-form-urlencoded
    private function encodeParams(array $params): string
    {
        $encodedParams = '';
        foreach($params as $key => $value) {
            $encodedParams .= '&' . $key . '=' . rawurlencode($value);
        }
        return substr($encodedParams, 1);
    }

    protected function apiCall(string $endpoint, string $method = 'GET', array $params = []): array 
    {
        try {
            $ch = curl_init();
            $httpHeader = ['x-apikey: ' . self::API_KEY];
            
            switch ($method) {
                case 'GET':
                    if (!empty($params)) {
                        $endpoint .= "?q={\"user_name\":\"{$params['user_name']}\"}";
                    }

                    $httpHeader[] = 'Content-type: application/json';
                    break;
                case 'POST':
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeParams($params));
                    
                    $httpHeader[] = 'Content-type: application/x-www-form-urlencoded';
                    break;
                case 'PUT':
                    $endpoint .= '/' . $params['_id'];
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeParams($params));
                    
                    $httpHeader[] = 'Content-type: application/x-www-form-urlencoded';
                    break;
                case 'DELETE':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    $endpoint .= '/' . $params['_id']; 
                    break;
            }
            
            curl_setopt($ch, CURLOPT_URL, self::API_BASE_URL . $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
            
            $response = json_decode(curl_exec($ch), true);
        } catch(Exception $e) {
            $response = ['exception' => $e->getMessage()];
        } catch(Error $e) {
            $response = ['error' => $e->getMessage()];
        } finally {
            curl_close($ch);
        }

        return $response;
    }
}