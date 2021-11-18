<?php
/**
 * Response Messages
 *
 * Class for building response messages
 */

namespace App\Helpers;

class ResponseMessage
{

    // Message for records not found
    public static function notFound($model, $data, $switch)
    {
        $handle = $switch ? 'email' : 'ID/name';
        return $model . ' with ' . $handle . ' ' . $data . ' not found';
    }

    // Message for invalid credentials
    public static function invalidCredentials()
    {
        return 'Invalid Credentials';
    }

    // Message for logout
    public static function logout()
    {
        return 'Logged out successfully';
    }

    // Message for invalid user
    public static function invalidToken()
    {
        return 'Invalid Token or User';
    }

    // Message for password reset successful
    public static function passwordResetSuccess()
    {
        return 'Password reset successful';
    }

    // Message for password reset link
    public static function passwordResetLink($email)
    {
        return 'Reset password link sent on your email id ' . $email;
    }

    // Message for not allowed to update
    public static function notAllowedToUpdate($model)
    {
        return 'You are not allowed to update this ' . $model;
    }

    // Message for not allowed to delete
    public static function notAllowedToDelete($model)
    {
        return 'You are not allowed to delete this ' . $model;
    }

    // Message for not allowed to view
    public static function notAllowedToView($model)
    {
        return 'You are not allowed to view ' . $model;
    }

    // Message for deleted successfully
    public static function deleteSuccess($model)
    {
        return $model . ' deleted successfully';
    }

    // Message for already exists
    public static function alreadyExists($model)
    {
        return $model . ' already exists';
    }

    // Message already assigned
    public static function alreadyAssigned($permissionOrRole, $assignee)
    {
        return $permissionOrRole . ' already assigned to ' . $assignee;
    }

    // Message for assigned
    public static function assigned($permissionOrRole, $assignee)
    {
        return $permissionOrRole . ' assigned to ' . $assignee;
    }

    // Message for not assigned
    public static function notAssigned($permissionOrRole, $model)
    {
        return $permissionOrRole . ' not assigned to ' . $model;
    }

    // Message for revoked
    public static function revoked($permissionOrRole, $model)
    {
        return $permissionOrRole . ' revoked for ' . $model;
    }

    // Message for allowed fields
    public static function allowedFields($allowedFields)
    {
        return 'Update request should contain any of the allowed fields ' . implode("|", $allowedFields);
    }

    // Message for not public
    public static function notPublic($model)
    {
        return $model . ' not public';
    }

    // Message for not belong to
    public static function notBelongTo($model, $entity)
    {
        return $model . ' does not belongs to ' . $entity;
    }

    // Message for custom message
    public static function customMessage($message)
    {
        return $message;
    }

    // Message for if the user is not active
    public static function userNotActive($user)
    {
        return 'The ' . $user . ' is not active';
    }

}