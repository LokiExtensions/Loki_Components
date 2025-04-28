<?php
declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;

class SchemaLocator implements SchemaLocatorInterface
{
    const CONFIG_FILE_SCHEMA = 'loki_components.xsd';

    protected string $schema = '';
    protected string $perFileSchema = '';

    public function __construct(
       private readonly Reader $moduleReader
    ) {
    }

    public function getSchema()
    {
        if ($this->schema === '' || $this->schema === '0') {
            $this->schema = $this->getModuleEtcFolder().'/'.self::CONFIG_FILE_SCHEMA;
        }

        return $this->schema;
    }

    public function getPerFileSchema()
    {
        if ($this->perFileSchema === '' || $this->perFileSchema === '0') {
            $this->perFileSchema = $this->getModuleEtcFolder().'/'.self::CONFIG_FILE_SCHEMA;
        }

        return $this->perFileSchema;
    }

    private function getModuleEtcFolder(): string
    {
        return $this->moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Yireo_LokiComponents');
    }
}
