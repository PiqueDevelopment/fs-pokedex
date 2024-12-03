<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$name = $gender = $type = $abilities = "";
$name_err = $gender_err = $type_err = $abilities_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $name = trim($_POST["name"]);
    if(empty($name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    }

    // Validate gender
    $gender = trim($_POST["gender"]);
    if(empty($gender)){
        $gender_err = "Please select a gender.";
    }

    // Validate type
    $type = $_POST["type"];
    if(empty($type)){
        $type_err = "Please select at least one type.";
    }

    // Validate abilities
    $abilities = $_POST["abilities"];
    if(empty($abilities)){
        $abilities_err = "Please select at least one ability.";
    }

    // Check input errors before inserting in database
    if(empty($name_err) && empty($gender_err) && empty($type_err) && empty($abilities_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO Pokemon (name, gender) VALUES (?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_name, $param_gender);

            // Set parameters
            $param_name = $name;
            $param_gender = $gender;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Get the last inserted ID
                $pokemon_id = mysqli_insert_id($link);

                // Insert types
                foreach($type as $type_id){
                    $sql_type = "INSERT INTO Pokemon_Type (pokemon_id, type_id) VALUES (?, ?)";
                    if($stmt_type = mysqli_prepare($link, $sql_type)){
                        mysqli_stmt_bind_param($stmt_type, "ii", $pokemon_id, $type_id);
                        mysqli_stmt_execute($stmt_type);
                    }
                }

                // Insert abilities
                foreach($abilities as $ability_id){
                    $sql_ability = "INSERT INTO Pokemon_Ability (pokemon_id, ability_id) VALUES (?, ?)";
                    if($stmt_ability = mysqli_prepare($link, $sql_ability)){
                        mysqli_stmt_bind_param($stmt_ability, "ii", $pokemon_id, $ability_id);
                        mysqli_stmt_execute($stmt_ability);
                    }
                }

                // Records created successfully. Redirect to landing page
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
    <title>Create Pokemon</title>
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
                        <h2>Create Pokemon</h2>
                    </div>
                    <p>Please fill this form and submit to add a new Pokemon to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($gender_err)) ? 'has-error' : ''; ?>">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="">Select Gender</option>
                                <option value="Male/Female" <?php echo ($gender == 'Male/Female') ? 'selected' : ''; ?>>Male/Female</option>
                                <option value="Male" <?php echo ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Genderless" <?php echo ($gender == 'Genderless') ? 'selected' : ''; ?>>Genderless</option>
                            </select>
                            <span class="help-block"><?php echo $gender_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($type_err)) ? 'has-error' : ''; ?>">
                            <label>Type (CTRL + CLICK to select multiple)</label>
                            <select name="type[]" class="form-control" multiple size="5" style="resize: vertical;">
                                <?php
                                $sql = "SELECT type_id, type_name FROM Type";
                                if($result = mysqli_query($link, $sql)){
                                    while($row = mysqli_fetch_array($result)){
                                        echo "<option value='" . $row['type_id'] . "'>" . $row['type_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <span class="help-block"><?php echo $type_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($abilities_err)) ? 'has-error' : ''; ?>">
                            <label>Abilities (CTRL + CLICK to select multiple)</label>
                            <select name="abilities[]" class="form-control" multiple size="5" style="resize: vertical;">
                                <?php
                                $sql = "SELECT ability_id, ability_name FROM Ability";
                                if($result = mysqli_query($link, $sql)){
                                    while($row = mysqli_fetch_array($result)){
                                        echo "<option value='" . $row['ability_id'] . "'>" . $row['ability_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <span class="help-block"><?php echo $abilities_err;?></span>
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