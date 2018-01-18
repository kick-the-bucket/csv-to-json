<?php

namespace CsvToJson\Command;

use CsvToJson\Constant\Alerts;
use CsvToJson\Constant\Fields;
use CsvToJson\Constant\Medals;
use Port\Csv\CsvReader;
use Port\SymfonyConsole\TableWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConvertCommand.
 */
class ConvertCommand extends Command
{
    public const ARG_FILE = 'file';
    public const ARG_OUTPUT = 'output';
    public const NAME = 'convert';

    /**
     * @var int
     */
    private $counter = 0;
    /**
     * @var \SplFileObject
     */
    private $inputFile;
    /**
     * @var Table
     */
    private $table;
    /**
     * @var string
     */
    private $outputFile;
    /**
     * @var TableWriter
     */
    private $writer;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    public function __destruct()
    {
        if ($this->writer instanceof TableWriter) {
            $this->table
                ->addRow(new TableSeparator())
                ->addRow(['Total', $this->counter])
            ;
            $this->writer->finish();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Converts CSV/TSV to JSON')
            ->addArgument(
                self::ARG_FILE,
                InputArgument::REQUIRED,
                'Filename'
            )
            ->addArgument(
                self::ARG_OUTPUT,
                InputArgument::OPTIONAL,
                'Output directory'
            )
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $file = $input->getArgument(self::ARG_FILE);
        $inputFile = is_file($file) ? $file : getcwd().DIRECTORY_SEPARATOR.$file;
        try {
            $this->inputFile = new \SplFileObject($inputFile);
        } catch (\Exception $exception) {
            throw new \InvalidArgumentException(sprintf('File %s not found in the working directory</error>', $file));
        }

        $outputDir = $input->getArgument(self::ARG_OUTPUT);
        $this->outputFile = sprintf(
            '%s%s%s-alerts.json',
            is_dir($outputDir) ? $outputDir : getcwd(),
            DIRECTORY_SEPARATOR === $outputDir[\strlen($outputDir) - 1] ? '' : DIRECTORY_SEPARATOR,
            pathinfo($file, PATHINFO_FILENAME)
        );

        if ($output->isVerbose()) {
            $this->table = (new Table($output))->setStyle('symfony-style-guide');
            $this->writer = new TableWriter($this->table);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $alerts = [];
        foreach ($this->createReader($this->inputFile) as $row) {
            if (!$this->isRowValid($row)) {
                continue;
            }
            $alerts[] = $this->generateAlert($row);
            $this->outputRow($output, $row);
            ++$this->counter;
        }
        $this->writeAlerts($alerts);
    }

    /**
     * @param $file
     *
     * @return CsvReader
     */
    private function createReader($file): CsvReader
    {
        $reader = new CsvReader($file, "\t");
        $reader->setStrict(false);
        $reader->setColumnHeaders(Fields::HEADERS);

        return $reader;
    }

    /**
     * @param array $row
     *
     * @return array
     */
    private function generateAlert(array $row): array
    {
        return [
            'alerttype' => Alerts::getAlertByDays(Medals::getDaysToNextMedal($row[Fields::MEDAL], $row[Fields::DAYS])),
            'nodeName' => $row[Fields::PORTAL],
            'lat' => $row[Fields::LAT],
            'lng' => $row[Fields::LNG],
            'comment' => $this->getComment($row),
        ];
    }

    /**
     * @param array $row
     *
     * @return string
     */
    private function getComment(array $row): string
    {
        return sprintf(
            '%s @%s (%s)',
            \DateTime::createFromFormat('Y-m-d H:i+', $row[Fields::DEADLINE])->format('Y-m-d H:i'),
            $row[Fields::AGENT],
            Medals::getNextMedal($row[Fields::MEDAL])
        );
    }

    /**
     * @param array $row
     *
     * @return bool
     */
    private function isRowValid(array $row): bool
    {
        static $links = [];
        $isValid = !empty($row[Fields::DEADLINE])
            && 'missed' !== $row[Fields::DEADLINE]
            && !\in_array($row[Fields::INTEL], $links, true)
        ;
        if ($isValid) {
            $links[] = $row[Fields::INTEL];
        }

        return $isValid;
    }

    /**
     * @param OutputInterface $output
     * @param array           $row
     */
    private function outputRow(OutputInterface $output, array $row): void
    {
        if ($output->isVerbose()) {
            $comment = $this->getComment($row);
            $this->writer->writeItem([
                'Portal' => $row[Fields::PORTAL],
                'Comment' => $comment,
                'Intel' => $row[Fields::INTEL],
            ]);
        }
    }

    /**
     * @param array $alerts
     */
    private function writeAlerts(array $alerts): void
    {
        file_put_contents($this->outputFile, json_encode(['Alerts' => $alerts]));
    }
}
