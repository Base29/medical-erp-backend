<?php
/**
 *
 */

namespace App\Helpers;

use Exception;

class UpdateService
{

    public static function updateModel($model, $fields, $excludeField)
    {

        try {
            foreach ($fields as $field => $value) {
                if ($field !== $excludeField) {
                    $model->$field = $value;
                }
            }
            $model->save();
            return true;

        } catch (Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}