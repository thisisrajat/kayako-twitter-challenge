# KAYAKO SEARCH WRAPPER

  - When a web request hits index.php at `<hostname>:<port>/`, index.php renders the html.
  - `src/` folder contains all the application logic.
  - `KayakoSearchWrapper.php` is under namespace `App` and defines the class. No other concerns are handled by this file.
  - Using Composer, a package manager for easy dependency management.
  - Test driven development with PHPUnit. Run `phpunit -c phpunit.xml`
  - Hit `src/api.php` for raw data (#custserv and rt >= 1). For command line : `curl -i -H "Accept: application/json" "kayako.rajatja.in:80/src/api.php" | tee kayako-search-wrapper-rajat.json`
  - `src/api.php` could have been refactored to another folder, perhaps `src/api/v1/api.php`; but it's just premature refactoring.
  - Javascript and CSS are currently embedded in the same index.php; Bad part is this doesn't leverage the power of caching. But since both css and js are so small, separate http calls is not justified.
  - Security: kept all the tokens and secrets in env variables
  - Return the raw data to the frontend and let javascript and css prettify the data. It doesn't make sense to format it in the backend. Why?
      1. CPU operation like this is a blocking operation and can become a serious performance bottleneck in single threaded servers like node.js.
      2. Bandwidth is required more since sending all those extra `\n` and `spaces`.
