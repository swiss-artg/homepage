# SWISS ARTG Homepage

This repository holds the codebase for the homepage of the SWISS Amateur Radio Teletype Group.

## Principles

This repository is the source of truth.
Changes made in this repository will be pushed the hosting.
Consequently, changes made directly on the hosting will be reverted.

Using [DDEV][ddev], a local development environment provides a save space to do code changes before they are being pushed to the hosting.

The Drupal Configuration Management is used to export site configuration to config files tracked within this repository.
Changes to the site must be done in the local development environment first and then pushed to the hosting through code changes.

## Set up a local development environment

### Prerequisites

* A working installation of [DDEV][ddev].
  See its [documentation](https://ddev.com/get-started/) on how to get started on your platform.
* A git client.
  Checkout the [git download page](https://git-scm.com/downloads) on how to get it for your platform.
* Read-only access to the database on the hosting.
* FTP access to the hosting.

### Procedure

1. Clone the source code

   ```sh
   git clone git@github.com:swiss-artg/homepage.git swiss-artg
   ```

   The folder `swiss-artg` will be referred to as _repository root_.

2. Configure the database access

   Create the file `.ddev/.env` using the text editor of your choice with the following content.

   ```
   REMOTE_HOST=<host>
   REMOTE_DB=<db>
   REMOTE_DB_USER=<db_user>
   REMOTE_DB_PASSWORD=<db_password>
   REMOTE_FTP_USER=<ftp_user>
   REMOTE_FTP_PASSWORD=<ftp_password>
   REMOTE_FTP_PORT=<ftp_port>
   ```

   Replace all instances of `<…>` with the values you received from a site administrator.

   Protect the file from prying eyes.

   ```sh
   chmod 600 .ddev/.env
   ```

3. Start the DDEV environment

  Execute this from within the repository root.

   ```sh
   ddev start
   ```

4. Install the dependencies

   ```sh
   ddev composer install
   ```

5. Fetch content from the hosting

   ```sh
   ddev fetch-db
   ddev import-db -f .dbdump/<dumpfile>
   ddev fetch-files
   ```

  `fetch-db` creates a new database dump file in `.dbdump`.
  Check the content of that directory for available dump files.
  Use an appropriate one for `<dumpfile>` in the command shown above.

6. Reset cache config and admin password

   ```sh
   ddev drush cim -y
   ddev drush cr
   ddev drush upwd admin admin
   ```

7. Access the site

   ```sh
   ddev launch
   ```

   Use user `admin` with password `admin` to login as site administrator.

## Related documentations

* [DDEV](https://ddev.readthedocs.io/)
* [Drupal User Guide](https://www.drupal.org/docs/user_guide/en/index.html)

[ddev]: https://ddev.com/
