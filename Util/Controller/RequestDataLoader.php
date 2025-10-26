<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use RuntimeException;
use Loki\Components\Util\Ajax;

class RequestDataLoader
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly Ajax $ajax
    ) {
    }

    public function load(): array
    {
        /** @var Http $request */
        $request = $this->request;
        $data = json_decode($request->getContent(), true);
        if (empty($data)) {
            throw new RuntimeException('Not a valid request');
        }

        $this->sanityCheck($data);

        return $data;
    }

    public function mergeRequestParams(): void
    {
        $data = $this->load();
        if (empty($data['request'])) {
            return;
        }

        $requestData = new DataObject((array)$data['request']);

        /** @var HttpRequest $request */
        $request = $this->request;
        $request->setParam('currentUri', (string)$requestData->getRequestUri());
        $request->setRequestUri((string)$request->getRequestUri());
        $request->setRouteName((string)$requestData->getRouteName());
        $request->setModuleName((string)$requestData->getModuleName());
        $request->setControllerName((string)$requestData->getControllerName());
        $request->setControllerModule((string)$requestData->getControllerModule());
        $request->setActionName((string)$requestData->getActionName());

        $params = array_merge((array)$requestData->getParams(), $this->request->getParams());
        $this->request->setParams($params);
    }

    private function sanityCheck(array $requestData = []): void
    {
        if (false === $this->ajax->isAjax()) {
            throw new RuntimeException('Not an Alpine request');
        }

        $requiredFields = ['targets', 'handles', 'updates'];
        foreach ($requiredFields as $requiredField) {
            if (false === array_key_exists($requiredField, $requestData)) {
                throw new RuntimeException('No '.$requiredField.' in request');
            }
        }
    }
}
