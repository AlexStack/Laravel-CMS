<?php
/**
 * This is the php-cs-fixer Laravel 6.x style .php_cs VsCode config file for Amila Laravel CMS
 */
$finder = PhpCsFixer\Finder::create()
    ->notPath('bootstrap/cache')
    ->notPath('storage')
    ->notPath('vendor')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
;

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@Symfony' => true,
        'binary_operator_spaces' => ['align_double_arrow' => true, 'align_equals' => true],
        'array_syntax' => ['syntax' => 'short'],
        'linebreak_after_opening_tag' => true,
        'not_operator_with_successor_space' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
    ))
    ->setFinder($finder)
;
