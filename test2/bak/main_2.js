function show() {
  // Generic setup
  var BASE_URL = "https://siteoptimus.com/test2/";
  var TOP_NODES_FILENAME = "gcverts.JSON";
  var TOP_EDGES_FILENAME = "gcedges.JSON";
  var DETAILS_NODES_FILENAME = "verts.JSON";
  var DETAILS_EDGES_FILENAME = "edges.JSON";

  // Load server data
  var TOP_NODES_URL = BASE_URL + TOP_NODES_FILENAME;
  var TOP_EDGES_URL = BASE_URL + TOP_EDGES_FILENAME;
  var DETAILS_NODES_URL = BASE_URL + DETAILS_NODES_FILENAME;
  var DETAILS_EDGES_URL = BASE_URL + DETAILS_EDGES_FILENAME;

  var breadcrumbArray = ["Top"];

  var containerWidth = 800,
    containerHeight = 600;

  var containerDiv = d3
    .select(".chart-container")
    .style("width", containerWidth + "px")
    .style("height", containerHeight + "px");

  var margin = { top: 20, right: 20, bottom: 20, left: 20 },
    width = containerWidth - margin.left - margin.right,
    height = containerHeight - margin.top - margin.bottom;

  var topNodes, topEdges, detailsNodes, detailsEdges;

  var topLinkNodes = [];

  var topNodesMap, detailsNodesMap;

  var detailsMap, filterDetailsNodesNames;

  var node, link, linkNode;

  var isDragging = false;

  var duration = 500;

  var zoom = d3
    .zoom()
    .scaleExtent([1, 1])
    .on("zoom", zoomed);

  // Scales
  // Scales domain depend on the data and are set after loading the data
  // Scales range is the actually visual attributes values
  var topLinkStrokeWidthScale = d3.scaleLinear().range([1, 4]);
  var topLinkDistanceScale = d3.scaleLinear().range([50, 150]);
  var topNodeRadiusScale = d3.scaleSqrt().range([10, 20]);
  var detailsLinkStrokeWidthScale = d3.scaleLinear().range([0, 4]);
  var detailsLinkDistanceScale = d3.scaleLinear().range([50, 150]);
  var detailsNodeRadiusScale = d3.scaleSqrt().range([10, 20]);

  // Force simulation
  var simulation = d3
    .forceSimulation()
    .force(
      "link",
      d3.forceLink().id(function(d) {
        return d.realname;
      })
    )
    .force("charge", d3.forceManyBody().strength(-5))
    .force("center", d3.forceCenter(width / 2, height / 2))
    .force("collision", d3.forceCollide().iterations(1));

  // SVG container
  var svg = d3
    .select(".chart")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom);

  svg
    .append("rect")
    .attr("class", "zoom")
    .attr("width", width)
    .attr("height", height)
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
    .call(zoom);

  var chart = svg
    .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

  var topG = chart.append("g").attr("class", "top-layer");
  var detailsG = chart.append("g").attr("class", "details-layer");

  // Tooltip
  var tooltip = d3.select(".tooltip");

  updateBreadcrumb(breadcrumbArray);

  d3.queue()
    .defer(d3.json, TOP_NODES_URL)
    .defer(d3.json, TOP_EDGES_URL)
    .defer(d3.json, DETAILS_NODES_URL)
    .defer(d3.json, DETAILS_EDGES_URL)
    .await(function(
      error,
      topNodesJSON,
      topEdgesJSON,
      detailsNodesJSON,
      detailsEdgesJSON
    ) {
      if (error) throw error;
      topNodes = topNodesJSON;
      topEdges = topEdgesJSON;
      topNodesMap = d3.map(topNodes, function(d) {
        return d.realname;
      });
      topEdges.forEach(function(d, i) {
        d.source = d.from;
        d.target = d.to;
        topLinkNodes.push({
          realname: "linkNode-" + i,
          source: topNodesMap.get(d.from),
          target: topNodesMap.get(d.to)
        });
      });
      detailsNodes = detailsNodesJSON;
      detailsEdges = detailsEdgesJSON;
      detailsEdges.forEach(function(d) {
        d.source = d.from;
        d.target = d.to;
      });
      detailsNodesMap = d3.map(detailsNodes, function(d) {
        return d.realname;
      });

      // Set scales' domains
      topLinkStrokeWidthScale.domain([
        0,
        d3.max(topEdges, function(d) {
          return d.weight;
        })
      ]);
      topLinkDistanceScale.domain([
        0,
        d3.max(topEdges, function(d) {
          return d.weight;
        })
      ]);
      topNodeRadiusScale.domain([
        0,
        d3.max(topNodes, function(d) {
          return d.size;
        })
      ]);
      detailsLinkStrokeWidthScale.domain([0, 1]);
      detailsLinkDistanceScale.domain([0, 1]);
      detailsNodeRadiusScale.domain([
        0,
        d3.max(detailsNodes, function(d) {
          return d.strength;
        })
      ]);

      detailsMap = d3.map(detailsNodes, function(d) {
        return d.realname;
      });

      renderTop();
    });

  function renderTop() {
    var t = d3.transition().duration(duration);
    link = topG
      .selectAll(".link")
      .data(topEdges, function(d) {
        return JSON.stringify(d);
      })
      .enter()
      .append("path")
      .attr("class", "link")
      .attr("stroke-width", function(d) {
        return topLinkStrokeWidthScale(d.weight);
      });

    node = topG
      .selectAll("g.node")
      .data(topNodes, function(d) {
        return d.realname;
      })
      .enter()
      .append("g")
      .attr("class", "node")
      .call(
        d3
          .drag()
          .on("start", dragstarted)
          .on("drag", dragged)
          .on("end", dragended)
      )
      .each(function(d) {
        d.x = width / 2;
        d.y = height / 2;
      });

    node
      .append("circle")
      .attr("class", "node-circle")
      .attr("r", 0)
      .on("mouseenter", mouseenter)
      .on("mouseleave", mouseleave)
      .on("click", transitionToDetails)
      .transition(t)
      .attr("r", function(d) {
        return topNodeRadiusScale(d.size);
      });

    node
      .append("text")
      .attr("class", "node-label")
      .attr("dy", "0.35em")
      .attr("text-anchor", "middle")
      .attr("fill-opacity", 0)
      .transition(t)
      .attr("fill-opacity", 1)
      .text(function(d) {
        return d.name;
      });

    linkNode = topG
      .selectAll(".link-node")
      .data(topLinkNodes)
      .enter()
      .append("circle")
      .attr("class", "link-node")
      .attr("r", 1);

    simulation.nodes(topNodes.concat(topLinkNodes)).on("tick", ticked);
    simulation
      .force("link")
      .distance(function(d) {
        return topLinkDistanceScale(d.weight);
      })
      .links(topEdges);
    simulation.force("collision").radius(function(d) {
      return topNodeRadiusScale(d.size);
    });
    simulation.alpha(1).restart();
  }

  function renderDetails(topNode) {
    var edgesToRender = filterDetailsEdges(topNode);
    var nodesToRender = detailsNodes.filter(function(d) {
      return filterDetailsNodesNames.has(d.name);
    });

    var linkNodesToRender = edgesToRender.map(function(d, i) {
      return {
        realname: "linkNode-" + i,
        source: detailsNodesMap.get(d.source),
        target: detailsNodesMap.get(d.target)
      };
    });

    if (nodesToRender.length === 0) {
      detailsG
        .append("text")
        .attr("class", "warning-info")
        .attr("x", width / 2)
        .attr("y", height / 2)
        .attr("text-anchor", "middle")
        .style("cursor", "pointer")
        .text(
          "No edges are found that match this category. Click to return to top."
        )
        .on("click", transitionToTop);
    } else {
      var t = d3.transition().duration(duration);
      link = detailsG
        .selectAll(".link")
        .data(edgesToRender, function(d) {
          return JSON.stringify(d);
        })
        .enter()
        .append("path")
        .attr("class", "link")
        .attr("stroke-width", function(d) {
          return detailsLinkStrokeWidthScale(d.weight);
        });

      node = detailsG
        .selectAll("g.node")
        .data(nodesToRender, function(d) {
          return d.realname;
        })
        .enter()
        .append("g")
        .attr("class", "node")
        .call(
          d3
            .drag()
            .on("start", dragstarted)
            .on("drag", dragged)
            .on("end", dragended)
        )
        .each(function(d) {
          d.x = width / 2;
          d.y = height / 2;
        });

      node
        .append("circle")
        .attr("class", "node-circle")
        .classed("other", function(d) {
          return d.group !== topNode.name;
        })
        .attr("r", 0)
        .on("mouseenter", mouseenter)
        .on("mouseleave", mouseleave)
        .on("click", transitionToTop)
        .transition(t)
        .attr("r", function(d) {
          return detailsNodeRadiusScale(d.strength);
        });

      node
        .append("text")
        .attr("class", "node-label")
        .attr("dy", "0.35em")
        .attr("text-anchor", "middle")
        .attr("fill-opacity", 0)
        .transition(t)
        .attr("fill-opacity", 1)
        .text(function(d) {
          return d.name;
        });

      linkNode = detailsG
        .selectAll(".link-node")
        .data(linkNodesToRender)
        .enter()
        .append("circle")
        .attr("class", "link-node")
        .attr("r", 1);

      simulation
        .nodes(nodesToRender.concat(linkNodesToRender))
        .on("tick", ticked);
      simulation
        .force("link")
        .distance(function(d) {
          return detailsLinkDistanceScale(d.weight);
        })
        .links(edgesToRender);
      simulation.force("collision").radius(function(d) {
        return detailsNodeRadiusScale(d.strength);
      });
      simulation.alpha(1).restart();
    }
  }

  function mouseenter(d) {
    if (isDragging) return;
    // Show tooltip
    tooltip.select("table").remove();

    var keys;
    if (d.hasOwnProperty("group")) {
      // Details
      keys = [
        "name",
        "realname",
        "group",
        "closeness",
        "betweenness",
        "strength",
        "degree"
      ];
    } else {
      // Top
      keys = [
        "name",
        "realname",
        "size",
        "closeness",
        "betweenness",
        "strength",
        "degree"
      ];
    }

    var tr = tooltip
      .append("table")
      .attr("class", "table")
      .append("tbody")
      .selectAll("tr")
      .data(keys)
      .enter()
      .append("tr");
    tr.append("th").text(function(e) {
      return e;
    });
    tr.append("td").text(function(e) {
      return d[e];
    });
    tooltip
      .style("left", d3.event.x + "px")
      .style("top", d3.event.y + "px")
      .transition()
      .style("opacity", 1);
  }

  function mouseleave(d) {
    if (isDragging) return;
    // Hide tooltip
    tooltip.transition().style("opacity", 0);
  }

  function transitionToDetails(d) {
    breadcrumbArray.push(d.realname);
    updateBreadcrumb(breadcrumbArray);

    tooltip.transition().style("opacity", 0);

    detailsG.style("display", "block");

    topG
      .selectAll(".node-circle")
      .transition()
      .duration(duration)
      .attr("r", 0);
    topG
      .transition()
      .duration(duration)
      .attr("opacity", 0)
      .on("end", function() {
        chart.selectAll(".top-layer *").remove();
        topG.style("display", "none");
        topG.attr("opacity", 1);
        renderDetails(d);
      });
  }

  function transitionToTop() {
    breadcrumbArray.pop();
    updateBreadcrumb(breadcrumbArray);

    tooltip.transition().style("opacity", 0);

    topG.style("display", "block");

    detailsG
      .selectAll(".node-circle")
      .transition()
      .duration(duration)
      .attr("r", 0);
    detailsG
      .transition()
      .duration(duration)
      .attr("opacity", 0)
      .on("end", function() {
        chart.selectAll(".details-layer *").remove();
        detailsG.style("display", "none");
        detailsG.attr("opacity", 1);
        renderTop();
      });
  }

  // https://stackoverflow.com/a/17687907/7612054
  function ticked() {
    link.attr("d", function(d) {
      var x1 = d.source.x,
        y1 = d.source.y,
        x2 = d.target.x,
        y2 = d.target.y,
        dx = x2 - x1,
        dy = y2 - y1,
        dr = Math.sqrt(dx * dx + dy * dy),
        // Defaults for normal edge.
        drx = 0,
        dry = 0,
        xRotation = 0, // degrees
        largeArc = 0, // 1 or 0
        sweep = 1; // 1 or 0

      // Self edge.
      if (x1 === x2 && y1 === y2) {
        (drx = dr),
          (dry = dr), // Fiddle with this angle to get loop oriented.
          (xRotation = -45);

        // Needs to be 1.
        largeArc = 1;

        // Change sweep to change orientation of loop.
        //sweep = 0;

        // Make drx and dry different to get an ellipse
        // instead of a circle.
        drx = 30;
        dry = 20;

        // For whatever reason the arc collapses to a point if the beginning
        // and ending points of the arc are the same, so kludge it.
        x2 = x2 + 1;
        y2 = y2 + 1;
      }

      return (
        "M" +
        x1 +
        "," +
        y1 +
        "A" +
        drx +
        "," +
        dry +
        " " +
        xRotation +
        "," +
        largeArc +
        "," +
        sweep +
        " " +
        x2 +
        "," +
        y2
      );
    });

    node.attr("transform", function(d) {
      return "translate(" + d.x + "," + d.y + ")";
    });

    linkNode
      .attr("cx", function(d) {
        return (d.x = (d.source.x + d.target.x) * 0.5);
      })
      .attr("cy", function(d) {
        return (d.y = (d.source.y + d.target.y) * 0.5);
      });
  }

  function dragstarted(d) {
    tooltip.transition().style("opacity", 0);
    isDragging = true;
  }

  function dragged(d) {
    d.x = d3.event.x;
    d.y = d3.event.y;
    ticked();
  }

  function dragended(d) {
    isDragging = false;
  }

  function zoomed() {
    chart.attr("transform", d3.event.transform);
  }

  function filterDetailsEdges(topNode) {
    filterDetailsNodesNames = d3.set();
    var selectedGroup = topNode.name;
    var filteredDetailsEdges = detailsEdges.filter(function(edge) {
      var fromNode = detailsMap.get(edge.from);
      var toNode = detailsMap.get(edge.to);

      if (fromNode.group === selectedGroup || toNode.group === selectedGroup) {
        filterDetailsNodesNames.add(fromNode.name);
        filterDetailsNodesNames.add(toNode.name);
        return true;
      } else {
        return false;
      }
    });
    return filteredDetailsEdges;
  }

  function updateBreadcrumb(breadcrumbArray) {
    var breadcrumb = d3
      .select(".breadcrumb ul")
      .selectAll("li")
      .data(breadcrumbArray);

    var breadcrumbMerge = breadcrumb
      .enter()
      .append("li")
      .merge(breadcrumb)
      .classed("is-active", function(d, i) {
        return i === breadcrumbArray.length - 1;
      });

    breadcrumbMerge.text(function(d) {
      return d;
    });

    if (breadcrumbArray.length > 1) {
      breadcrumbMerge
        .filter(function(d, i) {
          return i === 0;
        })
        .style("cursor", "pointer")
        .on("click", transitionToTop);
    } else {
      breadcrumbMerge
        .filter(function(d, i) {
          return i === 0;
        })
        .style("cursor", "auto");
    }

    breadcrumb.exit().remove();
  }
}
