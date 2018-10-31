@extends('layouts.custom')
@section('content')
<body>
<div class="flex-center position-ref full-height" style="width: 1800px">
    {{ Form::open([
    'url' => '/ecpm',
    'method' => 'get'
    ]) }}
    <div class="row" style="margin-left: 10px">
        <div class="col-md-2">
            <label for="countries">Country</label>
            <select name="countries[]" class="form-control" multiple="" id="countries">
                @foreach($country as $key => $c)
                    @if($countries && in_array($key,$countries))
                        <option value="{!! $key !!}" selected="selected">{!! $c.'('.$key.')' !!}</option>
                    @else
                        <option value="{!! $key !!}">{!! $c.'('.$key.')' !!}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="os_types">Os Type</label>
            <select name="os_types[]" class="form-control" multiple="" id="os_types">
                @foreach($osTypes as $name => $key)
                    @if($osType && in_array($key,$osType))
                        <option value="{!! $key !!}" selected="selected">{!! $name !!}</option>
                    @else
                        <option value="{!! $key !!}">{!! $name !!}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
                <label for="impression_limit">Impression limit</label>
                <select name="impression_limit" class="form-control" >
                    @foreach($impressionLimit as  $limits)
                        @if($limit == $limits)
                            <option  selected="selected">{!! $limits !!}</option>
                        @else
                            <option>{!! $limits !!}</option>
                        @endif
                    @endforeach
                </select>
        </div>
        <div class="col-md-2">
            <label for="start_date">Start date</label>
            <input class="form-control" value = "{!! $startDate !!}" name="start_date" type="text" id="from" readonly>
        </div>
        <div class="col-md-2">
            <label for="end_date">End date</label>
            <input class="form-control" value = "{!! $endDate !!}" name="end_date" type="text" id="to" readonly>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <button type="submit" class="btn btn-default buttonCustom">
                <span class="glyphicon glyphicon-search"></span> Search
            </button>
        </div>
    </div>
    {{ Form::close() }}

</div>
<div class="scrolldiv">
    <table border="1">
        <thead>
        <tr style="height: 50px; background: rgba(15,39,70,0.38);">
            <th rowspan="2" style="width: content-box; padding-left: 19px; padding-right: 19px">DATE</th>
            @foreach($feeds as $feedName)
                <th style="text-align: center" colspan="3">{!! $feedName !!}</th>
            @endforeach
        </tr>
        <tr>
            @foreach($feeds as $feedName)
                <th style="top: 48px">Revenue</th>
                <th style="top: 48px">Impression</th>
                <th style="padding-left: 15px; padding-right: 15px; top: 48px">eCPM</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        <? $color = 0;?>
        @foreach($data as $dat)
            <tr>
                <td style="background: rgba(15,39,70,0.38)" >{!! $dat['x'] !!}</td>
                <?$background = 'white';
                if($color % 2) {
                    $background = 'rgba(70,44,20,0.38)';
                }
                $color++;?>
                @foreach($feeds as $feedName)
                    <td style="background: {!! $background !!}">{!! isset($dat['revenue_'.$feedName]) ? $dat['revenue_'.$feedName] : 0!!}</td>
                    <td style="background: {!! $background !!}">{!! isset($dat['show_'.$feedName]) ? $dat['show_'.$feedName] : 0!!}</td>

                    <td style="background: {!! $background !!}">{!! isset($dat['show_'.$feedName]) && isset($dat['revenue_'.$feedName]) && $dat['show_'.$feedName] !=0 ? ($limit <= $dat['show_'.$feedName]) ? round(($dat['revenue_'.$feedName]/$dat['show_'.$feedName])*1000,3,PHP_ROUND_HALF_DOWN). ' $ ' : '0 $' : '0 $'!!}</td>
                @endforeach

            </tr>
        @endforeach
        </tbody>
    </table>
</div>


    {{--<div class="tabcontent scrolldiv">--}}

    {{--</div>--}}
</body>
@endsection
@section('script')
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    {{--<script src="https://code.jquery.com/jquery-1.12.4.js"></script>--}}
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $( function() {
            $("#to").datepicker({ dateFormat: 'yy-mm-dd' });
            $("#from").datepicker({ dateFormat: 'yy-mm-dd' });
        });
    </script>
    <script>
        $("#countries").select2({
            allowClear: true
        });
        $("#os_types").select2({
            allowClear: true
        });
    </script>

@endsection



