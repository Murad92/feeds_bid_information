@extends('layouts.app')
@section('content')
    <div style="margin: 1%; margin-bottom: 0">
        <a class="btn btn-info" href="{{ route('users.create') }}">Create User</a>
    </div>
    <div style="padding: 1%">
        {!! grid([
         'dataProvider' => $dataProvider,
         'class' => 'col-md-3',
         'rowsPerPage' => 10,
         'columns' => [
             'name',
             'email',
             //'role',
             [
                'title' => 'Action',
                'class' => App\Http\helper\CustomActionsColumn::class,
                'value' => '{delete} {edit}',
                'actionsUrls' => function($model) {
                    return [
                        'delete' => route('users.show',['id' => $model->id]),
                        'edit' => route('users.edit', ['id' => $model->id]),
                    ];
                }
            ]
         ]
        ])->render() !!}
    </div>
@endsection



