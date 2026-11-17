<?php
// app/core/Controller.php
// Lop Controller co so: render view theo layout, redirect, va abort404() dung chung
abstract class Controller
{
    /**
     * Render mot view, long vao trong 1 layout dung chung.
     *
     * @param string $view   duong dan view tinh tu app/views, vi du 'home/index'
     * @param array  $data   du lieu truyen cho view (duoc extract thanh bien)
     * @param string|false $layout ten layout trong app/views/layouts, hoac false de khong dung layout
     */
    protected function view(string $view, array $data = [], string|false $layout = 'main'): void
    {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            die('Khong tim thay view: ' . e($view));
        }

        if ($layout === false) {
            extract($data);
            require $viewFile;
            return;
        }

        $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            die('Khong tim thay layout: ' . e($layout));
        }

        // $content la mot closure de layout tu quyet dinh noi chen noi dung view
        $content = function () use ($viewFile, $data) {
            extract($data);
            require $viewFile;
        };
        require $layoutFile;
    }

    /** Chuyen huong toi mot URL noi bo (tinh tu BASE_URL) */
    protected function redirect(string $path): void
    {
        header('Location: ' . base_url($path));
        exit;
    }

    /** Dung o BAT KY controller nao khi du lieu khong ton tai hoac khong hop le */
    protected function abort404(): void
    {
        http_response_code(404);
        require __DIR__ . '/../views/errors/404.php';
        exit;
    }
}
