<?php
// app/core/App.php
// Router don gian: phan tich $_GET['url'] -> goi Controller::action(params)
class App
{
    protected string $controllerName;
    protected string $action;
    protected array $params = [];

    public function __construct()
    {
        $url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
        $segments = $url === '' ? [] : explode('/', $url);

        if (($segments[0] ?? '') === 'admin') {
            // Vi du: admin/product/edit/5 -> AdminProductController::edit(5)
            $this->controllerName = 'Admin' . ucfirst($segments[1] ?? 'dashboard') . 'Controller';
            $this->action = $segments[2] ?? 'index';
            $this->params = array_slice($segments, 3);
        } else {
            // Vi du: product/detail/5 -> ProductController::detail(5)
            $this->controllerName = ucfirst($segments[0] ?? 'home') . 'Controller';
            $this->action = $segments[1] ?? 'index';
            $this->params = array_slice($segments, 2);
        }

        $this->dispatch();
    }

    protected function dispatch(): void
    {
        // Ten controller/action chi cho phep ky tu chu-so, tranh nap class la
        if (!preg_match('/^[A-Za-z0-9_]+$/', $this->controllerName)
            || !preg_match('/^[A-Za-z0-9_]+$/', $this->action)) {
            $this->abort404('Ten controller/action khong hop le');
        }

        if (!class_exists($this->controllerName)) {
            $this->abort404('Khong tim thay controller: ' . $this->controllerName);
        }

        $controller = new $this->controllerName();

        // Chi cho goi cac ham public, khong cho goi ham protected/private va ham khoi tao
        if (!method_exists($controller, $this->action)
            || !is_callable([$controller, $this->action])
            || str_starts_with($this->action, '__')) {
            $this->abort404('Khong tim thay action: ' . $this->action);
        }

        call_user_func_array([$controller, $this->action], $this->params);
    }

    protected function abort404(string $debugMessage = ''): void
    {
        if (!headers_sent()) {
            http_response_code(404);
        }
        // $debugMessage chi ghi log, khong hien thi cho nguoi dung cuoi
        if ($debugMessage !== '') {
            error_log($debugMessage);
        }
        require __DIR__ . '/../views/errors/404.php';
        exit;
    }
}
