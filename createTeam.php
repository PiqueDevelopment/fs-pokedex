<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$team_name = "";
$team_name_err = "";
$selected_pokemon = [];

// Fetch all Pokémon for the checkbox grid
$pokemonList = [];
$sql = "SELECT pokemon_id, name FROM Pokemon";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $pokemonList[] = $row;
    }
    mysqli_free_result($result);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate team name
    $team_name = trim($_POST["team_name"]);
    if (empty($team_name)) {
        $team_name_err = "Please enter a team name.";
    } elseif (!filter_var($team_name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
        $team_name_err = "Please enter a valid team name.";
    }

    // Validate selected Pokémon
    if (!empty($_POST["pokemon_ids"])) {
        $selected_pokemon = $_POST["pokemon_ids"];
    }

    // Check input errors before inserting in database
    if (empty($team_name_err)) {
        // Begin a transaction
        mysqli_begin_transaction($link);

        try {
            // Insert the team name into Teams table
            $sql = "INSERT INTO Teams (team_name) VALUES (?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $param_team_name);
                $param_team_name = $team_name;

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Could not insert team name.");
                }
                mysqli_stmt_close($stmt);

                // Get the ID of the newly inserted team
                $team_id = mysqli_insert_id($link);

                // Insert the Pokémon-Team relationships
                if (!empty($selected_pokemon)) {
                    $sql = "INSERT INTO Pokemon_Team (team_id, pokemon_id) VALUES (?, ?)";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        foreach ($selected_pokemon as $pokemon_id) {
                            mysqli_stmt_bind_param($stmt, "ii", $team_id, $pokemon_id);
                            if (!mysqli_stmt_execute($stmt)) {
                                throw new Exception("Could not insert Pokémon-Team relationship.");
                            }
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
            }

            // Commit the transaction
            mysqli_commit($link);

            // Team created successfully. Redirect to landing page
            header("location: index.php");
            exit();
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            mysqli_rollback($link);
            echo "Something went wrong. Please try again later.";
        }
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Team</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style>
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
                        <h2>Create Team</h2>
                    </div>
                    <p>Please fill this form and submit to add a new team and assign Pokémon to it.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($team_name_err)) ? 'has-error' : ''; ?>">
                            <label>Team Name</label>
                            <input type="text" name="team_name" class="form-control" value="<?php echo $team_name; ?>">
                            <span class="help-block"><?php echo $team_name_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label class="pokemon-label">Pokémon Members</label><br>
                            <div class="checkbox-grid">
                                <?php foreach ($pokemonList as $pokemon): ?>
                                    <label>
                                        <input type="checkbox" name="pokemon_ids[]" value="<?php echo $pokemon['pokemon_id']; ?>">
                                        <?php echo htmlspecialchars($pokemon['name']); ?>
                                    </label>
                                <?php endforeach; ?>
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
