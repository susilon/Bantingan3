# Bantingan3
Bantingan MVC Framework

### Configuration
Open config/web.config.yml, to configure basic application settings.<br>
Open config/route.config.yml, to configure routing and path settings.<br>
Open config/database.config.yml, to configure database connection.<br>
or set value in Environment Variable (see Environment Variabel example below)

### Docker Image
We provided Dockerfile, for Docker image building.<br>
docker build -t image-tag-name .<br>
example :<br>
docker build -t susilon/bantingan3-app .

### Environment Variable Example
Example of change config file value with environment variable and JSON string value :<br>
Change only SiteTitle and DefaultController in application_settings : <br>
Environment Variable Key : BANTINGAN3_APPLICATION_SETTINGS<br>
Value : { "SiteTitle":"Bantingan Docker","DefaultController":"Home"}<br>
<br>
Example of database_settings :<br>
Environment Variable Key : BANTINGAN3_DATABASE_SETTINGS<br>
Value : {"default":{"server":"db","user":"root","password":"root","database":"maindb"},"usermanagement":<br>{"type":"mysql","server":"db","user":"root","password":"root","database":"memberdb"}}<br>
<br>
TODO :<br>
Documentation.<br>
Sample of Model files.