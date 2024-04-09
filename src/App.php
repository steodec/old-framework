<?php

namespace Humbrain\Framework;

use DI\ContainerBuilder;
use Exception;
use Humbrain\Framework\middleware\CombinedMiddleware;
use Humbrain\Framework\middleware\RoutePrefixedMiddleware;
use Humbrain\Framework\modules\Module;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class App implements Handler
{

    /** @var Module[] */
    private array $modules = [];

    private string|array|null $definitions;

    private ContainerInterface $container;

    private array $middlewares = [];

    private int $index = 0;

    /**
     * App constructor.
     * @param array|string|null $definitions
     */
    public function __construct(array|string|null $definitions = [])
    {
        if (is_string($definitions)) {
            $definitions = [$definitions];
        }
        if (!$this->isSequential($definitions)) {
            $definitions = [$definitions];
        }
        $this->definitions = $definitions;
    }

    /**
     * Rajoute un module à l'application
     *
     * @param string $module
     * @return self
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Ajoute un middleware
     *
     * @param callable|string|MiddlewareInterface $routePrefix
     * @param callable|string|MiddlewareInterface|null $middleware
     * @return self
     */
    public function pipe(
        callable|MiddlewareInterface|string $routePrefix,
        callable|MiddlewareInterface|string $middleware = null
    ): self {
        if ($middleware === null) {
            $this->middlewares[] = $routePrefix;
        } else {
            $this->middlewares[] = new RoutePrefixedMiddleware($this->getContainer(), $routePrefix, $middleware);
        }
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->index++;
        if ($this->index > 1) {
            throw new Exception();
        }
        $middleware = new CombinedMiddleware($this->getContainer(), $this->middlewares);
        return $middleware->process($request, $this);
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->handle($request);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $env = getenv('ENV') ?: 'production';
            if ($env === 'production') {
                $builder->enableCompilation('tmp/cache');
                $builder->writeProxiesToFile(true, 'tmp/proxies');
            }
            foreach ($this->definitions as $definition) {
                $builder->addDefinitions($definition);
            }
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $builder->addDefinitions([
                App::class => $this
            ]);
            try {
                $this->container = $builder->build();
            } catch (Exception $e) {
                return $this->container;
            }
        }
        return $this->container;
    }

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    private function isSequential(array $array): bool
    {
        if (empty($array)) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }
}
