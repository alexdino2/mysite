
           
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
                        categories: <?php echo $first_encode; ?>,
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
                        data: <?php echo $my_array; ?>
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
