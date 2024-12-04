<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$team_id = $team_name = "";
$team_id_err = $team_name_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate team name
    $team_name = trim($_POST["team_name"]);
    if(empty($team_name)){
        $team_name_err = "Please enter a team name.";
    }

    // Validate team ID
    $team_id = trim($_POST["team_id"]);
    if(empty($team_id)){
        $team_id_err = "Please enter a team ID.";
    } elseif(!ctype_digit($team_id)){
        $team_id_err = "Please enter a valid team ID.";
    }

    // Check input errors before updating in database
    if(empty($team_name_err) && empty($team_id_err)){
        // Prepare an update statement for the team
        $sql = "UPDATE Teams SET team_name = ? WHERE team_id = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_team_name, $param_team_id);

            // Set parameters
            $param_team_name = $team_name;
            $param_team_id = $team_id;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Now, update the Pokémon-Team relationships (optional)
                // Clear previous Pokémon-Team associations
                $delete_pokemon_sql = "DELETE FROM Pokemon_Team WHERE team_id = ?";
                if($delete_stmt = mysqli_prepare($link, $delete_pokemon_sql)){
                    mysqli_stmt_bind_param($delete_stmt, "i", $param_team_id);
                    mysqli_stmt_execute($delete_stmt);
                    mysqli_stmt_close($delete_stmt);
                }

                // Add the new Pokémon to the team (assuming you have a way to get new Pokémon IDs from a form)
                if(!empty($_POST['pokemon_ids'])){
                    foreach ($_POST['pokemon_ids'] as $pokemon_id) {
                        $insert_pokemon_sql = "INSERT INTO Pokemon_Team (team_id, pokemon_id) VALUES (?, ?)";
                        if($insert_stmt = mysqli_prepare($link, $insert_pokemon_sql)){
                            mysqli_stmt_bind_param($insert_stmt, "ii", $param_team_id, $param_pokemon_id);
                            $param_pokemon_id = $pokemon_id;
                            mysqli_stmt_execute($insert_stmt);
                            mysqli_stmt_close($insert_stmt);
                        }
                    }
                }

                // Redirect to landing page after successful update
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
} else {
    // Check if team_id is set in the URL
    if(isset($_GET["team_id"]) && !empty(trim($_GET["team_id"]))){
        // Get the team details
        $team_id = trim($_GET["team_id"]);
        
        // Fetch team data from the database
        $sql = "SELECT team_id, team_name FROM Teams WHERE team_id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $param_team_id);
            $param_team_id = $team_id;

            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
                if(mysqli_num_rows($result) == 1){
                    // Fetch the result row
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    $team_name = $row["team_name"];
                } else {
                    echo "No records found.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    } else {
        // Redirect to homepage if team_id is not provided
        header("location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Team</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper {
            width: 500px;
            margin: 0 auto;
        }

        /* Adjust the checkbox grid layout to make checkboxes wider */
        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);  /* 5 columns */
            gap: 10px; /* Space between checkboxes */
            margin-left: -50px;  /* Extend the grid beyond the wrapper */
            margin-right: -50px; /* Extend the grid beyond the wrapper */
        }

        /* Ensure labels don't wrap and increase the width */
        .checkbox-grid label {
            display: block;
            white-space: normal;  /* Allow the label to wrap if needed */
            width: 100%; /* Allow the labels to take up full width in their grid cells */
            word-wrap: break-word;  /* Allow long Pokémon names to break and wrap */
        }

        /* Make the layout responsive for smaller screens */
        @media (max-width: 768px) {
            .checkbox-grid {
                grid-template-columns: repeat(3, 1fr);  /* 3 columns on smaller screens */
            }
        }

        @media (max-width: 480px) {
            .checkbox-grid {
                grid-template-columns: repeat(2, 1fr); /* 2 columns on mobile screens */
            }
        }

        /* Extend title label outside the wrapper */
        .page-header {
            margin-left: -20px;  /* Extend the title beyond the wrapper */
            margin-right: -20px; /* Extend the title beyond the wrapper */
        }

        /* New style to apply to the label */
        .pokemon-label {
            position: relative;
            left: -50px; /* Shift the label 50px to the left */
        }
        </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Update Team</h2>
                    </div>
                    <p>Please edit the team name and Pokémon members, then submit to update the team.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($team_id_err)) ? 'has-error' : ''; ?>">
                            <label>Team ID</label>
                            <input type="text" name="team_id" class="form-control" value="<?php echo $team_id; ?>" readonly>
                            <span class="help-block"><?php echo $team_id_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($team_name_err)) ? 'has-error' : ''; ?>">
                            <label>Team Name</label>
                            <input type="text" name="team_name" class="form-control" value="<?php echo $team_name; ?>">
                            <span class="help-block"><?php echo $team_name_err;?></span>
                        </div>
                        <div class="form-group">
                        <label class="pokemon-label">Pokémon Members</label><br>
                            <div class="checkbox-grid">
                                <?php
                                // Fetch available Pokémon
                                $pokemon_sql = "SELECT pokemon_id, name FROM Pokemon";
                                if($result = mysqli_query($link, $pokemon_sql)){
                                    while($row = mysqli_fetch_array($result)){
                                        $checked = "";
                                        // Check if the Pokémon is already part of the team
                                        $check_team_sql = "SELECT * FROM Pokemon_Team WHERE team_id = ? AND pokemon_id = ?";
                                        if($check_stmt = mysqli_prepare($link, $check_team_sql)){
                                            mysqli_stmt_bind_param($check_stmt, "ii", $param_team_id, $row["pokemon_id"]);
                                            mysqli_stmt_execute($check_stmt);
                                            $check_result = mysqli_stmt_get_result($check_stmt);
                                            if(mysqli_num_rows($check_result) > 0){
                                                $checked = "checked";
                                            }
                                            mysqli_stmt_close($check_stmt);
                                        }

                                        echo "<label><input type='checkbox' name='pokemon_ids[]' value='" . $row["pokemon_id"] . "' $checked> " . $row["name"] . "</label>";
                                    }
                                }
                                ?>
                            </div>
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
