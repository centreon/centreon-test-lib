<?php declare(strict_types = 1);

namespace Centreon\PHPStan;

use \PHPStan\Command\ErrorFormatter\ErrorFormatter;
use \PHPStan\Command\AnalysisResult;
use \PHPStan\Command\Output;

class AbsolutePathErrorFormatter implements ErrorFormatter
{
	public function formatErrors(
		AnalysisResult $analysisResult,
		Output $output
	): int
	{
		$output->writeRaw('<?xml version="1.0" encoding="UTF-8"?>');
		$output->writeLineFormatted('');
		$output->writeRaw('<checkstyle>');
		$output->writeLineFormatted('');

		foreach ($this->groupByFile($analysisResult) as $filePath => $errors) {
			$output->writeRaw(sprintf(
				'<file name="%s">',
				$this->escape($filePath)
			));
			$output->writeLineFormatted('');

			foreach ($errors as $error) {
				$output->writeRaw(sprintf(
					'  <error line="%d" column="1" severity="error" message="%s" />',
					$this->escape((string) $error->getLine()),
					$this->escape((string) $error->getMessage())
				));
				$output->writeLineFormatted('');
			}
			$output->writeRaw('</file>');
			$output->writeLineFormatted('');
		}

		$notFileSpecificErrors = $analysisResult->getNotFileSpecificErrors();

		if (count($notFileSpecificErrors) > 0) {
			$output->writeRaw('<file>');
			$output->writeLineFormatted('');

			foreach ($notFileSpecificErrors as $error) {
				$output->writeRaw(sprintf('  <error severity="error" message="%s" />', $this->escape($error)));
				$output->writeLineFormatted('');
			}

			$output->writeRaw('</file>');
			$output->writeLineFormatted('');
		}

		if ($analysisResult->hasWarnings()) {
			$output->writeRaw('<file>');
			$output->writeLineFormatted('');

			foreach ($analysisResult->getWarnings() as $warning) {
				$output->writeRaw(
					sprintf('  <error severity="warning" message="%s" />', $this->escape($warning))
				);
				$output->writeLineFormatted('');
			}

			$output->writeRaw('</file>');
			$output->writeLineFormatted('');
		}

		$output->writeRaw('</checkstyle>');
		$output->writeLineFormatted('');

		return $analysisResult->hasErrors() ? 1 : 0;
	}

	/**
	 * Escapes values for using in XML
	 *
	 * @param string $string
	 * @return string
	 */
	protected function escape(string $string): string
	{
		return htmlspecialchars($string, ENT_XML1 | ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Group errors by file
	 *
	 * @param AnalysisResult $analysisResult
	 * @return array<string, array> Array that have as key the absolute path of file
	 *                              and as value an array with occured errors.
	 */
	private function groupByFile(AnalysisResult $analysisResult): array
	{
		$files = [];

		/** @var \PHPStan\Analyser\Error $fileSpecificError */
		foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
			$files[$fileSpecificError->getFile()][] = $fileSpecificError;
		}

		return $files;
	}
}