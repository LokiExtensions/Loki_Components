<?php declare(strict_types=1);

namespace Yireo\LokiComponents\Config\XmlConfig;

use Magento\Framework\Config\Dom;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\Reader\Filesystem;
use Magento\Framework\Config\ValidationStateInterface;
use Yireo\LokiCheckout\Config\XmlConfig\Converter;

class FileReader extends Filesystem
{
    public function __construct(
        FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationStateInterface $validationState,
        $fileName = 'yireo_loki_components.xml',
        $idAttributes = [
            '/components/component/name' => 'id',
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
