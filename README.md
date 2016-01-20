# Organogram Vizualization

The vizualization of government posts (and salaries).

    This is an organisational chart (organogram) visualisation for the
    structure of 'posts' within the UK government. Government
    departments are comprised of units which contain posts and these
    posts can be held by one or more people.

## Install

Install using puppet modules:

* Tomcat: https://github.com/SilexConsulting/puppet-tomcat

* Organogram dependencies: https://github.com/SilexConsulting/puppet-orgdc
    * Fuseki - triple store with Sparql endpoint
    * Elda - builds an API on top of a Sparql endpoint
    * Apache site config - converts Puelia-style calls to Elda-style ones


## Run

Start Fuseki:

    sudo service fuseki start

(Or on the commandline with `sudo java -Xmx1200M -jar /usr/share/fuseki/fuseki-server.jar --update --loc=/var/lib/fuseki/databases/ORG-DB /ds`)


## Notes

The apache config has lots of rewrite rules. Much of this is to convert from the URLs provided by the old Puelia-based API to the newer Elda API. The organogram viz was originally designed to use those old URLs.

# Legacy vizualizations

This repo also contains some legacy vizualizations: see the `departments` and `post-list` directories.
