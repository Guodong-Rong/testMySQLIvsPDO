<?php
    // 1: Global variables
    $prodID = "";
    $prodName = "";
    $prodPrice = "";
    $message = "";

    // 2: Handle events by calling correct function
    if (filter_input(INPUT_SERVER, "REQUEST_METHOD") == "POST") {
        if (filter_input(INPUT_POST, "NewProduct")) {
            newProduct();
        } 
        if (filter_input(INPUT_POST, "GetProduct")) {
            getProduct();
        } 
        if (filter_input(INPUT_POST, "UpdateProduct")) {
            updateProduct();
        } 
        if (filter_input(INPUT_POST, "DeleteProduct")) {
            deleteProduct();
        }
    }

    // 3: Functions
    function newProduct() {
        global $prodID, $prodName, $prodPrice, $message;

        // Get the inputted values
        $prodID = filter_input(INPUT_POST, "ProductID", FILTER_SANITIZE_STRING);
        $prodName = filter_input(INPUT_POST, "ProductName", FILTER_SANITIZE_STRING);
        $prodPrice = filter_input(INPUT_POST, "ProductPrice", FILTER_SANITIZE_STRING);

        // Connect to database
        try {
            $conn = new PDO("mysql:host=localhost;dbname=mydatabase", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL INSERT statement
            $statement = $conn->prepare("INSERT INTO product VALUES(" . $prodID . ",'" . $prodName . "'," . $prodPrice . ");");
            $result = $statement->execute();

            // Test if INSERT statement worked
            if ($result == null) {
                $message = "An error occurred. A new product was not created for some reason.";
            } else {
                $message = "A new product with the ID number of " . $prodID . " was created successfully.";
            }

        } catch (PDOException $ex) {
            $message = "Database connection failed with the following error: " . $ex->getMessage();
        }

        $conn = null;
    }
    
    function getProduct() {
        global $prodID, $prodName, $prodPrice, $message;
    
        // Get the ID value
        $prodID = filter_input(INPUT_POST, "ProductID", FILTER_SANITIZE_STRING);
    
        // Connect to the database
        try {
            $conn = new PDO("mysql:host=localhost;dbname=mydatabase", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // SQL SELECT statement
            $statement = $conn->query("SELECT * FROM Product WHERE ID=" . $prodID);
            $result = $statement->fetch();
    
            // Test if SELECT statement worked
            if ($result == null) {
                $message = "An error occurred. The ID number doesn't exist or has been entered incorrectly.";
            } else {
                $prodID = $result[0];
                $prodName = $result[1];
                $prodPrice = $result[2];
            }
    
        } catch (PDOException $ex) {
            $message = "Database connection failed with the following error: " . $ex->getMessage();
        }
    
        // Close the connection
        $conn = null;
    }
    
    function updateProduct() {
        global $prodID, $prodName, $prodPrice, $message;
    
        // Get the inputted values
        $prodID = filter_input(INPUT_POST, "ProductID", FILTER_SANITIZE_STRING);
        $prodName = filter_input(INPUT_POST, "ProductName", FILTER_SANITIZE_STRING);
        $prodPrice = filter_input(INPUT_POST, "ProductPrice", FILTER_SANITIZE_STRING);
    
        // Connect to the database
        try {
            $conn = new PDO("mysql:host=localhost;dbname=mydatabase", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Determine whether ID is valid or not
            $statement = $conn->query("SELECT * FROM Product WHERE ID=" . $prodID);
            $result = $statement->fetch();
    
            // If ID is valid then undertake update
            if ($result != null) {
                // SQL UPDATE statement
                $statement = $conn->prepare("UPDATE product SET Name='" . $prodName . "', Price=" . $prodPrice . " WHERE ID=" . $prodID);
                $result = $statement->execute();
    
                // Test if UPDATE statement worked
                if ($result == null) {
                    $message = "An error occurred. The product was not updated for some reason";
                } else {
                    $message = "The product with the ID number of " . $prodID . " was updated successfully";
                }
            } else {
                $message = "An error occurred. The product ID number is not valid. You can only update a product with a valid ID number";
            }
    
        } catch (PDOException $ex) {
            $message = "Database connection failed with the following error: " . $ex->getMessage();
        }
    
        // Close the connection
        $conn = null;
    }
    
    function deleteProduct() {
        global $prodID, $prodName, $prodPrice, $message;
    
        // Get the inputted values
        $prodID = filter_input(INPUT_POST, "ProductID", FILTER_SANITIZE_STRING);
    
        // Connect to the database
        try {
            $conn = new PDO("mysql:host=localhost;dbname=mydatabase", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Determine whether ID is valid or not
            $statement = $conn->query("SELECT * FROM Product WHERE ID=" . $prodID);
            $result = $statement->fetch();
    
            // If ID is valid then delete the record
            if ($result != null) {
                // SQL DELETE statement
                $statement = $conn->prepare("DELETE FROM product WHERE ID=" . $prodID);
                $result = $statement->execute();
    
                // Test if DELETE statement worked
                if ($result == null) {
                    $message = "An error occurred. The product was not deleted for some reason";
                } else {
                    $message = "The product with the ID number of " . $prodID . " was deleted successfully";
    
                    // Clear the global variable values
                    $prodID = "";
                    $prodName = "";
                    $prodPrice = "";
                }
            } else {
                $message = "An error occurred. The product ID number is not valid. You can only delete a product with a valid ID number";
            }
    
        } catch (PDOException $ex) {
            $message = "Database connection failed with the following error: " . $ex->getMessage();
        }
    
        // Close the connection
        $conn = null;
    }
    
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1 style="text-align: center;">Product Form</h1>
    <form action="" method="post">
        <table width="100%">
            <colgroup>
                <col style="width:20%">
                <col style="width:80%">
            </colgroup>
            <tr>
                <td>ID:</td>
                <td><input name="ProductID" type="number" style="width:100%;" value="<?php echo $prodID; ?>"></td>
            </tr>
            <tr>
                <td>Name:</td>
                <td><input name="ProductName" type="text" style="width:100%;" value="<?php echo $prodName; ?>"></td>
            </tr>
            <tr>
                <td>Price:</td>
                <td><input name="ProductPrice" type="text" style="width:100%;" value="<?php echo $prodPrice; ?>"></td>
            </tr>
        </table>
        <table width="100%">
            <colgroup>
                <col style="width:20%">
                <col style="width:20%">
                <col style="width:20%">
                <col style="width:20%">
                <col style="width:20%">
            </colgroup>
            <tr>
                <td> </td>
                <td> <input name="NewProduct" type="Submit" value="New Product" style="width:100%;"> </td>
                <td> <input name="GetProduct" type="Submit" value="Get Product" style="width:100%;"> </td>
                <td> <input name="UpdateProduct" type="Submit" value="Update Product" style="width:100%;"> </td>
                <td> <input name="DeleteProduct" type="Submit" value="Delete Product" style="width:100%;"> </td>
            </tr>
        </table>
    </form>
    <label name="OutputMessage"><?php echo "Output Message: " . $message; ?></label>
</body>

</html>