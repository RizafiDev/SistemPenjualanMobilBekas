<?php

namespace App\Http\Traits;

trait ApiProtected
{
    /**
     * Get the middleware for this API handler
     */
    public static function getRouteMiddleware(): array
    {
        $resourceName = static::getResourceName();

        return [
            'api.key:' . $resourceName
        ];
    }

    /**
     * Get the resource name for permission checking
     */
    protected static function getResourceName(): string
    {
        if (property_exists(static::class, 'resourceName')) {
            return static::$resourceName;
        }

        // Extract resource name from class name
        $className = class_basename(static::class);
        $resourceClass = str_replace('Handler', '', $className);

        // Get the resource class name
        if (property_exists(static::class, 'resource') && static::$resource) {
            $resourceClassName = class_basename(static::$resource);
            return strtolower(str_replace('Resource', '', $resourceClassName));
        }

        return strtolower($resourceClass);
    }
}
