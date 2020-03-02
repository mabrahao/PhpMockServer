<?php

namespace mabrahao\MockServer;

use mabrahao\MockServer\ExpectationRepository\ExpectationRepositoryInterface;
use mabrahao\MockServer\Matchers\RequestMatcher;

class RequestHandler
{
    /** @var ResponseBuilder */
    private $responseBuilder;
    /** @var RequestMatcher */
    private $requestMatcher;
    /** @var ExpectationRepositoryInterface */
    private $expectationRepository;
    /** @var array */
    private $serverData;
    /** @var array */
    private $formData;
    /** @var string|array|null */
    private $inputData;

    /**
     * RequestHandler constructor.
     * @param RequestMatcher $requestMatcher
     * @param ResponseBuilder $responseBuilder
     * @param ExpectationRepositoryInterface $expectationRepository
     * @param array $serverData
     * @param array $formData
     * @param $inputData
     */
    public function __construct(
        RequestMatcher $requestMatcher,
        ResponseBuilder $responseBuilder,
        ExpectationRepositoryInterface $expectationRepository,
        array $serverData,
        array $formData,
        $inputData
    ) {
        $this->responseBuilder = $responseBuilder;
        $this->requestMatcher = $requestMatcher;
        $this->expectationRepository = $expectationRepository;
        $this->serverData = $serverData;
        $this->formData = $formData;
        $this->inputData = $inputData;
    }

    public function dispatch()
    {
        $expectations = $this->expectationRepository->fetchAll();
        foreach($expectations as $expectation) {
            if($this->requestMatcher->matches($expectation, $this->serverData, $this->formData, $this->inputData)) {
                // TODO: handle Times
                $this->responseBuilder->buildFrom($expectation);
                return;
            }
        }

        throw new MatchNotFoundException('No match was found for this request!');
    }
}
