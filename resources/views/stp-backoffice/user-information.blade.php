@extends('stp-backoffice.base')

@section('title', 'Dashboard')

@section('style')
    @parent
    <style>
        .panel-wrapper {
            padding-bottom: 15px;
        }
        .box-wrapper {
            background: #fff;
            border-radius: 10px;
        }
        .box-body {
            background: #fafafa;
            padding: 20px;
        }
        .box-shadow-1 {
            box-shadow: 1px 1px 5px #eee;
        }
        /* User Information */
        .u-info-wrapper {
            display: flex;
            justify-content: space-between;
        }
        .u-info-wrapper .u-info-avatar {
            margin-right: 15px;
            border-radius: 4px;
            width: 140px;
            background-repeat: no-repeat;
            background-size: contain;
            background-position: center;
        }
        .u-info-wrapper .information-wrapper {
            display: flex;
            background: #fff;
            flex: 1;
            padding: 15px 20px;
            font-size: 1.7rem;
            border-radius: 4px;
        }
        .information-wrapper .info-left, .information-wrapper .info-right {
            width: 50%;
        }
        .information-wrapper .info-item {
            margin: 12px 0;
        }
        .info-item .info-val {
            font-weight: bold;
        }


        /* Healthcheck Records */
        .record-item-wrapper {
            border-radius: 10px;
            padding: 10px 0;
            margin-bottom: 20px;
            background: #fff;
        }
        .record-item-wrapper:last-child {
            margin-bottom: 0;
        }
        .record-item-wrapper .title:after, .record-item-wrapper .record:after {
            content: '';
            display: block;
            clear: both;
        }
        .record-item-wrapper .title {
            border-bottom: 1px dashed #eaf1f9;
            padding-bottom: 10px;
            padding-left: 15px;
            padding-right: 15px;
        }
        .record-item-wrapper .record {
            padding-top: 10px;
            padding-left: 15px;
            padding-right: 15px;
        }
        .record-item-wrapper .record span {
            line-height: 3rem;
        }
        .record-item-wrapper .record .fa {
            margin-right: 20px;
            font-size: 1.8rem;
            cursor: pointer;
        }
        .record-item-wrapper .record-title {
            font-size: 2rem;
        }
        .border-left-blue {
            border-left: 8px solid #81ced8;
        }
        .border-left-purple {
            border-left: 8px solid #8475f4;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 panel-wrapper">
                <div class="box-wrapper">
                    <div class="box-header with-border">
                        <h4>User Information</h4>
                    </div>
                    <div class="box-body">
                        <div class="u-info-wrapper">
                            <div class="u-info-avatar box-shadow-1" style="background-image: url({{$user['avatar']}})"></div>
                            <div class="information-wrapper box-shadow-1">
                                <div class="info-left">
                                    <div class="info-item">
                                        <span class="title">ID:</span>
                                        <span class="info-val">#{{ $user['id'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="title">Name:</span>
                                        <span class="info-val">{{ $user['name'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="title">Gender:</span>
                                        <span class="info-val">{{ $user['gender'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="title">Age:</span>
                                        <span class="info-val">{{ $user['age'] }}</span>
                                    </div>
                                </div>
                                <div class="info-right">
                                    <div class="info-item">
                                        <span class="title">Ethnic:</span>
                                        <span class="info-val">{{ $user['ethnic_bg'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="title">Blood Type:</span>
                                        <span class="info-val">{{ $user['blood_type'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="title">Height:</span>
                                        <span class="info-val">{{ $user['height'] ? $user['height'] . 'cm': '' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="title">Weight:</span>
                                        <span class="info-val">{{ $user['weight'] ? $user['weight'] . 'kg' : ''}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 panel-wrapper">
                <div class="box-wrapper">
                    <div class="box-header with-border">
                        <h4>Body Temperature</h4>
                    </div>
                    <div class="box-body" style="padding: 5px;">
                        <div id="todayBodyTemperature" class="temperature-wrapper" style="height: 216px;">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 panel-wrapper" style="">
                <div class="box-wrapper">
                    <div class="box-header with-border">
                        <h4>Healthcheck Records</h4>
                    </div>
                    <div class="box-body">
                        <div class="record-item-wrapper border-left-blue box-shadow-1">
                            <div class="title">
                                <span class="pull-left">1920-01-01 00:00:00</span>
                                <span class="pull-right"># report number</span>
                            </div>
                            <div class="record">
                                <span class="record-title pull-left">Mood Assessment</span>
                                <span class="pull-right fa fa-file-text"></span>
                                <span class="pull-right fa fa-download"></span>
                            </div>
                        </div>
                        <div class="record-item-wrapper border-left-purple box-shadow-1">
                            <div class="title">
                                <span class="pull-left">1920-01-01 00:00:00</span>
                                <span class="pull-right"># report number</span>
                            </div>
                            <div class="record">
                                <span class="record-title pull-left">Nutrition Assessment</span>
                                <span class="pull-right fa fa-file-text"></span>
                                <span class="pull-right fa fa-download"></span>
                            </div>
                        </div>
                        <div class="record-item-wrapper border-left-blue box-shadow-1">
                            <div class="title">
                                <span class="pull-left">1920-01-01 00:00:00</span>
                                <span class="pull-right"># report number</span>
                            </div>
                            <div class="record">
                                <span class="record-title pull-left">Nutrition Assessment</span>
                                <span class="pull-right fa fa-file-text"></span>
                                <span class="pull-right fa fa-download"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 panel-wrapper" style="">
                <div class="box-wrapper">
                    <div class="box-header with-border">
                        <h4>Blood Pressure</h4>
                    </div>
                    <div class="box-body" style="padding: 5px;">
                        <div id="bloodPressure" class="blood-pressure-wrapper" style="height: 216px;">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 panel-wrapper" style="">
                <div class="box-wrapper">
                    <div class="box-header with-border">
                        <h4>Pulse</h4>
                    </div>
                    <div class="box-body" style="padding: 5px;">
                        <div id="Pulse" class="pulse-wrapper" style="height: 216px;">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.0.2/dist/echarts.min.js"></script>


    <script>

        $(function() {
            let todayBodyTemperatureDom = document.getElementById('todayBodyTemperature');
            let todayBodyTemperatureChart = echarts.init(todayBodyTemperatureDom);
            let option = {
                color: ['#a2a4e9'],
                tooltip: {
                    trigger: 'axis'
                },
                toolbox: {
                    show: true
                },
                xAxis: {
                    type: 'time',
                    name: 'Time',
                },
                yAxis: {
                    type: 'value',
                    // min: '30'
                },
                series: [{
                    name: 'Body Temperature',
                    data: [
                        // [time, temperature]
                        // ['2012-03-01 05:06', '37.5'],
                    ],
                    type: 'line',
                    symbol: 'none',
                    // smooth: true
                }]
            };

            // 一天的温度数据
            dailyTemperatures('{{date('Y-m-d')}}');

            // 获取一天的温度数据
            function dailyTemperatures(day)
            {
                todayBodyTemperatureChart.showLoading();
                requestApi({
                    url: '{{config("app.url")}}/stp/daily-temperatures',
                    userToken: '{{$token}}',
                    data: {
                        day: day
                    },
                    callback: function(res) {
                        todayBodyTemperatureChart.hideLoading();
                        let data = [];
                        for (let i = 0; i < res.list.length; i++) {
                            let time = day + ' ' + res.list[i].time;
                            let temperature = res.list[i].data.replace('℃', '');
                            data.push([time, temperature]);
                        }
                        option.series[0].data = data;
                        option.xAxis.min = day + ' 00:00';
                        option.xAxis.max = day + ' 23:59';
                        todayBodyTemperatureChart.setOption(option);
                    },
                    contentWrapId: 'todayBodyTemperatureWrap',
                    showLoading: false
                });

            }


            // 血压
            let bloodPressure = echarts.init(document.getElementById('bloodPressure'));
            let optionBloodPressure = {
                color: ['#a2ead8'],
                tooltip: {
                    trigger: 'axis'
                },
                toolbox: {
                    show: true
                },
                xAxis: {
                    type: 'time',
                    name: 'Time',
                },
                yAxis: {
                    type: 'value',
                    // min: '30'
                },
                series: [{
                    name: 'Blood Pressure-Fake Data',
                    data: [
                        // [time, blood-pressure]
                        // ['2012-03-01 05:06', '30'],
                        // ['2012-03-01 10:06', '50'],
                        // ['2012-03-01 14:06', '80'],
                        // ['2012-03-01 18:06', '90'],
                    ],
                    type: 'line',
                    symbol: 'none',
                    smooth: true
                }]
            };
            bloodPressure.setOption(optionBloodPressure);


            // 心跳
            let pulse = echarts.init(document.getElementById('Pulse'));
            let optionPulsePressure = {
                color: ['#7873b5'],
                tooltip: {
                    trigger: 'axis'
                },
                toolbox: {
                    show: true
                },
                xAxis: {
                    type: 'time',
                    name: 'Time',
                },
                yAxis: {
                    type: 'value',
                    // min: '30'
                },
                series: [{
                    name: 'Pulse-Fake Data',
                    data: [
                        // [time, blood-pressure]
                        // ['2012-03-01 05:06:01', '30'],
                        // ['2012-03-01 05:06:02', '31'],
                        // ['2012-03-01 05:06:20', '33'],
                        // ['2012-03-01 05:06:30', '30'],
                        // ['2012-03-01 05:06:30', '33'],
                        // ['2012-03-01 10:06:01', '30'],
                        // ['2012-03-01 10:06:02', '31'],
                        // ['2012-03-01 10:06:20', '33'],
                        // ['2012-03-01 10:06:30', '30'],
                        // ['2012-03-01 10:06:30', '33'],
                    ],
                    type: 'line',
                    symbol: 'none',
                    smooth: true
                }]
            };
            pulse.setOption(optionPulsePressure);

        });








    </script>
@endsection
