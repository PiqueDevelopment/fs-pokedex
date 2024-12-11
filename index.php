<?php
session_start();
require_once "config.php";

// Initialize search query for each field
$name_query = isset($_GET['name-query']) ? $_GET['name-query'] : '';
$gender_query = isset($_GET['gender-query']) ? $_GET['gender-query'] : '';
$type_query = isset($_GET['type-query']) ? $_GET['type-query'] : '';
$ability_query = isset($_GET['ability-query']) ? $_GET['ability-query'] : '';

// Split the abilities into separate terms (if applicable)
$ability_terms = array_map('trim', explode(',', $ability_query));

// Build the WHERE clause based on search terms
$whereClauses = [];

if (!empty($name_query)) {
    $whereClauses[] = "p.name LIKE '%" . mysqli_real_escape_string($link, $name_query) . "%'";
}

if (!empty($gender_query)) {
    $whereClauses[] = "p.gender = '" . mysqli_real_escape_string($link, $gender_query) . "'";
}

if (!empty($type_query)) {
    $type_terms = array_map('trim', explode(',', $type_query));

    // If only one type is entered
    if (count($type_terms) == 1) {
        // For a single type search, use LIKE to match the type
        foreach ($type_terms as $type) {
            if (!empty($type)) {
                $whereClauses[] = "t.type_name LIKE '%" . mysqli_real_escape_string($link, $type) . "%'";
            }
        }
    } else {
        // For multiple types, ensure Pokémon has all of them
        $typeWhereClauses = [];
        foreach ($type_terms as $type) {
            if (!empty($type)) {
                $typeWhereClauses[] = "t.type_name LIKE '%" . mysqli_real_escape_string($link, $type) . "%'";
            }
        }

        // Use the EXISTS condition to check if Pokémon has all types
        $whereClauses[] = "
            EXISTS (
                SELECT 1
                FROM Pokemon_Type pt
                JOIN Type t ON pt.type_id = t.type_id
                WHERE pt.pokemon_id = p.pokemon_id
                AND (" . implode(" OR ", $typeWhereClauses) . ")
                GROUP BY pt.pokemon_id
                HAVING COUNT(DISTINCT t.type_name) = " . count($type_terms) . "  -- Ensures all types are present
            )
        ";
    }
}


if (!empty($ability_query)) {
    $ability_terms = array_map('trim', explode(',', $ability_query));

    // If only one ability is entered
    if (count($ability_terms) == 1) {
        // For a single ability search, use LIKE to match the ability
        foreach ($ability_terms as $ability) {
            if (!empty($ability)) {
                $whereClauses[] = "a.ability_name LIKE '%" . mysqli_real_escape_string($link, $ability) . "%'";
            }
        }
    } else {
        // For multiple abilities, ensure Pokémon has all of them
        $abilityWhereClauses = [];
        foreach ($ability_terms as $ability) {
            if (!empty($ability)) {
                $abilityWhereClauses[] = "a.ability_name LIKE '%" . mysqli_real_escape_string($link, $ability) . "%'";
            }
        }

        // Use the EXISTS condition to check if Pokémon has all abilities
        $whereClauses[] = "
            EXISTS (
                SELECT 1
                FROM Pokemon_Ability pa
                JOIN Ability a ON pa.ability_id = a.ability_id
                WHERE pa.pokemon_id = p.pokemon_id
                AND (" . implode(" OR ", $abilityWhereClauses) . ")
                GROUP BY pa.pokemon_id
                HAVING COUNT(DISTINCT a.ability_name) = " . count($ability_terms) . "  -- Ensures all abilities are present
            )
        ";
    }
}

// Combine the WHERE clauses with OR logic
$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(" OR ", $whereClauses);
}

// Fetch Pokémon data with the WHERE clause applied (search)
$sql = "
    SELECT 
        p.pokemon_id,
        p.name,
        p.gender,
        GROUP_CONCAT(DISTINCT t.type_name ORDER BY t.type_name SEPARATOR ', ') AS types,
        GROUP_CONCAT(DISTINCT a.ability_name ORDER BY a.ability_name SEPARATOR ', ') AS abilities
    FROM 
        Pokemon p
    LEFT JOIN 
        Pokemon_Type pt ON p.pokemon_id = pt.pokemon_id
    LEFT JOIN 
        Type t ON pt.type_id = t.type_id
    LEFT JOIN 
        Pokemon_Ability pa ON p.pokemon_id = pa.pokemon_id
    LEFT JOIN 
        Ability a ON pa.ability_id = a.ability_id
    $whereSql
    GROUP BY 
        p.pokemon_id, p.name, p.gender
";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Full Stack Pokedex</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Full Stack Pokedex</h2>
                        <p>Project should include CRUD operations. In this website you can:</p>
                        <ol>
                            <li>CREATE new Pokemon</li>
                            <li>RETRIEVE all Pokemon</li>
                            <li>UPDATE Pokemon</li>
                            <li>DELETE Pokemon</li>
                        </ol>
                        <div class="header-actions">
                            <h2>Pokemon Generation 1 Glossary</h2>
                        </div>
                    </div>

                    <!-- Search Form -->
                    <form action="index.php" method="get" class="form-inline">
                        <div class="form-group">
                            <label for="name-query">Search by Name:</label>
                            <input type="text" id="name-query" name="name-query" class="form-control" placeholder="e.g., Bulbasaur" value="<?php echo htmlspecialchars($name_query); ?>">
                        </div>
                        <div class="form-group">
                            <label for="gender-query">Search by Gender:</label>
                            <input type="text" id="gender-query" name="gender-query" class="form-control" placeholder="e.g., Male/Female" value="<?php echo htmlspecialchars($gender_query); ?>">
                        </div>
                        <div class="form-group">
                            <label for="type-query">Search by Type(s):</label>
                            <input type="text" id="type-query" name="type-query" class="form-control" placeholder="e.g., Grass, Poison" value="<?php echo htmlspecialchars($type_query); ?>">
                        </div>
                        <div class="form-group">
                            <label for="ability-query">Search by Ability(s):</label>
                            <input type="text" id="ability-query" name="ability-query" class="form-control" placeholder="e.g., Chlorophyll, Overgrow" value="<?php echo htmlspecialchars($ability_query); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                        
                        <!-- Reset the search filters to display the original table -->
                        <a href="index.php" class="btn btn-default">Display Original</a>
                        
                        <div class="button-group">
                            <a href="createPokemon.php" class="btn btn-success">Add New Pokemon</a>
                            <!-- <a href="deletePokemon.php" class="btn btn-danger">Delete A Pokemon</a> -->
                        </div>
                    </form>


                    <?php
                    // Fetch and display Pokémon data with search applied
                    if ($result = mysqli_query($link, $sql)) {
                        if (mysqli_num_rows($result) > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead><tr><th>ID</th><th>Name</th><th>Gender</th><th>Type</th><th>Abilities</th><th class='action-column'>Action</th></tr></thead><tbody>";
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['pokemon_id'] . "</td>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>" . $row['gender'] . "</td>";
                                echo "<td>" . $row['types'] . "</td>";
                                echo "<td>" . $row['abilities'] . "</td>";
                                echo "<td class='action-column'>";
                                echo "<a href='updatePokemon.php?id_or_name=" . $row['pokemon_id'] . "' class='btn btn-info' title='Update Pokémon'>Update</a> ";
                                echo "<a href='deletePokemon.php?id=" . $row['pokemon_id'] . "' class='btn btn-danger' title='Delete Pokémon' onclick='return confirm(\"Are you sure you want to delete this Pokémon?\")'>Delete</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                            mysqli_free_result($result);
                        } else {
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else {
                        echo "ERROR: Could not execute $sql. " . mysqli_error($link);
                    }

                    ?>

                    <!-- Team Section -->
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Teams Glossary</h2>
                    </div>
                    <div class="clearfix align-center" style="margin-bottom: 20px;">
                        <h3 class="pull-left">Team List</h3>
                        <div class="button-group pull-right">
                            <a href="createTeam.php" class="btn btn-success">Add New Team</a>
                            <!-- <a href="deleteTeam.php" class="btn btn-danger">Delete a Team</a> -->
                        </div>
                    </div>
                    </div>

                    <?php
                    // Fetch and display Team data with search applied
                    $teamSql = "
                        SELECT 
                            t.team_id, t.team_name, GROUP_CONCAT(p.name SEPARATOR ', ') AS team_members
                        FROM 
                            Teams t
                        LEFT JOIN 
                            Pokemon_Team pt ON t.team_id = pt.team_id
                        LEFT JOIN 
                            Pokemon p ON pt.pokemon_id = p.pokemon_id
                        GROUP BY 
                            t.team_id, t.team_name
                    ";

                    if ($result = mysqli_query($link, $teamSql)) {
                        if (mysqli_num_rows($result) > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead><tr><th>ID</th><th>Team Name</th><th>Team Members</th><th class='action-column'>Action</th></tr></thead><tbody>";
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['team_id'] . "</td>";
                                echo "<td>" . $row['team_name'] . "</td>";
                                echo "<td>" . ($row['team_members'] ? $row['team_members'] : "<em>No members yet</em>") . "</td>";
                                echo "<td class='action-column'>";
                                echo "<a href='updateTeam.php?team_id=" . $row['team_id'] . "' class='btn btn-info'>Update</a> ";
                                echo "<a href='deleteTeam.php?id=" . $row['team_id'] . "' class='btn btn-danger' title='Delete Team' onclick='return confirm(\"Are you sure you want to delete this team?\")'>Delete</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                            mysqli_free_result($result);
                        } else {
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else {
                        echo "ERROR: Could not execute $teamSql. " . mysqli_error($link);
                    }

                    // Close Connection
                    mysqli_close($link);
                    ?>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
