<?php
require_once "config.php";

// Initialize variables
$name = $gender = $type = $abilities = "";
$name_err = $gender_err = $type_err = $abilities_err = "";

// Check if an ID is provided
if (isset($_GET["id_or_name"]) && !empty($_GET["id_or_name"])) {
    $pokemon_id = $_GET["id_or_name"];

    // Fetch current data for the selected Pokémon
    $sql = "SELECT name, gender FROM Pokemon WHERE pokemon_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $pokemon_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $name, $gender);
                mysqli_stmt_fetch($stmt);
            } else {
                header("location: index.php");
                exit();
            }
        } else {
            echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    header("location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    $name = trim($_POST["name"]);
    if (empty($name)) {
        $name_err = "Please enter a name.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $name_err = "Please enter a valid name.";
    }

    // Validate gender
    $gender = trim($_POST["gender"]);
    if (empty($gender)) {
        $gender_err = "Please select a gender.";
    }

    // Validate types
    $type = $_POST["type"] ?? [];
    if (empty($type)) {
        $type_err = "Please select at least one type.";
    }

    // Validate abilities
    $abilities = $_POST["abilities"] ?? [];
    if (empty($abilities)) {
        $abilities_err = "Please select at least one ability.";
    }

    // If no errors, proceed with update
    if (empty($name_err) && empty($gender_err) && empty($type_err) && empty($abilities_err)) {
        $sql = "UPDATE Pokemon SET name = ?, gender = ? WHERE pokemon_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssi", $name, $gender, $pokemon_id);
            if (mysqli_stmt_execute($stmt)) {
                // Delete old types and abilities
                $sql_delete = "DELETE FROM Pokemon_Type WHERE pokemon_id = ?";
                if ($stmt_delete = mysqli_prepare($link, $sql_delete)) {
                    mysqli_stmt_bind_param($stmt_delete, "i", $pokemon_id);
                    mysqli_stmt_execute($stmt_delete);
                }

                $sql_delete_abilities = "DELETE FROM Pokemon_Ability WHERE pokemon_id = ?";
                if ($stmt_delete_abilities = mysqli_prepare($link, $sql_delete_abilities)) {
                    mysqli_stmt_bind_param($stmt_delete_abilities, "i", $pokemon_id);
                    mysqli_stmt_execute($stmt_delete_abilities);
                }

                // Insert new types
                foreach ($type as $type_id) {
                    $sql_type = "INSERT INTO Pokemon_Type (pokemon_id, type_id) VALUES (?, ?)";
                    if ($stmt_type = mysqli_prepare($link, $sql_type)) {
                        mysqli_stmt_bind_param($stmt_type, "ii", $pokemon_id, $type_id);
                        mysqli_stmt_execute($stmt_type);
                    }
                }

                // Insert new abilities
                foreach ($abilities as $ability_id) {
                    $sql_ability = "INSERT INTO Pokemon_Ability (pokemon_id, ability_id) VALUES (?, ?)";
                    if ($stmt_ability = mysqli_prepare($link, $sql_ability)) {
                        mysqli_stmt_bind_param($stmt_ability, "ii", $pokemon_id, $ability_id);
                        mysqli_stmt_execute($stmt_ability);
                    }
                }

                // Redirect to the main page
                header("location: index.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Pokémon</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper {
            width: 500px;
            margin: 0 auto;
        }

        /* Grid layout for types (3 columns) */
        .type-checkbox-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);  /* 3 equal-width columns */
            gap: 5px; /* Space between checkboxes */
        }

        .type-checkbox-grid label {
            display: block;
        }

        /* Grid layout for abilities (5 columns) */
        .ability-checkbox-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);  /* 5 equal-width columns */
            gap: 10px; /* Space between checkboxes */
            margin-left: -150px;  /* Extend the grid beyond the wrapper */
            margin-right: -150px; /* Extend the grid beyond the wrapper */
        }

        .ability-checkbox-grid label {
            display: block;

        }

        .abilities-label {
            position: relative;
            left: -150px; /* Shift the label 50px to the left */
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Update Pokémon</h2>
                    </div>
                    <p>Please fill this form to update the Pokémon's information.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id_or_name=" . $pokemon_id; ?>" method="post">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
                            <span class="help-block"><?php echo $name_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="">Select Gender</option>
                                <option value="Male/Female" <?php echo ($gender == 'Male/Female') ? 'selected' : ''; ?>>Male/Female</option>
                                <option value="Male" <?php echo ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Genderless" <?php echo ($gender == 'Genderless') ? 'selected' : ''; ?>>Genderless</option>
                            </select>
                            <span class="help-block"><?php echo $gender_err; ?></span>
                        </div>

                        <!-- Types Checkbox Grid (3 columns) -->
                        <div class="form-group">
                            <label>Types</label><br>
                            <div class="type-checkbox-grid">
                                <?php
                                // Fetch available types
                                $sql_types = "SELECT type_id, type_name FROM Type";
                                $result_types = mysqli_query($link, $sql_types);
                                while ($row_type = mysqli_fetch_array($result_types)) {
                                    // Check if the type is already associated with this Pokémon
                                    $checked = "";
                                    $sql_check_type = "SELECT * FROM Pokemon_Type WHERE pokemon_id = ? AND type_id = ?";
                                    if ($stmt_check_type = mysqli_prepare($link, $sql_check_type)) {
                                        mysqli_stmt_bind_param($stmt_check_type, "ii", $pokemon_id, $row_type['type_id']);
                                        mysqli_stmt_execute($stmt_check_type);
                                        $result_check_type = mysqli_stmt_get_result($stmt_check_type);
                                        if (mysqli_num_rows($result_check_type) > 0) {
                                            $checked = "checked";
                                        }
                                    }
                                    echo "<label><input type='checkbox' name='type[]' value='" . $row_type['type_id'] . "' $checked> " . $row_type['type_name'] . "</label>";
                                }
                                ?>
                            </div>
                            <span class="help-block"><?php echo $type_err; ?></span>
                        </div>

                        <!-- Abilities Checkbox Grid (5 columns, extends beyond wrapper) -->
                        <div class="form-group">
                        <label class="abilities-label">Abilities</label><br>
                            <div class="ability-checkbox-grid">
                                <?php
                                // Fetch available abilities
                                $sql_abilities = "SELECT ability_id, ability_name FROM Ability";
                                $result_abilities = mysqli_query($link, $sql_abilities);
                                while ($row_ability = mysqli_fetch_array($result_abilities)) {
                                    // Check if the ability is already associated with this Pokémon
                                    $checked = "";
                                    $sql_check_ability = "SELECT * FROM Pokemon_Ability WHERE pokemon_id = ? AND ability_id = ?";
                                    if ($stmt_check_ability = mysqli_prepare($link, $sql_check_ability)) {
                                        mysqli_stmt_bind_param($stmt_check_ability, "ii", $pokemon_id, $row_ability['ability_id']);
                                        mysqli_stmt_execute($stmt_check_ability);
                                        $result_check_ability = mysqli_stmt_get_result($stmt_check_ability);
                                        if (mysqli_num_rows($result_check_ability) > 0) {
                                            $checked = "checked";
                                        }
                                    }
                                    echo "<label><input type='checkbox' name='abilities[]' value='" . $row_ability['ability_id'] . "' $checked> " . $row_ability['ability_name'] . "</label>";
                                }
                                ?>
                            </div>
                            <span class="help-block"><?php echo $abilities_err; ?></span>
                        </div>

                        <input type="submit" class="btn btn-primary" value="Update">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
