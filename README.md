# WordPress Extended User Search

Extend the WordPress admin Users search to include additional native user fields and selected user meta values.

## Features

- Extends **Users → All Users** search
- Includes `user_nicename`
- Includes `display_name`
- Searches the `nickname` user meta field
- Searches the `mobile_user` user meta field
- Uses prepared SQL and `$wpdb->esc_like()`
- Prevents duplicate user rows with `DISTINCT`
- Runs only on the WordPress admin Users screen by default
- Provides filters for extra user meta keys and custom query scope
- No settings page, custom database table, or plugin-specific stored data

## Requirements

- WordPress 6.0+
- PHP 7.4+

## Installation

1. Upload the `WordPress-Extended-User-Search` folder to `/wp-content/plugins/`.
2. Activate **WordPress Extended User Search**.
3. Open **Users → All Users**.
4. Search by username, email, nicename, display name, nickname, or `mobile_user` value.

## How It Works

WordPress handles its normal user-table search first. The plugin adds `user_nicename` and `display_name` to the native searchable columns.

For user meta values, it adds one controlled `LEFT JOIN` to `wp_usermeta` and a prepared condition for the configured meta keys.

The extension is limited to `users.php` by default so unrelated frontend or application-level `WP_User_Query` calls are not changed.

## Searchable User Meta

Default keys:

- `nickname`
- `mobile_user`

## Hooks / Filters

### `wpeus_searchable_meta_keys`

Add or remove user meta keys from the search.

```php
add_filter( 'wpeus_searchable_meta_keys', function ( $keys ) {
	$keys[] = 'billing_phone';
	return $keys;
} );
```

### `wpeus_should_extend_query`

Control whether the plugin extends a specific `WP_User_Query`.

## Performance

A `%term%` search on `usermeta.meta_value` is inherently expensive on very large datasets and generally cannot use a normal value index efficiently.

The plugin limits this behavior to explicit searches on the admin Users screen and only searches selected meta keys. Very large sites with millions of user-meta rows should consider a dedicated indexed lookup table or search service.

## Data Storage

This plugin stores no custom data.

## License

GPL-3.0

## Author

Amirreza Shayesteh Far  
https://github.com/amirrezashf

---

# جستجوی توسعه‌یافته کاربران وردپرس

این افزونه جستجوی صفحه کاربران وردپرس را به فیلدهای بیشتر و Metaهای انتخاب‌شده کاربران گسترش می‌دهد.

## قابلیت‌ها

- توسعه جستجوی **Users → All Users**
- جستجو در `user_nicename`
- جستجو در `display_name`
- جستجو در User Meta با کلید `nickname`
- جستجو در User Meta با کلید `mobile_user`
- استفاده از Prepared SQL و `$wpdb->esc_like()`
- جلوگیری از نمایش تکراری کاربران با `DISTINCT`
- اجرا فقط در صفحه مدیریت کاربران به‌صورت پیش‌فرض
- Filter برای افزودن Meta Keyهای بیشتر
- بدون صفحه تنظیمات، Custom Table یا داده اختصاصی افزونه

## نیازمندی‌ها

- WordPress 6.0+
- PHP 7.4+

## نصب

1. پوشه `WordPress-Extended-User-Search` را در `/wp-content/plugins/` قرار دهید.
2. افزونه **WordPress Extended User Search** را فعال کنید.
3. وارد **Users → All Users** شوید.
4. بر اساس نام کاربری، ایمیل، Nicename، Display Name، Nickname یا مقدار `mobile_user` جستجو کنید.

## نحوه عملکرد

WordPress جستجوی عادی ستون‌های جدول کاربران را انجام می‌دهد. افزونه `user_nicename` و `display_name` را به همان جستجو اضافه می‌کند.

برای User Metaها یک `LEFT JOIN` کنترل‌شده به `wp_usermeta` و شرط جستجوی Prepared اضافه می‌شود.

به‌صورت پیش‌فرض این رفتار فقط در `users.php` فعال است تا سایر `WP_User_Query`های سایت ناخواسته تغییر نکنند.

## Metaهای قابل جستجو

پیش‌فرض:

- `nickname`
- `mobile_user`

## Hook و Filter

### `wpeus_searchable_meta_keys`

برای افزودن یا تغییر Meta Keyهای قابل جستجو.

```php
add_filter( 'wpeus_searchable_meta_keys', function ( $keys ) {
	$keys[] = 'billing_phone';
	return $keys;
} );
```

### `wpeus_should_extend_query`

برای کنترل اینکه افزونه روی یک `WP_User_Query` مشخص اعمال شود یا خیر.

## Performance

جستجوی `%term%` روی `usermeta.meta_value` در دیتابیس‌های بسیار بزرگ ذاتاً پرهزینه است و معمولاً از Index معمولی مقدار استفاده مؤثری نمی‌کند.

به همین دلیل افزونه فقط هنگام جستجوی صریح در صفحه کاربران اجرا می‌شود و فقط Meta Keyهای مشخص را بررسی می‌کند. برای سایت‌هایی با میلیون‌ها User Meta، ساختار Lookup اختصاصی و Index شده مناسب‌تر است.

## ذخیره‌سازی داده

این افزونه هیچ داده اختصاصی ذخیره نمی‌کند.

## مجوز

GPL-3.0

## نویسنده

Amirreza Shayesteh Far  
https://github.com/amirrezashf
