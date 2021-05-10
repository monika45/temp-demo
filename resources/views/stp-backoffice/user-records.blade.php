@extends('stp-backoffice.base')

@section('title', 'UsersRecord')

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <style>
        #data-records-table tr:hover {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12" style="padding: 0;">
                <div style="background: #fff;border-radius: 10px;">
                    <div class="box-header with-border">
                        <h4>List of Users</h4>
                    </div>
                    <div class="box-body">
                        <div class="dataTables_wrapper form-inline dt-bootstrap">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="data-records-table" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead>
                                        <tr role="row">
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Age</th>
                                            <th>Ethnic</th>
                                            <th>Blood Type</th>
                                            <th>Height</th>
                                            <th>Weight</th>
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
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script>




        // 表格
        $(document).ready(function() {
            //每页显示条数
            const pageLength = 10;
            $('#data-records-table').DataTable({
                'lengthChange': false,
                'searching': true,
                'ordering': false,
                'pageLength': pageLength,
                'processing': true,
                'serverSide': true,
                ajax(data, callback, settings) {
                    console.log(data);
                    let page = (data.start / pageLength) + 1;
                    requestApi({
                        url: '/admin/api/usersRecord',
                        data: {
                            page: page,
                            keyword: data.search.value
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
                            console.log(full)
                            let abnormal = full[8];
                            if (abnormal) {
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
                        item['ethnic_bg'],
                        item['blood_type'],
                        (item['height'] != 0) ? item['height'] + 'cm' : '',
                        (item['weight'] != 0) ? item['weight'] + 'kg' : '',
                        item['abnormal']
                    ]);
                }
                return d;
            }

            $('#data-records-table').on('click', 'tr', function() {
                let id = $(this).children('td').eq(0).text().replace('#', '');
                window.location.href = '/admin/user-information?id=' + id;
            });


        } );
    </script>
@endsection
