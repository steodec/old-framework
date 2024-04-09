<?php

namespace Humbrain\Framework\actions;

use Humbrain\Framework\database\Repository;
use Humbrain\Framework\renderer\RendererInterface;
use Humbrain\Framework\router\Router;
use Humbrain\Framework\sessions\FlashService;
use Humbrain\Framework\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudActions
{

    use RouterAwareAction;

    protected ?string $viewPath = null;
    protected ?string $routePrefix = null;
    protected array $messages = [
        'create' => "L'élément a bien été créé",
        'edit' => "L'élément a bien été modifié"
    ];
    private RendererInterface $renderer;
    protected Repository $repository;
    private Router $router;
    private FlashService $flash;

    public function __construct(
        FlashService $flash,
        RendererInterface $renderer,
        Repository $table,
        Router $router
    ) {
        $this->repository = $table;
        $this->router = $router;
        $this->renderer = $renderer;
        $this->flash = $flash;
    }

    public function __invoke(Request $request): string|ResponseInterface
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
        if ($request->getMethod() === 'DELETE') :
            return $this->delete($request);
        endif;
        if (str_ends_with($request->getUri()->getPath(), 'new')) :
            return $this->create($request);
        endif;
        $id = $request->getAttribute('id');
        if ($id) :
            return $this->edit($request);
        else :
            return $this->index($request);
        endif;
    }

    private function delete(Request $request): ResponseInterface
    {
        $this->repository->delete($request->getAttribute('id'));
        return $this->redirect($this->routePrefix . '.index');
    }

    public function create(Request $request): string|ResponseInterface
    {
        $item = $this->getNewEntity();
        $errors = null;
        if ($request->getMethod() === 'POST') :
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValidate()) :
                $this->repository->create($params);
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            endif;
            $item = $params;
            $errors = $validator->getErrors();
        endif;
        return $this->renderer->render($this->viewPath . '/create', $this->getFormParams(compact('item', 'errors')));
    }

    protected function getNewEntity(): object|array|null
    {
        return [];
    }

    protected function getParams(Request $request): object|array|null
    {
        return array_filter(
            $request->getParsedBody(),
            fn($key) => in_array($key, []),
            ARRAY_FILTER_USE_KEY
        );
    }

    protected function getValidator(Request $request): Validator
    {
        return new Validator($request->getParsedBody());
    }

    protected function getFormParams(array $params): array
    {
        return $params;
    }

    public function edit(Request $request): ResponseInterface|string
    {
        $id = $request->getAttribute('id');
        $item = $this->repository->find($id);
        $errors = null;
        if ($request->getMethod() === 'POST') :
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValidate()) :
                $this->repository->update($id, $params);
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix . '.index');
            endif;
            $errors = $validator->getErrors();
            $item = $params;
            $item["id"] = $id;
        endif;
        return $this->renderer->render($this->viewPath . '/edit', $this->getFormParams(compact('item', 'errors')));
    }

    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $page = $params['p'] ?? 1;
        $items = $this->repository->findPaginated($page);
        return $this->renderer->render($this->viewPath . '/index', $this->getFormParams(compact('items')));
    }
}
