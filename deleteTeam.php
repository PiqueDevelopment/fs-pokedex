<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$team_id = "";
$team_id_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate team_id
    $team_id = trim($_POST["team_id"]);
    if(empty($team_id)){
        $team_id_err = "Please enter a Team ID.";
    } elseif(!ctype_digit($team_id)){
        $team_id_err = "Please enter a valid Team ID.";
    }

    // Check input errors before deleting in database
    if(empty($team_id_err)){
        // Prepare a delete statement for the Pokemon_Team relationships first
        $sql = "DELETE FROM Pokemon_Team WHERE team_id = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_team_id);

            // Set parameters
            $param_team_id = $team_id;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Prepare a delete statement for the team
                $sql = "DELETE FROM Teams WHERE team_id = ?";
                if($stmt = mysqli_prepare($link, $sql)){
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "i", $param_team_id);

                    // Set parameters
                    $param_team_id = $team_id;

                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        // Redirect to landing page after successful deletion
                        header("location: index.php");
                        exit();
                    } else{
                        echo "Something went wrong while deleting the team. Please try again later.";
                    }
                }
            } else{
                echo "Something went wrong while deleting the Pokémon-Team relationship. Please try again later.";
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
    <title>Delete Team</title>
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
                        <h2>Delete Team</h2>
                    </div>
                    <p>Please fill this form and submit to delete a team from the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($team_id_err)) ? 'has-error' : ''; ?>">
                            <label>Team ID</label>
                            <input type="text" name="team_id" class="form-control" value="<?php echo $team_id; ?>">
                            <span class="help-block"><?php echo $team_id_err;?></span>
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
