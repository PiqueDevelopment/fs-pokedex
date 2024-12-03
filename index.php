<?php
session_start();
require_once "config.php";
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

                    <?php
                    // Fetch and display Pokemon data
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
                        GROUP BY 
                            p.pokemon_id, p.name, p.gender
                    ";

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
                        echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
                    }

                    mysqli_close($link);
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
