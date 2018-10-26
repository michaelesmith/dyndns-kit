<?php
declare(strict_types = 1);

namespace DynDNSKit\Processor;

use DynDNSKit\Query;

class JsonProcessor implements ProcessorInterface
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @param $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @inheritdoc
     */
    public function process(Query $query): bool
    {
        if (file_exists($this->filename)) {
            if (!is_writable($this->filename)) {
                throw new ProcessorException(sprintf('The file "%s" is not writable', $this->filename));
            }
            $data = file_get_contents($this->filename);
            $json = json_decode($data, true);
            if (!$json) {
                throw new ProcessorException(sprintf('The file "%s" does not appear to be valid json', $this->filename));
            }
        } else {
            $dirname = dirname($this->filename);
            mkdir($dirname, 0777, true);
            if (!is_writable($dirname)) {
                throw new ProcessorException(sprintf('The dir "%s" is not writable', $dirname));
            }
            $json = [];
        }

        foreach ($query->getHostnames() as $hostname) {
            $json[$hostname] = $query->getIp();
        }

        file_put_contents($this->filename, json_encode($json));

        return true;
    }
}
