<?php
/*
Template Name: Boxplots
*/

// bbPress fix while they update things...
if ( is_singular() || ( function_exists( 'is_bbpress' ) && is_bbpress() ) ) {
	get_template_part( 'singular' );
	return;
}

// Get header
get_header(); ?>

	<div id="content-wrap" class="container clr">

		<?php wpex_hook_primary_before(); ?>

		<div id="primary" class="content-area clr">

			<?php wpex_hook_content_before(); ?>

			<div id="content" class="site-content" role="main">
                            
                            <?php wpex_hook_content_top(); ?>
                            
                                <?php
				// YOUR POST LOOP STARTS HERE
				while ( have_posts() ) : the_post(); ?>

					<?php if ( has_post_thumbnail() && wpex_get_mod( 'page_featured_image' ) ) : ?>

						<div id="page-featured-img" class="clr">
							<?php the_post_thumbnail(); ?>
						</div><!-- #page-featured-img -->

					<?php endif; ?>

					<div class="entry-content entry clr">
						<?php the_content(); ?>
                                                    <?php
                                                        //create chart
                                                        global $wpdb;

                                                        //$Algo = mysql_pconnect($hostname_Algo, $username_Algo, $password_Algo) or trigger_error(mysql_error(),E_USER_ERROR); 
                                                        //$Algo = mysqli_connect($hostname_Algo, $username_Algo, $password_Algo, $database_Algo) or trigger_error(mysql_error(),E_USER_ERROR); 

                                                        $sql="SELECT  dim2,
                                                                max(c_rate) as max,
                                                                SUBSTRING_INDEX(
                                                                    SUBSTRING_INDEX(
                                                                        GROUP_CONCAT(                 -- 1) make a sorted list of values
                                                                            f.c_rate
                                                                            ORDER BY f.c_rate
                                                                            SEPARATOR ','
                                                                        )
                                                                    ,   ','                           -- 2) cut at the comma
                                                                    ,   75/100 * COUNT(*) + 1         --    at the position beyond the 90% portion
                                                                    )
                                                                ,   ','                               -- 3) cut at the comma
                                                                ,   -1                                --    right after the desired list entry
                                                                )                 AS `q3`,
                                                                SUBSTRING_INDEX(
                                                                    SUBSTRING_INDEX(
                                                                        GROUP_CONCAT(                 -- 1) make a sorted list of values
                                                                            f.c_rate
                                                                            ORDER BY f.c_rate
                                                                            SEPARATOR ','
                                                                        )
                                                                    ,   ','                           -- 2) cut at the comma
                                                                    ,   50/100 * COUNT(*) + 1         --    at the position beyond the 90% portion
                                                                    )
                                                                ,   ','                               -- 3) cut at the comma
                                                                ,   -1                                --    right after the desired list entry
                                                                )                 AS `q2`,
                                                                SUBSTRING_INDEX(
                                                                    SUBSTRING_INDEX(
                                                                        GROUP_CONCAT(                 -- 1) make a sorted list of values
                                                                            f.c_rate
                                                                            ORDER BY f.c_rate
                                                                            SEPARATOR ','
                                                                        )
                                                                    ,   ','                           -- 2) cut at the comma
                                                                    ,   25/100 * COUNT(*) + 1         --    at the position beyond the 90% portion
                                                                    )
                                                                ,   ','                               -- 3) cut at the comma
                                                                ,   -1                                --    right after the desired list entry
                                                                )                 AS `q1`,
                                                                min(c_rate) as min,
                                                                min(whichdim2) as whichdim2,
                                                                sum(den) as visits,
                                                                sum(num) as conversions
                                                        FROM    (select whichdim2, dim2, den, num, num/den as c_rate from tbl_dim1_dim2_den_num where den>100) AS f
                                                        GROUP BY dim2";

                                                        $so_result=$wpdb->get_results($sql) or die(mysql_error());

                                                        //print_r($so_result);

                                                        if ($wpdb->num_rows > 0) {
                                                            // looping through all results
                                                            $so_response = array();
                                                            $so_row = array();

                                                            foreach($so_result as $so_row) {
                                                                // temp user array
                                                                $fivenum = array();
                                                                $fivenum["x"] = $so_row->dim2;
                                                                $fivenum["low"] = $so_row->min;
                                                                $fivenum["q1"] = $so_row->q1;
                                                                $fivenum["median"] = $so_row->q2;
                                                                $fivenum["q3"] = $so_row->q3;
                                                                $fivenum["high"] = $so_row->max;
                                                                $fivenum["whichdim2"] = $so_row->whichdim2;
                                                                $fivenum["dim"] = $so_row->dim2;        
                                                                $fivenum["visits"] = $so_row->visits;        
                                                                $fivenum["conversions"] = $so_row->conversions;

                                                                // push single product into final response array
                                                                $data_array=array_push($so_response, $fivenum);
                                                            }
                                                            // success
                                                            //$so_response["success"] = 1;
                                                            $my_array=json_encode($so_response, JSON_NUMERIC_CHECK);
                                                            $my_array2 = json_encode(array_column($so_response, 0));
                                                            $first_elements = array();
                                                            $first_elements = array_column($so_response, 'x');
                                                            $first_encode = json_encode($first_elements);
                                                            // echoing JSON response
                                                            //echo json_encode($so_response, JSON_NUMERIC_CHECK);
                                                        } else {
                                                            // no products found
                                                            $so_response["success"] = 0;
                                                            $so_response["message"] = "No records found";

                                                            // echo no users JSON
                                                            //echo json_encode($so_response);
                                                        }

                                                        ?>
                                                        <?php
                                                        if (is_page('test-boxplot'))
                                                        {
                                                                echo"<script type='text/javascript'>

                                                                    $(function () {

                                                                        //container.innerHTML = JSON.stringify(data_array);

                                                                        $('#container').highcharts({

                                                                            chart: {
                                                                                type: 'boxplot'
                                                                            },

                                                                            title: {
                                                                                text: 'Conversion Rate Box Plot'
                                                                            },

                                                                            legend: {
                                                                                enabled: false
                                                                            },

                                                                            xAxis: {
                                                                                categories: ".$first_encode.",
                                                                                title: {
                                                                                    text: 'Browser'
                                                                                }
                                                                            },

                                                                            yAxis: {
                                                                                title: {
                                                                                    text: 'Conversion Rate by Browser'
                                                                                }
                                                                            },

                                                                            plotOptions: {
                                                                                boxplot: {
                                                                                    fillColor: '#F0F0E0',
                                                                                    lineWidth: 2,
                                                                                    medianColor: '#0C5DA5',
                                                                                    medianWidth: 3,
                                                                                    stemColor: '#A63400',
                                                                                    stemDashStyle: 'dot',
                                                                                    stemWidth: 1,
                                                                                    whiskerColor: '#3D9200',
                                                                                    whiskerLength: '20%',
                                                                                    whiskerWidth: 3
                                                                                }
                                                                            },

                                                                            series: [{
                                                                                //name: 'Browser',
                                                                                data: ".$my_array."
                                                                            }],

                                                                            tooltip: {
                                                                                formatter: function() {
                                                                                    return this.point.whichdim2 + ': <b>'+ this.point.dim +'</b><br/>'  
                                                                                            + 'Visits: <b>'+ this.point.visits +'</b><br/>'
                                                                                            + 'Conversions: <b>'+ this.point.conversions + '</b><br/>'
                                                                                            + 'Min: <b>'+ this.point.low + '</b><br/>'
                                                                                            + '1st Qtr: <b>'+ this.point.q1 + '</b><br/>'
                                                                                            + 'Median: <b>'+ this.point.median+ '</b><br/>'
                                                                                            + '3rd Qtr: <b>'+ this.point.q3 + '</b><br/>'
                                                                                            + 'Max: <b>'+ this.point.high + '</b><br/>'
                                                                                    ;
                                                                                }
                                                                           }

                                                                        });

                                                                    });
                                                                </script>";
                                                        }
                                                    ?>
					</div><!-- .entry-content -->

				<?php
				// YOUR POST LOOP ENDS HERE
				endwhile; ?>
                            
                            
                            
                            <?php wpex_hook_content_bottom(); ?>

			</div><!-- #content -->

		<?php wpex_hook_content_after(); ?>

		</div><!-- #primary -->

		<?php wpex_hook_primary_after(); ?>

	</div><!-- .container -->
	
<?php get_footer(); ?>