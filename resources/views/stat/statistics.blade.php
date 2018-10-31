@extends('layouts.custom')
@section('content')
<body>
<div class="flex-center position-ref full-height" style="width: 1800px">
    {{ Form::open([
    'url' => '/stat',
    'method' => 'get'
    ]) }}
    <div class="row" style="margin-left: 10px">
        <div class="col-md-2">
                <label for="countries">COUNTRY</label>
                <select name="countries[]" class="form-control" multiple="" id="tags">
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
<div style="margin-top: 3%; margin-bottom: 3%">
</div>
<div class="tab">
    <button id="defaultOpen" class="tablinks" onclick="openData(event, 'average_bid')">AVERAGE BID</button>
    <button class="tablinks" onclick="openData(event, 'max_bid')">MAX BID</button>
    <button class="tablinks" onclick="openData(event, 'min_bid')">MIN BID</button>
    <button class="tablinks" onclick="openData(event, 'coverage')">COVERAGE</button>
</div>
@foreach($dataType as $key => $type)
    <?$i=0?>
    <div id="{!! $type !!}" class="tabcontent scrolldiv">
        <table border="1">
            <thead>
            <tr style="height: 50px;">
                <th>COUNTRY</th>
                @foreach($feeds as $feedName)
                    @if($feedName != 'LizardTrack')
                        <th>{!! $feedName !!}</th>
                    @endif
                @endforeach
                @if($type === 'average_bid')
                    <th>Total</th>
                @elseif($type === 'min_bid')
                    <th>Min Bid</th>
                @else
                    <th>Max Bid</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach($data as $countr => $value)
                <? $i++; $cov = 0; $count = 0; $maximum = 0; $minimum = 1000;
                $background = '#CCCCCC';
                if($i%2 != 0) {
                    $background = '#FFB935';
                }
                 ?>
                <tr style="background: <?=$background?>">
                    <th>{!! isset($country[$countr]) ? $country[$countr].' ('.$countr.')' :  $countr!!}</th>
                    @foreach($feeds as $feedName)
                        @if($feedName != 'LizardTrack')
                            <?php $bid = isset($value[$feedName][$key]) ? number_format((float)$value[$feedName][$key],6,'.','') : '';
                            $count++;
                            $cov = $cov + (float)$bid;
                            if((float)$bid > $maximum) {
                                $maximum = (float)$bid;
                            }
                            if((float)$bid < $minimum) {
                                $minimum = (float)$bid;
                            }
                            ?>
                            <td>{!! $bid !!}</td>
                        @endif
                    @endforeach
                    @if($type === 'average_bid')
                        <th>{!! $cov/$count !!}</th>
                    @elseif($type === 'min_bid')
                        <th>{!! $minimum !!}</th>
                    @else
                        <th>{!! $maximum !!}</th>
                    @endif

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endforeach
<div id="coverage" class="tabcontent scrolldiv">
    <table border="1">
        <thead>
        <tr>
            <th rowspan="2">COUNTRY</th>
            @foreach($feeds as $feedName)
                <th style="text-align: center" colspan="3">{!! $feedName !!}</th>
            @endforeach
        </tr>
        <tr>
            @foreach($feeds as $feedName)
                <th style="top: 26px">request</th>
                <th style="top: 26px">response</th>
                <th style="top: 26px">coverage</th>
            @endforeach
        </tr>
        </thead>
        @foreach($coverage as $c => $value)
            <? $i++;
            $background = '#CCCCCC';
            if($i%2 == 0) {
                $background = '#FFB935';
            }
            ?>
            <tr style="background: <?=$background?>">
                <th>{!! $country[$c].' ('.$c.')' !!}</th>
                @foreach($feeds as $feedName)
                    <td>{!! isset($value[$feedName]['request']) ? $value[$feedName]['request'] : 0  !!}</td>
                    <td>{!! isset($value[$feedName]['response']) ? $value[$feedName]['response'] : 0  !!}</td>
                    <td>{!! (isset($value[$feedName]['request'])&& $value[$feedName]['request'] != 0)   ? (round($value[$feedName]['response']/$value[$feedName]['request']*100, 2)) .'%' : 0 .'%'  !!}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
</div>
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
        $("#tags").select2({
            allowClear: true
        });
    </script>

@endsection



