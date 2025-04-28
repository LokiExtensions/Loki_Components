<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig;

use Magento\Framework\Config\Dom;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\Reader\Filesystem;
use Magento\Framework\Config\ValidationStateInterface;

class FileReader extends Filesystem
{
    public function __construct(
        FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationStateInterface $validationState,
        $fileName = 'loki_components.xml',
        $idAttributes = [
            '/components/group/name' => 'id',
            '/components/group/component/name' => 'id',
            '/components/group/target/name' => 'id',
            '/components/component/target/name' => 'id',
            '/components/component/validator/name' => 'id',
            '/components/component/filter/name' => 'id',
        ],
        $domDocumentClass = Dom::class,
        $defaultScope = 'global'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }
}
