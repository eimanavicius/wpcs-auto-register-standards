# Composer plugin to automatically register WordPress-Coding-Standards

This is Composer plugin to automatically register WordPress-Coding-Standards to PHP_CodeSniffer.

PHP_CodeSniffer by default includes these coding standards:
> The installed coding standards are Squiz, PHPCS, Zend, PEAR, MySource, PSR1 and PSR2

With WordPress-Coding-Standards package you have:
> The installed coding standards are Squiz, PHPCS, Zend, PEAR, MySource, PSR1, PSR2, WordPress-Extra, WordPress, WordPress-VIP, WordPress-Docs and WordPress-Core

To achieve this you have to manually run command
`phpcs --config-set installed_paths '/path/to/vendor/wp-coding-standards/wpcs'`

I written post-update-cmd script to do this in dev mode only and now want to share it with other projects and especially you.
