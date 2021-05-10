<div class="container-fluid">
    <div id="todayBodyTemperatureWrap" class="today-body-temperature" style="background: #fff;">
        {{--  今天的体温  --}}
        <div id="todayBodyTemperature" style="width: 100%;height: 350px;"></div>
    </div>
    <div class="datas-box">
        {{--  历史数据  --}}


    </div>
    <div class="monitor-data-loading hide">
        <div class="center-block text-info" style="text-align: center;">
            <div class="loading-tips hide"><i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;Loading...</div>
            <div class="more-tips hide" style="cursor: pointer;">点击加载更多~</div>
        </div>
    </div>


</div>



<script src="https://cdn.jsdelivr.net/npm/echarts@5.0.2/dist/echarts.min.js"></script>
<script>


    $(function() {
        let monitor_data_page = 0;
        let monitor_loading_status = 'more';//more：更多 loading:加载中 no-more：没有更多数据了

        let todayBodyTemperatureDom = document.getElementById('todayBodyTemperature');
        let todayBodyTemperatureChart = echarts.init(todayBodyTemperatureDom);
        let option = {
            title: {
              text: 'Today'
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['Body Temperature']
            },
            toolbox: {
                show: true
            },
            xAxis: {
                type: 'time',
                name: 'Time',
                // min: '2012-03-01 00:00',
                // max: '2012-03-01 23:59'
            },
            yAxis: {
                type: 'value',
                name: 'Body Temperature(℃)',
                min: '30'
            },
            series: [{
                name: 'Body Temperature',
                data: [
                    // [time, temperature]
                    // ['2012-03-01 05:06', '37.5'],
                ],
                type: 'line',
                smooth: true
            }]
        };

        // 一天的温度数据
        dailyTemperatures('{{$today}}');

        // 数据列表
        monitor_data_page = 0;
        monitorDatas();

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

        // 获取下一页数据
        function monitorDatas()
        {
            if (monitor_loading_status != 'more') {
                return;
            }
            monitor_data_page += 1;
            console.log('page:' + monitor_data_page);
            changeLoadingStatus('loading');
            requestApi({
                url: '{{config("app.url")}}/stp/monitor-datas',
                userToken: '{{$token}}',
                data: {
                    page: monitor_data_page
                },
                callback: function(res) {
                    if (!res) {
                        changeLoadingStatus('no-more');
                        return;
                    }
                    if (res.length >= 10) {
                        changeLoadingStatus('more');
                    } else {
                        changeLoadingStatus('no-more');
                    }
                    showDatas(res);

                },
                showLoading: false
            });

        }

        function changeLoadingStatus(status)
        {
            if (status === 'loading') {
                monitor_loading_status = 'loading';
                $('.monitor-data-loading').removeClass('hide');
                $('.monitor-data-loading .loading-tips').removeClass('hide');
                $('.monitor-data-loading .more-tips').addClass('hide');
            }
            if (status === 'more') {
                monitor_loading_status = 'more';
                $('.monitor-data-loading').removeClass('hide');
                $('.monitor-data-loading .loading-tips').addClass('hide');
                $('.monitor-data-loading .more-tips').removeClass('hide');
            }
            if (status === 'no-more') {
                monitor_loading_status = 'no-more';
                $('.monitor-data-loading').addClass('hide');
                $('.monitor-data-loading .loading-tips').addClass('hide');
                $('.monitor-data-loading .more-tips').addClass('hide');
            }
        }

        $('.more-tips').click(function() {
            monitorDatas();
        });

        // 渲染列表数据
        function showDatas(datas)
        {

            let wraper = $('.datas-box');
            let tpl = $(`
                    <div class="col-md-6">
                        <div class="box box-show daily-wrap">
                            <div class="box-header with-border" title="点击在图表中查看">
                                <h3 class="box-title item-day"></h3>
                            </div>
                            <div class="box-body">
                                <ul class="nav nav-stacked">

                                </ul>
                            </div>
                        </div>
                    </div>`);
            let liobj = $('<li><span class="item-type"></span><span class="item-data"></span><span class="item-time pull-right"></span></li>');
            for (let i = 0; i< datas.length; i++) {
                let daily_tpl = tpl.clone();
                daily_tpl.find('.item-day').text(datas[i].day).attr('data-day', datas[i].day);
                if (datas[i].day === option.title.text) {
                    daily_tpl.find('.box-show').removeClass('box-show').addClass('box-info');
                }
                let daily_data = datas[i].list;
                for (let j = 0; j < daily_data.length; j++) {
                    let li_tpl = liobj.clone();
                    $(li_tpl.children('.item-type')[0]).text(dataTypeDesc(daily_data[j].type));
                    $(li_tpl.children('.item-data')[0]).text(daily_data[j].data);
                    $(li_tpl.children('.item-time')[0]).text(daily_data[j].time);
                    if (daily_data[j].variation != '0' && daily_data[j].variation != '') {
                        let variation = daily_data[j].variation;
                        variation = variation.replace('+', '↑');
                        variation = variation.replace('-', '↓');
                        $(li_tpl.children('.item-data')[0]).after('<span class="text-info">' + variation + '</span>')
                    }
                    daily_tpl.find('.nav').append(li_tpl);
                }
                wraper.append(daily_tpl);
            }


        }

        // 点击历史数据的天数，在echarts中显示点击日期的折线图
        $('.datas-box').on('click', '.daily-wrap .box-header', function() {
            let day = $(this).find('.item-day').attr('data-day');
            $(this).parent('.daily-wrap').parent().siblings().children().removeClass('box-info');
            $(this).parent('.daily-wrap').removeClass('box-show').addClass('box-info');
            option.title.text = day;
            dailyTemperatures(dateFormat(day));

        });


        function dataTypeDesc(type)
        {
            if (type === 'temperature') {
                return 'Body Temperature';
            }
            return '-';
        }

        function dateFormat(daystr)
        {
            let date = new Date();
            if (daystr !== 'Today') {
                date = new Date(daystr);
            }
            return [date.getFullYear(), date.getMonth() + 1, date.getDate()].map(n => {
                n = n.toString();
                return n[1] ? n : '0' + n;
            }).join('-');
        }









    });






</script>

<style>
    /*.today-body-temperature {*/
    /*    width: 100%;*/
    /*    height: 350px;*/
    /*    background: #fff;*/
    /*}*/
    .datas-box {
        margin-top: 15px;
    }
    .daily-wrap .box-header {
        cursor: pointer;
    }
    .item-type {
        margin-right: 20%;
    }
    .item-data {
        margin-right: 15px;
    }

    .hide{
        display: none;
    }


</style>
