@extends('stp-backoffice.base')

@section('title', 'Dashboard')

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <style>
        .statistic-wraper {
            /*border: 1px solid #fff;*/
            height: 220px;
            padding: 0;
        }
        .statistic-info-box {
            background: #fff;
            border-radius: 10px;
        }
        .statistic-info-box .box-body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 157px;
        }
        .statistic-info-box .box-body .today-num {
            height: 4rem;
            line-height: 4rem;
            color: #81ced8;
            font-size: 4rem;
            margin-right: 22px;
        }
        .statistic-info-box .box-body .total-num div{
            line-height: 2rem;
            font-size: 1.5rem;
        }
        .statistic-info-box .box-body .total-num .total-num-title {
            color: #aaa;
        }

        .abnormal-cities-wrap, .temperature-map-wrap {
            background: #fff;
            border-radius: 10px;
        }

        /*    地图的颜色说明 */
        .temperature-range-wrapper {
            display: flex;
            position: absolute;
            top: 100px;
            left: 0;
            width: 40px;
            background: rgba(255,255,255,0.99);
            padding: 25px 57px 10px 10px;
            border-radius: 4px;
        }
        .temperature-range-wrapper .colors-wrapper div {
            width: 8px;
            height: 25px;
            margin-bottom: 5px;
        }
        .temperature-range-wrapper .text-wrapper {
            margin-top: -10px;
        }
        .temperature-range-wrapper .text-wrapper span {
            display: block;
            height: 29px;
            vertical-align: top;
            padding-left: 2px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6" style="height: 600px;padding-left: 0;padding-top: 0;">
                <div>
                    <div class="col-md-6 statistic-wraper">
                        <div class="statistic-info-box" style="margin-right: 8px;">
                            <div class="box-header with-border">
                                <h4>New Users Registered Today</h4>
                            </div>
                            <div class="box-body">
                                <div class="today-num">{{ $today_user }}</div>
                                <div class="total-num">
                                    <div class="total-num-title">Total Users</div>
                                    <div class="total-num-value">{{ $total_user }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 statistic-wraper">
                        <div class="statistic-info-box" style="margin-left: 8px;">
                            <div class="box-header with-border">
                                <h4>New Temperature Data Entered</h4>
                            </div>
                            <div class="box-body">
                                <div class="today-num">{{ $today_data }}</div>
                                <div class="total-num">
                                    <div class="total-num-title">Total Data Recorded</div>
                                    <div class="total-num-value">{{ $total_data }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="height: 335px;margin-top: 15px;padding: 0;">
                    <div class="abnormal-cities-wrap">
                        <div class="box-header with-border">
                            <h4>Top Cities with Abnormal Temperature Data</h4>
                        </div>
                        <div class="box-body">
                            <div id="abnormal-temperature-cities" style="height: 268px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6" style="height: 600px;padding: 0;">
                <div class="temperature-map-wrap">
                    <div class="box-header with-border">
                        <h4>Temperature Map</h4>
                    </div>
                    <div class="box-body" style="position: relative;">
                        <div id="temperature-map" style="width: 100%;height: 504px;"></div>
                        <div class="temperature-range-wrapper">
                            <div class="colors-wrapper">
                                <div style="background: #ff9a7f;"></div>
                                <div style="background: #fdd396;"></div>
                                <div style="background: #ffefaf;"></div>
                                <div style="background: #a0ecff;"></div>
                                <div style="background: #7fb8ff;"></div>
                            </div>
                            <div class="text-wrapper">
                                <span>42℃</span>
                                <span>39℃</span>
                                <span>38℃</span>
                                <span>37.3℃</span>
                                <span>36℃</span>
                                <span><35℃</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--            Data Records                 --}}
        <div class="row">
            <div class="col-md-12" style="padding: 0;">
                <div style="background: #fff;border-radius: 10px;">
                    <div class="box-header with-border">
                        <h4>Data Records</h4>
                    </div>
                    <div class="box-body">
                        <div class="dataTables_wrapper form-inline dt-bootstrap">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="data-records-table" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead>
                                        <tr role="row">
                                            <th rowspan="1" colspan="1">ID</th>
                                            <th rowspan="1" colspan="1">Name</th>
                                            <th rowspan="1" colspan="1">Gender</th>
                                            <th rowspan="1" colspan="1">Age</th>
                                            <th rowspan="1" colspan="1">Time</th>
                                            <th rowspan="1" colspan="1">Location</th>
                                            <th rowspan="1" colspan="1">Body Temperature</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.0.2/dist/echarts.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    {{--地图--}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC6DVMCU4uiM0ZWF_dJiblu8mrODvi8NQw&callback=initMap&language=en" type="text/javascript"></script>


    <script>
        // 条形图
        requestApi({
            url: '/admin/api/abnormalTopCities',
            showLoading: false,
            callback: function(res) {
                let abnormalCitiesChart = echarts.init(document.getElementById('abnormal-temperature-cities'));
                let abnormalCitiesOption = {
                    color: ['#8475f5'],
                    tooltip: {
                        trigger: 'axis'
                    },
                    toolbox: {
                        show: true
                    },
                    xAxis: {
                        type: 'category',
                        data: (function() {
                            return res.map(v => v.location);
                        })()
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [{
                        data: (function() {
                            return res.map(v => v.num);
                        })(),
                        type: 'bar',
                        label: {

                        }
                    }]
                }
                abnormalCitiesChart.setOption(abnormalCitiesOption);
            }
        })



        // 地图
        const mapStyle1 = [
            {
                "stylers": [
                    {
                        "color": "#ffffff"
                    },
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#ffffff"
                    },
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#8b8989"
                    }
                ]
            },
            {
                "featureType": "administrative",
                "stylers": [
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "administrative.country",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "administrative.country",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#000000"
                    }
                ]
            },
            {
                "featureType": "administrative.land_parcel",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "administrative.locality",
                "stylers": [
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "administrative.locality",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#615f5f"
                    },
                    {
                        "visibility": "on"
                    }
                ]
            },
            {
                "featureType": "administrative.neighborhood",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "administrative.province",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#000000"
                    },
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "water",
                "stylers": [
                    {
                        "color": "#ebebeb"
                    },
                    {
                        "visibility": "on"
                    }
                ]
            }
        ];
        // console.log(temperatureMap.filter(item => item.temperature_range != 1 && item.temperature_range != 7));
        // const temperatureMap = [
        //     {
        //         location: 'chicago',
        //         coordinate: '41.878,-87.629',
        //         temperature_range: 2,
        //         num: 1000
        //     },
        //     {
        //         location: 'losangeles',
        //         coordinate: '34.052,-118.243',
        //         temperature_range: 4,
        //         num: 100000
        //     },
        //     {
        //         location: 'newyork',
        //         coordinate: '40.714,-74.005',
        //         temperature_range: 5,
        //         num: 5
        //     }
        // ];


        function initMap() {
            // 调接口获取数据
            requestApi({
                url: '/admin/api/temperatureMapData',
                showLoading: false,
                callback: function(res) {
                    console.log(res);
                    const temperatureMap = res;
                    const firstCoordinate = temperatureMap[0].coordinate.split(',');
                    const mapCenter = { lat: parseFloat(firstCoordinate[0]), lng: parseFloat(firstCoordinate[1]) };
                    const circleRange = {
                        '2': { color: '#7fb8ff', text: '35℃~36℃'},
                        '3': { color: '#a0ecff', text: '36℃~37.3℃'},
                        '4': { color: '#ffefaf', text: '37.3℃~38℃'},
                        '5': { color: '#fdd396', text: '38℃~39℃'},
                        '6': { color: '#ff9a7f', text: '39℃~42℃'}
                    };


                    map = new google.maps.Map(document.getElementById("temperature-map"), {
                        center: mapCenter,
                        // 1: World 5: Landmass/continent 10: City 15: Streets 20: Buildings
                        zoom: 5,
                        styles: mapStyle1,
                        streetViewControl: false,
                        mapTypeControl: false,
                        fullscreenControl: false
                    });

                    temperatureMap.forEach((city, index)=>{
                        let coordinate = city.coordinate.split(',');
                        let center = { lat: parseFloat(coordinate[0]), lng: parseFloat(coordinate[1]) };
                        const cityCircle = new google.maps.Circle({
                            strokeColor: circleRange[city.temperature_range].color,
                            strokeOpacity: 0,
                            strokeWeight: 2,
                            fillColor: circleRange[city.temperature_range].color,
                            fillOpacity: 0.8,
                            map,
                            center: center,
                            radius: Math.sqrt(Math.sqrt(city.num)) * 10000,
                        });
                        const infowindow = new google.maps.InfoWindow();
                        const contentString = '<div id="content"><h3>' + city.location + '</h3> <span style="color: ' + circleRange[city.temperature_range].color + '">' + circleRange[city.temperature_range].text + '</span><br/>Numbers：' + city.num + '</div>';
                        infowindow.setContent(contentString);
                        infowindow.setPosition(center);
                        // 默认打开前20个城市的infowindow
                        if (index < 20) {
                            infowindow.open(map);
                        }
                        google.maps.event.addListener(cityCircle, 'click', function() {
                            // console.log(city);
                            //点击circle显示infoWindow
                            // console.log(cityCircle.getRadius());
                            // console.log(cityCircle.getCenter().lat());
                            // console.log(cityCircle.getCenter().lng());
                            infowindow.open(map);
                        });
                    });
                }
            });




        }

        // 表格
        $(document).ready(function() {
            //每页显示条数
            const pageLength = 10;
            $('#data-records-table').DataTable({
                'lengthChange': false,
                'searching': false,
                'ordering': false,
                'pageLength': pageLength,
                'processing': true,
                'serverSide': true,
                ajax(data, callback, settings) {
                    console.log(data);
                    let page = (data.start / pageLength) + 1;
                    requestApi({
                        url: '/admin/api/dataRecords',
                        data: {
                            page: page,
                        },
                        showLoading: false,
                        callback: (res) => {
                            setTimeout( function () {
                                let out = {};
                                out.draw = data.draw;
                                out.recordsTotal = res.total;
                                out.recordsFiltered = res.total;
                                out.data = formatTabelData(res.data);
                                callback( out );
                            }, 50 );
                        }
                    });
                },
                columnDefs: [
                    {
                        'targets': [1],
                        'render': function(data, type, full) {
                            let temperature = full[6];
                            temperature = temperature.replace('℃', '');
                            if (Number(temperature) > 37.2) {
                                return '<div style="display: flex;flex-direction: row;align-items: center;"><span>'+data+'</span><span style="background: #f00;width: 10px;height: 10px;border-radius: 5px;display: block;margin-left: 10px;"></span></div>';
                            }
                            return data;

                        }
                    }
                ]




            });

            // 格式化接口返回的数据，用于表格显示
            function formatTabelData(data) {
                let d = [];
                for (let i = 0; i < data.length; i++) {
                    let item = data[i];
                    d.push([
                        '#'+item['id'],
                        item['name'],
                        item['gender'],
                        item['age'],
                        item['day'] + ' ' + item['time'],
                        item['location'],
                        item['data'] + '℃'
                    ]);
                }
                return d;
            }


        } );
    </script>
@endsection
