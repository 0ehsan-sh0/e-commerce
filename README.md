<h2>About E-Shop</h2>

<p><strong>E-Shop</strong> is a modern, full-stack e-commerce platform built on <strong>Laravel 10</strong>, offering a complete solution for small to mid-sized online retailers. Its modular architecture makes it easy to extend or customize, providing production-ready features out of the box.</p>

<h2>Key Features</h2>

<h3>Authentication & Authorization</h3>
<ul>
  <li>Role-Based Access Control via <a href="https://spatie.be/docs/laravel-permission/v6/introduction" target="_blank">Spatie Permissions</a>.</li>
  <li>Secure auth backend using <a href="https://laravel.com/docs/12.x/fortify" target="_blank">Laravel Fortify</a>.</li>
  <li>Social login via <a href="https://laravel.com/docs/12.x/socialite" target="_blank">Laravel Socialite</a>.</li>
  <li>Bot protection with Google <strong>reCAPTCHA v3</strong> integration.</li>
</ul>

<h3>Product & Order Management</h3>
<ul>
  <li>Admin dashboard to manage brands, categories, products, banners, orders, and more.</li>
  <li>Multiple product images, SEO-friendly slugs, and dynamic attributes.</li>
  <li>Shopping cart, coupon support, wishlist, and compare features.</li>
</ul>

<h3>User Experience</h3>
<ul>
  <li>Responsive Blade components for mobile and desktop.</li>
  <li>SEO meta tags via <a href="https://github.com/artesaos/seotools" target="_blank">artesaos/seotools</a>.</li>
  <li>In-page alerts using <a href="https://sweetalert2.github.io/" target="_blank">SweetAlert2</a>.</li>
  <li>Jalali (Persian) date formatting via <a href="https://hekmatinasser.github.io/verta/" target="_blank">Verta</a>.</li>
</ul>

<h2>Installation</h2>

<pre>
git clone https://github.com/0ehsan-sh0/e-commerce.git
cd e-commerce

composer install
npm install && npm run dev

cp .env.example .env
php artisan key:generate
php artisan migrate --seed

php artisan serve
</pre>

<p>Visit <code>http://localhost:8000</code> to explore your new storefront.</p>
