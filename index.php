<?php
session_start();
require_once "config.php";

// Initialize the search query
$search_query = isset($_GET['search-query']) ? $_GET['search-query'] : '';

// Split the query by commas into search terms
$search_terms = array_map('trim', explode(',', $search_query));

// Build the WHERE clause based on search terms
$whereClauses = [];
foreach ($search_terms as $term) {
    if (!empty($term)) {
        // Match the term in the name, gender, type, or ability fields
        // For types and abilities, we handle multiple matches, allowing for terms like 'Grass, Poison'
        $whereClauses[] = "(p.name LIKE '%" . mysqli_real_escape_string($link, $term) . "%' OR
                            p.gender LIKE '%" . mysqli_real_escape_string($link, $term) . "%' OR
                            t.type_name LIKE '%" . mysqli_real_escape_string($link, $term) . "%' OR
                            a.ability_name LIKE '%" . mysqli_real_escape_string($link, $term) . "%')";
    }
}

// If there are multiple terms, they should be treated as OR conditions between each term
$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $whereClauses);
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
    <title>Pokedex</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style type="text/css">
        .wrapper { width: 70%; margin: 0 auto; }
        table tr td:last-child a { margin-right: 15px; }
    </style>
    <script type="text/javascript">
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2>Full Stack Pokedex</h2>
                        <p>Project should include CRUD operations. In this website you can:</p>
                        <ol>
                            <li>CREATE new Pokemon</li>
                            <li>RETRIEVE all Pokemon</li>
                            <li>UPDATE Pokemon</li>
                            <li>DELETE Pokemon</li>
                        </ol>
                        <h2 class="pull-left">Generation 1 Pokemon Glossary</h2>
                        <a href="createPokemon.php" class="btn btn-success pull-right">Add New Pokemon</a>
                        <a href="deletePokemon.php" class="btn btn-danger pull-right">Delete A Pokemon</a>
                    </div>

                    <!-- Search Form -->
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Search by Attributes (Free-form)</h2>
                    </div>
                    <form action="index.php" method="get" class="form-inline">
                        <div class="form-group">
                            <label for="search-query">Search (name, gender, type, ability):</label>
                            <input type="text" id="search-query" name="search-query" class="form-control" placeholder="Search (e.g., Bulbasaur, Grass, Male)" value="<?php echo htmlspecialchars($search_query); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>

                    <?php
                    // Fetch and display Pokémon data with search applied
                    if ($result = mysqli_query($link, $sql)) {
                        if (mysqli_num_rows($result) > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead><tr><th>ID</th><th>Name</th><th>Gender</th><th>Type</th><th>Abilities</th><th>Action</th></tr></thead><tbody>";
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['pokemon_id'] . "</td>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>" . $row['gender'] . "</td>";
                                echo "<td>" . $row['types'] . "</td>";
                                echo "<td>" . $row['abilities'] . "</td>";
                                echo "<td><a href='updatePokemon.php?id_or_name=" . $row['pokemon_id'] . "' class='btn btn-info' title='Update Pokémon'>Update</a></td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                            mysqli_free_result($result);
                        } else {
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else {
                        echo "ERROR: Could not execute $sql. <br>" . mysqli_error($link);
                    }

                    ?>

                    <!-- Team Section -->
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Teams Glossary</h2>
                        <a href="createTeam.php" class="btn btn-success pull-right">Add New Team</a>
                        <a href="deleteTeam.php" class="btn btn-danger pull-right">Delete a Team</a>
                    </div>

                    <?php
                    // Fetch and display Team data
                    $teamSql = "
                        SELECT 
                            t.team_id, 
                            t.team_name, 
                            GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ', ') AS team_members
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
                            echo "<h3>Team List</h3>";
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead><tr><th>ID</th><th>Team Name</th><th>Team Members</th><th>Action</th></tr></thead><tbody>";
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['team_id'] . "</td>";
                                echo "<td>" . $row['team_name'] . "</td>";
                                echo "<td>" . ($row['team_members'] ? $row['team_members'] : "<em>No members yet</em>") . "</td>";
                                echo "<td>";
                                echo "<a href='updateTeam.php?team_id=" . $row['team_id'] . "' class='btn btn-info'>Update</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                            mysqli_free_result($result);
                        } else {
                            echo "<p class='lead'><em>No teams were found.</em></p>";
                        }
                    } else {
                        echo "ERROR: Could not execute $teamSql. " . mysqli_error($link);
                    }

                    mysqli_close($link);
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
