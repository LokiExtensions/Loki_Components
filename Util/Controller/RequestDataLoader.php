<?php declare(strict_types=1);

namespace Loki\Components\Util\Controller;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
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

    public function mergeRequestParams()
    {
        $data = $this->load();

        /** @var HttpRequest $request */
        $request = $this->request;
        if (!empty($data['currentUri'])) {
            $currentUri = explode('_', $data['currentUri']);
            $request->setParam('currentUri', $data['currentUri']);
            $request->setModuleName($currentUri[0]);
            $request->setControllerName($currentUri[1]);
            $request->setActionName($currentUri[2]);
        }

        $params = array_merge($data['request'], $this->request->getParams());
        $this->request->setParams($params);
    }

    private function sanityCheck(array $requestData = []): void
    {
        if (false === $this->ajax->isAjax()) {
            throw new RuntimeException('Not an Alpine request');
        }

        $requiredFields = ['targets', 'componentData', 'block', 'handles'];
        foreach ($requiredFields as $requiredField) {
            if (false === array_key_exists($requiredField, $requestData)) {
                throw new RuntimeException('No ' . $requiredField . ' in request');
            }
        }
    }
}
