megaCache
=========

php zip archive cache class

I moved to an unlimited diskspace shared host - hostgator - and came to realize they have something called an "inode limit".
It means there is a maximum number of files + folders an account can have. I beleive this is common with shared hosting plans.

So, I created this class: It works pretty similarly to most cache systems - it has a "get" and "put" with a max age in seconds parameter.

The settings I commited are the ones I use. They create 100000 zip nodes full of compressed cache data.



