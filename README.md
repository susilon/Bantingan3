# Bantingan3
Bantingan MVC Framework

### Configuration
Open config/web.config.yml, to configure basic application settings.
Open config/route.config.yml, to configure routing and path settings.
Open config/database.config.yml, to configure database connection.
or set value in Environment Variable (see Environment Variabel example below)

### Docker Image
We provided Dockerfile, for Docker image building.
docker build -t image-tag-name .
example :
docker build -t susilon/bantingan3-app .

### Environment Variable Example
Example of change config file value with environment variable and JSON string value :
Change only SiteTitle and DefaultController in application_settings : 
Environment Variable Key : BANTINGAN3_APPLICATION_SETTINGS
Value : { "SiteTitle":"Bantingan Docker","DefaultController":"Home"}

Example of database_settings :
Environment Variable Key : BANTINGAN3_DATABASE_SETTINGS
Value : {"default":{"server":"db","user":"root","password":"root","database":"maindb"},"usermanagement":{"type":"mysql","server":"db","user":"root","password":"root","database":"memberdb"}}

TODO :
Documentation.
Sample of Model files.