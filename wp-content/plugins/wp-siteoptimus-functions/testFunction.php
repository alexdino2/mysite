<?php
/**
 * Plugin Name: Site Optimus Functions
 * Plugin URI: http://siteoptimus.com
 * Description: This plugin tests adding functions to Wordpress
 * Version: 1.0.0
 * Author: Alex Destino
 * Author URI: http://alexdestino.com
 * License: GPL2
 */

function elh_get_api_data() {

	ob_start();

	?>
		<form action="" method="post">
			<label for="user_id">Hello there! What is your user ID?</label>
			<br />
			<input type="text" name="user_id" id="user_id" />
			<input type="submit" name="submit_form" value="submit" />
		</form>
	<?php

	if( $_POST["submit_form"] != '' && $_POST["user_id"] ) {

		$user_id = $_POST["user_id"];
		$json = file_get_contents( 'http://www.golflink.com.au/mobileService/GolferService.svc/HandicapHistory?userId=' . $user_id . '&numberOfResults=20' );
		$json_data = json_decode($json, true);

		$html = '<table>';
		$html .= '<tr><th>golflink_number</th> <th>category</th> <th>anchoredHandicap</th> </tr>';
		$html .= '<tr>';
		$html .= '<td>'.$json_data['golflink_number'].'</td>';
		$html .= '<td>'.$json_data['category'].'</td>';
		$html .= '<td>'.$json_data['anchoredHandicap'].'</td>';
		$html .= '</tr>';
		$html .= '</table>';

		$html .= '<h2>Handicap History:</h2>';

		$html .= '<table>';
		$html .= '<tr>';
		$html .= '<th>date</th> <th>club</th> <th>par</th> <th>rating</th> <th>gross</th> <th>net</th> <th>played_off</th> <th>played_to</th> <th>modified_date</th> <th>rounded_handicap</th> <th>handicap</th> <th>top_10</th> <th>score_status</th> <th>CompetitionId</th> <th>IsAnchored</th>';
		$html .= '</tr>';

		foreach ($json_data['handicap-history'] as $data) {

			$html .= '<tr>';
			$html .= '<td>'. date("Y-m-d", $data['date']) . '</td>';
			$html .= '<td>'. $data['club'] . '</td>';
			$html .= '<td>'. $data['par'] . '</td>';
			$html .= '<td>'. $data['rating'] . '</td>';
			$html .= '<td>'. $data['gross'] . '</td>';
			$html .= '<td>'. $data['net'] . '</td>';
			$html .= '<td>'. $data['played_off'] . '</td>';
			$html .= '<td>'. $data['played_to'] . '</td>';
			$html .= '<td>'. date("Y-m-d", $data['modified_date']) . '</td>';
			$html .= '<td>'. $data['rounded_handicap'] . '</td>';
			$html .= '<td>'. $data['handicap'] . '</td>';
			$html .= '<td>'. $data['top_10'] . '</td>';
			$html .= '<td>'. $data['score_status'] . '</td>';
			$html .= '<td>'. $data['CompetitionId'] . '</td>';
			$html .= '<td>'. $data['IsAnchored'] . '</td>';
			$html .= '</tr>';

		}

		$html .= '</table>';
	}

		echo $html;

	return ob_get_clean();
}

add_shortcode('handicap-data', 'elh_get_api_data');


function so_get_dimden_data() {

	ob_start();


		$json = file_get_contents( 'http://siteoptimus.com/get_result_details.php' );
		$json_data = json_decode($json, true);


		$html = '<h2>Opportunities:</h2>';

		$html .= '<table>';
		$html .= '<tr>';
		$html .= '<th>which dim</th> <th>dim</th> <th>den</th> <th>c_rate</th> <th>Opportunities</th>';
		$html .= '</tr>';

		foreach ($json_data['products'] as $data) {

                    $html .= '<tr>';
                    $html .= '<td>'.$data['whichdim'].'</td>';
                    $html .= '<td>'.$data['dim'].'</td>';
                    $html .= '<td>'.$data['den'].'</td>';
                    $html .= '<td>'.$data['c_rate'].'</td>';
                    $html .= '<td>'.$data['opp'].'</td>';
                    $html .= '</tr>';

		}

		$html .= '</table>';
	
		echo $html;

	return ob_get_clean();
}

add_shortcode('den_num_results_data', 'so_get_dimden_data');

//add query variables so that php codes can pick them up
add_filter('query_vars', 'parameter_queryvars' );

function parameter_queryvars( $qvars )
{
$qvars[] = 'dim';
return $qvars;
}