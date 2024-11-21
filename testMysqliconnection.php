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
        $conn = new mysqli("localhost", "root", "", "mydatabase");

        // Check connection
        if ($conn->connect_error) {
            $message = "Connection failed: " . $conn->connect_error;
            return;
        }

        // SQL INSERT statement
        $stmt = $conn->prepare("INSERT INTO product (ID, Name, Price) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $prodID, $prodName, $prodPrice);

        if ($stmt->execute()) {
            $message = "A new product with the ID number of $prodID was created successfully.";
        } else {
            $message = "An error occurred. A new product was not created: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
    
    function getProduct() {
        global $prodID, $prodName, $prodPrice, $message;
    
        // Get the ID value
        $prodID = filter_input(INPUT_POST, "ProductID", FILTER_SANITIZE_STRING);
    
        // Connect to the database
        $conn = new mysqli("localhost", "root", "", "mydatabase");

        if ($conn->connect_error) {
            $message = "Connection failed: " . $conn->connect_error;
            return;
        }

        // SQL SELECT statement
        $stmt = $conn->prepare("SELECT * FROM product WHERE ID = ?");
        $stmt->bind_param("i", $prodID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Test if SELECT statement worked
        if ($row) {
            $prodID = $row['ID'];
            $prodName = $row['Name'];
            $prodPrice = $row['Price'];
        } else {
            $message = "An error occurred. The ID number doesn't exist or has been entered incorrectly.";
        }

        $stmt->close();
        $conn->close();
    }
    
    function updateProduct() {
        global $prodID, $prodName, $prodPrice, $message;
    
        // Get the inputted values
        $prodID = filter_input(INPUT_POST, "ProductID", FILTER_SANITIZE_STRING);
        $prodName = filter_input(INPUT_POST, "ProductName", FILTER_SANITIZE_STRING);
        $prodPrice = filter_input(INPUT_POST, "ProductPrice", FILTER_SANITIZE_STRING);
    
        // Connect to the database
        $conn = new mysqli("localhost", "root", "", "mydatabase");

        if ($conn->connect_error) {
            $message = "Connection failed: " . $conn->connect_error;
            return;
        }

        // Determine whether ID is valid or not
        $stmt = $conn->prepare("SELECT * FROM product WHERE ID = ?");
        $stmt->bind_param("i", $prodID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->fetch_assoc()) {
            // SQL UPDATE statement
            $stmt = $conn->prepare("UPDATE product SET Name = ?, Price = ? WHERE ID = ?");
            $stmt->bind_param("sdi", $prodName, $prodPrice, $prodID);

            if ($stmt->execute()) {
                $message = "The product with the ID number of $prodID was updated successfully.";
            } else {
                $message = "An error occurred. The product was not updated: " . $stmt->error;
            }
        } else {
            $message = "An error occurred. The product ID number is not valid.";
        }

        $stmt->close();
        $conn->close();
    }
    
    function deleteProduct() {
        global $prodID, $prodName, $prodPrice, $message;
    
        // Get the inputted values
        $prodID = filter_input(INPUT_POST, "ProductID", FILTER_SANITIZE_STRING);
    
        // Connect to the database
        $conn = new mysqli("localhost", "root", "", "mydatabase");

        if ($conn->connect_error) {
            $message = "Connection failed: " . $conn->connect_error;
            return;
        }

        // Determine whether ID is valid or not
        $stmt = $conn->prepare("SELECT * FROM product WHERE ID = ?");
        $stmt->bind_param("i", $prodID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->fetch_assoc()) {
            // SQL DELETE statement
            $stmt = $conn->prepare("DELETE FROM product WHERE ID = ?");
            $stmt->bind_param("i", $prodID);

            if ($stmt->execute()) {
                $message = "The product with the ID number of $prodID was deleted successfully.";
                $prodID = $prodName = $prodPrice = "";
            } else {
                $message = "An error occurred. The product was not deleted: " . $stmt->error;
            }
        } else {
            $message = "An error occurred. The product ID number is not valid.";
        }

        $stmt->close();
        $conn->close();
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