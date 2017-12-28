<?php

namespace CsvToJson\Command;

use CsvToJson\Constant\Alerts;
use CsvToJson\Constant\Medals;
use Port\Csv\CsvReader;
use Port\SymfonyConsole\TableWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConvertCommand.
 */
class ConvertCommand extends Command
{
    const NAME = 'convert';
    const ARG_FILE = 'file';

    /**
     * @var string
     */
    private $inputPath = __DIR__.'/../../../input/';
    /**
     * @var string
     */
    private $outputPath = __DIR__.'/../../../output/';

    /**
     * ConvertCommand constructor.
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Converts CSV/TSV to JSON')
            ->addArgument(
                self::ARG_FILE,
                InputArgument::REQUIRED,
                'Filename'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument(self::ARG_FILE);
        try {
            $file = new \SplFileObject($this->inputPath.$filename);
        } catch (\RuntimeException $exception) {
            $output->writeln(sprintf('<error>File %s not found in the input directory</error>', $filename));

            return -1;
        }
        $reader = new CsvReader($file, "\t");
        $reader->setStrict(false);
        $reader->setColumnHeaders(['agent', 'medal', 'portal', 'lat', 'lng', 'intel', 'days', 'deadline']);
        $writer = new TableWriter((new Table($output))->setStyle('symfony-style-guide'));
        $data = ['Alerts' => []];
        $links = [];
        foreach ($reader as $row) {
            if ('missed' === $row['deadline'] || \in_array($row['intel'], $links, true)) {
                continue;
            }
            $links[] = $row['intel'];
            $date = \DateTime::createFromFormat('Y-m-d H:i+', $row['deadline']);
            $medal = Medals::$nextMedal[$row['medal']];
            $comment = sprintf('%s @%s (%s)', $date->format('Y-m-d H:i'), $row['agent'], $medal);
            $writer->writeItem([
                'Portal' => $row['portal'],
                'Comment' => $comment,
                'Intel' => $row['intel'],
            ]);
            $data['Alerts'][] = [
                'alerttype' => Alerts::getAlertByNumber(Medals::$days[$medal] - $row['days']),
                'nodeName' => $row['portal'],
                'lat' => $row['lat'],
                'lng' => $row['lng'],
                'comment' => $comment,
            ];
        }
        $writer->finish();
        file_put_contents($this->outputPath.str_replace('csv', 'json', $filename), json_encode($data));

        return 0;
    }
}
