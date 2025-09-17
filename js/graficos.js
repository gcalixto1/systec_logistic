/*Author: Ing. Ruben D. Chirinos R. Tlf: +58 0416-3422924, email: elsaiya@gmail.com*/

/*tipos de graficos
    bar
    horizontalBar
    line
    radar
    polarArea
    pie
    doughnut
    bubble
 Con pointRadius podrás establecer el radio del punto.

fill: false, –> no aparecerá relleno por debajo de la línea.

showLine: false, –> no aparecerá la línea.

Es decir, si ponemos fill y showLine a false, tendremos un gráfico de puntos, en lugar de un gráfico
de líneas. pointStyle: ‘circle’, ‘triangle’, ‘rect’, ‘rectRounded’, ‘rectRot’, ‘cross’, ‘crossRot’, ‘star’,
‘line’, and ‘dash’ Podría ser incluso una imagen.

spanGaps está por defecto a false. Si lo ponemos a true, cuando te falte un valor en la línea, no se 
romperá la línea.*/

/* GRAFICO PARA VENTAS POR SUCURSALES ANUAL
function showGraphBar()
        {
            {
                $.post("data.php",
                function (data)
                {
                    console.log(data);
                    var id = [];
                    var name = [];
                    var compras = [];
                    var cotizacion = [];
                    var ventas = [];
                    var myColors=[];

                    for (var i in data) {
                        id.push(data[i].codsucursal);
                        name.push(data[i].razonsocial);
                        compras.push(data[i].sumcompras);
                        cotizacion.push(data[i].sumcotizacion);
                        ventas.push(data[i].sumventas);
                    }


                    $.each(id, function( index,num ) {
                        if (num == 1) myColors[index]= "#f0ad4e";
                        if (num == 2) myColors[index]= "#008000";
                        if (num == 3) myColors[index]= "#E0E4CC";
                        if (num == 4) myColors[index]= "#003399";
                        if (num == 5) myColors[index]= "#969788";
                        if (num == 6) myColors[index]= "#970096";
                        if (num == 7) myColors[index]= "#169696"; 
                        if (num == 8) myColors[index]= "#69D2E7";   
                        if (num == 9) myColors[index]= "#F38630";   
                        if (num == 10) myColors[index]= "#F82330";  
                        if (num == 11) myColors[index]= "#008080";  
                        if (num == 12) myColors[index]= "#00FFFF";  
                        if (num == 13) myColors[index]= "#fff933";  
                        if (num == 14) myColors[index]= "#90ff33";  
                        if (num == 15) myColors[index]= "#8633ff";
                    });

                    var chartdata = {
                        labels: name,
                        datasets: [
                        {
                          label: "Compras",    
                          backgroundColor: ['#ff7676'],
                          borderColor: ['rgba(255,99,132,1)','rgba(255,99,132,1)','rgba(255,99,132,1)'],
                          borderWidth: 1,
                          data: compras
                      },
                      {
                          label: "Cotizaciones",
                          backgroundColor: ['#8EE1BC'],
                          borderColor: ['rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)'],
                          borderWidth: 1,
                          data: cotizacion
                      },
                      {
                          label: "Ventas",
                          backgroundColor: ['#25AECD'],
                          borderColor: ['rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 0.2)'],
                          borderWidth: 1,
                          data: ventas
                      }
                      ]
                  };

                    var graphTarget = $("#barChart");
                    //var steps = 3;

                    var barGraph = new Chart(graphTarget, {
                        type: 'bar',
                        data: chartdata,
                        responsive : true,
                        animation: true,
                        barValueSpacing : 2,
                        barDatasetSpacing : 1,
                        tooltipFillColor: "rgba(0,0,0,0.8)",
                        multiTooltipTemplate: "<%= datasetLabel %> - <%= value %>" 
                    });
                });
            }
        }*/



function showGraphDoughnutPV()
        {
            {
                $.post("data.php?ProductosVendidos=si",
                function (data)
                {
                    console.log(data);
                    var id = [];
                    var name = [];
                    var total = [];

                    for (var i in data) {
                        id.push(data[i].codproducto);
                        name.push(data[i].producto);
                        total.push(data[i].cantidad);
                    }

                    var chartdata = {
                        labels: name,
                        datasets: [
                        {
                          backgroundColor: ["#ff7676", "#3e95cd","#3cba9f","#003399","#f0ad4e","#987DDB","#E8AC9E","#D3E37D"],
                          borderWidth: 1,
                          data: total
                        }
                      ]
                  };

                    var graphTarget = $("#DoughnutChart");
                    //var steps = 3;

                    var barGraph = new Chart(graphTarget, {
                        type: 'doughnut',
                        data: chartdata,
                        responsive : true,
                        animation: true,
                        barValueSpacing : 2,
                        barDatasetSpacing : 1,
                        tooltipFillColor: "rgba(0,0,0,0.8)",
                        multiTooltipTemplate: "<%= datasetLabel %> - <%= value %>" 
                    });
                });
            }
        }



/* grafico de barra de estudiantes por discapacidades*/
function showGraphDoughnutVU()
        {
            {
                $.post("data.php?VentasxUsuarios=si",
                function (data)
                {
                    console.log(data);
                    var id = [];
                    var name = [];
                    var marks = [];
                    var myColors=[];

                    for (var i in data) {
                        id.push(data[i].codigo);
                        name.push(data[i].nombres);
                        marks.push(data[i].total);
                    }

                    $.each(id, function( index,num ) {
                        if (num == 1)
                            myColors[index]= "#f0ad4e";
                        if (num == 2)
                            myColors[index]= "#ff7676";
                        if (num == 3)
                            myColors[index]= "#E0E4CC";
                        if (num == 4)
                            myColors[index]= "#3e95cd";
                        if (num == 5)
                            myColors[index]= "#969788";
                        if (num == 6)
                            myColors[index]= "#987DDB";
                        if (num == 7)
                            myColors[index]= "#169696"; 
                        if (num == 8)
                            myColors[index]= "#69D2E7";   
                        if (num == 9)
                            myColors[index]= "#F38630";   
                        if (num == 10)
                            myColors[index]= "#F82330";  
                        if (num == 11)
                            myColors[index]= "#D3E37D";  
                        if (num == 12)
                            myColors[index]= "#00FFFF";  
                        if (num == 13)
                            myColors[index]= "#fff933";  
                        if (num == 14)
                            myColors[index]= "#90ff33";  
                        if (num == 15)
                            myColors[index]= "#E8AC9E";
                    });

                    var chartdata = {
                        labels: name,
                        datasets: [
                            {
                                label: 'Total en Ventas',
                                data: marks,  
                                backgroundColor: myColors,
                                borderWidth: 1
                            }
                        ]
                    };

                    var graphTarget = $("#DoughnutChart2");
                    //var steps = 3;

                    var barGraph = new Chart(graphTarget, {
                        type: 'doughnut',
                        data: chartdata,
                        responsive : true,
                        animation: true,
                        barValueSpacing : 5,
                        barDatasetSpacing : 1,
                        tooltipFillColor: "rgba(0,0,0,0.8)",
                        multiTooltipTemplate: "<%= datasetLabel %> - <%= value %>" 
                    });
                });
            }
        }