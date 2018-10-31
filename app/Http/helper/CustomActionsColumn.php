<?php
/**
 * Created by PhpStorm.
 * User: murad
 * Date: 17.10.2018
 * Time: 17:14
 */

namespace App\Http\helper;


use Illuminate\Support\Facades\Route;
use Woo\GridView\Columns\ActionsColumn;

class CustomActionsColumn extends ActionsColumn
{
    public function basicActions()
    {
        return [
            'show' => function($model) {
                return '<a href="' . call_user_func($this->actionsUrls, $model)['show'] . '">Show</a>';
            },
            'edit' => function($model) {
                return '<a href="' . call_user_func($this->actionsUrls, $model)['edit'] . '">Edit</a>';
            },
            'delete' => function($model) {
                return '<a onclick="return confirm(\'Are you sure to delete this user\')" href="' . call_user_func($this->actionsUrls, $model)['delete'] . '">Remove</a>';
            },
        ];

    }

    public function renderValue($row)
    {
        $result = $this->value;

        $actions = array_merge($this->basicActions(), $this->additionalActions);

        foreach ($actions as $key => $action) {
            if (strpos($result, '{' . $key . '}') === false) {
                continue;
            }

            $result = str_replace('{' . $key . '}', $action($row), $result);
        }

        return $result;
    }
}