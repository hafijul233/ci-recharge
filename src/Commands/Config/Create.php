<?php namespace Hafiz\Commands\Config;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

/**
 *
 * Creates a new migration file.
 *
 * @package CodeIgniter\Commands
 */
class Create extends BaseCommand
{

    /**
     * The group the command is lumped under
     * when listing commands.
     *
     * @var string
     */
    protected $group = 'CI4-RechargeI';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'make:config';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Creates a custom configuration file.';

    /**
     * the Command's usage
     *
     * @var string
     */
    protected $usage = 'make:config [config_name] [Options]';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'config_name' => 'The configuration file name',
    ];

    /**
     * the Command's Options
     *
     * @var array
     */
    protected $options = [
        '-n' => 'Set Configuration namespace',
    ];

    /**
     * Creates a new migration file with the current timestamp.
     *
     * @param $insConfig
     * @param array $params
     */
    public function run($insConfig, array $params = [])
    {
        helper(['inflector', 'filesystem']);

        $name = array_shift($params);

        if (empty($name)) {
            $name = CLI::prompt(lang('Config.nameConfig'));
        }

        if (empty($name)) {
            CLI::error(lang('Config.badCreateName'));
            return;
        }

        $ns = $params['-n'] ?? CLI::getOption('n');
        /** @var string real path $homepath */
        $homepath = APPPATH;

        if (!empty($ns)) {
            // Get all namespaces
            $namespaces = Services::autoloader()->getNamespace();

            foreach ($namespaces as $namespace => $path) {
                if ($namespace === $ns) {
                    $homepath = realpath(reset($path));
                    break;
                }
            }
        } else {
            $ins = "Config";
        }


        // Always use UTC/GMT so global teams can work together
        $fileName = ucfirst($name);

        // full path
        $path = $homepath . '/Config/' . $fileName . '.php';

        // Class name should be Pascal case
        $name = ucfirst($name);

        $template = <<<EOD
<?php namespace $ins;

use CodeIgniter\Config\BaseConfig;

/**
 * @class $name configuration.
 * @author CI-Recharge
 * @package Config
 *
 */

class $name extends BaseConfig
{
		//
}

EOD;

        if (!write_file($path, $template)) {
            CLI::error(lang('Config.writeError', [$path]));
            return;
        }

        CLI::write('Created file: ' . CLI::color(str_replace($homepath, $ns, $path), 'green'));
    }

}