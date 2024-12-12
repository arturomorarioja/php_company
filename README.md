# Company
Example of PHP API consumption with cUrl to [restdb.io](https://restdb.io/).

## Installation
1. A restdb.io database must be created with a collection `Customers` that contains the data in `data/customers.json`.
2. It is necessary to create a file `src/ApiConfig.php` with the following content:
    ```php
    <?php

    class ApiConfig 
    {
        private const API_BASE_URL = <restdb_url>;
        private const API_KEY = <your_api_key>;
    }
    ```

## Tools
PHP8 / CSS3 / HTML5

## Author
Arturo Mora-Rioja