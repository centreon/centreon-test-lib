<?php

declare(strict_types=1);

namespace Centreon\PHPStan\CustomRules\StringMiscRules;

use Centreon\PHPStan\CustomRules;
use Centreon\PHPStan\CustomRules\AbstractCustomRule;
use \PHPStan\Analyser\Scope;
use \PHPStan\Rules\Rule;
use \PHPStan\Rules\RuleErrorBuilder;
use \PhpParser\Node;

class StringBackTickCustomRule extends AbstractCustomRule implements Rule
{
    public const CENTREON_CONFIG_DATABASE = ':db';

    public const CENTREON_REALTIME_DATABASE = ':dbstg';

    /**
     * @inheritDoc
     *
     * @return string
     */
    public function getNodeType(): string
    {
        return \PhpParser\Node\Scalar\String_::class;
    }

    /**
     * @inheritDoc
     *
     * @param Node $node
     * @param Scope $scope
     * @return array
     */
    public function processNode(Node $node, Scope $scope): array
    {
        preg_match('/' . self::CENTREON_CONFIG_DATABASE . '/', $node->value, $matches);
        if (! empty($matches)
            && ! preg_match('/`' . self::CENTREON_REALTIME_DATABASE .
                            '`|`' . self::CENTREON_CONFIG_DATABASE . '`/', $node->value)) {
            $varName = $this->getVariableNameFromNode($node);
            return [
                RuleErrorBuilder::message(
                    $this->buildErrorMessage($varName)
                )->build(),
            ];
        }
        return [];
    }

    /**
     * @inheritDoc
     *
     * @param Node $node
     * @return string|null
     */
    public function getVariableNameFromNode(Node $node): ?string
    {
        if (strpos($node->value, self::CENTREON_REALTIME_DATABASE)) {
            return self::CENTREON_REALTIME_DATABASE;
        }
        if (strpos($node->value, self::CENTREON_CONFIG_DATABASE)) {
            return self::CENTREON_CONFIG_DATABASE;
        }
        return null;
    }
}
