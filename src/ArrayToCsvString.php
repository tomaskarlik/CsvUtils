<?php

declare(strict_types = 1);

namespace TomasKarlik\CsvUtils;

use Exception;
use InvalidArgumentException;


class ArrayToCsvString
{

	public const SEPARATOR_COMMA = ',';
	public const SEPARATOR_SEMICOLON = ';';
	public const SEPARATOR_TAB = '	';

	/**
	 * @var callable[]
	 */
	private $columnsCallbacks = [];

	/**
	 * @var string
	 */
	private $glue = self::SEPARATOR_SEMICOLON;

	/**
	 * @var array
	 */
	private $header = [];


	/**
	 * @param mixed $column
	 * @param callable $callback
	 */
	public function addColumnCallback($column, callable $callback): ArrayToCsvString
	{
		if (isset($this->columnsCallbacks[$column])) {
			throw new InvalidArgumentException(sprintf('%s: column "%s" callback exists!', __CLASS__, $column));
		}

		$this->columnsCallbacks[$column] = $callback;
		return $this;
	}


	public function setGlue(string $glue): ArrayToCsvString
	{
		if (empty($glue) || preg_match('/[\n\r"]/s', $glue)) {
			throw new InvalidArgumentException(sprintf('%s: glue cannot be an empty or reserved character!', __CLASS__));
		}

		$this->glue = $glue;
		return $this;
	}


	public function getGlue(): string
	{
		return $this->glue;
	}


	public function setHeader(array $header): ArrayToCsvString
	{
		$this->header = $header;
		return $this;
	}


	public function convert(array $rows): string
	{
		$buffer = fopen('php://temp', 'r+b');
		if ($buffer === FALSE) {
			throw new Exception(sprintf('%s: error create buffer!', __CLASS__));
		}

		if (count($this->header)) { // add header
			fputcsv($buffer, $this->header, $this->glue);
		}

		foreach ($rows as $row) {
			foreach ($this->columnsCallbacks as $column => $callback) {
				if ( ! isset($row[$column])) {
					continue;
				}
				$row[$column] = $callback($row[$column]);
			}
			fputcsv($buffer, $row, $this->glue);
		}

		rewind($buffer);
		$data = stream_get_contents($buffer);
		fclose($buffer);

		return $data;
	}

}
