<!-- Group Number: 46 -->
<!-- Group Names: Dylan Liu, Michael Bernardino, Brendon Tran -->

<?php
// Include config file
require_once "config.php";

// Check if an ID is provided
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $team_id = trim($_GET["id"]);

    // Prepare a delete statement
    $sql = "DELETE FROM Teams WHERE team_id = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_team_id);

        // Set parameters
        $param_team_id = $team_id;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Records deleted successfully. Redirect to landing page
            header("location: index.php");
            exit();
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($link);
} else {
    // Redirect to error page if ID is not provided
    header("location: error.php");
    exit();
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
