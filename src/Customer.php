<?php

/**
 * Customer class
 * 
 * @author  Arturo Mora-Rioja
 * @version 1.0, September 2022
 */

require_once 'ApiConnection.php';

class Customer extends ApiConnection 
{
    const BASE_ENDPOINT = 'customers';

    private function groupParams(string $username, string $firstName, string $lastName, string $address): array
    {
        return [
            'user_name' => $username,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'address' => $address,
            'date_addition' => date('d/m/Y')
        ];
    }

    public function getAll(): array 
    {
        $customers = $this->apiCall(self::BASE_ENDPOINT);

        return array_map(function($customer) {
            $customer['date_addition'] = substr($customer['date_addition'], 0, 10);
            return $customer;
        }, $customers);
    }

    public function getByUsername(string $username): array
    {
        $customer = $this->apiCall(self::BASE_ENDPOINT, 'GET', ['user_name' => $username]);

        if (count($customer) === 0) {
            return $customer;
        } else {
            $customer[0]['date_addition'] = substr($customer[0]['date_addition'], 0, 10);
            return $customer[0];
        }
    }

    public function insert(string $username, string $firstName, string $lastName, string $address): array
    {
        return $this->apiCall(self::BASE_ENDPOINT, 'POST', 
            $this->groupParams($username, $firstName, $lastName, $address));
    }

    public function update(string $username, string $firstName, string $lastName, string $address): array 
    {
        $customer = $this->getByUsername($username);

        if (empty($customer)) {
            return ['error' => 'Customer not found'];
        } else {
            return $this->apiCall(self::BASE_ENDPOINT, 'PUT', 
                ['_id' => $customer['_id']] + $this->groupParams($username, $firstName, $lastName, $address));
        }
    }

    public function delete(string $username): array
    {
        $customer = $this->getByUsername($username);

        if (empty($customer)) {
            return ['error' => 'Customer not found'];
        } else {
            return $this->apiCall(self::BASE_ENDPOINT, 'DELETE', ['_id' => $customer['_id']]);
        }
    }
}