<?php

namespace Humbrain\Framework\router;

use AltoRouter;
use App\modules\Blog\Actions\PostCrudAction;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Router
 * @package Humbrain\Framework\router
 * Router to manage routes
 */
class Router
{
    private AltoRouter $router;

    public function __construct()
    {
        $this->router = new AltoRouter();
        $this->router->addMatchTypes([
            "s" => "[a-z\-0-9]+"
        ]);
    }

    /**
     * @param string $path
     * @param string|callable $callable $callable
     * @param string|null $name
     * @return void
     */
    public function put(string $path, string|callable $callable, ?string $name = null): void
    {
        $this->add('PUT', $path, $callable, $name);
    }

    /**
     * @param string $method
     * @param string $path
     * @param string|callable $callback
     * @param string|null $name
     * @return void
     */
    public function add(string $method, string $path, string|callable $callback, ?string $name = null): void
    {
        try {
            $this->router->addRoutes([[$method, $path, $callback, $name]]);
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): Route|null
    {
        $result = $this->router->match($request->getUri()->getPath(), $request->getMethod());
        if ($result === false) :
            return null;
        endif;
        return new Route($result['name'] ?? '', $result['target'], $result['params']);
    }

    /**
     * @param string $prefix
     * @param string $callable
     * @param string $name
     * @return void
     */
    public function crud(string $prefix, string $callable, string $name): void
    {
        $this->get($prefix . '', $callable, $name . '.index');
        $this->get($prefix . '/[i:id]', $callable, $name . '.edit');
        $this->put($prefix . '/[i:id]', $callable);
        $this->get($prefix . '/new', $callable, $name . '.create');
        $this->post($prefix . '/new', $callable);
        $this->delete($prefix . '/[i:id]', $callable, $name . '.delete');
    }

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     * @return void
     */
    public function get(string $path, string|callable $callable, string $name): void
    {
        $this->add('GET', $path, $callable, $name);
    }

    /**
     * @param string $path
     * @param string|callable $callable $callable
     * @param string|null $name
     * @return void
     */
    public function post(string $path, string|callable $callable, ?string $name = null): void
    {
        $this->add('POST', $path, $callable, $name);
    }

    /**
     * @param string $path
     * @param string|callable $callable $callable
     * @param string|null $name
     * @return void
     */
    public function delete(string $path, string|callable $callable, ?string $name = null): void
    {
        $this->add('DELETE', $path, $callable, $name);
    }

    public function generateUri(string $string, ?array $array = [], ?array $queryParams = []): ?string
    {
        try {
            $uri = $this->router->generate($string, $array);
            if (!empty($queryParams)) :
                return $uri . '?' . http_build_query($queryParams);
            else :
                return $uri;
            endif;
        } catch (Exception $e) {
            return null;
        }
    }
}
