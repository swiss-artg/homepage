# SWISS ARTG Homepage

This repository holds the codebase for the homepage of the SWISS Amateure Radio Teletype Group.

## Principles

This repository is the source of truth.
Changes made in this repository will be pushed the the hosting.
Consequently, changes made directly on the hosting will be reverted.

Using [DDEV][ddev], a local develoment envrionment provides a save space to do code changes before they are being pushed to the hosting.

The Drupal Configuration Management is used to export site configureation to config files tracked within this repository.
Changes to the site must be done in the local develoment envrionment first and then pushed to the hosting through code changes.

[ddev]: https://ddev.com/
