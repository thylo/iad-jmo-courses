## General

- All documentation for Tempest can be found in vendor/tempest/framework/docs
- Use `php tempest make:` commands to create framework-specific classes.

## Frontend

- Use Tempest view components where it makes sense
- Templates live in `views/` at the root, classes in `app/View/`. The
  `"Views\\": "views/"` entry in composer.json has no class behind it: it is
  only there so Tempest scans the directory for `x-*` components.