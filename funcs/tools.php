<?php
    /**
     * Recovery on a site the distance in meter between two cities
     *
     * @param string $departureCity
     * @param string $arrivalCity
     * @return float
     */
    function recovery_distance(string $departureCity, string $arrivalCity) : float
    {
        // Processing to extracte data from the code of the page
        $data = explode('<div class="value">', file_get_contents('https://www.bonnesroutes.com/distance/?from=' . format_city($departureCity) . '&to=' . format_city($arrivalCity)));
        // Selection of distance data
        $data = explode('</div>', $data[3]);

        // If the distance exceeds the hundreds
        if(strlen($data[0]) > 6)
        {
            $dataTable = str_split($data[0]);
            $data = $dataTable[0] . $dataTable[1] . $dataTable[4] . $dataTable[5] . $dataTable[6];
            return floatval($data) * 1000;
        }
        // If the distance exceeds the hundreds
        else if(strlen($data[0]) > 3 AND strlen($data[0]) <= 6)
        {
            $dataTable = str_split($data[0]);
            $data = $dataTable[0] . $dataTable[3] . $dataTable[4] . $dataTable[5];
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
     * @param float $speed
     * @return float
     */
    function convert_kmh_to_ms(float $speed) : float
    {
        return $speed * 1000 / 3600;
    }
    
    /**
     * Verify if the text entered by the user is a city
     *
     * @param string $city
     * @return boolean
     */
    function verify_city(string $city) : bool
    {
        if(strlen($city) >= 4 AND ctype_alpha($city) OR !ctype_digit($city) AND !ctype_punct($city))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Format the city display in URL
     *
     * @param string $city
     * @return string
     */
    function format_city(string $city) : string
    {
        return str_replace(' ', '%20', $city);
    }

    /**
     * Format the time display
     *
     * @param float $time
     * @return void
     */
    function time_format(float $time)
    {
        if (intval($time / 3600) >= 24)
        {
            return "<strong style='margin-right: 12px;'>". strftime("%eJ", $time) . "</strong>" . strftime("%H : %M", $time);
        }
        else
        {
            return strftime("%H : %M", $time);
        }
    }

    /**
     * Calculate the number of breaks that the driver makes during the carry
     *
     * @param string $departureCity
     * @param string $arrivalCity
     * @return integer
     */
    function calculate_number_of_break(string $departureCity, string $arrivalCity) : int
    {
        // Recovery the $timeToAccelerateOrDecelerate and $traveledDistance variables defined in the calculate_time function
        global $timeToAccelerateOrDecelerate, $traveledDistance;
        // Calculate the remaining distance to travel
        $remainingDistance = recovery_distance($departureCity, $arrivalCity) - $traveledDistance;
        // Full time to travel this distance without breaks
        $timeWithoutBreak = 2 * $timeToAccelerateOrDecelerate + $remainingDistance / convert_kmh_to_ms(90);
        // Calculates the number of breaks to be made during the carry
        return intval($timeWithoutBreak / (2 * 3600));
    }

    /**
     * Calculate the time to do a given distance
     *
     * @param string $departureCity
     * @param string $arrivalCity
     * @return float
     */
    function calculate_time(string $departureCity, string $arrivalCity) : float
    {
        // Calculate the time needed to accelerate or deccelerate
        $timeToAccelerateOrDecelerate = convert_kmh_to_ms(90) / convert_kmh_to_ms(10) * 60;
        // Calculate the distance traveled during acceleration and deceleration
        $traveledDistance = 1/2 * (convert_kmh_to_ms(90) / $timeToAccelerateOrDecelerate) * pow($timeToAccelerateOrDecelerate, 2) * 2;
        // Calculate the average speed
        $averageSpeed = $traveledDistance / ($timeToAccelerateOrDecelerate * 2);
        // Calculate the travel time without a break if the distance is less than the distance traveled
        if(recovery_distance($departureCity, $arrivalCity) < $traveledDistance)
        {
            return recovery_distance($departureCity, $arrivalCity) / $averageSpeed;
        }
        // Otherwise calculate the travel time with breaks
        else
        {
            // Recovery of the number of breaks made during the carry
            $numberOfBreak = calculate_number_of_break($departureCity, $arrivalCity);
            // Full time to travel this distance with breaks
            return (2 * $timeToAccelerateOrDecelerate + (recovery_distance($departureCity, $arrivalCity) - $traveledDistance) / convert_kmh_to_ms(90)) + $numberOfBreak * 15 * 60;
        }
    }
?>