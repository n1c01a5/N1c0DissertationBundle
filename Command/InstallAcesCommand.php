<?php

namespace N1c0\DissertationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command installs global access control entries (ACEs)
 */
class InstallAcesCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('n1c0:dissertation:installAces')
            ->setDescription('Installs global ACEs')
            ->setDefinition(array(
                new InputOption('flush', null, InputOption::VALUE_NONE, 'Flush existing Acls'),
            ))
            ->setHelp(<<<EOT
This command should be run once during the installation process of the entire bundle or
after enabling Acl for the first time.

If you have been using DissertationBundle previously without Acl and are just enabling it, you
will also need to run n1c0:comment:fixAces.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->getContainer()->has('security.acl.provider')) {
            $output->writeln('You must setup the ACL system, see the Symfony2 documentation for how to do this.');

            return;
        }

        $dissertationAcl = $this->getContainer()->get('n1c0_dissertation.acl.dissertation');

        if ($input->getOption('flush')) {
            $output->writeln('Flushing Global ACEs');

            $threadAcl->uninstallFallbackAcl();
            $commentAcl->uninstallFallbackAcl();
            $voteAcl->uninstallFallbackAcl();
        }

        $dissertationAcl->installFallbackAcl();

        $output->writeln('Global ACEs have been installed.');
    }
}
