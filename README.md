# TITLE YET TO BE FIGURED OUT

- package manager

- test driven development

- javascript and css on the same page/ why bad why good

- security; env variables

-

- return the raw data to the frontend and let javascript and css prettify the data. It doesn't make sense to format it in the backend; Why?
1. Because we have a fixed amount of cpu power and it should not be wasted in trivial stuff. CPU operation is a blocking operation and can become a serious performance issue in node.js.
2. Bandwidth is required more since sending all those extra `\n` and `spaces`.
