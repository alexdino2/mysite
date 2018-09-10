function show(dataURL) {
	// Generic setup
	var margin = {top: 20, bottom: 20, right: 20, left: 20},
			width = 960 - margin.left - margin.right,
			height = 600 - margin.top - margin.bottom;

	var duration = 750, // Transition duration
			depthWidth = 250, // Width of one depth for the tree layout
			lineHeight = 30; // Height of one node for the tree layout

	var circleRadius = 8,
			circleHoverRadius = 80;

	var root,
			levelWidth; // Track number of nodes of each level

	var nodes0; // Track provious nodes

	var currentExpanded; // Track current expanded depth 2 node

	var currentHover; // Track current hover node

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

	var dataLength = 0;
	var timeout;
	function loadMoreData() {
		d3.json(dataURL, function(error, newData) {
			if (error) throw error;
			dataLength += 3; // Simulate adding 3 more data points each loading
			if (dataLength > 30) {
				processData(newData);
			} else {
				processData(newData.slice(0, dataLength+1));
				timeout = setTimeout(loadMoreData, 1000);
			}
		});
	}
	loadMoreData();


	function processData(data) {
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

		// Store the old positions for transition
		root.x0 = height / 2;
		root.y0 = 0;

		// Collapse after the second level
		root.children.forEach(function(child) {
			if (child.data.dim !== currentExpanded) {
				collapse(child);
			}
		});

		update(root);

		if (!currentExpanded) { // Only center if the user hasn't expaneded depth 2 nodes yet
			centerNode(root);
		}
	}

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
		nodes.forEach(function(d) {
			d.y = d.depth * depthWidth;
			if (nodes0) {
				var node0 = nodes0.find(function(e) { return e.data.dim === d.data.dim; });
				if (node0) {
					d.x0 = node0.x0;
					d.y0 = node0.y0;
				}
			}
		});

		// NODE SECTION
		var node = chart.selectAll("g.node")
				.data(nodes, function(d) { return d.data.dim; });

		// Enter any node at the parent's previous position
		var nodeEnter = node.enter().append("g")
				.attr("class", function(d) { return "node" + (d.data.opplevel ? " " + d.data.opplevel.toLowerCase() : ""); })
				.attr("transform", function(d) {
					if (d.parent && d.parent.hasOwnProperty("x0")) {
						return "translate(" + d.parent.y0 + "," + d.parent.x0 + ")";
					} else {
						return "translate(" + source.y0 + "," + source.x0 + ")";
					}

				});

		// Expand and collapse depth 1 nodes when clicking
		nodeEnter.filter(function(d) { return d.depth === 1; })
				.on("click", clicked);

		// Show tooltip when hover over depth 2 nodes
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
				.attr("r", function(d) {
					return d.data.dim === currentHover ? circleHoverRadius : circleRadius;
				})
				.style("cursor", "pointer");

		// Remvoe any exit nodes
		var nodeExit = node.exit()
			.transition()
				.duration(duration)
				.attr("transform", function(d) {
					return  "translate(" + d.parent.y + "," + d.parent.x + ")";
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
					var o;
					if (d.parent && d.parent.hasOwnProperty("x0")) {
						o = { x: d.parent.x0, y: d.parent.y0 };
					} else {
						o = { x: source.x0, y: source.y0 };
					}
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
					var o = { x: d.parent.x, y: d.parent.y };
					return diagonal(o, o);
				})
				.remove();

		// Store the old positions for transition
		nodes.forEach(function(d) {
			d.x0 = d.x;
			d.y0 = d.y;
		});

		nodes0 = nodes;
	}

	// Toggle children on click
	function clicked(d) {
		if (currentExpanded === d.data.dim) {
			currentExpanded = null;
		} else {
			currentExpanded = d.data.dim;
		}

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
		currentHover = d.data.dim;

		var node = d3.select(this);
		node.raise();
		node.select("circle")
			.transition("showTooltip")
				.attr("r", circleHoverRadius)
				.attr("cx", -(circleHoverRadius - circleRadius))
				.on("end", function() {
					var bcr = node.select("circle").node().getBoundingClientRect();
					info
							.style("left", bcr.left + window.scrollX + "px")
							.style("top", bcr.top + window.scrollY + "px")
							.style("width", bcr.width + "px")
							.style("height", bcr.height + "px")
						.transition("showTooltipInfo")
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
			.transition("showTooltip")
				.style("font-weight", "bold");
	}

	function hideInfo(d) {
		currentHover = null;

		var node = d3.select(this);
		node.select("circle")
			.transition("hideTooltip")
				.attr("r", circleRadius)
				.attr("cx", 0);
		node.select("text")
			.transition("hideTooltip")
				.style("font-weight", null);
		info.transition("hideTooltipInfo")
				.style("opacity", 0)
				.on("end", function() {
					info.html("");
				});
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