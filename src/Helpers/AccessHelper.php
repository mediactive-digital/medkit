<?php

namespace MedKit\Helpers;

class AccessHelper {

    /**
    * Validate access
    *
    * @param mixed string|array $middlewares
    * @return bool
    */
    public static function validate($middlewares = []) {

    	foreach ((array)$middlewares as $middleware) {

            if (!self::test($middleware)) {

                return false;
            }
    	}

        return true;
    }

    /**
    * Test middleware
    *
    * @param string $middleware
    * @return bool
    */
    private static function test($middleware) {

        $middleware = self::parseMiddleware($middleware);
        $class = 'App\Http\Middleware\\' . $middleware[0];

        return (new $class)->test($middleware[1]);
    }

    /**
     * Parse a middleware string to get the name and parameters.
     *
     * @param  string  $middleware
     * @return array
     */
    private static function parseMiddleware($middleware) {

        list($name, $parameters) = array_pad(explode(':', $middleware, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }
}