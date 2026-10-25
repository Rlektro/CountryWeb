<?php

namespace App\Controller;

use Exception;
use App\Model\CountryScenarios;
use App\Model\Country;
use App\Model\Exceptions\{
DuplicatedNameException,
InvalidCodeException,
CountryNotFoundException,
DuplicatedCodeException,
ErrorCodes,
ValidationException,
};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Route(path: '/api/country', name: 'app_api_country')]
final class CountryController extends AbstractController
{
    public function __construct(
        private readonly CountryScenarios $countries
    ) {

    }
    #[Route('', name: 'app_api_country_root', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        /*$countriesPreview = [];
        foreach ($this->countries->getAll() as $country) {
            $countryPreview = $this->buildCountryPreview(country: $country, request: $request);
            array_push($countriesPreview, $countryPreview);
        }*/
        return $this->json(data: $this->countries->getAll(), status: 200);
    }
    #[Route(path:'/{code}', name:'app_api_country_code', methods: ['GET'])] 
    public function get(string $code) :JsonResponse{
        try{
            $country = $this->countries->get($code);
            return $this->json(data: $country);
        }
        catch(InvalidCodeException $ex){
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        }
        catch(CountryNotFoundException $ex){
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 404);
            return $response;
        }
    }
    #[Route(path: '', name: 'app_api_country_add', methods: ['POST'])]
    public function add(Request $request, #[MapRequestPayload] Country $country) : JsonResponse {
        try {
            $this->countries->store(country: $country);
            return new JsonResponse(data: null, status: 204);
        } catch (InvalidCodeException | ValidationException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (DuplicatedCodeException | DuplicatedNameException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 409);
            return $response;
        }
    }
        // удаление аэропорта
    #[Route(path: '/{code}', name: 'app_api_country_remove', methods: ['DELETE'])]
    public function remove(string $code) : JsonResponse {
        try {
            $this->countries->delete($code);
            return $this->json(data: null, status: 204);
        } catch (InvalidCodeException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (CountryNotFoundException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 404);
            return $response;
        }
    }

    // редактирование аэропорта
    #[Route(path: '/{code}', name: 'app_api_country_edit', methods: ['PATCH'])]
    public function edit(Request $request, string $code, #[MapRequestPayload] Country $country) : JsonResponse {
        try {
            $this->countries->edit(code: $code, country: $country);
            return $this->json(data: $country, status: 200);
        } catch (InvalidCodeException | ValidationException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 400);
            return $response;
        } catch (CountryNotFoundException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 404);
            return $response;
        } catch (DuplicatedNameException $ex) {
            $response = $this->buildErrorResponse(ex: $ex);
            $response->setStatusCode(code: 409);
            return $response;
        }
    }

        // вспомогательный метод
    private function buildErrorResponse(Exception $ex): JsonResponse {
        return $this->json(data: [
            'errorCode' => $ex->getCode(),
            'errorMessage' => $ex->getMessage(),
        ]);
    } 
    private function buildCountryPreview(Country $country, Request $request) : CountryPreview {
        $uri = sprintf(
            '%s://%s/api/country/%s', 
            $request->getScheme(), 
            $request->getHttpHost(),
            $country->isoAlpha3,
        );
        return new CountryPreview(
            name: $country->shortName, 
            uri: $uri,
        );
    }
}
