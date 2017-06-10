#Hekate

Hekate is a commandline-application to manage repositories hosted on services like bitbucket from your local command line

##Installation

* Clone this repository

##Usage

### I. Bitbucket
#### Commands

##### bitbucket:init
```bash
php bin/hekate bitbucket:init [-f|--force]
```
will ask questions about setting-parameters and write it to the file hekate.yml in the root-folder.
If file already exists, no questions are asked and an info is displayed. With the --force option questions
and creation of a new config-file is enabled, even if a file already exists
 
The available settings for Hekate are:
 * Bitbucket-Account
 * Bitbucket-Username
 * Bitbucket-Password

##### bitbucket:repo-list
```bash
php bin/hekate bitbucket:repo-list [-u|--username] [-p|--password] [-a|--account] [-k|--projectkey]
```
will get a List of all Repositories for repositorys for the given parameters.
All Parameters are optional for the commandline. 
The following Parameters will be asked if omitted:

* username
* password
* account



Changelog
---------
- UNRELEASED
    - [ADDED] Command for Config-Init
    - [ADDED] YAML-Component for reading / writing Config-Files
    - [ADDED] possibility to filter the repository-List for a given projectkey
    - [ADDED] added command to get a list of all repositories for a given account