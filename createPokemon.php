<?php
require_once "config.php";

// Initialize variables
$name = $gender = $type = $abilities = "";
$name_err = $gender_err = $type_err = $abilities_err = "";

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

    // If no errors, proceed with insertion
    if (empty($name_err) && empty($gender_err) && empty($type_err) && empty($abilities_err)) {
        // Insert into Pokémon table
        $sql = "INSERT INTO Pokemon (name, gender) VALUES (?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $name, $gender);
            if (mysqli_stmt_execute($stmt)) {
                // Get the last inserted Pokémon ID
                $pokemon_id = mysqli_insert_id($link);

                // Insert into Pokémon_Type
                foreach ($type as $type_id) {
                    $sql_type = "INSERT INTO Pokemon_Type (pokemon_id, type_id) VALUES (?, ?)";
                    if ($stmt_type = mysqli_prepare($link, $sql_type)) {
                        mysqli_stmt_bind_param($stmt_type, "ii", $pokemon_id, $type_id);
                        mysqli_stmt_execute($stmt_type);
                    }
                }

                // Insert into Pokémon_Ability
                foreach ($abilities as $ability_id) {
                    $sql_ability = "INSERT INTO Pokemon_Ability (pokemon_id, ability_id) VALUES (?, ?)";
                    if ($stmt_ability = mysqli_prepare($link, $sql_ability)) {
                        mysqli_stmt_bind_param($stmt_ability, "ii", $pokemon_id, $ability_id);
                        mysqli_stmt_execute($stmt_ability);
                    }
                }

                // Redirect to index page
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
    <title>Create Pokémon</title>
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
                        <h2>Create Pokémon</h2>
                    </div>
                    <p>Please fill this form to add a new Pokémon to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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

                        <!-- Types Checkbox Grid -->
                        <div class="form-group">
                            <label>Types</label><br>
                            <div class="type-checkbox-grid">
                                <?php
                                $sql_types = "SELECT type_id, type_name FROM Type";
                                $result_types = mysqli_query($link, $sql_types);
                                while ($row_type = mysqli_fetch_array($result_types)) {
                                    echo "<label><input type='checkbox' name='type[]' value='" . $row_type['type_id'] . "'> " . $row_type['type_name'] . "</label>";
                                }
                                ?>
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
    </style></div>
                            <span class="help-block"><?php echo $type_err; ?></span>
                        </div>

                        <!-- Abilities Checkbox Grid -->
                        <div class="form-group">
                            <label class = "abilities-label">Abilities</label><br>
                            <div class="ability-checkbox-grid">
                                <?php
                                $sql_abilities = "SELECT ability_id, ability_name FROM Ability";
                                $result_abilities = mysqli_query($link, $sql_abilities);
                                while ($row_ability = mysqli_fetch_array($result_abilities)) {
                                    echo "<label><input type='checkbox' name='abilities[]' value='" . $row_ability['ability_id'] . "'> " . $row_ability['ability_name'] . "</label>";
                                }
                                ?>
                            </div>
                            <span class="help-block"><?php echo $abilities_err; ?></span>
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
