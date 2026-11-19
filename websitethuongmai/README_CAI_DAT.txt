HUONG DAN CAI DAT WEBSITE THUONG MAI PHP MVC (BUOI 1-4)
======================================================

1. Giai nen thu muc websitethuongmai vao:
   C:\\xampp\\htdocs\\

2. Mo XAMPP, bat Apache va MySQL.

3. Mo phpMyAdmin: http://localhost/phpmyadmin
   Chon tab Import, chon file database/schema.sql trong thu muc du an,
   sau do nhan Import/Thuc hien.

   CANH BAO: schema.sql co lenh DROP TABLE de tao lai CSDL mau tu dau.
   Neu database shop_db dang co du lieu can giu, hay sao luu truoc khi Import.

4. Cau hinh ket noi tai config/config.php:
   DB_HOST = localhost
   DB_NAME = shop_db
   DB_USER = root
   DB_PASS = '' (mac dinh XAMPP)
   Neu MySQL cua ban co mat khau root, sua DB_PASS cho dung.

5. Truy cap:
   http://localhost/websitethuongmai/public/

   Neu ban doi ten thu muc, BASE_URL tu dong lay theo duong dan dang truy cap.

6. Tai khoan mau:
   Quan tri: admin@shop.local / admin123
   Nhan vien: staff@shop.local / staff123

7. Neu khong ket noi duoc CSDL:
   - Kiem tra MySQL da bat chua.
   - Kiem tra DB_NAME, DB_USER, DB_PASS trong config/config.php.
   - Xem error log cua Apache/PHP trong XAMPP de doc loi chi tiet.

GHI CHU:
- Du an can PHP 8+ va cac extension PDO, pdo_mysql, fileinfo, gd.
- PHP lint da duoc chay tren cac file PHP trong goi nay.
- Ket noi MySQL va chuc nang upload can duoc kiem tra tren may XAMPP cua ban.
