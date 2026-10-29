<?php

namespace App\Controllers;

use App\Services\DataService;
use App\Validators\LibroValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class Libro {
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function read(Request $request, Response $response, array $args): Response {

        try {
            $dataService = $this->container->get(DataService::class);
            $path = isset($args['id']) ? 'biblioteca_libros/' . rawurlencode($args['id']) : 'biblioteca_libros';
            $upstream = $dataService->get($path);

            return $this->json($response, json_decode((string) $upstream->getBody(), true) ?? [],
            $upstream->getStatusCode());

        } catch (ConnectException $e) {
            return $this->json($response, ['error' => 'El servicio de datos no está disponible'], 502);
        } catch (RequestException $e) {
            return $this->json($response, ['error' => 'No se pudo consultar el servicio de datos'], 502);
        }
    }

    public function create(Request $request, Response $response, array $args): Response {

        try {
            $request->getBody()->rewind();
            $body = (string) $request->getBody();
            $data = json_decode($body, true);

            if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
                return $this->json($response, ['errors' => ['body' => 'Debe enviar un objeto JSON válido.']], 422);
            }

            $errors = (new LibroValidator())->validate($data);
            if ($errors !== []) {
                return $this->json($response, ['errors' => $errors], 422);
            }

            $dataService = $this->container->get(DataService::class);
            $upstream = $dataService->post('biblioteca_libros', json_encode($data, JSON_THROW_ON_ERROR));

            return $this->json($response, json_decode((string) $upstream->getBody(), true) ?? [],
            $upstream->getStatusCode());

        } catch (ConnectException $e) {
            return $this->json($response, ['error' => 'El servicio de datos no está disponible'], 502);
        } catch (RequestException $e) {
            return $this->json($response, ['error' => 'No se pudo consultar el servicio de datos'], 502);
        }
    }

    public function update(Request $request, Response $response, array $args): Response {

        try {
            $request->getBody()->rewind();
            $body = (string) $request->getBody();
            $data = json_decode($body, true);

            if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
                return $this->json($response, ['errors' => ['body' => 'Debe enviar un objeto JSON válido.']], 422);
            }

            $errors = (new LibroValidator())->validate($data);
            if ($errors !== []) {
                return $this->json($response, ['errors' => $errors], 422);
            }

            $dataService = $this->container->get(DataService::class);
            $upstream = $dataService->put('biblioteca_libros/' . rawurlencode($args['id']), $body);

            return $this->json($response, json_decode((string) $upstream->getBody(), true) ?? [],
            $upstream->getStatusCode());

        } catch (ConnectException $e) {
            return $this->json($response, ['error' => 'El servicio de datos no está disponible'], 502);
        } catch (RequestException $e) {
            return $this->json($response, ['error' => 'No se pudo consultar el servicio de datos'], 502);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response {

        try {
            $dataService = $this->container->get(DataService::class);
            $upstream = $dataService->delete('biblioteca_libros/' . rawurlencode($args['id']));

            if ($upstream->getStatusCode() === 204) {
                return $response->withStatus(204);
            }

            return $this->json($response, json_decode((string) $upstream->getBody(), true) ?? [],
            $upstream->getStatusCode());

        } catch (ConnectException $e) {
            return $this->json($response, ['error' => 'El servicio de datos no está disponible'], 502);
        } catch (RequestException $e) {
            return $this->json($response, ['error' => 'No se pudo consultar el servicio de datos'], 502);
        }
    }

    private function json(Response $response, array $payload, int $status): Response {
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
