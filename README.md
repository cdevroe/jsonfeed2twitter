# JSON Feed to Twitter using PHP

**By:** [Colin Devroe](http://cdevroe.com/)

Respository: https://github.com/cdevroe/jsonfeed2twitter

## Description

Parses a JSON Feed and updates a single Twitter account with a link to any new post.

## Installation

- Register an app at: https://apps.twitter.com/app/new (Be sure to set it up as Read & Write!)
- Update the configuration area of jsonfeed2twitter.php
    - This includes consumerKey, consumerToken, oAuthToken, oAuthTokenSecret from dev.twitter, JSON Feed URL, and Cache directory (optional)
- Authorize your domain for the application on dev.twitter
    - To setup a test you can always add localhost
- Make the /cache/ directory writable or change its location
- Copy to your server.
- Set up cron job. Example: /usr/local/php5/bin/php   /path/to/script/jsonfeed2twitter.php

That's it!

## Licensing

RSS to Twitter is released under the GPL License. This license is provided in license.txt. The [Twitter class](https://github.com/tijsverkoyen/TwitterOAuth) by [Tijs Verkoyen](https://github.com/tijsverkoyen) is released under the BSD License and that is included in twitter.php

## To do

Please check Github Issues for more.

## Version History

- **0.1 - June 2, 2017**
    - Initial codebase
