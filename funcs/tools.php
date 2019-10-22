<?php
    /**
     * Retrieve on a site the distance in meter between two cities
     *
     * @param string $departureCity
     * @param string $arrivalCity
     * @return float
     */
    function retrieve_distance(string $departureCity, string $arrivalCity):float
    {
        // Processing to extracte data from the code of the page
        $data = explode('<div class="value">', file_get_contents('https://www.bonnesroutes.com/distance/?from=' . $departureCity . '&to=' . $arrivalCity));
        // Selection of distance data
        $data = explode('</div>', $data[3]);

        // If the distance exceeds the hundreds
        if(strlen($data[0]) > 3 AND strlen($data[0]) <= 6)
        {
            $dataTable = str_split($data[0]);
            $data = $dataTable[0].$dataTable[3].$dataTable[4].$dataTable[5];
            return floatval($data) * 1000;
        }
        else
        {
            // Return of the distance converted into float
            return floatval($data[0]) * 1000;
        }
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

    /**
     * Calculate the time to do a given distance
     *
     * @param string $departureCity
     * @param string $arrivalCity
     * @return float
     */
    function calculate_time(string $departureCity, string $arrivalCity):float
    {
        // Calculate the time needed to accelerate or deccelerate
        $timeToAccelerateOrDecelerate = convert_kmh_to_ms(90) / convert_kmh_to_ms(10) * 60;
        // Calculate the distance traveled during acceleration and deceleration
        $traveledDistance = 1/2 * (convert_kmh_to_ms(90)/$timeToAccelerateOrDecelerate) * pow($timeToAccelerateOrDecelerate, 2) * 2;
        // Calculate the average speed
        $averageSpeed = $traveledDistance / ($timeToAccelerateOrDecelerate * 2);
        // Calculate the travel time without a break if the distance is less than the distance traveled
        if(retrieve_distance($departureCity, $arrivalCity) < $traveledDistance)
        {
            return $timeWithoutBreak = retrieve_distance($departureCity, $arrivalCity) / $averageSpeed;
        }
        // Otherwise calculate the travel time with breaks
        else
        {
            // Calculate the remaining distance to travel
            $remainingDistance = retrieve_distance($departureCity, $arrivalCity) - $traveledDistance;
            // Full time to travel this distance without breaks
            $timeWithoutBreak = 2 * $timeToAccelerateOrDecelerate + $remainingDistance / convert_kmh_to_ms(90);
            // Calculates the number of breaks to be made during the path
            $numberOfBreak = intval($timeWithoutBreak / (2 * 3600));
            // Full time to travel this distance with breaks
            return $timeWithBreak = ($timeWithoutBreak + $numberOfBreak * 15 * 60);
        }
    }
?>