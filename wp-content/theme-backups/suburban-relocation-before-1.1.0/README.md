# Suburban Relocation Systems — WordPress theme

Готова класична WordPress-тема з підтримкою Gutenberg. Не потребує ACF або платного конструктора.

## Встановлення

1. У WordPress відкрийте **Appearance → Themes → Add New → Upload Theme**.
2. Завантажте ZIP-архів теми й активуйте її.
3. Перейдіть у **Appearance → Starter Content** та натисніть **Import starter content**.
4. Якщо адреси сторінок повертають 404, один раз відкрийте **Settings → Permalinks** і натисніть **Save Changes**.

Імпортер не перезаписує записи або сторінки, якщо вони вже мають такий самий slug.

## Що і де редагувати

- **Pages → Home** — уся головна сторінка в Gutenberg. Hero, переваги, Services, Process, Locations, Reviews, FAQ і CTA створені окремими блоками/секціями.
- **Services** — додавання й редагування сервісів. Title, Excerpt, Featured Image та повна стаття редагуються стандартними блоками Gutenberg. Поля `Hero kicker` і `Short card label` — у правій панелі редактора.
- **Locations** — додавання й редагування локацій та міських SEO-сторінок. Є окремі поля для регіону, адреси, телефона та зони обслуговування. Увімкніть **Show in the home-page Locations section**, якщо локація має бути на головній.
- **Quote Requests** — усі заявки з форм. Навіть якщо поштовий сервер ще не налаштований, заявка зберігається тут.
- **Appearance → Company Details** — телефон, email, графік, основна адреса, email для заявок і ліцензійні номери.
- **Appearance → Customize → Site Identity** — заміна логотипа.
- **Appearance → Menus** — редагування головного меню. Підменю на десктопі відкриваються при наведенні; на телефоні — окремою кнопкою.

## Динамічні секції

Головна використовує короткі коди:

```text
[srs_services_grid count="-1"]
[srs_locations_grid count="-1" featured="yes"]
[srs_quote_form]
```

Тому нові сервіси з’являються в сітці автоматично. Для нової локації додатково поставте прапорець показу на головній.

## Форма оцінки

Поля відповідають погодженому набору:

- Full name — optional
- Phone number — required
- Preferred move date — optional
- Email — optional
- Moving from — optional
- Moving to — optional

Після відправлення WordPress створює приватний запис у **Quote Requests** і викликає `wp_mail()` для адреси з **Company Details**. Для гарантованої доставки пошти на бойовому сайті рекомендовано налаштувати SMTP.

## Шаблони та адаптивність

- окремі архіви й сторінки Services та Locations;
- шаблон довгої статті з текстом, зображеннями, списками та формою в sidebar;
- сторінки Testimonials, Moving Tips і Contact;
- адаптивні header, hover-dropdown, форми, сітки та footer;
- власна палітра й стилі в редакторі Gutenberg;
- reusable patterns: Article section with image і Quote call to action.

## Системні вимоги

- WordPress 6.3+
- PHP 7.4+
