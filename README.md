# Tidy feedback

This is a [Drupal module](https://www.drupal.org/docs/user_guide/en/understanding-modules.html) *and* a [Symfony
bundle](https://symfony.com/doc/current/bundles.html) to collection user feedback.

> [!CAUTION]
> The documentation is incomplete!

## Installation

``` shell
composer require itk-dev/tidy-feedback
```

> [!IMPORTANT]
> You may have to add `--with-all-dependencies` to the `composer require` command to make everything fall into place.

### Drupal

``` shell
drush pm:install tidy_feedback
```

### Symfony

Create `config/routes/tidy_feedback.yaml` (or copy
[`resources/config/routes/tidy_feedback.yaml`](resources/config/routes/tidy_feedback.yaml)):

``` yaml
#config/routes/tidy_feedback.yaml
tidy_feedback:
  resource: "@TidyFeedbackBundle/config/routes.php"
  prefix: /tidy-feedback
```

> [!NOTE]
> You can use any path as `prefix`, but for consistency with the Drupal version of Tidy feedback you should use
> `/tidy-feedback`.

If [Symfony Flex](https://symfony.com/doc/current/setup/flex.html) hasn't already done so, you must enable the
TidyFeedbackBundle bundle:

``` php
// config/bundles.php
return [
    …,
    ItkDev\TidyFeedbackBundle\TidyFeedbackBundle::class => ['all' => true],
];
```

## Configuration

We need a [Doctrine database
URL](https://www.doctrine-project.org/projects/doctrine-dbal/en/4.2/reference/configuration.html#connecting-using-a-url)
in the environment variable `TIDY_FEEDBACK_DATABASE_URL`, e.g.

``` dotenv
# .env
# See https://www.doctrine-project.org/projects/doctrine-dbal/en/4.2/reference/configuration.html#connecting-using-a-url for details.
TIDY_FEEDBACK_DATABASE_URL=pdo-sqlite://localhost//app/tidy-feedback.sqlite
```

As an alternative for Drupal you can set `TIDY_FEEDBACK_DATABASE_URL` in `settings.local.php`:

``` php
# web/sites/default/settings.local.php
putenv('TIDY_FEEDBACK_DATABASE_URL=pdo-sqlite://localhost//app/tidy-feedback.sqlite');
```

See [All configuration options](#all-configuration-options) for details and more options.

## Create Tidy feedback database

In a Drupal project, run

``` shell
drush tidy-feedback:doctrine:schema-update

```

In Symfony projects, run

``` shell
bin/console tidy-feedback:doctrine:schema-update
```

After installation and configuration, open `/tidy-feedback/test` on your site and enjoy!

All feedback items can be found on `/tidy-feedback`.

## All configuration options

Only `TIDY_FEEDBACK_DATABASE_URL` is required.

| Name                            | Default value        | Example                                            |
|---------------------------------|----------------------|----------------------------------------------------|
| `TIDY_FEEDBACK_DATABASE_URL`    |                      | `pdo-sqlite://localhost//app/tidy-feedback.sqlite` |
| `TIDY_FEEDBACK_DEBUG`           | `false`              |                                                    |
| `TIDY_FEEDBACK_DEFAULT_LOCALE`  | `da`                 |                                                    |
| `TIDY_FEEDBACK_DEV_MODE`        | `false`              |                                                    |
| `TIDY_FEEDBACK_DISABLE`         | `false`              |                                                    |
| `TIDY_FEEDBACK_DISABLE_PATTERN` | `@^/tidy-feedback$@` | `@^$@` (don't disable on all pages)                |
| `TIDY_FEEDBACK_USERS`           | `{}`                 | `{"admin": "password"}`                            |

Boolean options must be set to `true`, e.g. `TIDY_FEEDBACK_DISABLE=true`, to be true. Otherwise they're false.

The pattern in `TIDY_FEEDBACK_DISABLE_PATTERN` will be matched against the *path* of the request URL only, i.e. the
query string will *not* be included.

## Development

``` shell
task
```

### composer.json

In order to make this behave as both a Drupal module and a Synfony bundle, we use some tricks in
[`composer.json`](./composer.json):

``` javascript annotate
{
    // We use "type": "drupal-module" to make Drupal move the module into the
    // proper location (web/modules/contrib).
    // Symfony recommend using "type": "drupal-module" (cf. https://symfony.com/doc/current/bundles/best_practices.html#installation),
    // but Symfony and Flex don't seem to really care about this.
    "type": "drupal-module",
    "require": {
        // In order to not pull much of Symfony into a Drupal project or (worse)
        // much of Drupal into a Symfony project, we require only the bare
        // minimum to make this module/bundle work.
        "doctrine/dbal": "^3 || ^4",
        "doctrine/orm": "^2.8 || ^3",
        "symfony/cache": "^6 || ^7",
        "twig/twig": "^3"
    },
    "autoload": {
        "psr-4": {
            // The Symfony bundle namespace.
            "ItkDev\\TidyFeedbackBundle\\": "symfony/src/",
            // The shared code namespace.
            "ItkDev\\TidyFeedback\\": "src/"
        }
    },
    // …
}
```

### Twig

We use a watered-down instance of Twig with only a `trans` filter and a `path` function.

### Development app

For development, you can start a Symfony app with Tidy feedback installed:

``` shell
task app:start
```

Run `task app:stop` to stop the app.

> [!TIP]
> Use `tidy-feedback-show=form` in the query string, e.g. `/tidy-feedback/test?tidy-feedback-show=form`, to
> automatically show the feedback form when loading a page.
>
> Add default form values in `tidy-feedback`, e.g.
>
> ``` plain
> /tidy-feedback/test?tidy-feedback[subject]=test&tidy-feedback[email]=test@example.com&tidy-feedback[description]=My%20feedback
> ```

For easy testing, you can use [Bookmarklet Creator](https://mrcoles.com/bookmarklet/) to convert the code

``` javascript
const url = new URL(document.location);
url.searchParams.set('tidy-feedback-show', 'form');
url.searchParams.set('tidy-feedback[subject]', 'My feedback '+new Date().toISOString());
url.searchParams.set('tidy-feedback[email]', 'test@example.com');
url.searchParams.set('tidy-feedback[description]', 'This is cool!');
document.location = url
```

into a bookmarklet.
