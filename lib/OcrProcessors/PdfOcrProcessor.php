<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2020 Robin Windey <ro.windey@gmail.com>
 *
 *  @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\WorkflowOcr\OcrProcessors;

use OCA\WorkflowOcr\Exception\OcrNotPossibleException;
use OCA\WorkflowOcr\Wrapper\ICommand;

class PdfOcrProcessor implements IOcrProcessor {
	/** @var ICommand */
	private $command;

	public function __construct(ICommand $command) {
		$this->command = $command;
	}

	public function ocrFile(string $fileContent): string {
		$this->command
			->setCommand("ocrmypdf --redo-ocr -q - - | cat")
			->setStdIn($fileContent);

		$success = $this->command->execute();
		$errorOutput = $this->command->getError();
		$stdErr = $this->command->getStdErr();
		$exitCode = $this->command->getExitCode();

		if ($success && $errorOutput === '' && $stdErr === '') {
			return $this->command->getOutput();
		} else {
			throw new OcrNotPossibleException('OCRmyPDF exited abnormally with exit-code ' . $exitCode . '. Message: ' . $errorOutput . ' ' . $stdErr);
		}
	}
}
