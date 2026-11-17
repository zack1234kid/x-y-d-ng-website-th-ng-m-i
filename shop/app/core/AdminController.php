<?php
// app/core/AdminController.php
// Moi controller trong khu vuc /admin ke thua lop nay (tru AdminAuthController)
// de tu dong duoc bao ve boi Auth::requireRole() va dung layout rieng.
abstract class AdminController extends Controller
{
    public function __construct()
    {
        // Chua dang nhap -> dua ve trang dang nhap QUAN TRI (khong phai trang khach hang)
        if (!Auth::check()) {
            flash('error', 'Vui lòng đăng nhập tài khoản quản trị.');
            $this->redirect('admin/auth/login');
        }
        Auth::requireRole(['admin', 'staff']);
    }

    protected function view(string $view, array $data = [], string|false $layout = 'admin'): void
    {
        parent::view($view, $data, $layout);
    }
}
