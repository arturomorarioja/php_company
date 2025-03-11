<?php

/**
 * Customer class
 * 
 * @author  Arturo Mora-Rioja
 * @version 1.0.0 September 2022
 * @version 1.0.1 March 2025. JavaDoc comments added
 */

require_once 'ApiConnection.php';

class Customer extends ApiConnection 
{
    const BASE_ENDPOINT = 'customers';

    /**
     * Creates an associative array with user data
     * @param string The username
     * @param string The user's first name
     * @param string The user's last name
     * @param string The user's address
     * @return array An associative array with user data
     */
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

    /**
     * Retrieves all users
     * @return array An associative array with the API response 
     */
    public function getAll(): array 
    {
        $customers = $this->apiCall(self::BASE_ENDPOINT);

        return array_map(function($customer) {
            $customer['date_addition'] = substr($customer['date_addition'], 0, 10);
            return $customer;
        }, $customers);
    }

    /**
     * Retrieves a user by its username
     * @param string The username
     * @return array An associative array with the API response 
     */
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

    /**
     * Inserts a user
     * @param string The username
     * @param string The user's first name
     * @param string The user's last name
     * @param string The user's address
     * @return array An associative array with the API response 
     */
    public function insert(string $username, string $firstName, string $lastName, string $address): array
    {
        return $this->apiCall(self::BASE_ENDPOINT, 'POST', 
            $this->groupParams($username, $firstName, $lastName, $address));
    }

    /**
     * Updates a user
     * @param string The username
     * @param string The user's first name
     * @param string The user's last name
     * @param string The user's address
     * @return array An associative array with the API response 
     *               or an error message
     */
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

    /**
     * Deletes a user
     * @param string The username
     * @return array An associative array with the API response 
     *               or an error message
     */
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