<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer\CodeSamplePrinter;

use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\DirectoryToMarkdownPrinterTest
 */
final class CodeSamplePrinter
{
    /**
     * @param RuleCodeSamplePrinterInterface[] $ruleCodeSamplePrinters
     */
    public function __construct(
        private array $ruleCodeSamplePrinters
    ) {
    }

    /**
     * @return string[]
     */
    public function print(RuleDefinition $ruleDefinition, bool $shouldUseConfigureMethod): array
    {
        $lines = [];

        foreach ($ruleDefinition->getCodeSamples() as $codeSample) {
            foreach ($this->ruleCodeSamplePrinters as $ruleCodeSamplePrinter) {
                if (! $ruleCodeSamplePrinter->isMatch($ruleDefinition->getRuleClass())) {
                    continue;
                }

                $newLines = $ruleCodeSamplePrinter->print($codeSample, $ruleDefinition, $shouldUseConfigureMethod);
                $lines = array_merge($lines, $newLines);
                break;
            }

            $lines[] = '<br>';
        }

        return $lines;
    }
}
