
<?php
/*
 * Template: Custom Google Analytics Goal Cycle MetaData Template
*/

set_time_limit ( 300 );

if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
$gAuth = new Google_Auth();
$gAnalytics = new Google_Analytics();
If(isset($_SESSION['CGA_sessionData'])){
	$accessData = json_decode($_SESSION['CGA_sessionData']['token']);
	if(!$gAuth->isAccessTokenExpired()){
		$gaAccounts = $gAnalytics->getAccounts();
	}else{
		wp_redirect(get_bloginfo('url').'/ga-login');
	}
}else{
	wp_redirect(get_bloginfo('url').'/ga-login');
}


get_header();

$mem = new Memcached();
$mem->addServer("memcached1.udbhj2.cfg.use1.cache.amazonaws.com", 11211);

if ( is_user_logged_in() ) {
        // Current user is logged in,
        // so let's get current user info
        global $current_user;
        $current_user = wp_get_current_user();
        // User ID
        $user_id = $current_user->ID;
        $mem->set('userid',$user_id);
}

?>


	<style>
    body {

    }

    .chart {
      border: solid 1px black;
    }

    .link {
      fill: none;
      stroke: #3498db;
      stroke-linecap: round;
    }

    circle {
      fill: #3498db;
      stroke-width: 1.5;
    }

    .high circle {
      fill: #e74c3c;
    }

    .med circle {
      fill: #f39c12;
    }

    .low circle {
      fill: #2ecc71;
    }

    .info {
      position: absolute;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: #fff;
    }

    .info .value {
      font-weight: bold;
      font-size: 1.5em;
      padding: 4px;
    }
  </style>
	<script src="../assets/js/d3.v4.min.js"></script>

	<div id="content-wrap" class="container clr">

		<?php wpex_hook_primary_before(); ?>

		<div id="primary" class="content-area clr">

			<?php wpex_hook_content_before(); ?>

			<div id="content" class="site-content clr">

				<?php wpex_hook_content_top(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php wpex_get_template_part( 'page_single_blocks' ); ?>

				<?php endwhile; ?>

				<?php wpex_hook_content_bottom(); ?>

			</div><!-- #content -->

                       <div class="chart-container">
		<svg class="chart"></svg>
	</div>
	<script>
    function show(dataURL) {
      // Generic setup
      var margin = {top: 20, bottom: 20, right: 20, left: 20},
          width = 900 - margin.left - margin.right,
          height = 600 - margin.top - margin.bottom;

      var duration = 750, // Transition duration
          depthWidth = 250, // Width of one depth for the tree layout
          lineHeight = 30; // Height of one node for the tree layout

      var circleRadius = 8,
          circleHoverRadius = 80;

      var i = 0, // For unique id in treeData
          root,
          levelWidth; // Track number of nodes of each level

      // Zoom
      var zoom = d3.zoom()
          .scaleExtent([1, 1])
          .on("zoom", zoomed);

      d3.select(".chart").call(zoom);

      // Tree layout
      var tree = d3.tree()
          .size([height, width]);

      // SVG container
      var chart = d3.select(".chart")
          .attr("width", width + margin.left + margin.right)
          .attr("height", height + margin.top + margin.bottom)
        .append("g")
          .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

      // Info div
      var info = d3.select("body").append("div")
          .attr("class", "info")
          .style("opacity", 0)
          .style("pointer-event", "none");

      d3.json(dataURL, function(error, data) {

        // Add root node
        var rootDim = "siteoptimus";
        data.push({
          whichdim: "",
          dim: rootDim
        });

        // Add depth 1 nodes
        var whichdims = d3.set(data.map(function(d) { return d.whichdim; })).values();
        whichdims.forEach(function(whichdim) {
          if (whichdim) {
            data.push({
              whichdim: rootDim,
              dim: whichdim
            });
          }
        });

        // Convert data to hierarchical structure
        root = d3.stratify()
            .id(function(d) { return d.dim; })
            .parentId(function(d) { return d.whichdim; })
            (data);

        console.log(root);

        // Store the old positions for transition
        root.x0 = height / 2;
        root.y0 = 0;

        // Collapse after the second level
        root.children.forEach(collapse);

        update(root);
        centerNode(root);
      });

      function update(source) {
        // Compute the new height
        // Count the total number of children of the root node and set the tree height accordingly
        levelWidth = [1];
        var childCount = function(level, n) {
          if (n.children && n.children.length > 0) {
            if (levelWidth.length <= level + 1) levelWidth.push(0);
            levelWidth[level + 1] += n.children.length;
            n.children.forEach(function(d) {
              childCount(level + 1, d);
            });
          }
        };
        childCount(0, root);
        var newHeight = d3.max(levelWidth) * lineHeight;

        // Compute the new tree layout
        tree.size([newHeight, width])(root);
        var nodes = root.descendants(),
            links = root.descendants().slice(1);

        // Normalize for fixed width
        nodes.forEach(function(d) { d.y = d.depth * depthWidth; });

        // NODE SECTION
        var node = chart.selectAll("g.node")
            .data(nodes, function(d) { return d.data.dim; });

        // Enter any node at the parent's previous position
        var nodeEnter = node.enter().append("g")
            .attr("class", function(d) { return "node" + (d.data.opplevel ? " " + d.data.opplevel.toLowerCase() : ""); })
            .attr("transform", "translate(" + source.y0 + "," + source.x0 + ")");

        nodeEnter.filter(function(d) { return d.depth < 2; })
            .on("click", clicked);

        nodeEnter.filter(function(d) { return d.depth === 2; })
            .on("mouseover touchstart", showInfo)
            .on("mouseout touchend", hideInfo);

        // Add the label for the node
        nodeEnter.append("text")
            .attr("dy", "0.35em")
            .attr("x", 10)
            .style("cursor", "pointer")
            .text(function(d) { return d.depth === 2 ? d.data.dim.split("==")[1] : d.data.dim; });

        // Add the circle for the node
        nodeEnter.append("circle")
            .attr("r", 1e-6)
            .attr("cx", 0)
            .attr("cy", 0);

        var nodeMerge = nodeEnter.merge(node);

        // Transition to the proper position for the node
        nodeMerge.transition()
            .duration(duration)
            .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

        // Update the node attributes and styles
        nodeMerge.select("circle")
            .attr("r", circleRadius)
            .style("cursor", "pointer");

        // Remvoe any exit nodes
        var nodeExit = node.exit()
          .transition()
            .duration(duration)
            .attr("transform", function(d) {
              var ancestorToCollapse = d.ancestors().find(function(node) { return node.depth === source.depth; });
              return  "translate(" + ancestorToCollapse.y + "," + ancestorToCollapse.x + ")";
            })
            .remove();

        nodeExit.select("circle").attr("r", 1e-6);
        nodeExit.select("text").style("fill-opacity", 1e-6);

        // LINK SECTION
        var link = chart.selectAll("path.link")
            .data(links, function(d) { return d.data.dim; });

        // Enter any new links at the parent's previous position
        var linkEnter = link.enter().insert("path", "g")
            .attr("class", "link")
            .attr("d", function(d) {
              var o = { x: source.x0, y: source.y0 };
              return diagonal(o, o);
            });

        var linkMerge = linkEnter.merge(link);

        // Transition back to the parent element postition
        linkMerge.transition()
            .duration(duration)
            .attr("d", function(d) { return diagonal(d, d.parent); });

        // Remvoe any exisiting links
        var linkExit = link.exit()
          .transition()
            .duration(duration)
            .attr("d", function(d) {
              var ancestorToCollapse = d.ancestors().find(function(node) { return node.depth === source.depth; });
              var o = { x: ancestorToCollapse.x, y: ancestorToCollapse.y };
              return diagonal(o, o);
            })
            .remove();

        // Store the old positions for transition
        nodes.forEach(function(d) {
          d.x0 = d.x;
          d.y0 = d.y;
        });
      }

      // Toggle children on click
      function clicked(d) {

        if (!d.children && !d._children) {
          return;
        } else if (d.children) { // Collapse children
          collapse(d);
        } else if (d.depth < levelWidth.length - 1) { // Collapse children of other nodes of greater than depth
          root.each(function(node) {
            if (node.depth >= d.depth) {
              collapse(node);
            }
          });
          d.children = d._children;
          d._children = null;
        } else { // Expand
          d.children = d._children;
          d._children = null;
        }
        update(d);
        centerNode(d);
      }

      function showInfo(d) {
        var node = d3.select(this);
        node.raise();
        node.select("circle")
          .transition()
            .attr("r", circleHoverRadius)
            .attr("cx", -(circleHoverRadius - circleRadius))
            .on("end", function() {
              var bcr = node.select("circle").node().getBoundingClientRect();
              console.log(bcr);
              info
                  .style("left", bcr.left + window.scrollX + "px")
                  .style("top", bcr.top + window.scrollY + "px")
                  .style("width", bcr.width + "px")
                  .style("height", bcr.height + "px")
                .transition()
                  .style("opacity", 1);
              var html = "<div>Visits</div>" +
                         "<div class='value'>" + d.data.den + "</div>" +
                         "<div>Successes</div>" +
                         "<div class='value'>" + d.data.num + "</div>" +
                         "<div>Estimated</div>" +
                         "<div>Opportunities</div>" +
                         "<div class='value'>" + d.data.opps + "</div>";
              info.html(html);
            });
        node.select("text")
          .transition()
            .style("font-weight", "bold");

        console.log(d);
      }

      function hideInfo(d) {
        var node = d3.select(this);
        node.select("circle")
          .transition()
            .attr("r", circleRadius)
            .attr("cx", 0);
        node.select("text")
          .transition()
            .style("font-weight", null);
        info.transition()
            .style("opacity", 0);
      }

      function centerNode(source) {
        var xOffset = source.children ? 50 : 50 + depthWidth;
        var transform = d3.zoomTransform(d3.select(".chart").node()),
              k = transform.k,
              x = -source.y0 * k + xOffset,
              y = -source.x0 * k + height / 2,
              t = d3.zoomIdentity.translate(x, y).scale(k);
        d3.select(".chart")
          .transition()
            .duration(duration)
            .call(zoom.transform, t);
      }

      function collapse(d) {
        if (d.children) {
          d._children = d.children;
          d._children.forEach(collapse);
          d.children = null;
        }
      }

      function zoomed() {
        chart.attr("transform", d3.event.transform);
      }

      // Create a curved path between parent and child
      function diagonal(child, parent) {
        return "M" + child.y + "," + child.x +
               "C" + (child.y + parent.y) / 2 + " " + child.x + "," +
                     (child.y + parent.y) / 2 + " " + parent.x + "," +
                     parent.y + " " + parent.x;
      }
    }
  </script>
	<script>
		(function() {
			show("http://siteoptimus.com/memc_test.php");
		})();
	</script> 
                            
<?php
//echo '<a href="http://siteoptimus.com/site-optimus-dashboard/#">Analyze different domain</a></br></br>';

if(isset($_GET['account'])){
    $days=$_GET['days'];
}
else
{
$days=30;

}

if(!empty($gaAccounts)){
	if(isset($_GET['account']) && $_GET['account'] != '' && isset($_GET['property']) && $_GET['property'] != '' ){
		//$gAnalytics->getPropertyViews($_GET['account'], $_GET['property'], $_GET['days']);
		
		if(isset($_GET['goalID'])){
			
			$gaPropereties = $gAnalytics->getListGoalsMeta($_GET['account'],$_GET['profileId'], $days, $_GET['goalID']);
                        if(isset($_GET['filter'])){
                            $gaOpportunities = $gAnalytics->getOppsCyclev31($_GET['account'],$_GET['profileId'], date('Y-m-d', strtotime($_GET['startDate'])), date('Y-m-d', strtotime($_GET['endDate'])), $_GET['goalID'], $_GET['filter']);
                        }
                        else{
                            $gaOpportunities = $gAnalytics->getOppsCyclev31($_GET['account'],$_GET['profileId'], date('Y-m-d', strtotime($_GET['startDate'])), date('Y-m-d', strtotime($_GET['endDate'])), $_GET['goalID'], NULL);
                        }
                        
                        $goalID = $_GET['goalID'];

                        if(isset($_GET['goalcompletes'])){
                            $mem->set($user_id.'goalcompletes',$_GET['goalcompletes']);
                        }
                        if(isset($_GET['sessions'])){
                            $mem->set($user_id.'sessions',$_GET['sessions']);
                        }
                        
                        $gaOpportunitiesf = $gAnalytics->array_flatten($gaOpportunities,$goalID);

			
                        //$mem->set($user_id.'dimdennum',json_encode($gaOpportunitiesf));
                        
		}else {
			$gaPropereties = $gAnalytics->getListGoalsMeta($_GET['account'],$_GET['profileId'], $days);
                        $gaOpportunities = $gAnalytics->getOppsCyclev3($_GET['account'],$_GET['profileId'], $days, NULL);
                        $gaOpportunitiesf = $gAnalytics->array_flatten($gaOpportunities);

                        //$mem->set($user_id.'dimdennum',json_encode($gaOpportunitiesf));
                        
		}

		if(!isset($_GET['goalID']) && !isset($_GET['allGoalComplete'])){
			if($gaPropereties['totalResults']) {
				
				echo '<div class="goalMetaData">';
				echo "<h2>Site Name: ".$gaPropereties['siteName']."</h2>";
				echo "<h2>Total Active User: ".$gaPropereties['totalResults']."</h2>";
				
					
					$last_names = array_column($columHeader, 'name');
					$i = 1;
					foreach($rowValue as $item){
						
						echo "<h3>User ".$i."</h3>";
						$dataArray = array_combine($last_names, $item);
						foreach($dataArray as $key => $value){
							
							$key = str_replace('rt:','',$key);
							if($key != 'activeUsers'){
								
								$key = setStringColumnHeader($key);
								echo '<b>'.$key.'</b> : '.$value." <br />";
							}
						}
						echo "<br /> ";
						$i++;
					}
				echo "</div>";
			}else {
				echo "<center><h1>There are no results during this time period</h1></center>";
			}
		}
		
		if(isset($_GET['goalID']) || isset($_GET['allGoalComplete'])){
			//echo '<div class="goalMetaData">';
				echo "Site Name: ".$gaPropereties['siteName'];
                                //echo "<h2>ID : ".$gaPropereties['ID']."</h2>";
                                //echo "<h2>Opps : ".print_r($gaOpportunities)."</h2>";
                                //echo "<h2>Oppsf" . " : ".print_r($gaOpportunitiesf)."</h2>";
                                //echo do_shortcode("[wpdatatable id=16]");
                                //echo "<h2>Profile : ".printProfileInformation($gaOpportunities)."</h2>";
                                echo do_shortcode("[wpdatatable id=17]");
                                //echo do_shortcode("[wpdatachart id=4]");
                                echo '<audio autoplay>';
                                echo '<source src="http://siteoptimus.com/MagicMoment.mp3" type="audio/mpeg">';
                                echo '</audio>';
                                
			if(isset($gaPropereties['goalMeta'])){
                                //$mem->set('sessions',$gaPropereties['goalMeta']['totalsForAllResults']['ga:sessions']);	
                                //echo "<b>Sessions :</b> ".$gaPropereties['goalMeta']['totalsForAllResults']['ga:sessions']." <br />";
                                echo "<b>Sessions :</b> ".$mem->get($user_id.'sessions')." <br />";
                                //$mem->set('goalcompletes',$gaPropereties['goalMeta']['totalsForAllResults']['ga:goal'.$goalID.'Completions']);
				echo "<b>Goal Completes :</b> ".$mem->get($user_id.'goalcompletes')." <br />";
                                //print_r($gaPropereties);
                                //echo "<b>Goal Cycle :</b> ".$gaOpportunities['goalMeta']['totalsForAllResults']['ga:goal'.$goalID.'Completions']." <br />";
                                //$gAnalytics->printDataTable($gaOpportunities['ga:source']);

                                //$gAnalytics->printCycleTableExp($gaOpportunities);
                                //echo $gAnalytics->calcOpps(123,1124,12344,1325123512);
			}
			if(isset($_GET['allGoalComplete'])){
				echo "<b>Sessions :</b> ".$mem->get($user_id.'sessions')." <br />";
                                //echo "<b>Sessions :</b> ".$_GET['session']." <br />";
                                echo "<b>All Goal Completions :</b> ".$mem->get($user_id.'goalcompletes')." <br />";
				//echo "<b>All Goal Completions :</b> ".$_GET['allGoalComplete']." <br />";
			}
			//echo "</div>";
		}
	}
}
?>

			<?php wpex_hook_content_after(); ?>

		</div><!-- #primary -->

		<?php wpex_hook_primary_after(); ?>

	</div><!-- .container -->

<?php
get_footer();
$mem->quit();
