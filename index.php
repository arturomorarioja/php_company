<?php

define('HOME', 'home');
define('CUSTOMERS', 'customers');

$menuOption = HOME;
if (isset($_GET['m'])) {
    $menuOption = trim($_GET['m']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Company</h1>
    </header>
    <nav>
        <ul>
            <li>
                <?=$menuOption === HOME ? '<strong>' : '' ?>
                <a href="index.php?m=home">Home</a>
                <?=$menuOption === HOME ? '</strong>' : '' ?>
            </li>
            <li>
                <?=$menuOption === CUSTOMERS ? '<strong>' : '' ?>
                <a href="index.php?m=customers">Customers</a>
                <?=$menuOption === CUSTOMERS ? '</strong>' : '' ?>
            </li>
        </ul>
    </nav>
    <main>
        <?php 
            if ($menuOption === HOME) {
        ?>
            <img src="img/company.jpg" alt="Our company" class="picture">
            <p>At our company, we believe in shaping a brighter, more inspired tomorrow. With a passion for innovation and a commitment to excellence, we aim to create experiences that enrich lives and spark possibility.</p>  
            <p>Every step we take is guided by a simple principle: putting people first. Whether you’re here to explore, connect, or discover something new, we’re dedicated to making your journey seamless and fulfilling.  </p>
            <p>Our team thrives on curiosity, creativity, and collaboration, ensuring that every solution we offer is crafted with care and purpose. Together, we’re building more than just a business—we’re fostering a community where ideas flourish and dreams take flight.  </p>
            <p>Looking for something extraordinary? You’ve come to the right place. Let us guide you to what you didn’t even know you were searching for.  </p>
            <p>At our company, the possibilities are endless, and your experience is our priority. Welcome to a new chapter of opportunity. Let’s make it unforgettable.</p>
        <?php 
            } else {
                require_once 'src/Customer.php';
                $customer = new Customer;

                if (!isset($_GET['action'])) {
                    if (!isset($_GET['execution'])) {
        ?>
        <div>
            <a id="addCustomer" href="index.php?m=<?=CUSTOMERS ?>&action=new" 
                target="_self" alt="Add customer">New customer</a>
        </div>
        <table>
            <tr>
                <th>User name</th>
                <th>First name</th>
                <th>Last name</th>
                <th>Address</th>
                <th>Creation date</th>
                <th></th>
                <th></th>
            </tr>
        <?php
            $customers = $customer->getAll();
            foreach ($customers as $individualCustomer) {
                $userName = $individualCustomer['user_name'];
                echo <<<CUSTOMER
                    <tr>
                        <td>$userName</td>
                        <td>{$individualCustomer['first_name']}</td>
                        <td>{$individualCustomer['last_name']}</td>
                        <td>{$individualCustomer['address']}</td>
                        <td>{$individualCustomer['date_addition']}</td>
                        <td><a href="index.php?m=customers&action=edit&user_name=$userName"><img src="img/edit.png" alt="Edit customer"></a></td>
                        <td><a href="index.php?m=customers&action=delete&user_name=$userName"><img src="img/delete.png" alt="Delete customer"></a></td>
                    </tr>                    
                CUSTOMER;
            }
        ?>            
        </table>
        <?php 
                    } else {    // if (isset($_GET['execution'])) 
                        switch ($_GET['execution']) {
                            case 'new':
                                $return = $customer->insert(
                                    $_GET['userName'], $_GET['firstName'], 
                                    $_GET['lastName'], $_GET['address']
                                );
                                break;
                            case 'edit':
                                $return = $customer->update(
                                    $_GET['userName'], $_GET['firstName'], 
                                    $_GET['lastName'], $_GET['address']
                                );
                                break;
                            case 'delete':
                                $return = $customer->delete($_GET['userName']);
                                break;
                        }
                        if (isset($return['error'])) {
                            echo $return['error'];
                        } else {
                            header('Location: index.php?m=customers');
                        }
                    }
                } else {    // if (isset($_GET['action']))
                    $action = $_GET['action'];
                    $userName = '';

                    switch ($action) {
                        case 'new':
                            settype($customer, 'array');
                            $customer['first_name'] = '';
                            $customer['last_name'] = '';
                            $customer['address'] = '';
                            $customer['date_addition'] = date('d/m/Y');
                            break;
                        case 'edit':
                        case 'delete':
                            if (!isset($_GET['user_name'])) {
                                header('Location: index.php');
                            } else {        
                                $userName = $_GET['user_name'];
                                $customer = $customer->getByUsername($userName);
                            }
                            break;
                    }
        ?>
        <form action='index.php' method='GET'>
            <fieldset>
                <input type="hidden" name="m" value="<?=CUSTOMERS ?>">
                <input type="hidden" name="execution" value="<?=$action ?>">
                <div>
                    <label for="txtUserName">User name</label>
                    <input type="text" id="txtUserName" name="userName" 
                        value="<?=$userName ?>" <?=($action !== 'new' ? ' readonly' : '') ?>>
                </div>
                <div>
                    <label for="txtFirstName">First name</label>
                    <input type="text" id="txtFirstName" name="firstName" 
                        value="<?=$customer['first_name'] ?>" <?=($action === 'delete' ? ' readonly' : '') ?>>
                </div>
                <div>
                    <label for="txtLastName">Last name</label>
                    <input type="text" id="txtLastName" name="lastName" 
                        value="<?=$customer['last_name'] ?>" <?=($action === 'delete' ? ' readonly' : '') ?>>
                </div>
                <div>
                    <label for="txtAddress">Address</label>
                    <input type="text" id="txtAddress" name="address" 
                        value="<?=$customer['address'] ?>" <?=($action === 'delete' ? ' readonly' : '') ?>>
                </div>
                <div>
                    <label for="txtDateAddition">Creation date</label>
                    <input type="text" id="txtDateAddition" name="dateAddition" 
                        value="<?=$customer['date_addition'] ?>" readonly>
                </div>
                <div>
                    <input type="submit" 
                        value="<?=($action === 'new' ? 'Add' : ($action === 'edit' ? 'Update' : 'Delete')) ?>">
                </div>
                <div>
                    <a href="index.php?m=customers" title="Back to the customers page">Back</a>
                </div>
            </fieldset>
        </form>
        <?php                        
                }   // if (!isset($_GET['action']))
            }       // if ($menuOption === HOME)
        ?>
    </main>
    <footer>
        <p>&copy; 2022-2025 Arturo Mora-Rioja</p>
        <p class="small">Homepage text by <a href="https://chatgpt.com/" 
            target="_blank" title="ChatGPT 4o">ChatGPT 4o</a></p>
        <p class="small">Company picture by <a href="https://www.pexels.com/@polina-zimmerman/" 
            target="_blank" title="Polina Zimmerman">Polina Zimmerman</a> at <a href="https://www.pexels.com/" 
            target="_blank" title="Pexels">Pexels</a></p>
    </footer>
</body>
</html>