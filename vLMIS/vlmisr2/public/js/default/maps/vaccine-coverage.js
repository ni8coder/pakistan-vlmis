
$(window).load(function() {

   map = new OpenLayers.Map('map', {
        projection: new OpenLayers.Projection("EPSG:900913"),
        displayProjection: new OpenLayers.Projection("EPSG:4326"),
        maxExtent: restricted,
        restrictedExtent: restricted,
        maxResolution: "auto",
        allOverlays: true,
        controls: [
            new OpenLayers.Control.Navigation({
                'zoomWheelEnabled': false
            }),
            new OpenLayers.Control.MousePosition({
                prefix: 'coordinates: ',
                numDigits: 2,
                separator: ' | '
            }),
            new OpenLayers.Control.Zoom({
                zoomInId: "customZoomIn",
                zoomOutId: "customZoomOut"
            }),
            new OpenLayers.Control.ScaleLine()
        ]
    });

   province = new OpenLayers.Layer.Vector(
        "Province", {
            protocol: new OpenLayers.Protocol.HTTP({
                url: appName + "/js/province.geojson",
                format: new OpenLayers.Format.GeoJSON({
                    internalProjection: new OpenLayers.Projection("EPSG:3857"),
                    externalProjection: new OpenLayers.Projection("EPSG:3857")
                })
            }),
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: province_style_label,
            isBaseLayer: true
        });

    district = new OpenLayers.Layer.Vector(
        "District", {
            protocol: new OpenLayers.Protocol.HTTP({
                url: appName + "/js/district.geojson",
                format: new OpenLayers.Format.GeoJSON({
                    internalProjection: new OpenLayers.Projection("EPSG:3857"),
                    externalProjection: new OpenLayers.Projection("EPSG:3857")
                })
            }),
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: district_style
        });

        vLMIS = new OpenLayers.Layer.Vector("Vaccine Coverage", {
            styleMap: vlMIS_style
        });
        map.addLayers([province, district, vLMIS]);
        district.setZIndex(900);
        province.setZIndex(1001);

        selectfeature = new OpenLayers.Control.SelectFeature([vLMIS]);
        map.addControl(selectfeature);
        selectfeature.activate();
        selectfeature.handlers.feature.stopDown = false;

        vLMIS.events.on({
            "featureselected": onFeatureSelect,
            "featureunselected": onFeatureUnselect
        });
        map.zoomToExtent(bounds);

    getLegend('7', 'Vaccine Coverage');
    handler = setInterval(readData, 2000);
});

function readData() {
    if (province.features.length == "9" && district.features.length == "147") {
        getData();
        clearInterval(handler);
    }
}

$("#submit").click(function() {
    getData();
});


function getData() {
    clearData();  
    var year = $("#year").val();
    var month = $('#slider').slider("option", "value");
    var region = $("#province").val();
    var product = $("#product").val();
    product_name = $("#product option:selected").text();
    mapTitle();
    onFeatureUnselect();
    
    $.ajax({
        url: appName + "/api/geo/get-vaccine-coverage",
        type: "GET",
        data: {
            year: year,
            month: month,
            province: region,
            product: product
        },
        dataType: "json",
        success: callback,
        error: function(response) {
            alert("No Data Available");
            $("#loader").css("display", "none");
            $("#submit").attr("disabled", false);
            $("#slider").slider("enable");
        }
    });

    function callback(response) {
        var data = [];
        data = response;

        if (vLMIS.features.length > 0) {
            vLMIS.removeAllFeatures();
        }
        FilterData();
        if (data.length <= 0) {
            alert("No Data Available");
            $("#loader").css("display", "none");
            $("#submit").attr("disabled", false);
            $("#slider").slider("enable");
        }
        data.sort(SortByID);
        for (var i = 0; i < data.length; i++) {
            chkeArray(data[i].district_id, Number(data[i].population), Number(data[i].consumption), Number(data[i].vaccine_coverage));
        }
        drawGrid();
        districtCountGraph();
        $("#submit").attr("disabled", false);
        $("#slider").slider("enable");
    }
}

function chkeArray(district_id, population, consumption, coverage) {
    for (var i = 0; i < district.features.length; i++) {
        if (district_id == district.features[i].attributes.district_id) {
            vLMISLayer(district.features[i].geometry, district.features[i].attributes.province_name, product_name, district_id, district.features[i].attributes.district_name, population, consumption, coverage);
            break;
        }
    }
}

function vLMISLayer(wkt, province, product, district_id, district_name, population, consumption, value) {
    feature = new OpenLayers.Feature.Vector(wkt);

    if (value == classesArray[0].start_value && value == classesArray[0].end_value) {
        color = classesArray[0].color_code;
        NoData = Number(NoData) + 1;
        status = classesArray[0].description;
    }
    if (value > classesArray[1].start_value && value <= classesArray[1].end_value) {
        color = classesArray[1].color_code;
        class1 = Number(class1) + 1;
        status = classesArray[1].description;
    }
    if (value > classesArray[2].start_value && value <= classesArray[2].end_value) {
        color = classesArray[2].color_code;
        class2 = Number(class2) + 1;
        status = classesArray[2].description;
    }
    if (value > classesArray[3].start_value && value <= classesArray[3].end_value) {
        color = classesArray[3].color_code;
        class3 = Number(class3) + 1;
        status = classesArray[3].description;
    }
    if (value > classesArray[4].start_value && value <= classesArray[4].end_value) {
        color = classesArray[4].color_code;
        class4 = Number(class4) + 1;
        status = classesArray[4].description;
    }
    if (value > classesArray[5].start_value) {
        color = classesArray[5].color_code;
        class5 = Number(class5) + 1;
        status = classesArray[5].description;
    }
    feature.attributes = {
        district_id: district_id,
        district: district_name,
        province: province,
        product: product,
        population: population.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
        consumption: consumption.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
        coverage: value,
        status : status,
        color: color
    };
    vLMIS.addFeatures(feature);

    $("#loader").css("display", "none");
}


function onFeatureSelect(e)
{
    $("#prov").html(e.feature.attributes['province']);
    $("#district").html(e.feature.attributes['district']);
    $("#consumption").html(e.feature.attributes['consumption']);
    $("#population").html(e.feature.attributes['population']);
    $("#vaccine_coverage").html(e.feature.attributes['coverage']);
}

function onFeatureUnselect(e) {
    $("#prov").html("");
    $("#district").html("");
    $("#consumption").html("");
    $("#population").html("");
    $("#vaccine_coverage").html("");
}

function mapTitle() {

    prov_name = $("#province option:selected").text();
    if(prov_name == "Select"){prov_name = "Pakistan"};
    year_name = $("#year option:selected").text();
    month_value = ($('#slider').slider("option", "value")) - 1;
    var month_name = monthNames[month_value];
    month_year = month_name + " " + year_name;
    download = month_year;
    
    $("#mapTitle").html("<font color='green' size='4'><b> Vaccine Coverage <br/> (" + month_year + ")</b></font> <br/> " + product_name);

    var date = new Date();
    var d = date.getDate();
    var day = (d < 10) ? '0' + d : d;
    var m = date.getMonth() + 1;
    var month = (m < 10) ? '0' + m : m;
    var yy = date.getYear();
    var year = (yy < 1000) ? yy + 1900 : yy;

    var printdate = "Printed Date: " + day + "/" + month + "/" + year;
    $("#printedDate").html("<b>" + printdate + "</b>");
}

function clearData(){
    $("#loader").css("display", "block");
    $("#info").html("");
    $("#mapTitle").html("");
    $('.radio-button').prop('checked', false);
    $("#submit").attr("disabled", true);
    $("#slider").slider("disable");
    pieArray.length = 0;
    NoData = '0';
    DataProblem = '0';
    class1 = '0';
    class2 = '0';
    class3 = '0';
    class4 = '0';
    class5 = '0';
}

function SortByID(x, y) {
    return x.vaccine_coverage - y.vaccine_coverage;
}

function drawGrid(){
    $("#attributeGrid").html("");
    $("#districtRanking").html("");
    dataDownload.length = 0;
    jsonData.length = 0;
    var features = vLMIS.features;
    table = "<table class='table table-condensed table-hover'>";
    table += "<thead><th>Province</th><th>District</th><th>Consumption</th><th>Population</th><th>Vaccine Coverage</th></thead>";
    for (var i = 0; i < features.length; i++) {
        table += "<tr><td>" + features[i].attributes.province + "</td><td>" + features[i].attributes.district + "</td><td align='right'>" + features[i].attributes.consumption + "</td><td align='right'>" + features[i].attributes.population + "</td><td align='right'>" + features[i].attributes.coverage + "</td><td><div style='width:30px;height:18px;background-color:" + features[i].attributes.color + "'></div></td></tr>";
        jsonData.push({
            label: features[i].attributes.district,
            value: features[i].attributes.coverage,
            color: features[i].attributes.color
        });
        dataDownload.push({
            province: features[i].attributes.province,
            district_name: features[i].attributes.district,
            Consumption: features[i].attributes.consumption,
            Population: features[i].attributes.population,
            Vaccine_coverage: features[i].attributes.coverage
        });
    }
    table += "</table>";
    $("#attributeGrid").append(table);
    maximum = vLMIS.features.length;
    districtRanking(jsonData,"");
}

function districtRanking(Data,title) {

    Data.sort(SortByRankingID);
   
    if (Data.length > 52) {
        width = '480%';
    } 
    else {
        width = '180%';
    }

    var revenueChart = new FusionCharts({
        type: 'column2D',
        renderAt: 'chart-container',
        width: width,
        height: '100%',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": prov_name+" - District Wise Vaccine Coverage Ranking "+ title,
                "subcaption": download,
                "showLabels": "1",
                "slantLabels": '1',
                "enableLink": '0',
                "showValues": '1',
                "rotateValues": '1',
                "placeValuesInside": '1',
                "xAxisName": "",
                "adjustDiv":'0',
                "numDivLines":'3',
                "numberSuffix":"%",
                "yAxisName": "Vaccine Coverage",
                "exportEnabled": "1",
                "theme": "fint"
            },
            "data": Data
        }
    });
    revenueChart.render("districtRanking");
}

function gridFilter(color){
    $("#attributeGrid").html("");
    dataDownload.length = 0;
    var features = vLMIS.features;
    table = "<table class='table table-condensed table-hover'>";
    table += "<thead><th>Province</th><th>District</th><th>Consumption</th><th>Population</th><th>Vaccine Coverage</th></thead>";
    for (var i = 0; i < features.length; i++) {
        if (features[i].attributes.color == color) {
           table += "<tr><td>" + features[i].attributes.province + "</td><td>" + features[i].attributes.district + "</td><td align='right'>" + features[i].attributes.consumption + "</td><td align='right'>" + features[i].attributes.population + "</td><td align='right'>" + features[i].attributes.coverage + "</td><td><div style='width:30px;height:18px;background-color:" + features[i].attributes.color + "'></div></td></tr>";
           dataDownload.push({
                province: features[i].attributes.province,
                district_name: features[i].attributes.district,
                Consumption: features[i].attributes.consumption,
                Population: features[i].attributes.population,
                Vaccine_coverage: features[i].attributes.coverage
            });
        }
    }
    table += "</table>";
    $("#attributeGrid").append(table);
}

function districtCountGraph() {

    var ND = CalculatePercent(NoData, maximum);
    var cls1 = CalculatePercent(class1, maximum);
    var cls2 = CalculatePercent(class2, maximum);
    var cls3 = CalculatePercent(class3, maximum);
    var cls4 = CalculatePercent(class4, maximum);
    var cls5 = CalculatePercent(class5, maximum);

    pieArray.push({
        label: classesArray[0].description,
        value: ND,
        color: classesArray[0].color_code
    });
    pieArray.push({
        label: classesArray[1].description,
        value: cls1,
        color: classesArray[1].color_code
    });
    pieArray.push({
        label: classesArray[2].description,
        value: cls2,
        color: classesArray[2].color_code
    });
    pieArray.push({
        label: classesArray[3].description,
        value: cls3,
        color: classesArray[3].color_code
    });
    pieArray.push({
        label: classesArray[4].description,
        value: cls4,
        color: classesArray[4].color_code
    });
    pieArray.push({
        label: classesArray[5].description,
        value: cls5,
        color: classesArray[5].color_code
    });


    var revenueChart = new FusionCharts({
        type: 'column3D',
        renderAt: 'chart-container',
        width: '100%',
        height: '260px',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": prov_name+" - Vaccine Coverage Status",
                "subcaption":download,
                "showLabels": "1",
                "showlegend":"1",
                "slantLabels": '1',
                "labelDisplay":'Rotate',
                "rotateValues": '1',
                "enableLink": '1',
                "showValues": '1',
                "xAxisName": "",
                "numberSuffix": "%",
                "exportEnabled": "1",
                "theme": "fint"
            },
            "data": pieArray
        }
    });
    revenueChart.render("pie");
}