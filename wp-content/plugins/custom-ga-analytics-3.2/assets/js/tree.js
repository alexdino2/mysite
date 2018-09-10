function show(dataURL) {
	// Generic setup
	var containerWidth = 800,
			containerHeight = 600;

	var containerDiv = d3.select(".chart-container")
			.style("width", containerWidth + "px")
			.style("height", containerHeight + "px");

	var margin = {top: 80, right: 20, bottom: 80, left: 20},
			width = containerWidth - margin.left - margin.right,
			height = containerHeight - margin.top - margin.bottom;

	var duration = 500, // Transition duration
			depthWidth = 250, // Width of one depth for the tree layout
			lineHeight = 30; // Height of one node for the tree layout

	var circleRadius = 8,
			circleHoverRadius = 80;

	var BASEURL = "siteoptimus.com/site-optimus-metadata-source/";
	var interestAffinityCategoryString = "ga:interestAffinityCategory";
	var interestInMarketCategoryString = "ga:interestInMarketCategory";

	var root,
			levelWidth; // Track number of nodes of each level

	var nodes0; // Track provious nodes

	var currentExpanded; // Track current expanded depth 2 node
	var currentExpandedAncestors;

	var currentHover; // Track current hover node

	// Tree layout
	var tree = d3.tree()
			.size([height, width]);

	// SVG container
	var svg = d3.select(".chart")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom);
	var chart = svg
		.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	// Info div
	var info = d3.select("body").append("div")
			.attr("class", "info")
			.style("opacity", 0)
			.style("pointer-event", "none");

	// Simulating data loading process
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

	function processData(json) {
		var data = json;
		data.forEach(function(d) { // Leaf node
			d.label = d.dim.split("||")[1];
			d.filter = d.whichdim;
			d.filterValue = d.label;
		})
		// Add root node
		var rootDim = "siteoptimus";
		data.push({
			whichdim: "",
			dim: rootDim,
			label: rootDim
		});

		// Add depth 1 nodes
		var whichdims = d3.set(data.map(function(d) { return d.whichdim; })).values();
		whichdims.forEach(function(whichdim) {
			if (whichdim) {
				var filteredData = data.filter(function (d) {
					return d.whichdim === whichdim;
				});
				var opplevels = d3.set(
					filteredData,
					function(d) {
						return d.opplevel;
					}
				).values();
				var opplevel = parentOpplevel(opplevels);
				data.push({
					whichdim: rootDim,
					dim: whichdim,
					opplevel: opplevel,
					label: whichdim
				});
			}
		});

		// Add subcategories
		var interestAffinityCategoryData = data.filter(isInterestAffinityCategory);
		var interestAffinityCategories = d3.set(interestAffinityCategoryData,
			function(d) {
				return d.label.split("/")[0];
			}
		).values();
		interestAffinityCategories.forEach(function(d) {
			var filteredData = interestAffinityCategoryData.filter(function(e) {
				if (e.label.slice(0, d.length) === d) {
					e.whichdim = interestAffinityCategoryString + d;
					e.label = e.label.length === d.length ? e.label : e.label.slice(d.length + 1);
					return true;
				} else {
					return false;
				}
			});
			var opplevels = d3.set(
				filteredData,
				function (e) {
					return e.opplevel;
				}
			).values();
			var opplevel = parentOpplevel(opplevels);
			data.push({
				whichdim: interestAffinityCategoryString,
				dim: interestAffinityCategoryString + d,
				opplevel: opplevel,
				label: d
			});
		})

		var interestInMarketCategoryData = data.filter(isInterestInMarketCategory);
		var interestInMarketCategories = d3.set(interestInMarketCategoryData,
			function (d) {
				return d.label.split("/")[0];
			}
		).values();
		interestInMarketCategories.forEach(function (d) {
			var filteredData = interestInMarketCategoryData.filter(function (e) {
				if (e.label.slice(0, d.length) === d) {
					e.whichdim = interestInMarketCategoryString + d;
					e.label = e.label.length === d.length ? e.label : e.label.slice(d.length + 1);
					return true;
				} else {
					return false;
				}
			});
			var opplevels = d3.set(
				filteredData,
				function (e) {
					return e.opplevel;
				}
			).values();
			var opplevel = parentOpplevel(opplevels);
			data.push({
				whichdim: interestInMarketCategoryString,
				dim: interestInMarketCategoryString + d,
				opplevel: opplevel,
				label: d
			});
		})

		// Convert data to hierarchical structure
		root = d3.stratify()
				.id(function(d) { return d.dim; })
				.parentId(function(d) { return d.whichdim; })
				(data);

		// Store the old positions for transition
		root.x0 = height / 2;
		root.y0 = 0;

		// Collapse after the second level except currentExpanded
		if (!currentExpandedAncestors) currentExpandedAncestors = [root.id];
		root.each(function(node) {
			if (currentExpandedAncestors.indexOf(node.id) !== -1) return;
			collapse(node);
		});

		update(root);
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

		// Resize SVG container height
		svg.attr("height", newHeight + margin.top + margin.bottom);

		// Compute the new tree layout
		tree.size([newHeight, width])(root);
		var nodes = root.descendants(),
				links = root.descendants().slice(1);

		// Normalize for fixed width
		nodes.forEach(function(d) {
			d.y = d.depth * depthWidth;
			// if (d.depth === 0) {
			// 	d.y = circleRadius; 
			// } // Move the root circle to the right
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
				.attr("class", function (d) { return "node" + (d.data.opplevel ? " " + d.data.opplevel.toLowerCase() : ""); })
				.attr("transform", function(d) {
					if (d.parent && d.parent.hasOwnProperty("x0")) {
						return "translate(" + d.parent.y0 + "," + d.parent.x0 + ")";
					} else {
						return "translate(" + source.y0 + "," + source.x0 + ")";
					}

				});

		// Expand and collapse depth 1 nodes when clicking
		nodeEnter.filter(function(d) { return d.depth !== 0 && d.height !== 0; })
				.on("click", clicked);

		// Show tooltip when hover over depth 2 nodes
		nodeEnter.filter(function (d) { return d.height === 0; })
				.on("mouseover touchstart", showInfo)
				.on("mouseout touchend", hideInfo)
				.on("click", redirect);

		// Add the label for the node
		nodeEnter.append("text")
				.attr("dy", "0.35em")
				.attr("x", 10)
				.style("cursor", "pointer")
				.text(function(d) { return d.data.label; });

		// Add the circle for the node
		nodeEnter.append("circle")
				.attr("r", 1e-6)
				.attr("cx", 0)
				.attr("cy", 0);

		var nodeMerge = nodeEnter.merge(node)
				.attr("class", function (d) { return "node" + (d.data.opplevel ? " " + d.data.opplevel.toLowerCase() : ""); });

		// Calculate the text lengh for adjusting SVG width
		var maxTextWidth = 0;
		nodeMerge.selectAll("text")
				.filter(function(d) {
					return d.depth === levelWidth.length - 1;
				})
				.each(function() {
					var textWidth = this.getBBox().width;
					maxTextWidth = textWidth > maxTextWidth ? textWidth : maxTextWidth;
				})
		// Resize SVG container width
		var newWidth = depthWidth * (levelWidth.length - 1) + maxTextWidth + 80;
		svg.attr("width", newWidth + margin.left + margin.right);


		// Transition to the proper position for the node
		nodeMerge.transition()
				.duration(duration)
				.attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

		// Update the node attributes and styles
		nodeMerge.select("circle")
				.attr("r", function(d) {
					return d.id === currentHover ? circleHoverRadius : circleRadius;
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
		currentExpandedAncestors = d.ancestors().map(function (e) {
			return e.id;
		});
		if (currentExpanded === d.id) {
			currentExpanded = currentExpandedAncestors[0];
			currentExpandedAncestors = currentExpandedAncestors.slice(1);
		} else {
			currentExpanded = d.id;
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
	}

	function redirect(d) {
		var url = BASEURL + "?filter=" + d.data.filter + "==" + d.data.filterValue;
		window.open(url, "_blank");
	}

	function showInfo(d) {
		currentHover = d.id;

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

	function collapse(d) {
		if (d.children) {
			d._children = d.children;
			d._children.forEach(collapse);
			d.children = null;
		}
	}

	// Create a curved path between parent and child
	function diagonal(child, parent) {
		return "M" + child.y + "," + child.x +
					 "C" + (child.y + parent.y) / 2 + " " + child.x + "," +
								 (child.y + parent.y) / 2 + " " + parent.x + "," +
								 parent.y + " " + parent.x;
	}

	function isInterestAffinityCategory(d) {
		return d.whichdim && d.whichdim === interestAffinityCategoryString;
	}

	function isInterestInMarketCategory(d) {
		return d.whichdim && d.whichdim === interestInMarketCategoryString;
	}

	function parentOpplevel(opplevels) {
		if (opplevels.indexOf("High") !== -1) {
			return "High";
		} else if (opplevels.indexOf("Med") !== -1) {
			return "Med";
		} else {
			return "Low";
		}
	}

	// Reset button
	d3.select("button.reset")
			.on("click", function() {
				currentExpanded = null;
				currentExpandedAncestors = [root.id];
				// Collapse after the second level
				root.children.forEach(function (child) {
					collapse(child);
				});

				update(root);
			})
}