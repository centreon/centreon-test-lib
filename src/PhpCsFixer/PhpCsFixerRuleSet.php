<?php

/*
 * Copyright 2005 - 2023 Centreon (https://www.centreon.com/)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * For more information : contact@centreon.com
 *
 */

declare(strict_types=1);

namespace Centreon\PhpCsFixer;

class PhpCsFixerRuleSet
{
    private const YEAR = 2023;
    private const LICENSE_APACHE = 'apache';
    private const LICENSE_PRIVATE = 'private';
    private const LICENSES = [
        'centreon/centreon-test-lib' => self::LICENSE_APACHE,
        'centreon/centreon' => self::LICENSE_APACHE,
        'centreon/centreon-anomaly-detection' => self::LICENSE_PRIVATE,
        'centreon/centreon-autodiscovery' => self::LICENSE_PRIVATE,
        'centreon/centreon-bam' => self::LICENSE_PRIVATE,
        'centreon/centreon-cloud-business-extensions' => self::LICENSE_PRIVATE,
        'centreon/centreon-cloud-extensions' => self::LICENSE_PRIVATE,
        'centreon/centreon-it-edition-extensions' => self::LICENSE_PRIVATE,
        'centreon/centreon-license-manager' => self::LICENSE_PRIVATE,
        'centreon/centreon-map' => self::LICENSE_PRIVATE,
        'centreon/centreon-mbi' => self::LICENSE_PRIVATE,
        'centreon/centreon-pp-manager' => self::LICENSE_PRIVATE,
    ];

    /**
     * This method returns an array of defined rules Php-Cs-Fixer.
     *
     * @return array
     */
    public static function getRules(): array
    {
        $rules = [
            'align_multiline_comment' => true,
            'array_indentation' => true,
            'array_push' => true,
            'array_syntax' => true,
            'assign_null_coalescing_to_coalesce_equal' => true,
            'backtick_to_shell_exec' => true,
            'binary_operator_spaces' => true,
            'blank_line_before_statement' => ['statements' => ['exit', 'return', 'throw', 'yield']],
            'cast_spaces' => true,
            'class_attributes_separation' => [
                'elements' => [
                    'method' => 'one',
                    'property' => 'one',
                    'const' => 'only_if_meta',
                    'trait_import' => 'none',
                    'case' => 'none',
                ],
            ],
            'clean_namespace' => true,
            'combine_consecutive_issets' => true,
            'combine_consecutive_unsets' => true,
            'combine_nested_dirname' => true,
            'declare_parentheses' => true,
            'dir_constant' => true,
            'explicit_indirect_variable' => true,
            'explicit_string_variable' => true,
            'fopen_flag_order' => true,
            'fully_qualified_strict_types' => true,
            'function_to_constant' => true,
            'function_typehint_space' => true,
            'general_phpdoc_annotation_remove' => ['annotations' => ['author', 'package', 'subpackage']],
            'general_phpdoc_tag_rename' => ['case_sensitive' => true, 'replacements' => ['inheritdoc' => 'inheritDoc']],
            'get_class_to_class_keyword' => true,
            'heredoc_indentation' => true,
            'heredoc_to_nowdoc' => true,
            'implode_call' => true,
            'include' => true,
            'lambda_not_used_import' => true,
            'list_syntax' => true,
            'logical_operators' => true,
            'lowercase_cast' => true,
            'mb_str_functions' => true,
            'method_chaining_indentation' => true,
            'modernize_strpos' => true,
            'modernize_types_casting' => true,
            'multiline_whitespace_before_semicolons' => true,
            'no_alias_functions' => true,
            'no_alias_language_construct_call' => true,
            'no_alternative_syntax' => true,
            'no_binary_string' => true,
            'no_blank_lines_after_phpdoc' => true,
            'no_empty_comment' => true,
            'no_empty_phpdoc' => true,
            'no_empty_statement' => true,
            'no_extra_blank_lines' => [
                'tokens' => [
                    'break',
                    'case',
                    'continue',
                    'default',
                    'extra',
                    'parenthesis_brace_block',
                    'return',
                    'square_brace_block',
                    'switch',
                    'throw',
                    'use',
                ],
            ],
            'no_homoglyph_names' => true,
            'no_leading_namespace_whitespace' => true,
            'no_mixed_echo_print' => true,
            'no_multiline_whitespace_around_double_arrow' => true,
            'no_short_bool_cast' => true,
            'no_singleline_whitespace_before_semicolons' => true,
            'no_spaces_around_offset' => true,
            'no_superfluous_elseif' => true,
            'no_trailing_comma_in_singleline' => [
                'elements' => [
                    'arguments',
                    'array',
                    'array_destructuring',
                    'group_import',
                ],
            ],
            'no_trailing_whitespace_in_string' => true,
            'no_unneeded_control_parentheses' => true,
            'no_unneeded_curly_braces' => true,
            'no_unneeded_import_alias' => true,
            'no_unreachable_default_argument_value' => true,
            'no_unset_on_property' => true,
            'no_unused_imports' => true,
            'no_useless_else' => true,
            'no_useless_sprintf' => true,
            'no_whitespace_before_comma_in_array' => true,
            'normalize_index_brace' => true,
            'not_operator_with_successor_space' => true,
            'nullable_type_declaration_for_default_null_value' => true,
            'object_operator_without_whitespace' => true,
            'operator_linebreak' => true,
            'ordered_class_elements' => true,
            'ordered_imports' => ['imports_order' => ['const', 'class', 'function'], 'sort_algorithm' => 'alpha'],
            'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
            'phpdoc_align' => ['align' => 'left'],
            'phpdoc_annotation_without_dot' => true,
            'phpdoc_indent' => true,
            'phpdoc_inline_tag_normalizer' => true,
            'phpdoc_line_span' => ['const' => null, 'property' => 'single'],
            'phpdoc_no_access' => true,
            'phpdoc_no_alias_tag' => true,
            'phpdoc_no_empty_return' => true,
            'phpdoc_no_package' => true,
            'phpdoc_no_useless_inheritdoc' => true,
            'phpdoc_order' => true,
            'phpdoc_return_self_reference' => ['replacements' => ['this' => 'self', '@this' => 'self']],
            'phpdoc_scalar' => true,
            'phpdoc_separation' => true,
            'phpdoc_single_line_var_spacing' => true,
            'phpdoc_summary' => true,
            // phpdoc_to_comment is error-prone for inline type-hinting (ex: phpstan, psalm, ...)
            'phpdoc_to_comment' => false,
            'phpdoc_to_property_type' => true,
            'phpdoc_to_return_type' => true,
            'phpdoc_trim' => true,
            'phpdoc_trim_consecutive_blank_line_separation' => true,
            'phpdoc_types' => true,
            'phpdoc_var_annotation_correct_order' => true,
            'phpdoc_var_without_name' => true,
            'psr_autoloading' => ['dir' => './src'],
            'random_api_migration' => true,
            'regular_callable_call' => true,
            'return_assignment' => true,
            'self_accessor' => true,
            'self_static_accessor' => true,
            'semicolon_after_instruction' => true,
            'set_type_to_cast' => true,
            'short_scalar_cast' => true,
            'simple_to_complex_string_variable' => true,
            'simplified_if_return' => true,
            'simplified_null_return' => true,
            'single_line_comment_spacing' => true,
            'single_line_comment_style' => true,
            'single_quote' => true,
            'single_space_after_construct' => true,
            'space_after_semicolon' => true,
            'standardize_increment' => true,
            'standardize_not_equals' => true,
            'strict_comparison' => true,
            'strict_param' => true,
            'string_line_ending' => true,
            'switch_continue_to_break' => true,
            'ternary_to_null_coalescing' => true,
            'trailing_comma_in_multiline' => true,
            'trim_array_spaces' => true,
            'types_spaces' => true,
            'unary_operator_spaces' => true,
            'use_arrow_functions' => true,
            'visibility_required' => true,
            'void_return' => true,
            'whitespace_after_comma_in_array' => true,
            'declare_strict_types' => true,
        ];

        // Set the header dynamically based on the current detected project name.
        $projectLicense = self::detectCentreonProjectLicense(__DIR__);
        if ($phpLicenseHeader = self::getLicenseHeader($projectLicense)) {
            $rules += [
                'header_comment' => [
                    'location' => 'after_open',
                    'header' => $phpLicenseHeader,
                ],
            ];
        }

        return $rules;
    }

    /**
     * Return the header based on the project license type.
     *
     * @param string $projectLicense
     */
    private static function getLicenseHeader(string $projectLicense): string
    {
        $content = match ($projectLicense) {
            self::LICENSE_APACHE => (string) file_get_contents(__DIR__ . '/license.apache.txt'),
            self::LICENSE_PRIVATE => (string) file_get_contents(__DIR__ . '/license.private.txt'),
            default => '',
        };

        return str_replace('{YEAR}', (string) self::YEAR, $content);
    }

    /**
     * Recursively find the root main project which uses this project in its vendor.
     *
     * @param string $directory
     *
     * @return string|null
     */
    private static function detectCentreonProjectLicense(string $directory): ?string
    {
        // "end" conditions -> '', '.', '/'
        while (\mb_strlen($directory) > 1) {
            if (
                // A composer.json file is mandatory.
                is_file($composerFile = $directory . '/composer.json')

                // Avoid a composer.json from inside the Centreon vendor directory.
                && ! str_ends_with(dirname($directory), '/vendor/centreon')

                // There should have a defined license.
                && ($license = self::getCentreonProjectLicense($composerFile))
            ) {
                return $license;
            }

            $directory = dirname($directory);
        }

        return null;
    }

    /**
     * Extract the project name from the composer json file
     * and then return the related license if defined.
     *
     * @param string $composerFile
     */
    private static function getCentreonProjectLicense(string $composerFile): ?string
    {
        try {
            $composerContent = (string) file_get_contents($composerFile);
            $composerData = json_decode($composerContent, true, 512, JSON_THROW_ON_ERROR);
            $projectName = $composerData['name'] ?? null;

            if ($projectName && isset(self::LICENSES[$projectName])) {
                return self::LICENSES[$projectName];
            }
        } catch (\JsonException) {
        }

        return null;
    }
}
