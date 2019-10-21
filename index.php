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
        <input type="text" name="departureCity" placeholder="Entrez votre ville de départ">
        <input type="text" name="arrivalCity" placeholder="Entrez votre ville d'arrivée">
        <br/>
        <button type="submit" name="sendinfos" value="Calculer la temps du trajet">Calculer la temps du trajet</button>
    </form>
    <?php if(!empty($_GET['departureCity']) AND !empty($_GET['arrivalCity'])){ ?>
        <?php if($_GET['departureCity'] !== $_GET['arrivalCity']){ ?>
            <table>
                <thead>
                    <tr>
                        <th>Ville de départ</th>
                        <th>Ville d'arrivée</th>
                        <th>Distance parcourue</th>
                        <th>Temps (en HH/mm)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $_GET['departureCity']; ?></td>
                        <td><?php echo $_GET['arrivalCity']; ?></td>
                        <td><?php echo retrieve_distance($_GET['departureCity'], $_GET['arrivalCity']) / 1000 . " km"; ?></td>
                        <td><?php echo strftime("%H/%M", calculate_time($_GET['departureCity'], $_GET['arrivalCity'])); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } else { ?>
            <p>Eurreur: Vos villes de départ et d'arrivée sont identiques</p>
        <?php } ?>
    <?php } ?>
    <footer>
        &copy; <?php echo strftime("%Y"); ?><span style="margin-left: 0.6rem;">Yann Le Coz</span>
    </footer>
</body>
</html>