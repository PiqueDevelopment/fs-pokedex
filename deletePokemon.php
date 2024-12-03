<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$pokemon_id = "";
$pokemon_id_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate pokemon_id
    $pokemon_id = trim($_POST["pokemon_id"]);
    if(empty($pokemon_id)){
        $pokemon_id_err = "Please enter a Pokémon ID.";
    } elseif(!ctype_digit($pokemon_id)){
        $pokemon_id_err = "Please enter a valid Pokémon ID.";
    }

    // Check input errors before deleting in database
    if(empty($pokemon_id_err)){
        // Prepare a delete statement
        $sql = "DELETE FROM Pokemon WHERE pokemon_id = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_pokemon_id);

            // Set parameters
            $param_pokemon_id = $pokemon_id;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Get the max pokemon_id
                $sql_max_id = "SELECT MAX(pokemon_id) AS max_id FROM Pokemon";
                if($result = mysqli_query($link, $sql_max_id)){
                    $row = mysqli_fetch_array($result);
                    $max_id = $row['max_id'] + 1;

                    // Set the auto-increment value to the next highest value
                    $sql_auto_increment = "ALTER TABLE Pokemon AUTO_INCREMENT = $max_id";
                    mysqli_query($link, $sql_auto_increment);
                }

                // Records deleted successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Pokemon</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Delete Pokemon</h2>
                    </div>
                    <p>Please fill this form and submit to delete a Pokemon from the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($pokemon_id_err)) ? 'has-error' : ''; ?>">
                            <label>Pokemon ID</label>
                            <input type="text" name="pokemon_id" class="form-control" value="<?php echo $pokemon_id; ?>">
                            <span class="help-block"><?php echo $pokemon_id_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>