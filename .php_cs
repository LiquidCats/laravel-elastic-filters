<?php

//--rules=@Symfony,@PhpCsFixer,@PSR1,@PSR2
$finder = PhpCsFixer\Finder::create()
    ->notPath('bootstrap/cache')
    ->notPath('storage')
    ->notPath('vendor')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->notName('_ide_helper*')
    ->notName('.phpstorm.meta.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
;

return PhpCsFixer\Config::create()
    ->setRules([
        'psr0' => false,
        '@PSR1' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        '@PhpCsFixer' => true,
        'blank_line_after_namespace' => true,
        'braces' => true,
        'class_definition' => true,
        'elseif' => true,
        'function_declaration' => true,
        'indentation_type' => true,
        'line_ending' => true,
        'lowercase_constants' => false,
        'lowercase_keywords' => true,
        'method_argument_space' => [
            'ensure_fully_multiline' => true,
        ],
        'no_unused_imports' => true,
        'no_break_comment' => true,
        'no_closing_tag' => true,
        'no_spaces_after_function_name' => true,
        'no_spaces_inside_parenthesis' => true,
        'no_trailing_whitespace' => true,
        'no_trailing_whitespace_in_comment' => true,
        'single_blank_line_at_eof' => true,
        'single_class_element_per_statement' => [
            'elements' => ['property'],
        ],
        'single_import_per_statement' => true,
        'single_line_after_imports' => true,
        'switch_case_semicolon_to_colon' => true,
        'switch_case_space' => true,
        'visibility_required' => true,
        'encoding' => true,
        'full_opening_tag' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'length',
        ],
    ])
    ->setFinder($finder)
;
