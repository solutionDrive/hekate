#Hekate

Hekate is a commandline-application to manage repositories hosted on services like bitbucket from your local command line

##Installation

* Clone this repository

##Usage

### I. Bitbucket
#### Commands

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
    - [ADDED] possibility to filter the repository-List for a given projectkey
    - [ADDED] added command to get a list of all repositories for a given account