<?php


namespace Christophedlr\Pharcreator;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class CreatePharCommand extends Command
{
    protected static $defaultName = 'create';

    protected $pharFileName;
    protected $projectDir;
    protected $stubFile;
    protected $compression;

    protected function configure()
    {
        $this
            ->setDescription('Create a PHAR file')
            ->setHelp('This command allow you tocreate a PHAR file')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        name:
        $question = new Question("<info>Name of PHAR file</info>\n");
        $this->pharFileName = $helper->ask($input, $output, $question);

        if (empty($this->pharFileName)) {
            $output->writeln("<error>Not empty value accepted</error>");
            goto name;
        }

        $output->writeln(sprintf("<bg=green;fg=black>%s PHAR name file</>", $this->pharFileName));

        $question = new Question("\n<info>Project directory</info>\n");
        $this->projectDir = $helper->ask($input, $output, $question);

        if (empty($this->projectDir)) {
            $this->projectDir = dirname(__FILE__);
        }

        $output->writeln(sprintf("<bg=green;fg=black>%s project dir</>", $this->projectDir));

        $question = new Question("\n<info>Stub file</info>\n");
        $this->stubFile = $helper->ask($input, $output, $question);

        if (!empty($this->stubFile)) {
            $output->writeln(sprintf("<bg=green;fg=black>%s stub file</>", $this->stubFile));
        } else {
            $output->writeln("<bg=green;fg=black>None stub file</>");
        }

        $question = new ChoiceQuestion(
            "\n<info>Type of compression (default gzip)</info>\n",
            ['gzip', 'bzip2', 'none'],
            0
        );
        $question->setErrorMessage('%s is invalid.');
        $this->compression = $helper->ask($input, $output, $question);

        $output->writeln(sprintf("<bg=green;fg=black>%s Compression</>\n", $this->compression));

        $this->createPhar($output);

        return 0;
    }

    private function createPhar(OutputInterface $output)
    {
        $output->writeln("<bg=green;fg=black>Processing</>\n");
        $compression = ($this->compression === 'gzip') ? \Phar::GZ : (($this->compression === 'bzip2') ? \Phar::BZ2 : \Phar::NONE);

        $p = new \Phar($this->pharFileName);

        $p->buildFromDirectory($this->projectDir);

        if (!empty($this->stubFile)) {
            $p->setDefaultStub($this->stubFile, $this->stubFile);
        }

        $p->compressFiles($compression);

        $output->writeln("<bg=green;fg=black>Successfully created</>\n");
    }
}
