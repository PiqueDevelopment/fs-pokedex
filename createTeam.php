<!-- Group Number: 46 -->
<!-- Group Names: Dylan Liu, Michael Bernardino, Brendon Tran -->

<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$team_name = "";
$team_name_err = "";
$selected_pokemon = [];

// Fetch all Pokémon for the multi-select dropdown
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
            // Fetch the maximum team ID
            $sql = "SELECT MAX(team_id) AS max_team_id FROM Teams";
            $result = mysqli_query($link, $sql);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $max_team_id = $row['max_team_id'];
            $new_team_id = $max_team_id + 1;

            // Insert the team name into Teams table with the new team ID
            $sql = "INSERT INTO Teams (team_id, team_name) VALUES (?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "is", $new_team_id, $param_team_name);
                $param_team_name = $team_name;

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Could not insert team name.");
                }
                mysqli_stmt_close($stmt);

                // Insert the Pokémon-Team relationships
                if (!empty($selected_pokemon)) {
                    $sql = "INSERT INTO Pokemon_Team (team_id, pokemon_id) VALUES (?, ?)";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        foreach ($selected_pokemon as $pokemon_id) {
                            mysqli_stmt_bind_param($stmt, "ii", $new_team_id, $pokemon_id);
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
    <style type="text/css">
        .wrapper {
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
                            <label>Assign Pokémon to Team</label>
                            <select name="pokemon_ids[]" class="form-control" multiple>
                                <?php foreach ($pokemonList as $pokemon): ?>
                                    <option value="<?php echo $pokemon['pokemon_id']; ?>">
                                        <?php echo htmlspecialchars($pokemon['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Hold down the Ctrl (Windows) / Command (Mac) button to select multiple Pokémon.</small>
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
