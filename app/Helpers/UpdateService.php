<?php
/**
 *
 */

namespace App\Helpers;

class UpdateService
{

    public static function updateModel($model, $fields, $excludeField)
    {
        //TODO: Needs enhancement to allow updating files on S3.
        try {
            foreach ($fields as $field => $value) {
                if ($field !== $excludeField) {
                    $model->$field = $value;
                }
            }
            $model->save();
            return true;

        } catch (\Exception$e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}