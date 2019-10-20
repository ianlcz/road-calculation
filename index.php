<?php
    $villeDepart = 'Bordeaux';
    $villeArrivee = 'Douarnenez';

    /**
     * Retrieve on a site the distance in meter between two cities
     *
     * @param string $departureCity
     * @param string $arrivalCity
     * @return float
     */
    function retrieve_distance(string $departureCity, string $arrivalCity):float
    {
        // Traitement des données récupérées du code de la page
        $datas = explode('<div class="value">', file_get_contents('https://www.bonnesroutes.com/distance/?from=' . $departureCity . '&to=' . $arrivalCity));
        // On choisit les données de distance
        $datas = $datas[3];
        // On récupère la valeur de la distance
        $datas = explode('</div>', $datas);
        // On retourne la valeur de la distance convertie en float
        return $distance = floatval($datas[0]) * 1000;
    }

    /**
     * Convert speed in kilometer per hour to meter per second
     *
     * @param float $number
     * @return float
     */
    function convert_kmh_to_ms(float $number):float
    {
        return $number * 1000 / 3600;
    }

    function calculate_time(string $departureCity, string $arrivalCity):float
    {
        // On calcule le temps nécessaire pour accéler ou décélérer
        $tempsPourAccelererOuDecelerer = convert_kmh_to_ms(90) / convert_kmh_to_ms(10) * 60;
        // On calcule la distance parcourue pendant l'accélération et la décélération
        $distanceParcourue = 1/2 * (convert_kmh_to_ms(90)/$tempsPourAccelererOuDecelerer) * pow($tempsPourAccelererOuDecelerer, 2) * 2;
        $vitesseMoyenne = $distanceParcourue / ($tempsPourAccelererOuDecelerer*2);


        // Si la distance est inférieur à $distanceParcourue
        if(retrieve_distance($departureCity, $arrivalCity) < $distanceParcourue)
        {
            return $tempsSansPause = retrieve_distance($departureCity, $arrivalCity) / $vitesseMoyenne;
        }
        // Sinon on calcule le temps de trajet avec des pauses
        else
        {
            // On calcule la distance restante à parcourir
            $distanceRestante = retrieve_distance($departureCity, $arrivalCity) - $distanceParcourue;
            // On calcule en combien de temps il doit accomplir cette distance
            $tempsPourFaireDistanceRestante = $distanceRestante / convert_kmh_to_ms(90);
            // Temps complet pour parcourir cette distance sans pause
            $tempsSansPause = 2 * $tempsPourAccelererOuDecelerer + $tempsPourFaireDistanceRestante;

            // On calcule le nombre de pause à faire durant le trajet
            $nombreDePause = intval($tempsSansPause / (2 * 3600));
            // Temps complet pour parcourir cette distance avec pause
            return $tempsAvecPause = ($tempsSansPause + $nombreDePause * 15 * 60);
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="favicon.png" />
    <title>Road Calculation | Yann Le Coz</title>
</head>
<body>
    <p>Ville de départ: <?php echo $villeDepart; ?></p>
    <p>Ville d'arrivée: <?php echo $villeArrivee; ?></p>
    <p>Distance entre les deux villes: <?php echo retrieve_distance($villeDepart, $villeArrivee); ?></p>
    <p>Temps complet: <?php echo strftime("%H/%M", calculate_time($villeDepart, $villeArrivee)); ?></p>
</body>
</html>