<?php
    require_once('./funcs/tools.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" type="image/png" href="favicon.png" />

    <title>Road Calculation | Yann Le Coz</title>
</head>
<body>
    <form methode='POST'>
        <input type="text" name="departureCity" placeholder="Entrez votre ville de départ" style="margin-right: 3%;">
        <input type="text" name="arrivalCity" placeholder="Entrez votre ville d'arrivée">
        <button type="submit" name="sendinfos" value="Calculer la temps du trajet">Calculer la temps du trajet</button>
    </form>
    <?php if(!empty($_GET['departureCity']) AND !empty($_GET['arrivalCity'])){ ?>
        <?php if(format_city(verify_city($_GET['departureCity'])) AND format_city(verify_city($_GET['arrivalCity']))){ ?>
            <?php if(format_city($_GET['departureCity']) !== format_city($_GET['arrivalCity'])){ ?>
                <table>
                    <thead>
                        <tr>
                            <th>Ville de départ</th>
                            <th>Ville d'arrivée</th>
                            <th>Distance parcourue</th>
                            <th>Nombre de pauses prises</th>
                            <th>Temps de pause (en min)</th>
                            <th>Temps estimé (en HH:min)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $_GET['departureCity']; ?></td>
                            <td><?php echo $_GET['arrivalCity']; ?></td>
                            <td><?php echo recovery_distance(format_city($_GET['departureCity']), format_city($_GET['arrivalCity'])) / 1000 . " km"; ?></td>
                            <td><?php echo calculate_number_of_break(format_city($_GET['departureCity']), format_city($_GET['arrivalCity'])); ?></td>
                            <td><?php echo calculate_number_of_break(format_city($_GET['departureCity']), format_city($_GET['arrivalCity'])) * 15; ?></td>
                            <td><?php echo time_format(calculate_time(format_city($_GET['departureCity']), format_city($_GET['arrivalCity']))); ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>Vos villes de départ et d'arrivée sont identiques</p>
            <?php } ?>
        <?php } else { ?>
            <p>Veuillez entrer des noms de villes</p>
        <?php } ?>
    <?php } ?>
    <footer>
        &copy; <?php echo strftime("%Y"); ?><span style="margin-left: 0.6rem;">Yann Le Coz</span>
    </footer>
</body>
</html>