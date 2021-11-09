<?php
/**
 *
 */

class UpdateService
{

    public static function updateModel($model, $fields, $except)
    {
        try {
            foreach ($fields as $field => $value) {
                if ($field !== $except) {
                    $model->$field = $value;
                }
            }
            $model->save();
            return true;

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}